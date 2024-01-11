<?php

namespace TomatoPHP\TomatoEddy\Http\Controllers;

use TomatoPHP\TomatoEddy\Jobs\InstallDatabase;
use TomatoPHP\TomatoEddy\Jobs\InstallDatabaseUser;
use TomatoPHP\TomatoEddy\Jobs\UninstallDatabase;
use TomatoPHP\TomatoEddy\Models\Database;
use TomatoPHP\TomatoEddy\Models\DatabaseUser;
use TomatoPHP\TomatoEddy\Models\Server;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Validation\Rule;
use ProtoneMedia\Splade\Facades\Toast;
use ProtoneMedia\Splade\SpladeTable;
use TomatoPHP\TomatoEddy\Tables\DatabaseTable;
use TomatoPHP\TomatoEddy\Tables\DatabaseUsersTable;

/**
 * @codeCoverageIgnore Handled by Dusk tests.
 */
class DatabaseController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Server $server)
    {
        return view('tomato-eddy::databases.index', [
            'server' => $server,
            'databases' => (new DatabaseTable($server->databases(), $server)),
            'users' => (new DatabaseUsersTable($server->databaseUsers(), $server))
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Server $server)
    {
        return view('tomato-eddy::databases.create', [
            'server' => $server,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Server $server, Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('databases', 'name')->where('server_id', $server->id)],
            'create_user' => ['required', 'boolean'],
            'user' => ['nullable', 'required_if:create_user,1', 'string', 'max:255', Rule::unique('database_users', 'name')->where('server_id', $server->id)],
            'password' => ['nullable', 'required_if:create_user,1', 'string', 'max:255'],
        ]);

        /** @var Database */
        $database = $server->databases()->create([
            'name' => $data['name'],
        ]);

        $this->logActivity(__("Created database ':name' on server ':server'", ['name' => $database->name, 'server' => $server->name]), $database);

        if (! $data['create_user']) {
            dispatch(new InstallDatabase($database, $this->user()));

            Toast::message(__('The database will be created shortly.'));

            return to_route('servers.databases.index', $server);
        }

        /** @var DatabaseUser */
        $databaseUser = $database->users()->make([
            'name' => $data['user'],
        ])->forceFill([
            'server_id' => $server->id,
        ]);

        $databaseUser->save();
        $databaseUser->databases()->attach($database);

        $this->logActivity(__("Created database user ':name' on server ':server'", ['name' => $databaseUser->name, 'server' => $server->name]), $databaseUser);

        Bus::chain([
            new InstallDatabase($database, $this->user()->fresh()),
            new InstallDatabaseUser($databaseUser, $data['password'], $this->user()->fresh()),
        ])->dispatch();

        Toast::message(__('The database and user will be created shortly.'));

        return to_route('admin.servers.databases.index', $server);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Server $server, Database $database)
    {
        return view('tomato-eddy::databases.edit', [
            'database' => $database,
            'server' => $server,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Server $server, Database $database)
    {
        $database->markUninstallationRequest();

        dispatch(new UninstallDatabase($database, $this->user()));

        $this->logActivity(__("Deleted database ':name' from server ':server'", ['name' => $database->name, 'server' => $server->name]), $database);

        Toast::message(__('The database will be uninstalled from the server.'));

        return to_route('admin.servers.databases.index', $server);
    }

    public function update(Request $request, Server $server, Database $database)
    {
        /** @var DatabaseUser */
        $databaseUser = $database->users()->first();

        if(!$databaseUser){
            $databaseUser = $database->users()->make([
                'name' => $database->name,
            ])->forceFill([
                'server_id' => $server->id,
            ]);

            $databaseUser->save();
            $databaseUser->databases()->attach($database);
        }

        Bus::chain([
            new InstallDatabase($database, $this->user()->fresh()),
            new InstallDatabaseUser($databaseUser, $server->database_password, $this->user()->fresh()),
        ])->dispatch();

        Toast::success(__('Retry Install Database Run on background'))->autoDismiss(2);
        return back();
    }
}
