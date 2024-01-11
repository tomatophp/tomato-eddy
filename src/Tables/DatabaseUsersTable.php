<?php

namespace TomatoPHP\TomatoEddy\Tables;

use Illuminate\Http\Request;
use ProtoneMedia\Splade\AbstractTable;
use ProtoneMedia\Splade\SpladeTable;
use TomatoPHP\TomatoEddy\Models\Credentials;
use TomatoPHP\TomatoEddy\Models\Database;
use TomatoPHP\TomatoEddy\Models\DatabaseUser;
use TomatoPHP\TomatoEddy\Models\Server;

class DatabaseUsersTable extends AbstractTable
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
            $this->query = DatabaseUser::query();
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
            ->column('name', __('Name'))
            ->column('status', __('Status'))
            ->rowModal(fn (DatabaseUser $databaseUser) => route('admin.servers.database-users.edit', [$this->server, $databaseUser]))
            ->defaultSort('name')
            ->paginate(15);
    }
}
