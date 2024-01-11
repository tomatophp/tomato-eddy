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
class SiteFileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Server $server, Site $site)
    {
        return view('tomato-eddy::sites.files.index', [
            'server' => $server,
            'site' => $site,
            'files' => (new FilesTable($site->files()->editableFiles(), $server))
        ]);
    }
}
