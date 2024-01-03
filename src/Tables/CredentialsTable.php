<?php

namespace TomatoPHP\TomatoEddy\Tables;

use Illuminate\Http\Request;
use ProtoneMedia\Splade\AbstractTable;
use ProtoneMedia\Splade\SpladeTable;
use TomatoPHP\TomatoEddy\Models\Credentials;

class CredentialsTable extends AbstractTable
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
            $this->query = Credentials::query();
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
            ->column('id', __('ID'))
            ->column('name', __('Name'))
            ->column('provider_name', __('Provider'))
            ->column('actions')
            ->rowModal(fn (Credentials $credentials) => route('admin.credentials.edit', $credentials))
            ->defaultSort('name')
            ->paginate(15);
    }
}
