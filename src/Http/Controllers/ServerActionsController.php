<?php

namespace TomatoPHP\TomatoEddy\Http\Controllers;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use TomatoPHP\TomatoAdmin\Facade\Tomato;
use TomatoPHP\TomatoEddy\Enums\Firewall\RuleAction;
use TomatoPHP\TomatoEddy\Http\Requests\CreateServerRequest;
use TomatoPHP\TomatoEddy\Enums\Infrastructure\ServerStatus;
use TomatoPHP\TomatoEddy\Infrastructure\HetznerCloud;
use TomatoPHP\TomatoEddy\Jobs\AttachStorageToServer;
use TomatoPHP\TomatoEddy\Jobs\CreateBulkServers;
use TomatoPHP\TomatoEddy\Jobs\CreateBulkSites;
use TomatoPHP\TomatoEddy\Jobs\DeleteServerFromInfrastructure;
use TomatoPHP\TomatoEddy\Jobs\DeleteStorageFromServer;
use TomatoPHP\TomatoEddy\Jobs\ResetServerFromInfrastructure;
use TomatoPHP\TomatoEddy\Jobs\RestartServerFromInfrastructure;
use TomatoPHP\TomatoEddy\Jobs\ScanAccounts;
use TomatoPHP\TomatoEddy\Jobs\StartServerFromInfrastructure;
use TomatoPHP\TomatoEddy\Jobs\StopServerFromInfrastructure;
use TomatoPHP\TomatoEddy\Jobs\UpdateUserPublicKey;
use TomatoPHP\TomatoEddy\Mail\ServerProvisioned;
use TomatoPHP\TomatoEddy\Models\FirewallRule;
use TomatoPHP\TomatoEddy\Services\KeyPairGenerator;
use TomatoPHP\TomatoEddy\Models\Credentials;
use TomatoPHP\TomatoEddy\Models\Server;
use TomatoPHP\TomatoEddy\Models\SshKey;
use TomatoPHP\TomatoEddy\Enums\Services\Provider;
use TomatoPHP\TomatoEddy\Tables\ServerTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use ProtoneMedia\Splade\Facades\Toast;
use ProtoneMedia\Splade\SpladeTable;

/**
 * @codeCoverageIgnore Handled by Dusk tests.
 */
class ServerActionsController extends Controller
{
    protected string $model;

    public function __construct()
    {
        $this->model = Server::class;
    }

    /**
     * @return RedirectResponse
     */
    public function restart(Server $server)
    {
        dispatch(new RestartServerFromInfrastructure($server, $this->user()));

        $this->logActivity(__("Restarted server ':server'", ['server' => $server->name]), $server);

        Toast::success(__('Your server is being restarted.'))->autoDismiss(2);

        return back();
    }

    /**
     * @return RedirectResponse
     */
    public function start(Server $server)
    {
        dispatch(new StartServerFromInfrastructure($server, $this->user()));

        $this->logActivity(__("Started server ':server'", ['server' => $server->name]), $server);

        $server->status = ServerStatus::Running;
        $server->save();

        Toast::success(__('Your server is being started.'))->autoDismiss(2);

        return back();
    }

    /**
     * @return RedirectResponse
     */
    public function stop(Server $server)
    {
        dispatch(new StopServerFromInfrastructure($server, $this->user()));

        $server->status = ServerStatus::Stopped;
        $server->save();

        $this->logActivity(__("Stopped server ':server'", ['server' => $server->name]), $server);

        Toast::success(__('Your server is being stopped.'))->autoDismiss(2);

        return back();
    }

    public function resetView(Server $server)
    {
        return view('tomato-eddy::servers.password', [
            'server' => $server,
        ]);
    }

    public function reset(Server $server, Request $request)
    {

        $request->validate([
            'password' => 'required|min:8',
        ]);

        dispatch(new ResetServerFromInfrastructure($server, config('tomato-eddy.server_defaults.username'), $request->get('password')));

        $this->logActivity(__("Resting server ':server'", ['server' => $server->name]), $server);

        Toast::success(__('Your server is password reset.'))->autoDismiss(2);

        return back();
    }

    public function storageView(Server $server)
    {
        return view('tomato-eddy::servers.storage', [
            'server' => $server,
        ]);
    }

    public function storage(Server $server, Request $request)
    {

        $request->validate([
            'size' => 'required|int',
        ]);

        dispatch(new AttachStorageToServer($server, $request->get('size')));

        $this->logActivity(__("Resting server ':server'", ['server' => $server->name]), $server);

        Toast::success(__('Your storage has been created and attached to the server.'))->autoDismiss(2);

        return back();
    }

    public function destroyVolumes(Server $server)
    {
        dispatch(new DeleteStorageFromServer($server));

        $this->logActivity(__("Destroy Storage Success server ':server'", ['server' => $server->name]), $server);

        Toast::success(__('Your server storage has been deleted.'))->autoDismiss(2);

        return back();
    }

    public function build(KeyPairGenerator $keyPairGenerator)
    {
        dispatch(new CreateBulkSites($this->user(), $keyPairGenerator));

        Toast::success(__('Your servers is building now!'))->autoDismiss(2);

        return back();
    }

    public function restartAll()
    {
        $servers = Server::all();
        foreach ($servers as $server) {
            dispatch(new RestartServerFromInfrastructure($server, $this->user()));
        }

        Toast::success(__('Your servers is restarting now!'))->autoDismiss(2);

        return back();
    }


    public function connect(Request $request)
    {
        $credentials = $this->user()
            ->credentials()
            ->provider(Provider::forServers())
            ->select('id', 'name', 'provider')
            ->get()
            ->mapWithKeys(fn (Credentials $credentials) => [$credentials->id => $credentials->nameWithProvider]);

        if ($credentials->isEmpty() && ! $request->query('withoutCredentials')) {
            return view('tomato-eddy::servers.credentials-missing');
        }

        $sshKeys = $this->user()
            ->sshKeys()
            ->select('id', 'name')
            ->get()
            ->mapWithKeys(fn (SshKey $sshKey) => [$sshKey->id => $sshKey->name]);

        $defaultCredentials = $request->query('credentials') && $credentials->has($request->query('credentials'))
            ? $request->query('credentials')
            : Credentials::first()?->id;

        return view('tomato-eddy::servers.connect', [
            'defaultCredentials' => $defaultCredentials,
            'credentials' => $credentials,
            'sshKeys' => $sshKeys,
            'hasGithubCredentials' => $this->user()->hasGithubCredentials(),
        ]);
    }

    public function link(Request $request, KeyPairGenerator $keyPairGenerator)
    {
        $credentialsExistsRule = Rule::exists('credentials', 'id')->where(function (Builder $query) {
            $query->where('user_id', $this->user()->id)
                ->where(fn (Builder $query) => $query->whereNull('team_id')->orWhere('team_id', $this->user()->currentTeam->id));
        });

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'credentials_id' => ['nullable', 'required_unless:custom_server,1', $credentialsExistsRule],
            'public_ipv4' => ['nullable', 'required_if:custom_server,1', 'ipv4'],
            'ssh_keys' => ['array'],
            'ssh_keys.*' => [Rule::exists('ssh_keys', 'id')->where('user_id', $this->user()->id)],
            'add_key_to_github' => ['boolean'],
        ]);

        /** @var Credentials|null */
        $credentials = $this->user()->credentials()
            ->findOrFail($request->input('credentials_id'));


        if($credentials->provider === Provider::HetznerCloud){
            $checkAllServersOnTheProvder = new HetznerCloud($credentials->credentials['hetzner_cloud_token']);
            $checkAllServersOnTheProvder = $checkAllServersOnTheProvder->getAllServers();

            foreach ($checkAllServersOnTheProvder as $server){
                if($server['public_net']['ipv4']['ip'] === $request->input('public_ipv4')){
                    $newServer = $this->team()->servers()->make([
                        'name' => $request->input('name'),
                        'credentials_id' => $credentials?->id,
                        'region' => $server['datacenter']['name'],
                        'type' => $server['server_type']['name'],
                        'image' => $server['image']['name']
                    ]);

                    if($server['volumes'] && count($server['volumes'])){
                        $newServer->storage_id = $server['volumes'][0];
                    }

                    $keyPair = $keyPairGenerator->ed25519();
                    $newServer->public_key = $keyPair->publicKey;
                    $newServer->private_key = $keyPair->privateKey;

                    $newServer->working_directory = config('tomato-eddy.server_defaults.working_directory');
                    $newServer->ssh_port = config('tomato-eddy.server_defaults.ssh_port');
                    $newServer->username = config('tomato-eddy.server_defaults.username');

                    $newServer->public_ipv4 = $request->input('public_ipv4');
                    $newServer->provider = $credentials->provider;
                    $newServer->created_by_user_id = $this->user()->id;

                    $newServer->password = Str::password(symbols: false);
                    $newServer->database_password = Str::password(symbols: false);

                    $newServer->save();


                    $newServer->forceFill([
                        'user_public_key' => SshKey::find($request->input('ssh_keys')[0]??null)?->public_key,
                        'provider_id' => $server['id'],
                        'provisioned_at' => now(),
                        'status' => ServerStatus::Running,
                    ])->save();

                    Mail::to($newServer->createdByUser)->queue(new ServerProvisioned($newServer));

                    $this->logActivity(__("Created server ':server'", ['server' => $newServer->name]), $newServer);

                    Toast::success(__('Your server is has been connected!'));

                    return to_route('admin.servers.show', $newServer);
                }
            }
        }
    }

    public function disconect(Server $server)
    {
        $this->logActivity(__("Deleted server ':server'", ['server' => $server->name]), $server);
        $server->delete();

        Toast::success(__('Your server is being disconected.'))->autoDismiss(2);
        return redirect()->route('admin.servers.index');
    }
}
