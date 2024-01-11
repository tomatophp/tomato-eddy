<?php

namespace TomatoPHP\TomatoEddy\Tables;

use Illuminate\Http\Request;
use ProtoneMedia\Splade\AbstractTable;
use ProtoneMedia\Splade\SpladeTable;
use TomatoPHP\TomatoEddy\Models\Credentials;
use TomatoPHP\TomatoEddy\Models\Database;
use TomatoPHP\TomatoEddy\Models\Server;

class DatabaseTable extends AbstractTable
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
            $this->query = Database::query();
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
            ->column('site.address', __('Site'))
            ->column('name', __('Database'))
            ->column('status', __('Status'))
            ->rowModal(fn (Database $database) => route('admin.servers.databases.edit', [$this->server, $database]))
            ->defaultSort('name')
            ->paginate(15);
    }
}
