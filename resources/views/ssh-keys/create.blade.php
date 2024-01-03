<x-tomato-admin-container :label="__('Add SSH Key')">
    <x-splade-form :action="route('admin.ssh-keys.store')" class="space-y-4">
        <x-splade-input name="name" :label="__('Name')" />

        <x-splade-textarea
            autosize
            name="public_key"
            :label="__('Public Key')"
        />

        <x-splade-submit />
    </x-splade-form>
</x-tomato-admin-container>
