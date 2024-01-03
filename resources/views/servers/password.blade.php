@seoTitle(__('Change Root Password'))

<x-app-layout>
    <x-action-section>
        <x-slot:title>
            {{ __('Change Root Password') }}
            </x-slot>

            <x-slot:description>
                {{ __('You can change root password of server to anything you went') }}
                </x-slot>

                <x-slot:content>
                    <x-splade-form confirm :action="route('servers.reset', $server)" class="space-y-4" :default="[
                            'password' => 'Bingbing55'
                        ]">
                        <x-splade-input name="password"  :label="__('Password')" />
                        <x-splade-submit />
                    </x-splade-form>
               </x-slot>
    </x-action-section>
</x-app-layout>
