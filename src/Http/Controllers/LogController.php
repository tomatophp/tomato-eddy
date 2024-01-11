<?php

namespace TomatoPHP\TomatoEddy\Http\Controllers;

use TomatoPHP\TomatoEddy\Services\FileOnServer;
use TomatoPHP\TomatoEddy\Models\Server;
use ProtoneMedia\Splade\SpladeTable;
use TomatoPHP\TomatoEddy\Tables\FilesTable;

/**
 * @codeCoverageIgnore Handled by Dusk tests.
 */
class LogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __invoke(Server $server)
    {
        return view('tomato-eddy::logs.index', [
            'server' => $server,
            'logs' => ( new FilesTable($server->files()->logFiles(), $server, true)),
        ]);
    }
}
