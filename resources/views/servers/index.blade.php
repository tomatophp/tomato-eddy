<x-tomato-admin-layout>
    <x-slot:header>
        {{ __('Servers') }}
    </x-slot:header>
    <x-slot:buttons>
        <x-tomato-admin-button type="link" modal href="{{ route('admin.servers.create') }}">
            {{ __('New Server') }}
        </x-tomato-admin-button>
        <x-tomato-admin-button type="link" modal href="{{ route('admin.servers.connect') }}">
            {{ __('Connect Server') }}
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
                        if($projectID){
                            $vncLink = "https://console.hetzner.cloud/console/".$projectID."/".$item->provider_id;
                        }
                        else {
                            $vncLink = false;
                        }
                    @endphp
                    <div class="flex justify-start gap-2">
                        @if($item->sites()->count())
                            <a target="_blank" href="https://{{$item->sites()->first()?->address}}" class="px-2 text-orange-500">
                                <x-tomato-admin-tooltip :text="__('Open Website')">
                                    <i class="bx bx-globe bx-sm"></i>
                                </x-tomato-admin-tooltip>
                            </a>
                        @endif
                        @if($vncLink)
                            <a target="_blank" href="{{$vncLink}}" class="px-2 text-orange-500">
                                <x-tomato-admin-tooltip :text="__('View VNC')">
                                    <i class="bx bx-desktop bx-sm"></i>
                                </x-tomato-admin-tooltip>
                            </a>
                        @endif
                        <x-tomato-admin-button :href="route('admin.servers.show', $item)" icon="bx bxs-show" :label="__('View')" />
                    </div>
                </x-splade-cell>
            </x-splade-table>
        </div>
    </div>
</x-tomato-admin-layout>
