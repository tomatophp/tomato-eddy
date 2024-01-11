<x-tomato-admin-container :label="__('Edit Cron')">
    <x-splade-form method="PATCH" :action="route('admin.servers.crons.update', [$server, $cron])" :default="$cron" class="space-y-4">
        <x-splade-input name="command" :label="__('Command')" placeholder="php8.2 /home/eddy/site.com/current/artisan schedule:run" autofocus />
        <x-splade-input name="user" :label="__('User')" />
        <x-splade-radios name="frequency" :label="__('Frequency')" :options="$frequencies" />
        <x-splade-input v-if="form.frequency == 'custom'" name="custom_expression" :label="__('Expression')" />

        <div class="flex flex-row justify-between items-center">
            <x-tomato-admin-submit spinner :label="__('Deploy')" />

            <x-tomato-admin-button :label="__('Delete Cron')" danger confirm-danger method="DELETE" :href="route('admin.servers.crons.destroy', [$server, $cron])" />
        </div>

    </x-splade-form>
</x-tomato-admin-container>
