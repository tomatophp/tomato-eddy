<?php

namespace TomatoPHP\TomatoEddy\Tables;

use Illuminate\Http\Request;
use ProtoneMedia\Splade\AbstractTable;
use ProtoneMedia\Splade\SpladeTable;
use TomatoPHP\TomatoEddy\Models\Credentials;
use TomatoPHP\TomatoEddy\Models\Cron;
use TomatoPHP\TomatoEddy\Models\Server;

class CronsTable extends AbstractTable
{
    /**
     * Create a new instance.
     *
     * @return void
     */
    public function __construct(
        private mixed $query = null,
        public Server $server,
        public array $frequencies
    ) {
        if(!$this->query){
            $this->query = Cron::query();
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
            ->column('expression', __('Frequency'), as: fn ($expression) => $this->frequencies[$expression] ?? $expression)
            ->column('status', __('Status'))
            ->rowModal(fn (Cron $cron) => route('admin.servers.crons.edit', [$this->server, $cron]))
            ->defaultSort('command')
            ->paginate(15);
    }
}
