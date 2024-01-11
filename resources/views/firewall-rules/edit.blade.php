<x-tomato-admin-container :label="__('Edit Firewall Rule on server')">
    <x-splade-form method="PATCH" :action="route('admin.servers.firewall-rules.update', [$server, $firewallRule])" :default="$firewallRule" class="space-y-4">
        <x-splade-input name="name" :label="__('Name')" />

        <div class="flex flex-row justify-between items-center">
            <x-tomato-admin-submit spinner :label="__('Save Or Retry')" />

            <x-tomato-admin-button danger confirm-danger method="DELETE" :href="route('admin.servers.firewall-rules.destroy', [$server, $firewallRule])">
                {{ __('Delete Firewall Rule') }}
            </x-tomato-admin-button>
        </div>

    </x-splade-form>
</x-tomato-admin-container>
