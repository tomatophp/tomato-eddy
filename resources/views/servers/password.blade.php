<x-tomato-admin-container :label="__('Change Root Password')">
    <x-splade-script>
        $splade.generatePassword = function () {
        const charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        return Array.from(crypto.getRandomValues(new Uint32Array(32)))
        .map((x) => charset[x % charset.length])
        .join('')
        };
    </x-splade-script>
    <x-splade-form confirm :action="route('admin.servers.reset', $server)" class="flex flex-col gap-4">
        <x-splade-input name="password"  :label="__('Password')">
            <x-slot:append>
                <button @click="form.password = $splade.generatePassword()" type="button" class="text-gray-500 hover:text-gray-700 focus:outline-none focus:text-gray-700">
                    @svg('heroicon-o-sparkles', 'h-5 w-5')
                </button>
            </x-slot:append>
        </x-splade-input>
        <x-splade-submit />
    </x-splade-form>
</x-tomato-admin-container>
