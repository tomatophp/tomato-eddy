<x-server-layout :server="$site->server" :title="__('Update Site Settings')">
    <x-action-section in-sidebar-layout>
        <x-slot:title>
            {{ __("Update Site Settings") }}
            </x-slot>

            <x-slot:content>
                <x-splade-form :action="route('site.settings.update', $site)" :default="$site->settings">
                    <div class="flex flex-col gap-4">
                        <x-splade-select :label="__('Server Protocol')" choices option-value="id" option-label="name" name="protocol" :options=' [
                        [
                            "name" => "HTTP",
                            "id" => "http://"
                        ],
                        [
                                "name" => "SOCKS4",
                                "id" => "socks4://"
                        ],
                        [
                                "name" => "SOCKS5",
                                "id" => "socks5://"
                        ]
                    ]' />

                        <x-splade-select :label="__('Server Browser')" choices option-value="id" option-label="name" name="browser" :options=' [
                        [
                            "name" => "Chrome",
                            "id" => "chrome"
                        ],
                        [
                            "name" => "Edge",
                            "id" => "edge"
                        ],
                        [
                            "name" => "Phone Android",
                            "id" => "android"
                        ],
                        [
                            "name" => "Phone IOS",
                            "id" => "ios"
                        ],
                        [
                            "name" => "Firefox",
                            "id" => "firefox"
                        ],
                        [
                            "name" => "Safari",
                            "id" => "safari"
                        ]
                    ]' />

                        <x-splade-select :label="__('Allow Install EX')" choices option-value="id" option-label="name" name="install_ex" :options=' [
                        [
                            "name" => "Yes",
                            "id" => "yes"
                        ],
                        [
                                "name" => "No",
                                "id" => "no"
                        ]
                    ]' />

                        <x-splade-select :label="__('Active 00')" choices option-value="id" option-label="name" name="active_00_password" :options=' [
                        [
                            "name" => "Active",
                            "id" => "yes"
                        ],
                        [
                            "name" => "Inactive",
                            "id" => "no"
                        ]
                    ]' />

                        <x-splade-select :label="__('Active Recovery Popup')" choices option-value="id" option-label="name" name="recovery_popup" :options=' [
                        [
                            "name" => "Active",
                            "id" => "yes"
                        ],
                        [
                            "name" => "Inactive",
                            "id" => "no"
                        ]
                    ]' />
                    </div>

                    <x-splade-submit class="mt-8" :label="__('Update')" />
                </x-splade-form>
                </x-slot>
                </x-action>
</x-server-layout>
