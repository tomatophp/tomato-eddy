<x-tomato-admin-layout>
    <x-slot:header>
        <h1><x-splade-link :href="route('admin.servers.show', $server->id)">{{ $server->name }}</x-splade-link> [{{ $site->address }}]</h1>
        <div class="flex justify-start gap-2 text-sm font-normal">
            <div>
                {{ $server->public_ipv4 }}
            </div>

            <x-tomato-admin-copy :text="$server->public_ipv4">
                <div class="flex flex-col justify-center">
                    <i class="bx bx-copy"></i>
                </div>
            </x-tomato-admin-copy>
        </div>
    </x-slot:header>
    <x-slot:icon>
        bx bx-globe
    </x-slot:icon>
    <x-slot:buttons>
        <x-tomato-admin-button
            :confirm="$site->isDeploying() ? false : __('Are you sure you want to start a new deployment?')"
            :method="$site->isDeploying() ? 'GET' : 'POST'"
            type="link"
            :href="$site->isDeploying() ? route('admin.servers.sites.deployments.show', [$server, $site, $site->latestDeployment]) : route('admin.servers.sites.deployments.store', [$server, $site])"
            class="flex items-center justify-center"
            dusk="deploy-site"
        >
            @if ($site->isDeploying())
                @svg('heroicon-o-cog-6-tooth', 'h-5 w-5 -ml-1 mr-2 animate-spin')
                <span>{{ __('Deploying...') }}</span>
            @else
                @svg('heroicon-o-arrow-up-on-square-stack', 'h-5 w-5 -ml-1 mr-2')
                <span>{{ __('Deploy') }}</span>
            @endif
        </x-tomato-admin-button>
    </x-slot:buttons>

    <x-splade-data remember="server-sidebar" local-storage default="{menu: false}">
        <div class="grid grid-cols-12 gap-4">
            <div class="col-span-12 md:col-span-4 xl:col-span-2 flex flex-col gap-2" v-if="data.menu">
                <div class="hidden md:block">
                    @include('tomato-eddy::sites.sidebar')
                </div>
                <div class="block md:hidden">
                    @include('tomato-eddy::sites.sidebar-mobile')
                </div>
            </div>
            <div class="flex flex-col gap-4" :class="{'col-span-12 md:col-span-8 xl:col-span-10 ':data.menu, 'col-span-12':!data.menu}">
                <div class="dark:bg-gray-800 dark:text-white p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <section class="mb-4 @hasSection('buttons') flex justify-between @endif">
                        <div class="flex justify-start gap-4">
                            <div>
                                <x-tomato-admin-button type="button" @click.prevent="data.menu = !data.menu">
                                    <i class="bx bx-menu-alt-left"></i>
                                </x-tomato-admin-button>
                            </div>
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
    </x-splade-data>
</x-tomato-admin-layout>
