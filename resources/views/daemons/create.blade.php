<x-tomato-admin-container :label="__('Add Daemon')">
    <x-splade-form :action="route('admin.servers.daemons.store', $server)" :default="[
                'user' => $server->username,
                'processes' => 1,
                'stop_wait_seconds' => 10,
                'stop_signal' => 'TERM',
                'command' => null,
                'directory' => null,
                'generate' => false,
                'site_id' => null,
                'php_version' => $server->php_version
            ]" class="space-y-4">
        <x-splade-input name="command" :label="__('Command')" placeholder="php8.2 artisan horizon" autofocus />
        <x-splade-input name="directory" :label="__('Directory (optional)')" placeholder="/home/eddy/site.com/current" />

        <div class="grid grid-cols-2 gap-4">
            <x-splade-input name="user" :label="__('User')" />
            <x-splade-input name="processes" :label="__('Processes')" />
            <x-splade-input name="stop_wait_seconds" :label="__('Stop Wait Seconds')" />
            <x-splade-select name="stop_signal" :label="__('Stop Signal')" :options="$signals" />
        </div>
        <div v-if="form.generate">
            <x-tomato-admin-select
                name="site_id"
                option-label="address"
                option-value="object"
                :label="__('Site')"
                :options="$server->sites"
                @select="form.command = $event.php_version + ' artisan queue:work --timeout=0'; form.directory='/home/eddy/'+$event.address+'/repository'"
            />
        </div>
        <div class="flex justify-start gap-4">
            <x-tomato-admin-submit spinner :label="__('Deploy')" />
            <x-tomato-admin-button type="button" warning @click.prevent="form.generate = !form.generate" :label="__('Generate Laravel Queue Worker')" />
        </div>
    </x-splade-form>
</x-tomato-admin-container>
