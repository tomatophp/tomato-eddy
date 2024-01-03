<?php

namespace TomatoPHP\TomatoEddy\Tables;

use Illuminate\Http\Request;
use ProtoneMedia\Splade\AbstractTable;
use ProtoneMedia\Splade\SpladeTable;
use TomatoPHP\TomatoEddy\Models\SshKey;

class SshKeyTable extends AbstractTable
{
    /**
     * Create a new instance.
     *
     * @return void
     */
    public function __construct(
        private mixed $query = null
    ) {
        if(!$this->query){
            $this->query = SshKey::query();
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
            ->column('name', __('Name'))
            ->column('fingerprint', __('MD5 Fingerprint'))
            ->column('actions', label: '')
            ->defaultSort('name')
            ->paginate(15);
    }
}
