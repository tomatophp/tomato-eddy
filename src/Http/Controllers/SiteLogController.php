<?php

namespace TomatoPHP\TomatoEddy\Http\Controllers;

use TomatoPHP\TomatoEddy\Services\FileOnServer;
use TomatoPHP\TomatoEddy\Models\Server;
use TomatoPHP\TomatoEddy\Models\Site;
use ProtoneMedia\Splade\SpladeTable;
use TomatoPHP\TomatoEddy\Tables\FilesTable;

/**
 * @codeCoverageIgnore Handled by Dusk tests.
 */
class SiteLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Server $server, Site $site)
    {
        return view('tomato-eddy::sites.logs.index', [
            'server' => $server,
            'site' => $site,
            'logs' => (new FilesTable($site->files()->logFiles(), $server, true)),
        ]);
    }
}
