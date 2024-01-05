<?php

namespace TomatoPHP\TomatoEddy\Tables;

use Illuminate\Http\Request;
use ProtoneMedia\Splade\AbstractTable;
use ProtoneMedia\Splade\SpladeTable;

class SiteTable extends AbstractTable
{
    /**
     * Create a new instance.
     *
     * @return void
     */
    public function __construct(
        public string $server
    ) {
        //
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
        return \App\Models\Site::query()->where('forge_server_id', $this->server);
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
                'name',
                'forge_site_id',
            ])
            ->export()
            ->defaultSort('id')
            ->column(key: 'actions', label: trans('tomato-admin::global.crud.actions'))
            ->column(label: 'Id', sortable: true)
            ->column(key: 'forge_site_id', label: 'Site ID', sortable: true)
            ->column(key: 'name', label: 'Domain', sortable: true)
            ->column(key: 'status', label: 'Status', sortable: true)
            ->paginate(15);
    }
}
