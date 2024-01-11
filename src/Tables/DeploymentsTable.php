<?php

namespace TomatoPHP\TomatoEddy\Tables;

use Illuminate\Http\Request;
use ProtoneMedia\Splade\AbstractTable;
use ProtoneMedia\Splade\SpladeTable;
use TomatoPHP\TomatoEddy\Models\Credentials;
use TomatoPHP\TomatoEddy\Models\Cron;
use TomatoPHP\TomatoEddy\Models\Deployment;
use TomatoPHP\TomatoEddy\Models\Server;
use TomatoPHP\TomatoEddy\Models\Site;

class DeploymentsTable extends AbstractTable
{
    /**
     * Create a new instance.
     *
     * @return void
     */
    public function __construct(
        private mixed $query = null,
        public Server $server,
        public Site $site,
    ) {
        if(!$this->query){
            $this->query = Deployment::query();
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
            ->column('updated_at', __('Deployed at'), sortable: true)
            ->column('user.name', __('User'), as: fn ($name) => $name ?: __('Via Deploy URL'))
            ->column('short_git_hash', __('Git Hash'))
            ->column('status', __('Status'))
            ->withGlobalSearch(__('Search Git Hash...'), ['git_hash'])
            ->rowLink(fn (Deployment $deployment) => route('admin.servers.sites.deployments.show', [$this->server, $this->site, $deployment]))
            ->defaultSort('-updated_at')
            ->paginate(15);
    }
}
