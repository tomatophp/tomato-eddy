<x-splade-table :for="$table" striped>
    <x-splade-cell actions>
        <div class="flex justify-start">
            <x-tomato-admin-button success type="icon" title="{{trans('tomato-admin::global.crud.view')}}" :href="route('admin.recipes.show', $item->id)">
                <x-heroicon-s-eye class="h-6 w-6"/>
            </x-tomato-admin-button>
        </div>
    </x-splade-cell>
</x-splade-table>
