<x-server-layout :server="$server" :title="__('Server Overview')">
    <x-action-section in-sidebar-layout>
        <x-slot:title>
            {{ __('Server Overview') }}
        </x-slot:title>


        <x-slot:content>
            <dl class="sm:divide-y sm:divide-gray-200">
                <x-description-list-item :label="__('Name')">
                    <span>{{ $server->name }}</span>
                </x-description-list-item>

                <x-description-list-item :label="__('IP Address')">
                    <span>{{ $server->public_ipv4 }}</span>
                    <x-clipboard class="ml-1 w-5 h-5">{{ $server->public_ipv4 }}</x-clipboard>
                </x-description-list-item>

                <x-description-list-item :label="__('Provider')">
                    <span>{{ $server->provider->getDisplayName() }}</span>
                </x-description-list-item>

                <x-description-list-item :label="__('Sudo Password')">
                    <div class="flex justify-start gap-4">
                        {{ $server->password }}
                    </div>
                </x-description-list-item>

                <x-description-list-item :label="__('SSH Key')">
                    <x-clipboard class="ml-1 w-5 h-5">{{ $server->user_public_key }}</x-clipboard>
                </x-description-list-item>

                <x-description-list-item :label="__('Database Password')">
                    <span>{{ $server->database_password }}</span>
                </x-description-list-item>

                <x-description-list-item :label="__('Server Storage')">
                    <span>{{  $server->storage_name }}</span>
                </x-description-list-item>

                <x-description-list-item :label="__('VNC')">
                    <span>
                        @php
                            $projectID = \App\Models\Credentials::where('provider', 'hetzner_cloud')->first()->credentials['hetzner_cloud_project_id'];
                            $vncLink = "https://console.hetzner.cloud/console/".$projectID."/".$server->provider_id;
                        @endphp
                        <a href="{{ $vncLink }}" target="_blank" class="text-blue-600 hover:text-blue-500">{{ __('Open VNC') }}</a>
                    </span>
                </x-description-list-item>

                @if($server->provider_id)
                    <x-description-list-item :label="__('Provider ID')">
                        <span>{{ $server->provider_id }}</span>
                    </x-description-list-item>
                @endif

                <x-description-list-item :label="__('Installed Software')">
                    <ul>
                        @foreach($server->installed_software as $software)
                            <li>{{ \App\Server\Software::from($software)->getDisplayName() }}</li>
                        @endforeach
                    </ul>
                </x-description-list-item>
            </dl>
        </x-slot>
    </x-action-section>

    <x-action-section in-sidebar-layout class='mt-8'>
        <x-slot:title>
            {{ __('Server Actions') }}
            </x-slot>

            <x-slot:description>
                {{ __('You Can restart your sever by click on this button') }}
                </x-slot>

                <x-slot:content>
                    <div class="flex justify-start gap-4">
                        <x-splade-form confirm method="POST" :action="route('servers.restart', $server)">
                            <x-splade-submit warring :label="__('Restart Server')" />
                        </x-splade-form>
                        <x-splade-form confirm method="POST" :action="route('servers.stop', $server)">
                            <x-splade-submit warring :label="__('Stop Server')" />
                        </x-splade-form>
                        <x-splade-form confirm method="POST" :action="route('servers.start', $server)">
                            <x-splade-submit warring :label="__('Start Server')" />
                        </x-splade-form>
                        <x-splade-link class="border rounded-md shadow-sm font-bold py-2 px-4 focus:outline-none focus:ring focus:ring-opacity-50 bg-indigo-500 hover:bg-indigo-700 text-white border-transparent focus:border-indigo-300 focus:ring-indigo-200"  modal :href="route('servers.reset.view', $server)">
                            {{__('Reset Server Password')}}
                        </x-splade-link>
                        <x-splade-link class="border rounded-md shadow-sm font-bold py-2 px-4 focus:outline-none focus:ring focus:ring-opacity-50 bg-indigo-500 hover:bg-indigo-700 text-white border-transparent focus:border-indigo-300 focus:ring-indigo-200"  modal :href="route('servers.storage', $server)">
                            {{__('Attach Storage')}}
                        </x-splade-link>
                    </div>

                </x-slot:content>
    </x-action-section>

    <x-action-section in-sidebar-layout class='mt-8'>
        <x-slot:title>
            {{ __('Delete Server') }}
        </x-slot>

        <x-slot:description>
            {{ __('Deleting a server will remove all settings, sites and deployments associated with it.') }}
        </x-slot>

        <x-slot:content>
            <x-splade-form confirm-danger method="DELETE" :action="route('servers.destroy', $server)">
                <x-splade-submit danger :label="__('Delete Server')" />
            </x-splade-form>
        </x-slot:content>
    </x-action-section>
</x-server-layout>

