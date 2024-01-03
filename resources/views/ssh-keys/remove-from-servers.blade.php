<x-tomato-admin-container :label="__('Remove SSH Key')">
    <x-splade-form :action="route('admin.ssh-keys.servers.remove', $sshKey)" class="space-y-4">
        <x-splade-checkboxes :options="$servers" name="servers"  />
        <x-splade-submit />
    </x-splade-form>
</x-tomato-admin-container>
