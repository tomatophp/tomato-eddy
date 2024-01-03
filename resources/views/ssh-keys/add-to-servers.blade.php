<x-tomato-admin-container :label=" __('Add SSH Key to Servers')">
    <x-splade-form :action="route('admin.ssh-keys.servers.add', $sshKey)" class="space-y-4">
        <x-splade-checkboxes :options="$servers" name="servers"  />
        <x-splade-submit />
    </x-splade-form>
</x-tomato-admin-container>
