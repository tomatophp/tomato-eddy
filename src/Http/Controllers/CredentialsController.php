<?php

namespace TomatoPHP\TomatoEddy\Http\Controllers;

use TomatoPHP\TomatoAdmin\Facade\Tomato;
use TomatoPHP\TomatoEddy\Enums\Enum;
use TomatoPHP\TomatoEddy\Http\Requests\UpdateCredentialsRequest;
use TomatoPHP\TomatoEddy\Models\Credentials;
use TomatoPHP\TomatoEddy\Models\Server;
use TomatoPHP\TomatoEddy\Models\Team;
use TomatoPHP\TomatoEddy\Enums\Services\Provider;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use ProtoneMedia\Splade\Facades\Toast;
use ProtoneMedia\Splade\SpladeTable;
use TomatoPHP\TomatoEddy\Tables\CredentialsTable;
use TomatoPHP\TomatoEddy\Tables\ServerTable;

/**
 * @codeCoverageIgnore Handled by Dusk tests.
 */
class CredentialsController extends Controller
{
    public function __construct()
    {
        $this->model = Credentials::class;
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Tomato::index(
            request: $request,
            model: $this->model,
            view: 'tomato-eddy::credentials.index',
            table: CredentialsTable::class,
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $forServer = $request->query('forServer') ? true : false;

        $options = Provider::userManagable();

        if (! $forServer && ! $this->user()->hasGithubCredentials()) {
            $options[] = Provider::Github;
        }

        $options[] = Provider::Cloudflare;

        return view('tomato-eddy::credentials.create', [
            'providers' => Enum::options($options),
            'forServer' => $forServer,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'provider' => ['required', Rule::in(Enum::values(Provider::userManagable()))],
            'credentials' => ['array'],
        ]);

        $credentials = $this->user()->credentials()->make($data);
        $credentials->save();

        Toast::message(__('Credentials added.'))->autoDismiss(2);

        $forServer = $request->query('forServer') ? true : false;

        return $forServer
            ? to_route('admin.servers.create', ['credentials' => $credentials->id])
            : to_route('admin.credentials.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Credentials $credentials)
    {
        return view('tomato-eddy::credentials.edit', [
            'credentials' => $credentials,
            'providers' => Enum::options(Provider::class),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCredentialsRequest $request, Credentials $credentials)
    {
        $data = $request->validated();

        $credentials->update($data);

        Toast::message(__('Credentials updated.'));

        return to_route('admin.credentials.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Credentials $credentials)
    {
        $credentials->delete();

        Toast::message(__('Credentials deleted.'));

        return to_route('admin.credentials.index');
    }
}
