<x-server-layout :$server :title="__('Edit Cron')">
    <x-action-section in-sidebar-layout>
        <x-slot:title>
            {{ __("Edit Cron on server ':server'.", ['server' => $server->name]) }}
        </x-slot>

        <x-slot:content>
            <x-splade-form method="PATCH" :action="route('servers.crons.update', [$server, $cron])" :default="$cron" class="space-y-4">
                <x-splade-input name="command" :label="__('Command')" placeholder="php8.2 /home/eddy/site.com/current/artisan schedule:run" autofocus />
                <x-splade-input name="user" :label="__('User')" />
                <x-splade-radios name="frequency" :label="__('Frequency')" :options="$frequencies" />
                <x-splade-input v-if="form.frequency == 'custom'" name="custom_expression" :label="__('Expression')" />

                <div class="flex flex-row justify-between items-center">
                    <x-splade-submit :label="__('Deploy')" />

                    <x-splade-link confirm-danger method="DELETE" :href="route('servers.crons.destroy', [$server, $cron])">
                        <x-splade-button danger :label="__('Delete Cron')" />
                    </x-splade-link>
                </div>

            </x-splade-form>
        </x-slot>
    </x-action>
</x-server-layout>