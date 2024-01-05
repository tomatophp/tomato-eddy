<?php

namespace TomatoPHP\TomatoEddy\Http\Controllers;

use TomatoPHP\TomatoAdmin\Facade\Tomato;
use TomatoPHP\TomatoEddy\Jobs\RemoveSshKeyFromServer;
use TomatoPHP\TomatoEddy\Models\Server;
use TomatoPHP\TomatoEddy\Models\SshKey;
use TomatoPHP\TomatoEddy\Rules\PublicKey;
use Illuminate\Http\Request;
use ProtoneMedia\Splade\Facades\Toast;
use ProtoneMedia\Splade\SpladeTable;
use TomatoPHP\TomatoEddy\Tables\SshKeyTable;

/**
 * @codeCoverageIgnore Handled by Dusk tests.
 */
class SshKeyController extends Controller
{
    public function __construct()
    {
        $this->model = SshKey::class;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Tomato::index(
            request: $request,
            model: $this->model,
            view: 'tomato-eddy::ssh-keys.index',
            table: SshKeyTable::class,
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tomato-eddy::ssh-keys.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'public_key' => ['required', 'string', new PublicKey],
        ]);

        $this->user()->sshKeys()->create($data);

        Toast::message(__('SSH Key added.'))->autoDismiss(2);

        return to_route('admin.ssh-keys.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, SshKey $sshKey)
    {
        if ($request->query('remove-from-servers')) {
            $this->team()->servers()->each(function (Server $server) use ($sshKey) {
                dispatch(new RemoveSshKeyFromServer($sshKey->public_key, $server));
            });

            Toast::message(__('The SSH Key will be deleted and removed from all servers. This may take a few minutes.'))->autoDismiss(2);
        } else {
            Toast::message(__('SSH Key deleted.'))->autoDismiss(2);
        }

        $sshKey->delete();

        return to_route('admin.ssh-keys.index');
    }
}
