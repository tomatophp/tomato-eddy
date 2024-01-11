<?php

namespace TomatoPHP\TomatoEddy\Http\Controllers;

use TomatoPHP\TomatoEddy\Enums\Enum;
use TomatoPHP\TomatoEddy\Http\Requests\UpdateSiteRequest;
use TomatoPHP\TomatoEddy\Jobs\AddServerSshKeyToGithub;
use TomatoPHP\TomatoEddy\Jobs\FireEventAPI;
use TomatoPHP\TomatoEddy\Jobs\InstallCron;
use TomatoPHP\TomatoEddy\Jobs\InstallDaemon;
use TomatoPHP\TomatoEddy\Jobs\InstallDatabase;
use TomatoPHP\TomatoEddy\Jobs\InstallDatabaseUser;
use TomatoPHP\TomatoEddy\Jobs\UninstallSite;
use TomatoPHP\TomatoEddy\Models\SiteTemplate;
use TomatoPHP\TomatoEddy\Services\KeyPair;
use TomatoPHP\TomatoEddy\Services\KeyPairGenerator;
use TomatoPHP\TomatoEddy\Models\DatabaseUser;
use TomatoPHP\TomatoEddy\Models\Server;
use TomatoPHP\TomatoEddy\Models\Site;
use TomatoPHP\TomatoEddy\Enums\Models\SiteType;
use TomatoPHP\TomatoEddy\Enums\Models\TlsSetting;
use TomatoPHP\TomatoEddy\Enums\Server\PhpVersion;
use TomatoPHP\TomatoEddy\Services\Cloudflare;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use ProtoneMedia\Splade\Facades\Toast;
use ProtoneMedia\Splade\SpladeTable;
use TomatoPHP\TomatoEddy\Tables\SiteTable;

/**
 * @codeCoverageIgnore Handled by Dusk tests.
 */
class SiteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Server $server)
    {
        return view('tomato-eddy::sites.index', [
            'server' => $server,
            'sites' => (new SiteTable($server->sites()->with('latestDeployment'), $server))
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, Server $server, KeyPairGenerator $keyPairGenerator)
    {
        $deployKeyUuid = Str::uuid()->toString();

        $keyPair = Cache::remember(
            key: "deploy-key-{$server->id}-{$deployKeyUuid}",
            ttl: config('session.lifetime') * 60,
            callback: fn () => $keyPairGenerator->ed25519()
        );

        return view('tomato-eddy::sites.create', [
            'uuid' => Str::uuid()->toString(),
            'deployKey' => $keyPair,
            'deployKeyUuid' => $deployKeyUuid,
            'server' => $server,
            'phpVersions' => $server->installedPhpVersions(),
            'types' => Enum::options(SiteType::class),
            'hasGithubCredentials' => $this->user()->hasGithubCredentials(),
            'hasCloudflareCredential' => $this->user()->hasCloudflareCredential(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Server $server, Request $request)
    {
        if($request->has('site_template') && $request->get('site_template')){
            $request->validate([
                'address' => ['required', 'string', 'max:255', Rule::unique('sites', 'address')->where('server_id', $server->id)],
                'site_template' => ['required', 'string', 'exists:site_templates,id'],
                'deploy_key_uuid' => ['nullable', 'string', 'uuid'],
            ]);

            $siteTemplate = SiteTemplate::find($request->get('site_template'));
            $siteUsername = Str::of($request->get('address'))->explode('.')->first();
            $request->merge([
                'php_version' => $siteTemplate->php_version,
                'type' => $siteTemplate->type,
                'web_folder' => $siteTemplate->web_folder,
                'zero_downtime_deployment' => $siteTemplate->zero_downtime_deployment,
                'repository_url' => $siteTemplate->repository_url,
                'repository_branch' => $siteTemplate->repository_branch,
                'add_dns_zone_to_cloudflare' => $siteTemplate->add_dns_zone_to_cloudflare,
                'add_server_ssh_key_to_github' => $siteTemplate->add_server_ssh_key_to_github,
                'has_database' => $siteTemplate->has_database,
                'database_name' => $siteTemplate->database_name.'-'.$siteUsername,
                'database_user' => $siteTemplate->database_user.'-'.$siteUsername,
                'database_password' => $siteTemplate->database_password,
                'has_queue' => $siteTemplate->has_queue,
                'has_schedule' => $siteTemplate->has_schedule,
            ]);

            $data = $request->all();
        }
        else {
            $data = $request->validate([
                'address' => ['required', 'string', 'max:255', Rule::unique('sites', 'address')->where('server_id', $server->id)],
                'php_version' => ['required', Enum::rule(PhpVersion::class), Rule::in(array_keys($server->installedPhpVersions()))],
                'type' => ['required', Enum::rule(SiteType::class)],
                'web_folder' => [Enum::requiredUnless(SiteType::Wordpress, 'type'), 'string', 'max:255'],
                'zero_downtime_deployment' => ['boolean'],
                'repository_url' => ['nullable', 'string', 'max:255'],
                'repository_branch' => ['nullable', 'string', 'max:255'],
                'deploy_key_uuid' => ['nullable', 'string', 'uuid'],
            ]);
        }


        if($request->has('add_dns_zone_to_cloudflare') && $request->get('add_dns_zone_to_cloudflare')){
            //Link with Cloudflare
            $cloudflare = new Cloudflare();
            $cloudflare->create($request->get('address'), $server->public_ipv4);

            $this->logActivity(__("Created Cloudflare ':address'", ['address' => $request->get('address')]));
        }
        /** @var Site */
        $site = $server->sites()->make(Arr::except($data, 'deploy_key_uuid'));
        $site->tls_setting = TlsSetting::Auto;
        $site->user = $server->username;
        $site->path = "/home/{$site->user}/{$site->address}";
        $site->forceFill($site->type->defaultAttributes($site->zero_downtime_deployment));

        if ($data['deploy_key_uuid']) {
            /** @var KeyPair|null */
            $deployKey = Cache::get("deploy-key-{$server->id}-{$data['deploy_key_uuid']}");

            if (! $deployKey) {
                Toast::danger(__('The deploy key has expired. Please try again.'));

                return back();
            }

            $site->deploy_key_public = $deployKey->publicKey;
            $site->deploy_key_private = $deployKey->privateKey;
        }

        $site->save();


        if(
            $request->get('has_database') &&
            !empty($request->get('database_name')) &&
            !empty($request->get('database_user')) &&
            !empty($request->get('database_password'))
        ){
            $databaseName = $request->get('database_name');
            //Create Database
            $database = $server->databases()->create([
                'name' => $databaseName,
                'site_id' => $site->id
            ]);

            $this->logActivity(__("Created database ':name' on server ':server'", ['name' => $database->name, 'server' => $server->name]), $database);

            $databaseUser = $database->users()->make([
                'name' => $request->get('database_user'),
            ])->forceFill([
                'server_id' => $server->id,
                'site_id' => $site->id
            ]);

            $databaseUser->save();
            $databaseUser->databases()->attach($database);

            $this->logActivity(__("Created database user ':name' on server ':server'", ['name' => $databaseUser->name, 'server' => $server->name]), $databaseUser);

            Bus::chain([
                new InstallDatabase($database, $this->user()->fresh()),
                new InstallDatabaseUser($databaseUser, $request->get('database_password'), $this->user()->fresh()),
            ])->dispatch();
        }

        if($request->has('add_server_ssh_key_to_github') && $request->get('add_server_ssh_key_to_github')){
            dispatch(new AddServerSshKeyToGithub($server, $this->user()->githubCredentials->fresh()));
        }


        $this->logActivity(__("Created site ':address' on server ':server'", ['address' => $site->address, 'server' => $server->name]), $site);

        if(
            $request->get('has_database') &&
            !empty($request->get('database_name')) &&
            !empty($request->get('database_user')) &&
            !empty($request->get('database_password'))
        ) {
            $deployment = $site->deploy(user: $this->user(), environmentVariables: [
                "DB_DATABASE" => $request->get('database_name'),
                "DB_USERNAME" => $request->get('database_user'),
                "DB_PASSWORD" => $request->get('database_password')
            ]);
        }
        else {
            $deployment = $site->deploy(user: $this->user());
        }

        if ($data['deploy_key_uuid']) {
            Cache::forget($data['deploy_key_uuid']);
        }


        if($request->has('has_queue') && $request->get('has_queue')){
            $dataDaemons = [
                'user' => $server->username,
                'processes' => 1,
                'stop_wait_seconds' => 10,
                'stop_signal' => 'TERM',
                'command' => $site->php_version->getBinary() .' artisan queue:work --timeout=0',
                'directory' => '/home/'.$server->username.'/'.$site->address.'/repository',
                'site_id' => $site->id
            ];

            $daemon = $server->daemons()->create($dataDaemons);
            dispatch(new InstallDaemon($daemon, $this->user()));
        }

        if($request->has('has_schedule') && $request->get('has_schedule')){
            $dataCron = [
                'user' => $server->username,
                'command' => $site->php_version->getBinary() . ' /home/'.$server->username.'/'.$site->address.'/repository/artisan schedule:run',
                'expression' => '* * * * *',
                'site_id' => $site->id
            ];

            $cron = $server->crons()->create($dataCron);
            dispatch(new InstallCron($cron, $this->user()));
        }

        return to_route('admin.servers.sites.deployments.show', [$server, $site, $deployment]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Server $server, Site $site)
    {
        return view('tomato-eddy::sites.show', [
            'server' => $server,
            'site' => $site,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Server $server, Site $site)
    {
        if ($site->pending_caddyfile_update_since?->diffInMinutes() > 3) {
            $site->forceFill(['pending_caddyfile_update_since' => null])->saveQuietly();
        }

        return view('tomato-eddy::sites.edit', [
            'server' => $server,
            'site' => $site,
            'phpVersions' => $server->installedPhpVersions(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSiteRequest $request, Server $server, Site $site)
    {
        $data = $request->validated();

        if ($site->type !== SiteType::Wordpress) {
            $site->fill([
                'repository_url' => $data['repository_url'] ?? $site->repository_url,
                'repository_branch' => $data['repository_branch'] ?? $site->repository_branch,
            ]);
        }

        $newPhpVersion = PhpVersion::from($data['php_version']);

        $this->logActivity(__("Updated site ':address' on server ':server'", ['address' => $site->address, 'server' => $server->name]), $site);

        if ($site->php_version !== $newPhpVersion || $site->web_folder !== $data['web_folder']) {
            $site->updateCaddyfile($newPhpVersion, $data['web_folder'], $this->user());

            Toast::info(__('The site settings are being saved. The Caddyfile will be updated and the site will be deployed.'));

            return to_route('servers.sites.edit', [$server, $site]);
        }

        if (! $site->isDirty()) {
            Toast::info(__('No changes were made.'))->autoDismiss(2);

            return to_route('admin.servers.sites.edit', [$server, $site]);
        }

        $site->save();
        $deployment = $site->deploy(user: $this->user());

        Toast::info(__('The site settings have been saved and the site is being deployed.'))->autoDismiss(2);

        return to_route('admin.servers.sites.deployments.show', [$server, $site, $deployment]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Server $server, Site $site)
    {
        $site->dropDamons();
        $site->dropDatabases();
        $site->dropDatabaseUsers();
        $site->dropCrons();
        $site->delete();

        dispatch(new UninstallSite($server, $site->path));

        $this->logActivity(__("Deleted site ':address' from server ':server'", ['address' => $site->address, 'server' => $server->name]), $site);

        Toast::message(__('The site is deleted and will be uninstalled from the server shortly.'));

        return to_route('admin.servers.sites.index', $server);
    }
}
