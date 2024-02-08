<?php

namespace TomatoPHP\TomatoEddy\Http\Controllers;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\View\View;
use ProtoneMedia\Splade\Facades\Toast;
use TomatoPHP\TomatoAdmin\Facade\Tomato;
use TomatoPHP\TomatoEddy\Enums\Enum;
use TomatoPHP\TomatoEddy\Enums\Models\DeploymentStatus;
use TomatoPHP\TomatoEddy\Enums\Models\SiteType;
use TomatoPHP\TomatoEddy\Enums\Models\TlsSetting;
use TomatoPHP\TomatoEddy\Enums\Server\PhpVersion;
use TomatoPHP\TomatoEddy\Enums\Services\Provider;
use TomatoPHP\TomatoEddy\Exceptions\Models\PendingDeploymentException;
use TomatoPHP\TomatoEddy\Jobs\AddServerSshKeyToGithub;
use TomatoPHP\TomatoEddy\Jobs\CreateBulkServers;
use TomatoPHP\TomatoEddy\Jobs\CreateServerOnInfrastructure;
use TomatoPHP\TomatoEddy\Jobs\DeploySite;
use TomatoPHP\TomatoEddy\Jobs\InstallCron;
use TomatoPHP\TomatoEddy\Jobs\InstallDaemon;
use TomatoPHP\TomatoEddy\Jobs\InstallDatabase;
use TomatoPHP\TomatoEddy\Jobs\InstallDatabaseUser;
use TomatoPHP\TomatoEddy\Jobs\LinkDomainToCloudflare;
use TomatoPHP\TomatoEddy\Jobs\ProvisionServer;
use TomatoPHP\TomatoEddy\Jobs\WaitForServerToConnect;
use TomatoPHP\TomatoEddy\Models\Credentials;
use TomatoPHP\TomatoEddy\Models\Deployment;
use TomatoPHP\TomatoEddy\Models\Server;
use TomatoPHP\TomatoEddy\Models\Site;
use TomatoPHP\TomatoEddy\Models\SiteTemplate;
use TomatoPHP\TomatoEddy\Models\SshKey;
use TomatoPHP\TomatoEddy\Services\KeyPair;
use TomatoPHP\TomatoEddy\Services\KeyPairGenerator;

class SiteTemplateController extends Controller
{
    public string $model;

    public function __construct()
    {
        $this->model = \TomatoPHP\TomatoEddy\Models\SiteTemplate::class;
    }

    /**
     * @param Request $request
     * @return View|JsonResponse
     */
    public function index(Request $request): View|JsonResponse
    {
        return Tomato::index(
            request: $request,
            model: $this->model,
            view: 'tomato-eddy::site-templates.index',
            table: \TomatoPHP\TomatoEddy\Tables\SiteTemplateTable::class
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function api(Request $request): JsonResponse
    {
        return Tomato::json(
            request: $request,
            model: \TomatoPHP\TomatoEddy\Models\SiteTemplate::class,
        );
    }

    /**
     * @return View
     */
    public function create(Request $request): View
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

        return Tomato::create(
            view: 'tomato-eddy::site-templates.create',
            data: [
                'phpVersions' => Enum::options(PhpVersion::class),
                'types' => Enum::options(SiteType::class),
                'hasGithubCredentials' => $this->user()->hasGithubCredentials(),
                'hasCloudflareCredential' => $this->user()->hasCloudflareCredential(),
                'defaultCredentials' => $defaultCredentials,
                'credentials' => $credentials,
                'sshKeys' => $sshKeys,
                'hasGithubCredentials' => $this->user()->hasGithubCredentials(),
            ]
        );
    }

    /**
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $response = Tomato::store(
            request: $request,
            model: \TomatoPHP\TomatoEddy\Models\SiteTemplate::class,
            validation: [
                'name' => 'required|max:255|string',
                'type' => 'required|max:255|string',
                'zero_downtime_deployment' => 'required',
                'repository_url' => 'nullable|max:255|string',
                'repository_branch' => 'nullable|max:255|string',
                'web_folder' => 'required|max:255|string',
                'php_version' => 'nullable|max:255|string',
                'hook_before_updating_repository' => 'nullable',
                'hook_after_updating_repository' => 'nullable',
                'hook_before_making_current' => 'nullable',
                'hook_after_making_current' => 'nullable',
                'add_server_ssh_key_to_github' => 'required',
                'add_dns_zone_to_cloudflare' => 'required',
                'has_queue' => 'nullable',
                'has_schedule' => 'nullable',
                'has_database' => 'nullable',
                'database_name' => 'nullable|max:255|string',
                'database_user' => 'nullable|max:255|string',
                'database_password' => 'nullable|max:255'
            ],
            message: __('SiteTemplate updated successfully'),
            redirect: 'admin.site-templates.index',
        );

        if($response instanceof JsonResponse){
            return $response;
        }

        return $response->redirect;
    }

    /**
     * @param \TomatoPHP\TomatoEddy\Models\SiteTemplate $model
     * @return View|JsonResponse
     */
    public function show(\TomatoPHP\TomatoEddy\Models\SiteTemplate $model): View|JsonResponse
    {
        return Tomato::get(
            model: $model,
            view: 'tomato-eddy::site-templates.show',
        );
    }

    /**
     * @param \TomatoPHP\TomatoEddy\Models\SiteTemplate $model
     * @return View
     */
    public function edit(\TomatoPHP\TomatoEddy\Models\SiteTemplate $model, Request $request): View
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


        return Tomato::get(
            model: $model,
            view: 'tomato-eddy::site-templates.edit',
            data: [
                'phpVersions' => Enum::options(PhpVersion::class),
                'types' => Enum::options(SiteType::class),
                'hasGithubCredentials' => $this->user()->hasGithubCredentials(),
                'hasCloudflareCredential' => $this->user()->hasCloudflareCredential(),
                'defaultCredentials' => $defaultCredentials,
                'credentials' => $credentials,
                'sshKeys' => $sshKeys,
                'hasGithubCredentials' => $this->user()->hasGithubCredentials(),
            ]
        );
    }

    /**
     * @param Request $request
     * @param \TomatoPHP\TomatoEddy\Models\SiteTemplate $model
     * @return RedirectResponse|JsonResponse
     */
    public function update(Request $request, \TomatoPHP\TomatoEddy\Models\SiteTemplate $model): RedirectResponse|JsonResponse
    {
        $response = Tomato::update(
            request: $request,
            model: $model,
            validation: [
                'name' => 'sometimes|max:255|string',
                'type' => 'sometimes|max:255|string',
                'zero_downtime_deployment' => 'sometimes',
                'repository_url' => 'nullable|max:255|string',
                'repository_branch' => 'nullable|max:255|string',
                'web_folder' => 'sometimes|max:255|string',
                'php_version' => 'nullable|max:255|string',
                'hook_before_updating_repository' => 'nullable',
                'hook_after_updating_repository' => 'nullable',
                'hook_before_making_current' => 'nullable',
                'hook_after_making_current' => 'nullable',
                'add_server_ssh_key_to_github' => 'sometimes',
                'add_dns_zone_to_cloudflare' => 'sometimes',
                'has_queue' => 'nullable',
                'has_schedule' => 'nullable',
                'has_database' => 'nullable',
                'database_name' => 'nullable|max:255|string',
                'database_user' => 'nullable|max:255|string',
                'database_password' => 'nullable|max:255'
            ],
            message: __('SiteTemplate updated successfully'),
            redirect: 'admin.site-templates.index',
        );

        if($response instanceof JsonResponse){
            return $response;
        }

        return $response->redirect;
    }

    public function count(\TomatoPHP\TomatoEddy\Models\SiteTemplate $model)
    {
        return view('tomato-eddy::site-templates.count', compact('model'));
    }

    /**
     * @param \TomatoPHP\TomatoEddy\Models\SiteTemplate $model
     * @return RedirectResponse|JsonResponse
     */
    public function destroy(\TomatoPHP\TomatoEddy\Models\SiteTemplate $model): RedirectResponse|JsonResponse
    {
        $response = Tomato::destroy(
            model: $model,
            message: __('SiteTemplate deleted successfully'),
            redirect: 'admin.site-templates.index',
        );

        if($response instanceof JsonResponse){
            return $response;
        }

        return $response->redirect;
    }

    public function server(SiteTemplate $model, Request $request, KeyPairGenerator $keyPairGenerator)
    {
        /** @var Credentials|null */
        $credentials = $this->user()->credentials()->findOrFail($model->server_credentials_id);

        if ($request->has('count')) {
            dispatch(new CreateBulkServers(
                count: $request->get('count'),
                credentials: $credentials,
                name: $model->server_name,
                region: $model->server_region,
                type: $model->server_type,
                image: $model->server_image,
                user: $this->user(),
                ssh_keys: [$model->server_ssh_keys],
                keyPairGenerator: $keyPairGenerator
            ));

            $this->logActivity(__('Bulk Servers Has Been Created'));

            Toast::success(__('Bulk Servers Has Been Created'));

            return to_route('admin.servers.index');
        }

        Toast::success(__('Your server is being created and provisioned.'));

        return to_route('admin.servers.show', $server);
    }

    public function sites(SiteTemplate $model, KeyPairGenerator $keyPairGenerator)
    {
        $servers = Server::all();
        foreach ($servers as $server) {
            $siteTemplate = $model;
            $siteUsername = $server->name;
            $data = [
                'address' => $siteUsername . '.'. $siteTemplate->domain,
                'php_version' => $siteTemplate->php_version,
                'type' => $siteTemplate->type,
                'web_folder' => $siteTemplate->web_folder,
                'zero_downtime_deployment' => $siteTemplate->zero_downtime_deployment,
                'repository_url' => $siteTemplate->repository_url,
                'repository_branch' => $siteTemplate->repository_branch,
                'add_dns_zone_to_cloudflare' => $siteTemplate->add_dns_zone_to_cloudflare,
                'add_server_ssh_key_to_github' => $siteTemplate->add_server_ssh_key_to_github,
                'has_database' => $siteTemplate->has_database,
                'database_name' => $siteTemplate->database_name.'_'.$siteUsername,
                'database_user' => $siteTemplate->database_user.'_'.$siteUsername,
                'database_password' => $siteTemplate->database_password,
                'has_queue' => $siteTemplate->has_queue,
                'has_schedule' => $siteTemplate->has_schedule,
                'hook_before_updating_repository' => $siteTemplate->hook_before_updating_repository,
                'hook_after_updating_repository' => $siteTemplate->hook_after_updating_repository,
                'hook_before_making_current' => $siteTemplate->hook_before_making_current,
                'hook_after_making_current' => $siteTemplate->hook_after_making_current,
            ];

            $checkIfSiteExists = Site::where('address', $data['address'])->first();

           if(!$checkIfSiteExists){
               $deployKeyUuid = Str::uuid()->toString();
               $keyPair = Cache::remember(
                   key: "deploy-key-{$server->id}-{$deployKeyUuid}",
                   ttl: config('session.lifetime') * 60,
                   callback: fn () => $keyPairGenerator->ed25519()
               );

               $data['deploy_key_uuid'] = $deployKeyUuid;

               if($siteTemplate->add_server_ssh_key_to_github){
                   dispatch(new AddServerSshKeyToGithub($server, $this->user()->githubCredentials->fresh()));
               }

               $jobs = [];

               if($siteTemplate->add_dns_zone_to_cloudflare){
                   $jobs[] = new LinkDomainToCloudflare($server, $data['address']);

                   $this->logActivity(__("Created Cloudflare ':address'", ['address' => $data['address']]));
               }
               /** @var Site */
               $site = $server->sites()->make($data);
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
                   $data['has_database'] &&
                   !empty($data['database_name']) &&
                   !empty($data['database_user']) &&
                   !empty($data['database_password'])
               ){
                   $databaseName = $data['database_name'];
                   //Create Database
                   $database = $server->databases()->create([
                       'name' => $databaseName,
                       'site_id' => $site->id
                   ]);

                   $this->logActivity(__("Created database ':name' on server ':server'", ['name' => $database->name, 'server' => $server->name]), $database);

                   $databaseUser = $database->users()->make([
                       'name' => $data['database_user'],
                   ])->forceFill([
                       'server_id' => $server->id,
                       'site_id' => $site->id
                   ]);

                   $databaseUser->save();
                   $databaseUser->databases()->attach($database);

                   $this->logActivity(__("Created database user ':name' on server ':server'", ['name' => $databaseUser->name, 'server' => $server->name]), $databaseUser);

                   $jobs[] = new InstallDatabase($database, $this->user()->fresh());
                   $jobs[] = new InstallDatabaseUser($databaseUser, $data['database_password'], $this->user()->fresh());

               }


               $this->logActivity(__("Created site ':address' on server ':server'", ['address' => $site->address, 'server' => $server->name]), $site);


               if ($site->fresh()->latestDeployment?->status === DeploymentStatus::Pending) {
                   throw new PendingDeploymentException($site);
               }

               /** @var Deployment */
               $deployment = $site->deployments()->create([
                   'status' => DeploymentStatus::Pending,
                   'user_id' => $this->user()?->exists ? $this->user()->id : null,
               ]);

               $site->server->team->activityLogs()->create([
                   'subject_id' => $site->getKey(),
                   'subject_type' => $site->getMorphClass(),
                   'description' => __(__("Deployed site ':address' on server ':server'", ['address' => $site->address, 'server' => $site->server->name])),
                   'user_id' => $this->user()?->exists ? $this->user()->id : null,
               ]);

               if(
                   $data['has_database'] &&
                   !empty($data['database_name']) &&
                   !empty($data['database_user']) &&
                   !empty($data['database_password'])
               ) {
                   $jobs[] = new DeploySite($deployment, [
                       "DB_DATABASE" => $data['database_name'],
                       "DB_USERNAME" => $data['database_user'],
                       "DB_PASSWORD" => $data['database_password']
                   ]);
               }
               else {
                   $jobs[] = new DeploySite($deployment);
               }

               if ($data['deploy_key_uuid']) {
                   Cache::forget($data['deploy_key_uuid']);
               }


               if($data['has_queue']){
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
                   $jobs[] = new InstallDaemon($daemon, $this->user());
               }

               if($data['has_schedule']){
                   $dataCron = [
                       'user' => $server->username,
                       'command' => $site->php_version->getBinary() . ' /home/'.$server->username.'/'.$site->address.'/repository/artisan schedule:run',
                       'expression' => '* * * * *',
                       'site_id' => $site->id
                   ];

                   $cron = $server->crons()->create($dataCron);
                   $jobs[] = new InstallCron($cron, $this->user());
               }
           }
           else {
               /** @var Deployment */
               $deployment = $checkIfSiteExists->deployments()->create([
                   'status' => DeploymentStatus::Pending,
                   'user_id' => $this->user()?->exists ? $this->user()->id : null,
               ]);

               if(
                   $data['has_database'] &&
                   !empty($data['database_name']) &&
                   !empty($data['database_user']) &&
                   !empty($data['database_password'])
               ) {
                   $jobs[] = new DeploySite($deployment, [
                       "DB_DATABASE" => $data['database_name'],
                       "DB_USERNAME" => $data['database_user'],
                       "DB_PASSWORD" => $data['database_password']
                   ]);
               }
               else {
                   $jobs[] = new DeploySite($deployment);
               }
           }

            Bus::chain($jobs)->dispatch();
        }

        Toast::success(__('Site has been created successfully'))->autoDismiss(2);
        return back();
    }
}
