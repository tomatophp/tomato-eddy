<?php

namespace TomatoPHP\TomatoEddy\Http\Controllers;

use TomatoPHP\TomatoEddy\Services\FileOnServer;
use TomatoPHP\TomatoEddy\Models\Server;
use TomatoPHP\TomatoEddy\Models\Site;
use ProtoneMedia\Splade\SpladeTable;

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
        return view('sites.logs.index', [
            'server' => $server,
            'site' => $site,
            'logs' => SpladeTable::for($site->files()->logFiles())
                ->column('name', __('Name'))
                ->column('description', __('Description'))
                ->rowModal(fn (FileOnServer $file) => $file->showRoute($server)),
        ]);
    }
}
