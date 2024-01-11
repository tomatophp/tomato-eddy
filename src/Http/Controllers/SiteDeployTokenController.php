<?php

namespace TomatoPHP\TomatoEddy\Http\Controllers;

use TomatoPHP\TomatoEddy\Models\Server;
use TomatoPHP\TomatoEddy\Models\Site;
use Illuminate\Support\Str;
use ProtoneMedia\Splade\Facades\Toast;

/**
 * @codeCoverageIgnore Handled by Dusk tests.
 */
class SiteDeployTokenController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function __invoke(Server $server, Site $site)
    {
        $site->deploy_token = Str::random(32);
        $site->save();

        $this->logActivity(__("Updated deploy token of site ':address' on server ':server'", ['address' => $site->address, 'server' => $server->name]), $site);

        Toast::success(__('The deploy token has been regenerated.'))->autoDismiss(2);

        return to_route('admin.servers.sites.show', [$server, $site]);
    }
}
