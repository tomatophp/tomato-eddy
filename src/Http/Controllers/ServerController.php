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
class ServerController extends Controller
{
    protected string $model;

    public function __construct()
    {
        $this->model = Server::class;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        return Tomato::index(
            request: $request,
            model: $this->model,
            view: 'tomato-eddy::servers.index',
            table: ServerTable::class,
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
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

        return view('tomato-eddy::servers.create', [
            'defaultCredentials' => $defaultCredentials,
            'credentials' => $credentials,
            'sshKeys' => $sshKeys,
            'hasGithubCredentials' => $this->user()->hasGithubCredentials(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateServerRequest $request, KeyPairGenerator $keyPairGenerator)
    {
        $customServer = $request->boolean('custom_server');

        /** @var Credentials|null */
        $credentials = $customServer ? null : $this->user()->credentials()
            ->findOrFail($request->input('credentials_id'));

        if ($request->has('multi') && $request->has('count')) {
            CreateBulkServers::dispatch(
                $request->get('count'),
                $credentials,
                $request->input('region'),
                $request->input('type'),
                $request->input('image'),
                $this->user(),
                $request->input('public_ipv4'),
                $request->input('ssh_keys'),
                $keyPairGenerator
            );

            $this->logActivity(__('Bulk Servers Has Been Created'));

            Toast::success(__('Bulk Servers Has Been Created'));

            return to_route('admin.servers.index');
        } else {
            /** @var Server */
            $server = $this->team()->servers()->make([
                'name' => $request->input('name'),
                'credentials_id' => $credentials?->id,
                'region' => $request->input('region'),
                'type' => $request->input('type'),
                'image' => $request->input('image'),
            ]);

            $keyPair = $keyPairGenerator->ed25519();
            $server->public_key = $keyPair->publicKey;
            $server->private_key = $keyPair->privateKey;

            $server->working_directory = config('tomato-eddy.server_defaults.working_directory');
            $server->ssh_port = config('tomato-eddy.server_defaults.ssh_port');
            $server->username = config('tomato-eddy.server_defaults.username');

            $server->password = Str::password(symbols: false);
            $server->database_password = Str::password(symbols: false);

            $server->public_ipv4 = $customServer ? $request->input('public_ipv4') : null;
            $server->provider = $customServer ? Provider::CustomServer : $credentials->provider;
            $server->created_by_user_id = $this->user()->id;

            $server->save();
            $server->dispatchCreateAndProvisionJobs(
                SshKey::whereKey($request->input('ssh_keys'))->get(),
                $request->boolean('add_key_to_github') ? $this->user()->githubCredentials : null,
            );

            $this->logActivity(__("Created server ':server'", ['server' => $server->name]), $server);

            Toast::success(__('Your server is being created and provisioned.'));

            return to_route('admin.servers.show', $server);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(Server $server)
    {
        if ($server->status === ServerStatus::Deleting) {
            Toast::warning(__('Your server is being deleted.'));

            return to_route('admin.servers.index');
        }

        if (! $server->provisioned_at) {
            return view('tomato-eddy::servers.provisioning', [
                'server' => $server,
            ]);
        }

        return view('tomato-eddy::servers.show', [
            'server' => $server,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Server $server)
    {
        $server->forceFill([
            'status' => ServerStatus::Deleting,
            'uninstallation_requested_at' => now(),
        ])->save();

        dispatch(new DeleteServerFromInfrastructure($server, $this->user()));

        $this->logActivity(__("Deleted server ':server'", ['server' => $server->name]), $server);

        Toast::message(__('Your server is being deleted.'));

        return to_route('admin.servers.index');
    }
}
