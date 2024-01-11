<?php

namespace TomatoPHP\TomatoEddy\Tables;

use Illuminate\Http\Request;
use ProtoneMedia\Splade\AbstractTable;
use ProtoneMedia\Splade\SpladeTable;
use TomatoPHP\TomatoEddy\Enums\Server\Software;
use TomatoPHP\TomatoEddy\Models\Credentials;
use TomatoPHP\TomatoEddy\Models\Cron;
use TomatoPHP\TomatoEddy\Models\Server;

class SoftwaresTable extends AbstractTable
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
            $this->query = Software::query();
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
                'name',
            ])
            ->defaultSort('name')
            ->column('name', __('Name'))
            ->column('actions', __('Actions'));
    }
}
