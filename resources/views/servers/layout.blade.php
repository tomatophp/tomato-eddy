<x-tomato-admin-layout>
    <x-slot:header>
        <h1>{{ $server->name }}</h1>
        <p class="text-sm font-normal">{{ $server->public_ipv4 }}</p>
    </x-slot:header>
    <x-slot:icon>
        bx bx-server
    </x-slot:icon>
    <x-slot:buttons>
        <x-tomato-admin-dropdown>
            <x-slot:button>
                <x-tomato-admin-button type="button">
                    {{ __('Server Actions') }}
                </x-tomato-admin-button>
            </x-slot:button>

            @php
                $projectID = \TomatoPHP\TomatoEddy\Models\Credentials::where('provider', 'hetzner_cloud')->first()?->credentials['hetzner_cloud_project_id'];
                if($projectID){
                    $vncLink = "https://console.hetzner.cloud/console/".$projectID."/".$server->provider_id;
                }
                else {
                    $vncLink = false;
                }
            @endphp
            <x-tomato-admin-dropdown-item
                type="a"
                icon="bx bx-desktop"
                :label="__('Server VNC')"
                :link="$vncLink"
            />

            <x-tomato-admin-dropdown-item
                type="link"
                icon="bx bx-arrow-back"
                :label="__('Servers List')"
                :href="route('admin.servers.index')"
            />

            <x-tomato-admin-dropdown-item
                type="link"
                icon="bx bx-reset"
                :label="__('Restart Server')"
                confirm
                method="POST"
                :href="route('admin.servers.restart', $server)"
            />
            <x-tomato-admin-dropdown-item
                type="link"
                icon="bx bx-pause-circle"
                :label="__('Stop Server')"
                confirm
                method="POST"
                :href="route('admin.servers.stop', $server)"
            />
            <x-tomato-admin-dropdown-item
                type="link"
                icon="bx bx-play-circle"
                :label="__('Start Server')"
                confirm
                method="POST"
                :href="route('admin.servers.start', $server)"
            />
            <x-tomato-admin-dropdown-item
                type="link"
                icon="bx bx-lock-alt"
                :label="__('Reset Server Password')"
                modal
                :href="route('admin.servers.reset.view', $server)"
            />
            <x-tomato-admin-dropdown-item
                type="link"
                icon="bx bx-play-circle"
                :label="__('Attach Voulme')"
                modal
                :href="route('admin.servers.storage', $server)"
            />
            <x-tomato-admin-dropdown-item
                danger
                confirm-danger
                type="link"
                icon="bx bx-x-circle"
                :label="__('Destory Attached Voulme')"
                method="DELETE"
                :href="route('admin.servers.storage.destory', $server)"
            />
            <x-tomato-admin-dropdown-item
                danger
                type="link"
                icon="bx bx-trash"
                :label="__('Delete Server')"
                confirm-danger method="DELETE"
                :href="route('admin.servers.destroy', $server)"
            />
            <x-tomato-admin-dropdown-item
                danger
                type="link"
                icon="bx bx-x-circle"
                :label="__('Disconect Server')"
                confirm-danger
                method="POST"
                :href="route('admin.servers.disconect', $server)"
            />
        </x-tomato-admin-dropdown>
    </x-slot:buttons>

    <div class="grid grid-cols-12 gap-4">
        <div class="col-span-12 md:col-span-4 xl:col-span-2 flex flex-col gap-2">
            <div class="hidden md:block">
                @include('tomato-eddy::servers.sidebar')
            </div>
            <div class="block md:hidden">
                @include('tomato-eddy::servers.sidebar-mobile')
            </div>
        </div>
        <div class="col-span-12 md:col-span-8 xl:col-span-10 flex flex-col gap-4">
            <div class="dark:bg-gray-800 dark:text-white p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <section class="mb-4 @hasSection('buttons') flex justify-between @endif">
                    <div>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                                @yield('title')
                            </h2>
                        </header>

                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                            @yield('description')
                        </p>
                    </div>
                    @hasSection('buttons')
                    <div>
                        @yield('buttons')
                    </div>
                    @endif
                </section>
                <div>
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
</x-tomato-admin-layout>
