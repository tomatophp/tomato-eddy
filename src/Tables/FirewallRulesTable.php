<?php

namespace TomatoPHP\TomatoEddy\Tables;

use Illuminate\Http\Request;
use ProtoneMedia\Splade\AbstractTable;
use ProtoneMedia\Splade\SpladeTable;
use TomatoPHP\TomatoEddy\Enums\Firewall\RuleAction;
use TomatoPHP\TomatoEddy\Models\Credentials;
use TomatoPHP\TomatoEddy\Models\Database;
use TomatoPHP\TomatoEddy\Models\FirewallRule;
use TomatoPHP\TomatoEddy\Models\Server;

class FirewallRulesTable extends AbstractTable
{
    /**
     * Create a new instance.
     *
     * @return void
     */
    public function __construct(
        private mixed $query = null,
        public Server $server
    ) {
        if(!$this->query){
            $this->query = FirewallRule::query();
        }
    }

    /**
     * Determine if the user is authorized to perform bulk actions and exports.
     *
     * @return bool
     */
    public function authorize(Request $request)
    {
        return true;
    }

    /**
     * The resource or query builder.
     *
     * @return mixed
     */
    public function for()
    {
        return $this->query;
    }

    /**
     * Configure the given SpladeTable.
     *
     * @return void
     */
    public function configure(SpladeTable $table)
    {
        $table
            ->withGlobalSearch(label: __('Search By Name'), columns: [
                'name', 'port', 'from_ipv4'
            ])
            ->column('name', __('Name'), sortable: true)
            ->column('port', __('Port'), sortable: true)
            ->column('action', __('Action'), sortable: true, as: fn (RuleAction $action) => $action->name)
            ->column('from_ipv4', __('From IP'), sortable: true, as: fn ($ip) => $ip ?: __('Any'))
            ->column('status', __('Status'))
            ->rowModal(fn (FirewallRule $firewallRule) => route('admin.servers.firewall-rules.edit', [$this->server, $firewallRule]))
            ->defaultSort('name')
            ->paginate(15);
    }
}
