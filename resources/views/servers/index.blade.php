<x-tomato-admin-layout>
    <x-slot:header>
        {{ __('Servers') }}
    </x-slot:header>
    <x-slot:buttons>
        <x-tomato-admin-button type="link" modal href="{{ route('admin.servers.create') }}">
            {{ __('New Server') }}
        </x-tomato-admin-button>
    </x-slot:buttons>

    <div class="pb-12">
        <div class="mx-auto">
            <x-splade-table :for="$table" striped>
                <x-splade-cell status>
                    <p class="space-x-2">
                        <span>{{ $item->status_name }}</span>
                    </p>
                </x-splade-cell>
                <x-splade-cell actions>
                    @php
                        $projectID = \TomatoPHP\TomatoEddy\Models\Credentials::where('provider', 'hetzner_cloud')->first()?->credentials['hetzner_cloud_project_id'];
                        $vncLink = "https://console.hetzner.cloud/console/".$projectID."/".$item->provider_id;
                    @endphp
                    <a target="_blank" href="https://{{$item->sites()->first()?->address}}/admin" class="px-2 text-orange-500">
                        <div class="flex justify-start space-x-2">
                            <x-heroicon-s-globe-alt class="h-4 w-4 ltr:mr-2 rtl:ml-2"/>
                            <span>{{__('Open Site')}}</span>
                        </div>
                    </a>
                    <a target="_blank" href="{{$vncLink}}" class="px-2 text-orange-500">
                        <div class="flex justify-start space-x-2">
                            <x-heroicon-s-server class="h-4 w-4 ltr:mr-2 rtl:ml-2"/>
                            <span>{{__('VNC')}}</span>
                        </div>
                    </a>
                    <x-splade-link :href="route('admin.servers.show', $item)" class="px-2 text-blue-500">
                        <div class="flex justify-start space-x-2">
                            <x-heroicon-s-eye class="h-4 w-4 ltr:mr-2 rtl:ml-2"/>
                            <span>{{__('View')}}</span>
                        </div>
                    </x-splade-link>
                    <x-splade-link :href="route('admin.servers.edit', $item)" class="px-2 text-yellow-400" modal>
                        <div class="flex justify-start space-x-2">
                            <x-heroicon-s-pencil class="h-4 w-4 ltr:mr-2 rtl:ml-2"/>
                            <span>{{__('Edit')}}</span>
                        </div>
                    </x-splade-link>
                </x-splade-cell>
            </x-splade-table>
        </div>
    </div>

{{--    <x-slot:description>--}}
{{--        {{ __('Manage your servers.') }}--}}
{{--    </x-slot>--}}

{{--    @if($servers->query->count())--}}
{{--        <x-slot:actions>--}}
{{--            <div class="flex justify-start gap-4">--}}
{{--                <x-splade-button type="link" modal href="{{ route('servers.create') }}">--}}
{{--                    {{ __('New Server') }}--}}
{{--                </x-splade-button>--}}
{{--                <x-splade-button type="link" confirm  href="{{ route('servers.build') }}">--}}
{{--                    {{ __('Build Sites') }}--}}
{{--                </x-splade-button>--}}
{{--                <x-splade-button type="link" confirm  href="{{ route('servers.scan') }}">--}}
{{--                    {{ __('Scan Sites') }}--}}
{{--                </x-splade-button>--}}
{{--                <x-splade-button type="link" confirm  href="{{ route('servers.restart.all') }}">--}}
{{--                    {{ __('Restart All') }}--}}
{{--                </x-splade-button>--}}
{{--                <x-splade-button type="link" confirm  href="{{ route('servers.sync') }}">--}}
{{--                    {{ __('Sync All') }}--}}
{{--                </x-splade-button>--}}
{{--            </div>--}}
{{--        </x-slot>--}}
{{--    @endif--}}
{{--    <div class="border p-2 rounded-lg my-4">--}}
{{--        Current Tasks on Queue: {{\App\Models\Job::count()}}--}}
{{--    </div>--}}

{{--    <x-splade-table :for="$servers">--}}
{{--        <x-splade-cell status>--}}
{{--            <p class="space-x-2">--}}
{{--                <span>{{ $item->status_name }}</span>--}}
{{--            </p>--}}
{{--        </x-splade-cell>--}}
{{--        <x-splade-cell tags>--}}
{{--            <div class="flex justify-start">--}}
{{--                @foreach($item->tags as $tag)--}}
{{--                    <a href="/servers?tag={{$tag->id}}" class="mx-2 px-2 py-1 text-xs font-medium leading-4 text-white bg-indigo-500 rounded-full">--}}
{{--                        {{$tag->name}}--}}
{{--                    </a>--}}
{{--                @endforeach--}}
{{--            </div>--}}
{{--        </x-splade-cell>--}}
{{--        <x-splade-cell actions>--}}
{{--            @php--}}
{{--                $projectID = \App\Models\Credentials::where('provider', 'hetzner_cloud')->first()?->credentials['hetzner_cloud_project_id'];--}}
{{--                $vncLink = "https://console.hetzner.cloud/console/".$projectID."/".$item->provider_id;--}}
{{--            @endphp--}}
{{--            <a target="_blank" href="https://{{$item->sites()->first()?->address}}/admin" class="px-2 text-orange-500">--}}
{{--                <div class="flex justify-start space-x-2">--}}
{{--                    <x-heroicon-s-globe-alt class="h-4 w-4 ltr:mr-2 rtl:ml-2"/>--}}
{{--                    <span>{{__('Open Site')}}</span>--}}
{{--                </div>--}}
{{--            </a>--}}
{{--            <a target="_blank" href="{{$vncLink}}" class="px-2 text-orange-500">--}}
{{--                <div class="flex justify-start space-x-2">--}}
{{--                    <x-heroicon-s-server class="h-4 w-4 ltr:mr-2 rtl:ml-2"/>--}}
{{--                    <span>{{__('VNC')}}</span>--}}
{{--                </div>--}}
{{--            </a>--}}
{{--            <x-splade-link :href="route('servers.show', $item)" class="px-2 text-blue-500">--}}
{{--                <div class="flex justify-start space-x-2">--}}
{{--                    <x-heroicon-s-eye class="h-4 w-4 ltr:mr-2 rtl:ml-2"/>--}}
{{--                    <span>{{__('View')}}</span>--}}
{{--                </div>--}}
{{--            </x-splade-link>--}}
{{--            <x-splade-link :href="route('servers.actions.edit', $item)" class="px-2 text-yellow-400" modal>--}}
{{--                <div class="flex justify-start space-x-2">--}}
{{--                    <x-heroicon-s-pencil class="h-4 w-4 ltr:mr-2 rtl:ml-2"/>--}}
{{--                    <span>{{__('Edit')}}</span>--}}
{{--                </div>--}}
{{--            </x-splade-link>--}}
{{--        </x-splade-cell>--}}

{{--        <x-slot:empty-state>--}}
{{--            <x-empty-state modal :href="route('servers.create')">--}}
{{--                {{ __('New Server') }}--}}
{{--            </x-empty-state>--}}
{{--        </x-slot>--}}
{{--    </x-splade-table>--}}
</x-tomato-admin-layout>
