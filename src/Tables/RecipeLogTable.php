<?php

namespace TomatoPHP\TomatoEddy\Tables;

use Illuminate\Http\Request;
use ProtoneMedia\Splade\AbstractTable;
use ProtoneMedia\Splade\SpladeTable;
use TomatoPHP\TomatoEddy\Models\Credentials;
use TomatoPHP\TomatoEddy\Models\Cron;
use TomatoPHP\TomatoEddy\Models\RecipesServerLog;
use TomatoPHP\TomatoEddy\Models\Server;

class RecipeLogTable extends AbstractTable
{
    /**
     * Create a new instance.
     *
     * @return void
     */
    public function __construct(
        private mixed $query = null,
    ) {
        if(!$this->query){
            $this->query = RecipeLogTable::query();
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
            ->column('id', __('ID'))
            ->column('server.name', __('Server'))
            ->column('created_at', __('Date'))
            ->rowModal(fn (RecipesServerLog $model) => route('admin.recipes.log.show', [$model]))
            ->defaultSort('id', 'desc')
            ->paginate(10);
    }
}
