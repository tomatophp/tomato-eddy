<?php

namespace TomatoPHP\TomatoEddy\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;
use ProtoneMedia\Splade\Facades\Toast;
use TomatoPHP\TomatoAdmin\Facade\Tomato;
use TomatoPHP\TomatoEddy\Jobs\FireRecipe;
use TomatoPHP\TomatoEddy\Models\Server;

class RecipeController extends Controller
{
    public string $model;

    public function __construct()
    {
        $this->model = \TomatoPHP\TomatoEddy\Models\Recipe::class;
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
            view: 'tomato-eddy::recipes.index',
            table: \TomatoPHP\TomatoEddy\Tables\RecipeTable::class
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
            model: \TomatoPHP\TomatoEddy\Models\Recipe::class,
        );
    }

    /**
     * @return View
     */
    public function create(): View
    {
        return Tomato::create(
            view: 'tomato-eddy::recipes.create',
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
            model: \TomatoPHP\TomatoEddy\Models\Recipe::class,
            validation: [
                'name' => 'required|max:255|string',
                'description' => 'nullable|max:255|string',
                'user' => 'nullable|max:255|string',
                'type' => 'nullable|max:255|string',
                'script' => 'nullable',
                'view' => 'nullable|max:255|string'
            ],
            message: __('Recipe updated successfully'),
            redirect: 'admin.recipes.index',
        );

        if($response instanceof JsonResponse){
            return $response;
        }

        return $response->redirect;
    }

    /**
     * @param \TomatoPHP\TomatoEddy\Models\Recipe $model
     * @return View|JsonResponse
     */
    public function show(\TomatoPHP\TomatoEddy\Models\Recipe $model): View|JsonResponse
    {
        return Tomato::get(
            model: $model,
            view: 'tomato-eddy::recipes.show',
        );
    }

    /**
     * @param \TomatoPHP\TomatoEddy\Models\Recipe $model
     * @return View
     */
    public function edit(\TomatoPHP\TomatoEddy\Models\Recipe $model): View
    {
        return Tomato::get(
            model: $model,
            view: 'tomato-eddy::recipes.edit',
        );
    }

    /**
     * @param Request $request
     * @param \TomatoPHP\TomatoEddy\Models\Recipe $model
     * @return RedirectResponse|JsonResponse
     */
    public function update(Request $request, \TomatoPHP\TomatoEddy\Models\Recipe $model): RedirectResponse|JsonResponse
    {
        $response = Tomato::update(
            request: $request,
            model: $model,
            validation: [
                'name' => 'sometimes|max:255|string',
                'description' => 'nullable|max:255|string',
                'user' => 'nullable|max:255|string',
                'type' => 'nullable|max:255|string',
                'script' => 'nullable',
                'view' => 'nullable|max:255|string'
            ],
            message: __('Recipe updated successfully'),
            redirect: 'admin.recipes.index',
        );

         if($response instanceof JsonResponse){
             return $response;
         }

         return $response->redirect;
    }

    /**
     * @param \TomatoPHP\TomatoEddy\Models\Recipe $model
     * @return RedirectResponse|JsonResponse
     */
    public function destroy(\TomatoPHP\TomatoEddy\Models\Recipe $model): RedirectResponse|JsonResponse
    {
        $response = Tomato::destroy(
            model: $model,
            message: __('Recipe deleted successfully'),
            redirect: 'admin.recipes.index',
        );

        if($response instanceof JsonResponse){
            return $response;
        }

        return $response->redirect;
    }

    public function server(\TomatoPHP\TomatoEddy\Models\Recipe $model)
    {
        return view('tomato-eddy::recipes.servers', [
            'recipe' => $model,
            'servers' => Server::all(),
        ]);
    }

    public function fire(\TomatoPHP\TomatoEddy\Models\Recipe $model, Request $request)
    {
        $request->validate([
            "servers" => "required|array",
            "servers*" => "required|string|exists:servers,id"
        ]);

        $servers = $request->input('servers');
        foreach ($servers as $server){
            $server = Server::find($server);
            FireRecipe::dispatch($server, $model);
        }

        Toast::success(__('Recipe has been fired'))->autoDismiss(2);
        return back();
    }

    public function log(\TomatoPHP\TomatoEddy\Models\RecipesServerLog $model)
    {
        return view('tomato-eddy::recipes.show-log', [
            'model' => $model,
        ]);
    }
}
