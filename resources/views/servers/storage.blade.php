@seoTitle(__('Attach Storage To Server'))

<x-app-layout>
    <x-action-section>
        <x-slot:title>
            {{ __('Attach Storage To Server') }}
            </x-slot>

            <x-slot:description>
                {{ __('If you like to attach more space to the server.') }}
                </x-slot>

                <x-slot:content>
                    <x-splade-form confirm :action="route('servers.storage', $server)" class="space-y-4" :default="[
                            'size' => 250
                        ]">
                        <x-splade-input name="size" type="number"  :label="__('Size')" />
                        <x-splade-submit />
                    </x-splade-form>
               </x-slot>
    </x-action-section>
</x-app-layout>
