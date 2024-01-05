<?php

namespace TomatoPHP\TomatoEddy\Http\Controllers;

use TomatoPHP\TomatoEddy\Jobs\AddSshKeyToServer;
use TomatoPHP\TomatoEddy\Models\Server;
use TomatoPHP\TomatoEddy\Models\SshKey;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use ProtoneMedia\Splade\Facades\Toast;

/**
 * @codeCoverageIgnore Handled by Dusk tests.
 */
class AddSshKeyToServerController extends Controller
{
    /**
     * Show a form to add an SSH key to a server.
     */
    public function create(SshKey $sshKey)
    {
        return view('tomato-eddy::ssh-keys.add-to-servers', [
            'sshKey' => $sshKey,
            'servers' => Server::get()->mapWithKeys(function (Server $server) {
                return [$server->id => $server->name_with_ip];
            }),
        ]);
    }

    /**
     * Add an SSH key to a server.
     */
    public function store(Request $request, SshKey $sshKey)
    {
        $request->validate([
            'servers' => ['required', 'array', 'min:1'],
            'servers.*' => ['required', 'exists:servers,id'],
        ]);

        $request->collect('servers')->each(function ($serverId) use ($sshKey) {
            $server = Server::find($serverId);
            dispatch(new AddSshKeyToServer($sshKey, $server));
        });

        Toast::message(__('The SSH Key will be added to the selected servers. This may take a few minutes.'))->autoDismiss(2);

        return to_route('admin.ssh-keys.index');
    }
}
