<x-tomato-admin-container :label="__('Edit Daemon')">
    <x-splade-form method="PATCH" :action="route('admin.servers.daemons.update', [$server, $daemon])" :default="$daemon" class="space-y-4">
        <x-splade-input name="command" :label="__('Command')" placeholder="php8.2 artisan horizon" autofocus />
        <x-splade-input name="directory" :label="__('Directory (optional)')" placeholder="/home/eddy/site.com/repository" />

        <div class="grid grid-cols-2 gap-4">
            <x-splade-input name="user" :label="__('User')" />
            <x-splade-input name="processes" :label="__('Processes')" />
            <x-splade-input name="stop_wait_seconds" :label="__('Stop Wait Seconds')" />
            <x-splade-select name="stop_signal" :label="__('Stop Signal')" :options="$signals" />
        </div>

        <div class="flex flex-row justify-between items-center">
            <x-tomato-admin-submit spinner :label="__('Deploy')" />
            <x-tomato-admin-button  danger :label="__('Delete Daemon')" confirm-danger method="DELETE" :href="route('admin.servers.daemons.destroy', [$server, $daemon])" />
        </div>

    </x-splade-form>
</x-tomato-admin-container>
