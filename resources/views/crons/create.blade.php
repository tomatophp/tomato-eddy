<x-tomato-admin-container :label="__('Add Cron on server')">
    <x-splade-form :action="route('admin.servers.crons.store', $server)" :default="[
                'user' => $server->username,
                'frequency' => '* * * * *',
                'generate' => false,
                'site_id' => null,
            ]" class="flex flex-col gap-4">
        <x-splade-input name="command" :label="__('Command')" placeholder="php8.2 /home/eddy/site.com/repository/artisan schedule:run" autofocus />
        <x-splade-input name="user" :label="__('User')" />
        <x-splade-radios name="frequency" :label="__('Frequency')" :options="$frequencies" />
        <x-splade-input v-if="form.frequency == 'custom'" name="custom_expression" :label="__('Expression')" />
        <div v-show="form.generate">
            <x-tomato-admin-select
                name="site_id"
                option-label="address"
                option-value="object"
                :label="__('Site')"
                :options="$server->sites"
                @select="form.command = $event.php_version + ' /home/eddy/' + $event.address + '/repository/artisan schedule:run'"
            />
        </div>
        <div class="flex justify-start gap-4">
            <x-tomato-admin-submit spinner :label="__('Deploy')" />
            <x-tomato-admin-button type="button" warning @click.prevent="form.generate = !form.generate" :label="__('Generate Laravel Schedule Command')" />
        </div>

    </x-splade-form>
</x-tomato-admin-container>
