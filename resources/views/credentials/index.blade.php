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
                        <x-tomato-admin-button danger method="DELETE" confirm :href="route('admin.credentials.destroy', $item->id)">
                            {{__('Delete Credentials')}}
                        </x-tomato-admin-button>
                    @endif
                </x-splade-cell>
            </x-splade-table>
        </div>
    </div>
</x-tomato-admin-layout>

