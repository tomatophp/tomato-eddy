<x-tomato-admin-container :label="__('Database')">
    <x-splade-form :default="$database" class="space-y-4">
        <x-splade-input name="name" :label="__('Name')" disabled />


        <div class="flex flex-row justify-between items-center">
            @if ($database->installation_failed_at || $database->uninstallation_failed_at)
                <x-tomato-admin-button warning confirm-danger method="PUT" :href="route('admin.servers.databases.update', ['server'=>$server,'database'=> $database])">
                    {{__('Retry')}}
                </x-tomato-admin-button>
            @endif

            <x-tomato-admin-button danger confirm-danger method="DELETE" :href="route('admin.servers.databases.destroy', [$server, $database])">
                {{ __('Delete Database') }}
            </x-tomato-admin-button>
        </div>

    </x-splade-form>
</x-tomato-admin-container>
