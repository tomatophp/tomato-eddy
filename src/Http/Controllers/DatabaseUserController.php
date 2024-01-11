<?php

namespace TomatoPHP\TomatoEddy\Http\Controllers;

use TomatoPHP\TomatoEddy\Jobs\InstallDatabaseUser;
use TomatoPHP\TomatoEddy\Jobs\UninstallDatabaseUser;
use TomatoPHP\TomatoEddy\Jobs\UpdateDatabaseUser;
use TomatoPHP\TomatoEddy\Models\Database;
use TomatoPHP\TomatoEddy\Models\DatabaseUser;
use TomatoPHP\TomatoEddy\Models\Server;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use ProtoneMedia\Splade\Facades\Toast;

/**
 * @codeCoverageIgnore Handled by Dusk tests.
 */
class DatabaseUserController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create(Server $server)
    {
        return view('tomato-eddy::database-users.create', [
            'server' => $server,
            'databases' => $server->databases->mapWithKeys(fn (Database $database) => [$database->id => $database->name]),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Server $server, Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('database_users', 'name')->where('server_id', $server->id)],
            'password' => ['required', 'string', 'max:255'],
            'databases' => ['nullable', 'array'],
            'databases.*' => ['string', Rule::exists('databases', 'id')->where('server_id', $server->id)],
        ]);

        /** @var DatabaseUser */
        $databaseUser = $server->databaseUsers()->create([
            'name' => $data['name'],
        ]);

        $this->logActivity(__("Created database user ':name' on server ':server'", ['name' => $databaseUser->name, 'server' => $server->name]), $databaseUser);

        if ($request->collect('databases')->isNotEmpty()) {
            $databaseUser->databases()->attach($data['databases']);
        }

        dispatch(new InstallDatabaseUser($databaseUser, $data['password'], $this->user()));

        Toast::success(__('The database user will be created shortly.'));

        return to_route('admin.servers.databases.index', $server);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Server $server, DatabaseUser $databaseUser)
    {
        return view('tomato-eddy::database-users.edit', [
            'databaseUser' => $databaseUser,
            'server' => $server,
            'databases' => $server->databases->mapWithKeys(fn (Database $database) => [$database->id => $database->name]),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Server $server, DatabaseUser $databaseUser)
    {
        $data = $request->validate([
            'password' => ['nullable', 'string', 'max:255'],
            'databases' => ['nullable', 'array'],
            'databases.*' => ['string', Rule::exists('databases', 'id')->where('server_id', $server->id)],
        ]);

        $databaseUser->databases()->sync($data['databases'] ?? []);

        $databaseUser->forceFill([
            'installed_at' => null,
            'installation_failed_at' => null,
            'uninstallation_failed_at' => null,
        ])->save();

        $this->logActivity(__("Updated database user ':name' on server ':server'", ['name' => $databaseUser->name, 'server' => $server->name]), $databaseUser);

        dispatch(new UpdateDatabaseUser($databaseUser, $data['password'], $this->user()));

        Toast::message(__('The database user will be updated shortly.'));

        return to_route('admin.servers.databases.index', $server);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Server $server, DatabaseUser $databaseUser)
    {
        $databaseUser->markUninstallationRequest();

        dispatch(new UninstallDatabaseUser($databaseUser, $this->user()));

        $this->logActivity(__("Deleted database user ':name' from server ':server'", ['name' => $databaseUser->name, 'server' => $server->name]), $databaseUser);

        Toast::message(__('The database user will be uninstalled from the server.'));

        return to_route('admin.servers.databases.index', $server);
    }
}
