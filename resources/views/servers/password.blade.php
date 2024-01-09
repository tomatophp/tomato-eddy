<x-tomato-admin-container :label="__('Change Root Password')">
    <x-splade-form confirm :action="route('admin.servers.reset', $server)" class="flex flex-col gap-4" :default="[
                            'password' => str()->password()
                        ]">
        <x-splade-input name="password"  :label="__('Password')" />
        <x-splade-submit />
    </x-splade-form>
</x-tomato-admin-container>
