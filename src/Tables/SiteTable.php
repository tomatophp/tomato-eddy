<?php

namespace TomatoPHP\TomatoEddy\Tables;

use Illuminate\Http\Request;
use ProtoneMedia\Splade\AbstractTable;
use ProtoneMedia\Splade\SpladeTable;
use TomatoPHP\TomatoEddy\Models\Server;
use TomatoPHP\TomatoEddy\Models\Site;

class SiteTable extends AbstractTable
{
    /**
     * Create a new instance.
     *
     * @return void
     */
    public function __construct(
        public mixed $query,
        public Server $server,
    ) {
        if(!$this->query){
            $this->query = Site::query();
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
            ->withGlobalSearch(label: trans('tomato-admin::global.search'), columns: [
                'id',
                'address'
            ])
            ->export()
            ->defaultSort('id')
            ->column('address', __('Address'))
            ->column('php_version_formatted', __('PHP Version'))
            ->column('latestDeployment.updated_at', __('Deployed'))
            ->rowLink(fn (Site $site) => route('admin.servers.sites.show', [$this->server, $site]))
            ->selectFilter('php_version', $this->server->installedPhpVersions(), __('PHP Version'))
            ->defaultSort('address')
            ->paginate(15);
    }
}
