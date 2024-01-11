<x-tomato-admin-container :label="__('Add Firewall Rule on server')">
    <x-splade-form :action="route('admin.servers.firewall-rules.store', $server)" :default="[
                'user' => $server->username
            ]" class="flex flex-col gap-4">
        <x-splade-input name="name" :label="__('Name')" />
        <x-splade-radios name="action" :label="__('Action')" :options="$actions" inline />

        <div class="grid grid-cols-2 gap-4">
            <x-splade-input name="port" :label="__('Port')" />
            <x-splade-input name="from_ipv4" :label="__('From IP (optional)')" />
        </div>

        <x-tomato-admin-submit spinner :label="__('Deploy')" />
    </x-splade-form>
</x-tomato-admin-container>
