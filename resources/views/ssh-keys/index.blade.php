<x-tomato-admin-layout>
    <x-slot:header>
        {{ __('SSH Keys') }}
    </x-slot:header>
    <x-slot:buttons>
        <x-tomato-admin-button type="link" modal href="{{ route('admin.ssh-keys.create') }}">
            {{ __('Add SSH Key') }}
        </x-tomato-admin-button>
    </x-slot:buttons>

    <div class="pb-12">
        <div class="mx-auto">
            <x-splade-table :for="$table" striped>
                <x-splade-cell actions>
                    <x-splade-button-with-dropdown class="max-w-fit" inline teleport>
                        <x-slot:button> {{ __('Actions...') }} </x-slot:button>

                        <ul class="divide-y divide-gray-200 text-sm text-gray-700">

                            @if(\TomatoPHP\TomatoEddy\Models\Server::count() > 0)
                                <Link href="{{ route('admin.ssh-keys.servers.add-form', $item) }}" modal class="px-4 py-2 flex items-center justify-between hover:bg-gray-100 hover:text-gray-900 rounded-t-md">
                                    {{ __('Add To Servers') }}
                                </Link>

                                <Link href="{{ route('admin.ssh-keys.servers.remove-form', $item) }}" modal class="px-4 py-2 flex items-center justify-between hover:bg-gray-100 hover:text-gray-900">
                                    {{ __('Remove From Servers') }}
                                </Link>

                                <Link href="{{ route('admin.ssh-keys.destroy', [$item, 'remove-from-servers' => 1]) }}" method="DELETE" confirm-danger class="px-4 py-2 flex items-center justify-between hover:bg-gray-100 hover:text-gray-900 rounded-b-md">
                                    {{ __('Delete Key and Remove From Servers') }}
                                </Link>
                            @endif

                            <Link href="{{ route('admin.ssh-keys.destroy', $item) }}" method="DELETE" confirm-danger class="px-4 py-2 flex items-center justify-between hover:bg-gray-100 hover:text-gray-900 rounded-b-md">
                                {{ __('Delete Key') }}
                            </Link>


                        </ul>
                    </x-splade-button-with-dropdown>
                </x-splade-cell>

            </x-splade-table>
        </div>
    </div>
</x-tomato-admin-layout>
