<x-tomato-admin-container :label="__('Attach Storage To Server')">
    <x-splade-form confirm :action="route('admin.servers.storage', $server)" class="flex flex-col gap-4" :default="[
                            'size' => 250
                        ]">
        <x-splade-input name="size" type="number"  :label="__('Size')" />
        <x-splade-submit />
    </x-splade-form>
</x-tomato-admin-container>
