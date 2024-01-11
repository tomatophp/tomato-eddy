<?php

namespace TomatoPHP\TomatoEddy\Tables;

use Illuminate\Http\Request;
use ProtoneMedia\Splade\AbstractTable;
use ProtoneMedia\Splade\SpladeTable;
use TomatoPHP\TomatoEddy\Models\Credentials;
use TomatoPHP\TomatoEddy\Models\Cron;
use TomatoPHP\TomatoEddy\Models\Daemon;
use TomatoPHP\TomatoEddy\Models\Server;

class DaemonsTable extends AbstractTable
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
            $this->query = Daemon::query();
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
            ->withGlobalSearch(label: __('Search By Command'), columns: [
                'command',
            ])
            ->column('command', __('Command'))
            ->column('user', __('User'))
            ->column('processes', __('Processes'))
            ->column('status', __('Status'))
            ->rowModal(fn (Daemon $daemon) => route('admin.servers.daemons.edit', [$this->server, $daemon]))
            ->defaultSort('command')
            ->paginate(15);
    }
}
