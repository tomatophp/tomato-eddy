<x-tomato-admin-layout>
    <x-slot:header>
        {{ __('SiteTemplate') }}
    </x-slot:header>
    <x-slot:buttons>
        <x-tomato-admin-button :href="route('admin.site-templates.create')" type="link">
            {{trans('tomato-admin::global.crud.create-new')}} {{__('Template')}}
        </x-tomato-admin-button>
    </x-slot:buttons>

    <div class="pb-12">
        <div class="mx-auto">
            <x-splade-table :for="$table" striped>
                <x-splade-cell actions>
                    <div class="flex justify-start">
                        @if($item->has_server)
                        <div class="mx-4">
                            <x-splade-link confirm success  method="POST" title="{{trans('tomato-admin::global.crud.view')}}" :href="route('admin.site-templates.server', $item->id)" class="filament-button inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset dark:focus:ring-offset-0 min-h-[2.25rem] px-4 text-sm shadow-sm focus:ring-white filament-page-button-action bg-success-600 hover:bg-success-500 focus:bg-success-700 focus:ring-offset-success-700 text-white border-transparent cursor-pointer transition-colors ease-in-out duration-20">
                                {{ __('Create Server') }}
                            </x-splade-link>
                        </div>
                        @endif
                        <div class="flex flex-col justify-center items-center">
                            <x-tomato-admin-button modal success type="icon" title="{{trans('tomato-admin::global.crud.view')}}" :href="route('admin.site-templates.show', $item->id)">
                                <x-heroicon-s-eye class="h-6 w-6"/>
                            </x-tomato-admin-button>
                        </div>
                        <div class="flex flex-col justify-center items-center">
                            <x-tomato-admin-button warning type="icon" title="{{trans('tomato-admin::global.crud.edit')}}" :href="route('admin.site-templates.edit', $item->id)">
                                <x-heroicon-s-pencil class="h-6 w-6"/>
                            </x-tomato-admin-button>
                        </div>
                        <div class="flex flex-col justify-center items-center">
                            <x-tomato-admin-button danger type="icon" title="{{trans('tomato-admin::global.crud.delete')}}" :href="route('admin.site-templates.destroy', $item->id)"
                                                   confirm="{{trans('tomato-admin::global.crud.delete-confirm')}}"
                                                   confirm-text="{{trans('tomato-admin::global.crud.delete-confirm-text')}}"
                                                   confirm-button="{{trans('tomato-admin::global.crud.delete-confirm-button')}}"
                                                   cancel-button="{{trans('tomato-admin::global.crud.delete-confirm-cancel-button')}}"
                                                   method="delete"
                            >
                                <x-heroicon-s-trash class="h-6 w-6"/>
                            </x-tomato-admin-button>
                        </div>
                    </div>
                </x-splade-cell>
            </x-splade-table>
        </div>
    </div>
</x-tomato-admin-layout>
