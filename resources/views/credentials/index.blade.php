<x-tomato-admin-layout>
    <x-slot:header>
        {{ __('Credentials') }}
    </x-slot:header>
    <x-slot:buttons>
        <x-tomato-admin-button type="link" modal href="{{ route('admin.credentials.create') }}">
            {{ __('Add Credentials') }}
        </x-tomato-admin-button>
    </x-slot:buttons>

    <div class="pb-12">
        <div class="mx-auto">
            <x-splade-table :for="$table" striped>
                <x-splade-cell actions>
                    @if($item->provider !== \TomatoPHP\TomatoEddy\Enums\Services\Provider::Github && $item->provider !== \TomatoPHP\TomatoEddy\Enums\Services\Provider::Cloudflare)
                        <x-tomato-admin-button type="link" modal href="{{ route('admin.servers.create', ['credentials' => $item->id]) }}">
                            {{ __('Create Server') }}
                        </x-tomato-admin-button>
                    @else
                        <x-splade-link class="filament-button inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset dark:focus:ring-offset-0 min-h-[2.25rem] px-4 text-sm shadow-sm focus:ring-white filament-page-button-action bg-danger-600 hover:bg-danger-500 focus:bg-danger-700 focus:ring-offset-danger-700 text-white border-transparent cursor-pointer transition-colors ease-in-out duration-20" method="DELETE" confirm-danger :href="route('admin.credentials.destroy', $item->id)">
                            {{__('Delete Credentials')}}
                        </x-splade-link>
                    @endif
                </x-splade-cell>
            </x-splade-table>
        </div>
    </div>
</x-tomato-admin-layout>

