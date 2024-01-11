<?php

namespace TomatoPHP\TomatoEddy\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use TomatoPHP\TomatoAdmin\Facade\Tomato;
use TomatoPHP\TomatoEddy\Enums\Enum;
use TomatoPHP\TomatoEddy\Enums\Models\SiteType;
use TomatoPHP\TomatoEddy\Enums\Server\PhpVersion;

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
    public function create(): View
    {
        return Tomato::create(
            view: 'tomato-eddy::site-templates.create',
            data: [
                'phpVersions' => Enum::options(PhpVersion::class),
                'types' => Enum::options(SiteType::class),
                'hasGithubCredentials' => $this->user()->hasGithubCredentials(),
                'hasCloudflareCredential' => $this->user()->hasCloudflareCredential(),
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
    public function edit(\TomatoPHP\TomatoEddy\Models\SiteTemplate $model): View
    {
        return Tomato::get(
            model: $model,
            view: 'tomato-eddy::site-templates.edit',
            data: [
                'phpVersions' => Enum::options(PhpVersion::class),
                'types' => Enum::options(SiteType::class),
                'hasGithubCredentials' => $this->user()->hasGithubCredentials(),
                'hasCloudflareCredential' => $this->user()->hasCloudflareCredential(),
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
}
