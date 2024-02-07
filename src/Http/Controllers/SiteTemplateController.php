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
        $serverName = $model->server_name.Str::random(6);
        while (Server::where('name', $serverName)->first()){
            $serverName = $model->server_name.Str::random(6);
        }
        $request->merge([
            'name' => $serverName,
            'region' => $model->server_region,
            'type' => $model->server_type,
            'image' => $model->server_image,
            'image' => $model->server_image,
            'ssh_keys' => [$model->server_ssh_keys],
            'credentials_id' => [$model->server_credentials_id],
            'add_key_to_github' => $model->add_server_ssh_key_to_github
        ]);

        /** @var Credentials|null */
        $credentials = $this->user()->credentials()->findOrFail($request->input('credentials_id'));


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
                'credentials_id' => $credentials[0]?->id,
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

            $server->provider = $credentials[0]->provider;
            $server->created_by_user_id = $this->user()->id;

            $server->save();


            $server = $this->fresh();

            $jobs = [
                new CreateServerOnInfrastructure($server),
                new WaitForServerToConnect($server),
                new ProvisionServer($server, EloquentCollection::make(SshKey::whereKey($request->input('ssh_keys'))->get())),
            ];

            $addSshKeyToGithub = $request->boolean('add_key_to_github') ? $this->user()->githubCredentials : null;
            if ($addSshKeyToGithub && $addSshKeyToGithub->exists) {
                $jobs[] = new AddServerSshKeyToGithub($server, $addSshKeyToGithub->fresh());
            }

            $siteTemplate = $model;
            $siteUsername = $model->name.Str::random(6);
            $data = [
                'address' => $siteUsername.'.'.$siteTemplate->domain,
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

             if($deploySite->add_dns_zone_to_cloudflare){
                 $jobs[] = new LinkDomainToCloudflare($server, $data['address']);

                 $this->logActivity(__("Created Cloudflare ':address'", ['address' => $data['address']]));
             }

            Bus::chain($jobs)->dispatch();

            $this->logActivity(__("Created server ':server'", ['server' => $server->name]), $server);

            Toast::success(__('Your server is being created and provisioned.'));

            return to_route('admin.servers.show', $server);
        }
    }
}
