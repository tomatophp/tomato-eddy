<?php

namespace TomatoPHP\TomatoEddy\Http\Controllers;

use TomatoPHP\TomatoEddy\Jobs\RemoveSshKeyFromServer;
use TomatoPHP\TomatoEddy\Models\Server;
use TomatoPHP\TomatoEddy\Models\SshKey;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use ProtoneMedia\Splade\Facades\Toast;

/**
 * @codeCoverageIgnore Handled by Dusk tests.
 */
class RemoveSshKeyFromServerController extends Controller
{

    /**
     * Show a form to remove an SSH key to a server.
     */
    public function edit(SshKey $sshKey)
    {
        return view('tomato-eddy::ssh-keys.remove-from-servers', [
            'sshKey' => $sshKey,
            'servers' => Server::get()->mapWithKeys(function (Server $server) {
                return [$server->id => $server->name_with_ip];
            }),
        ]);
    }

    /**
     * Remove an SSH key from a server.
     */
    public function destroy(Request $request, SshKey $sshKey)
    {
        $request->validate([
            'servers' => ['required', 'array', 'min:1'],
            'servers.*' => ['required', 'exists:servers,id'],
        ]);

        $request->collect('servers')->each(function ($serverId) use ($sshKey) {
            $server = Server::find($serverId);

            dispatch(new RemoveSshKeyFromServer($sshKey->public_key, $server));
        });

        Toast::message(__('The SSH Key will be removed from the selected servers. This may take a few minutes.'))->autoDismiss(2);

        return to_route('admin.ssh-keys.index');
    }
}
