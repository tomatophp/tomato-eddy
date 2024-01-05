<?php

namespace TomatoPHP\TomatoEddy\Tables;

use TomatoPHP\TomatoEddy\Enums\Infrastructure\ServerStatus;
use TomatoPHP\TomatoEddy\Jobs\DeleteServerFromInfrastructure;
use TomatoPHP\TomatoEddy\Models\Account;
use TomatoPHP\TomatoEddy\Models\Log;
use TomatoPHP\TomatoEddy\Models\Server;
use Illuminate\Http\Request;
use ProtoneMedia\Splade\AbstractTable;
use ProtoneMedia\Splade\Facades\Toast;
use ProtoneMedia\Splade\SpladeTable;

class ServerTable extends AbstractTable
{
    /**
     * Create a new instance.
     *
     * @return void
     */
    public function __construct(
        public mixed $query=null
    )
    {
        if(!$query){
            $this->query = Server::query();
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
            ->withGlobalSearch(label: __('Search By Name, IP, Description'), columns: [
                'name',
                'public_ipv4',
                'description'
            ])
            ->bulkAction(
                label: 'Delete Selected',
                each: function (Server $server) {
                    try {
                        $server->forceFill([
                            'status' => ServerStatus::Deleting,
                            'uninstallation_requested_at' => now(),
                        ])->save();

                        try{
                            dispatch(new DeleteServerFromInfrastructure($server,auth()->user()));
                        }catch (\Exception $e){}
                    }
                    catch (\Exception $e) {}
                },
                after: fn () => Toast::message(__('Your server is being deleted.'))->autoDismiss(2),
                confirm: true
            )
            ->column(label: 'Id', sortable: true)
            ->column('name', __('Name'))
            ->column('provider_name', __('Provider'))
            ->column('public_ipv4', __('IP Address'), classes: 'tabular-nums')
            ->column('status', __('Status'))
            ->column('description', __('Description'))
            ->column('actions',__('Actions'))
            ->defaultSort('name')
            ->export()
            ->paginate(15);
    }
}
