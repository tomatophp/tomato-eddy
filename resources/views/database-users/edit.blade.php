<x-tomato-admin-container :label="__('Edit Database User')">
    <x-splade-script>
        $splade.generatePassword = function () {
        const charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        return Array.from(crypto.getRandomValues(new Uint32Array(32)))
        .map((x) => charset[x % charset.length])
        .join('')
        };
    </x-splade-script>

    <x-splade-form
        method="PATCH"
        :action="route('admin.servers.database-users.update', [$server, $databaseUser])"
        :default="$databaseUser"
        class="space-y-4"
    >
        <x-splade-input name="name" :label="__('User')" disabled />
        <x-splade-input name="password" :label="__('Password')" :help="__('Leave empty to keep the current password.')">
            <x-slot:append>
                <button @click="form.password = $splade.generatePassword()" type="button" class="text-gray-500 hover:text-gray-700 focus:outline-none focus:text-gray-700">
                    @svg('heroicon-o-sparkles', 'h-5 w-5')
                </button>
            </x-slot:append>
        </x-splade-input>

        @if(!empty($databases))
            <x-splade-checkboxes relation name="databases" :label="__('Allowed Databases')" :options="$databases" />
        @endif

        <div class="flex flex-row justify-between items-center">
            <x-splade-submit />

            <x-splade-link confirm-danger method="DELETE" :href="route('admin.servers.database-users.destroy', [$server, $databaseUser])">
                <x-splade-button danger :label="__('Delete Database User')" />
            </x-splade-link>
        </div>
    </x-splade-form>
</x-tomato-admin-container>
