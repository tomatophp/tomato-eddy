<?php

namespace TomatoPHP\TomatoEddy\Http\Controllers;

use TomatoPHP\TomatoEddy\Models\Deployment;
use TomatoPHP\TomatoEddy\Models\Server;
use TomatoPHP\TomatoEddy\Models\Site;
use ProtoneMedia\Splade\Facades\Toast;
use ProtoneMedia\Splade\SpladeTable;
use TomatoPHP\TomatoEddy\Tables\DeploymentsTable;

/**
 * @codeCoverageIgnore Handled by Dusk tests.
 */
class SiteDeploymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Server $server, Site $site)
    {
        $query = $site->deployments();

        return view('tomato-eddy::sites.deployments.index', [
            'server' => $server,
            'site' => $site,
            'deployments' => (new DeploymentsTable($query, $server, $site)),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Server $server, Site $site)
    {
        $deployment = $site->deploy(user: $this->user());

        Toast::success(__('Deployment queued.'));

        return to_route('admin.servers.sites.deployments.show', [$server, $site, $deployment]);
    }

    /**
     * Deploy the site with the given token.
     */
    public function deployWithToken(Site $site, string $token)
    {
        if ($token !== $site->deploy_token) {
            abort(403);
        }

        $teamSubscriptionOptions = $site->server->team->subscriptionOptions();

        if ($teamSubscriptionOptions->mustVerifySubscription() && ! $teamSubscriptionOptions->onTrialOrIsSubscribed()) {
            abort(402, 'Your team must have an active subscription to perform this action.');
        }

        $site->deploy();

        return response()->noContent(200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Server $server, Site $site, Deployment $deployment)
    {
        return view('tomato-eddy::sites.deployments.show', [
            'server' => $server,
            'site' => $site,
            'deployment' => $deployment,
        ]);
    }
}
