<?php

namespace TomatoPHP\TomatoEddy\Http\Controllers;

use TomatoPHP\TomatoEddy\Jobs\MakeSoftwareDefaultOnServer;
use TomatoPHP\TomatoEddy\Jobs\RestartSoftwareOnServer;
use TomatoPHP\TomatoEddy\Models\Server;
use TomatoPHP\TomatoEddy\Enums\Server\Software;
use ProtoneMedia\Splade\Facades\Toast;
use ProtoneMedia\Splade\SpladeTable;
use TomatoPHP\TomatoEddy\Tables\SoftwaresTable;
use TomatoPHP\TomatoEddy\Tasks\ReloadSupervisor;

/**
 * @codeCoverageIgnore Handled by Dusk tests.
 */
class SoftwareController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Server $server)
    {
        return view('tomato-eddy::software.index', [
            'server' => $server,
            'software' => (new SoftwaresTable($server->installedSoftware()->merge([Software::SuperVisor])->map(function (Software $software) {
                return [
                    'id' => $software->value,
                    'name' => $software->getDisplayName(),
                    'hasRestartTask' => $software->restartTaskClass() ? true : false,
                    'hasUpdateAlternativesTask' => $software->updateAlternativesTask() ? true : false,
                ];
            }), $server))
        ]);
    }

    /**
     * Make the specified resource the 'default' one with update-alternatives.
     */
    public function default(Server $server, Software $software)
    {
        dispatch(new MakeSoftwareDefaultOnServer($server, $software));

        $this->logActivity(__("Made ':software' the CLI default on server ':server'", ['software' => $software->getDisplayName(), 'server' => $server->name]), $server);

        Toast::success(__(':software will now be the CLI default on the server.', ['software' => $software->getDisplayName()]))->autoDismiss(2);

        return to_route('admin.servers.software.index', $server);
    }

    /**
     * Restart the specified resource.
     */
    public function restart(Server $server, Software $software)
    {
        dispatch(new RestartSoftwareOnServer($server, $software));

        $this->logActivity(__("Restarted ':software' on server ':server'", ['software' => $software->getDisplayName(), 'server' => $server->name]), $server);

        Toast::success(__(':software will be restarted on the server.', ['software' => $software->getDisplayName()]))->autoDismiss(2);

        return to_route('admin.servers.software.index', $server);
    }
}
