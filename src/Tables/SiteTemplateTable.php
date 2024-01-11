<?php

namespace TomatoPHP\TomatoEddy\Tables;

use Illuminate\Http\Request;
use ProtoneMedia\Splade\AbstractTable;
use ProtoneMedia\Splade\Facades\Toast;
use ProtoneMedia\Splade\SpladeTable;
use Illuminate\Database\Eloquent\Builder;

class SiteTemplateTable extends AbstractTable
{
    /**
     * Create a new instance.
     *
     * @return void
     */
    public function __construct(public mixed $query=null)
    {
        if(!$query){
            $this->query = \TomatoPHP\TomatoEdd\Models\SiteTemplate::query();
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
     * @param \ProtoneMedia\Splade\SpladeTable $table
     * @return void
     */
    public function configure(SpladeTable $table)
    {
        $table
            ->withGlobalSearch(
                label: trans('tomato-admin::global.search'),
                columns: ['id','name']
            )
            ->bulkAction(
                label: trans('tomato-admin::global.crud.delete'),
                each: fn (\TomatoPHP\TomatoEdd\Models\SiteTemplate $model) => $model->delete(),
                after: fn () => Toast::danger(__('SiteTemplate Has Been Deleted'))->autoDismiss(2),
                confirm: true
            )
            ->defaultSort('id', 'desc')
            ->column(
                key: 'id',
                label: __('Id'),
                sortable: true,
                hidden: true
            )
            ->column(
                key: 'name',
                label: __('Name'),
                sortable: true,
                searchable: true
            )
            ->column(
                key: 'type',
                label: __('Type'),
                sortable: true
            )
            ->column(
                key: 'repository_url',
                label: __('Repository url'),
                sortable: true
            )
            ->column(
                key: 'repository_branch',
                label: __('Repository branch'),
                sortable: true
            )
            ->column(
                key: 'php_version',
                label: __('Php version'),
                sortable: true
            )
            ->column(key: 'actions',label: trans('tomato-admin::global.crud.actions'))
            ->export()
            ->paginate(10);
    }
}
