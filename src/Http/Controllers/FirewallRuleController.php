<?php

namespace TomatoPHP\TomatoEddy\Http\Controllers;

use TomatoPHP\TomatoEddy\Enums\Enum;
use TomatoPHP\TomatoEddy\Jobs\InstallFirewallRule;
use TomatoPHP\TomatoEddy\Jobs\UninstallFirewallRule;
use TomatoPHP\TomatoEddy\Models\FirewallRule;
use TomatoPHP\TomatoEddy\Models\Server;
use TomatoPHP\TomatoEddy\Rules\FirewallPort;
use TomatoPHP\TomatoEddy\Enums\Firewall\RuleAction;
use Illuminate\Http\Request;
use ProtoneMedia\Splade\Facades\Toast;
use ProtoneMedia\Splade\SpladeTable;
use TomatoPHP\TomatoEddy\Tables\FirewallRulesTable;

/**
 * @codeCoverageIgnore Handled by Dusk tests.
 */
class FirewallRuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Server $server)
    {
        return view('tomato-eddy::firewall-rules.index', [
            'server' => $server,
            'firewallRules' => (new FirewallRulesTable($server->firewallRules(), $server))
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Server $server)
    {
        return view('tomato-eddy::firewall-rules.create', [
            'server' => $server,
            'actions' => Enum::options(RuleAction::class),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Server $server, Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'action' => ['required', 'string', Enum::rule(RuleAction::class)],
            'port' => ['required',  new FirewallPort],
            'from_ipv4' => ['nullable', 'string', 'ipv4'],
        ]);

        /** @var FirewallRule */
        $firewallRule = $server->firewallRules()->create($data);

        dispatch(new InstallFirewallRule($firewallRule, $this->user()));

        $this->logActivity(__("Created firewall rule ':name' on server ':server'", ['name' => $firewallRule->name, 'server' => $server->name]), $firewallRule);

        Toast::message(__('The Firewall Rule has been created and will be installed on the server.'))->autoDismiss(2);

        return to_route('admin.servers.firewall-rules.index', $server);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Server $server, FirewallRule $firewallRule)
    {
        return view('tomato-eddy::firewall-rules.edit', [
            'firewallRule' => $firewallRule,
            'server' => $server,
            'actions' => Enum::options(RuleAction::class),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Server $server, FirewallRule $firewallRule)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $firewallRule->update($data);

        if($firewallRule->installation_failed_at || $firewallRule->uninstallation_failed_at){
            $firewallRule->forceFill([
                'installation_failed_at' => null,
                'uninstallation_failed_at' => null,
                'installed_at' => now(),
            ])->save();
            dispatch(new InstallFirewallRule($firewallRule, $this->user()));
        }

        $this->logActivity(__("Updated firewall rule ':name' on server ':server'", ['name' => $firewallRule->name, 'server' => $server->name]), $firewallRule);

        Toast::message(__('The Firewall Rule name has been updated.'))->autoDismiss(2);

        return to_route('admin.servers.firewall-rules.index', $server);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Server $server, FirewallRule $firewallRule)
    {
        $firewallRule->markUninstallationRequest();

        dispatch(new UninstallFirewallRule($firewallRule, $this->user()));

        $this->logActivity(__("Deleted firewall rule ':name' from server ':server'", ['name' => $firewallRule->name, 'server' => $server->name]), $firewallRule);

        Toast::message(__('The Firewall Rule will be uninstalled from the server.'))->autoDismiss(2);

        return to_route('admin.servers.firewall-rules.index', $server);
    }
}
