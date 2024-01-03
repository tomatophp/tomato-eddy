<x-splade-event private channel="teams.{{ auth()->user()->currentTeam->id }}" listen="SiteUpdated" preserve-scroll />

<x-site-layout :site="$site" :title="__('Accounts')">
    @if(\App\Models\Account::count()>0)
        <x-slot:actions>
            <x-splade-button type="link" modal href="{{ route('servers.sites.create', $server) }}">
                {{ __('New Account') }}
            </x-splade-button>
        </x-slot>
    @endif
        <div class="my-4 grid grid-cols-1 gap-4 mb-4 filament-widgets-container lg:grid-cols-3">
            <x-widget label="Accounts" :counter="$counters['total']" />
            <x-widget label="Web Login" :counter="$counters['web']" />
            <x-widget label="Mobile Login" :counter="$counters['mobile']" />
            <x-widget label="Not Login" :counter="$counters['not']" />
            <x-widget label="Fails Account" :counter="$counters['fail']" />
            <x-widget label="Blocked Account" :counter="$counters['blocked']" />
            <x-widget label="Points" :counter="$counters['points']" />
            <x-widget label="Money" :counter="'$'.number_format((int)$counters['money'])" />
            <x-widget label="Cards" :counter="number_format($counters['cards'])" />
            <x-widget label="Suspended" :counter="$counters['suspended']" style="col-span-3" />
        </div>
            <div class="my-4 grid grid-cols-2 gap-4 mb-4 filament-widgets-container lg:grid-cols-3">
                <x-splade-button type="link" confirm href="{{ url('actions?action=runAll&site_id='.$site->id) }}">
                    {{ __('Run All') }}
                </x-splade-button>
                <x-splade-button type="link" confirm href="{{ url('actions?action=runAllSearch&site_id='.$site->id) }}">
                    {{ __('Search All') }}
                </x-splade-button>
                <x-splade-button type="link" confirm href="{{ url('actions?action=runAllLinks&site_id='.$site->id) }}">
                    {{ __('Links All') }}
                </x-splade-button>
            </div>
            <div class="my-4 grid grid-cols-2 gap-4 mb-4 filament-widgets-container lg:grid-cols-4">
                <x-splade-button type="link" confirm href="{{ url('actions?action=redeem&site_id='.$site->id) }}">
                    {{ __('Redeem All') }}
                </x-splade-button>
                <x-splade-button type="link" confirm  href="{{ url('actions?action=checkAll&site_id='.$site->id) }}">
                    {{ __('Check All') }}
                </x-splade-button>
                <x-splade-button type="link" confirm  href="{{ url('actions?action=checkAllWeb&site_id='.$site->id) }}">
                    {{ __('Check Web All') }}
                </x-splade-button>
                <x-splade-button type="link" confirm  href="{{ url('actions?action=checkAllMobile&site_id='.$site->id) }}">
                    {{ __('Check Mobile All') }}
                </x-splade-button>
                <x-splade-button type="link" confirm  href="{{ url('actions?action=gmail&site_id='.$site->id) }}">
                    {{ __('Gmail Login All') }}
                </x-splade-button>
                <x-splade-button type="link" confirm  href="{{ url('actions?action=goal&site_id='.$site->id) }}">
                    {{ __('Set Goal All') }}
                </x-splade-button>
                <x-splade-button type="link" confirm  href="{{ url('actions?action=card&site_id='.$site->id) }}">
                    {{ __('Card List All') }}
                </x-splade-button>
                <x-splade-button type="link" confirm  href="{{ url('actions?action=switch&site_id='.$site->id) }}">
                    {{ __('Switch Ips All') }}
                </x-splade-button>
                <x-splade-button class="col-span-2" danger  type="link" confirm  href="{{ url('actions?action=hold&site_id='.$site->id) }}">
                    {{ __('Hold All') }}
                </x-splade-button>
                <x-splade-button class="col-span-2" type="link" confirm  href="{{ url('actions?action=unhold&site_id='.$site->id) }}">
                    {{ __('Un Hold All') }}
                </x-splade-button>
            </div>
            <div class="my-4 grid grid-cols-2 gap-4 mb-4 filament-widgets-container lg:grid-cols-3">
                <x-splade-button secondary type="link" confirm  href="{{ url('actions?action=cookiesAll&site_id='.$site->id) }}">
                    {{ __('Clear Cookies All') }}
                </x-splade-button>
                <x-splade-button secondary type="link" confirm  href="{{ url('actions?action=cookiesAll&site_id='.$site->id) }}">
                    {{ __('Clear Cookies & Check Blocked') }}
                </x-splade-button>
                <x-splade-button secondary type="link" confirm  href="{{ url('actions?action=clear-mobile&site_id='.$site->id) }}">
                    {{ __('Clear Mobile All') }}
                </x-splade-button>
                <x-splade-button secondary type="link" confirm  href="{{ url('actions?action=clear-suspended&site_id='.$site->id) }}">
                    {{ __('Clear Suspended All') }}
                </x-splade-button>
                <x-splade-button secondary type="link" confirm  href="{{ url('actions?action=clear-password&site_id='.$site->id) }}">
                    {{ __('Clear Password All') }}
                </x-splade-button>
                <x-splade-button secondary type="link" confirm  href="{{ url('actions?action=clear-failed&site_id='.$site->id) }}">
                    {{ __('Clear Failed All') }}
                </x-splade-button>
            </div>
            <div class="my-4 grid grid-cols-2 gap-4 mb-4 filament-widgets-container lg:grid-cols-4">
                @php
                    $projectID = \App\Models\Credentials::where('provider', 'hetzner_cloud')->first()->credentials['hetzner_cloud_project_id'];
                    $vncLink = "https://console.hetzner.cloud/console/".$projectID."/".$server->provider_id;
                @endphp
                <a href="{{$vncLink}}" target="_blank" class="border rounded-md shadow-sm font-bold py-2 px-4 focus:outline-none focus:ring focus:ring-opacity-50 bg-indigo-500 hover:bg-indigo-700 text-white border-transparent focus:border-indigo-300 focus:ring-indigo-200" >
                    {{ __('Open VNC') }}
                </a>
                <x-splade-button danger type="link" confirm  href="{{ url('actions?action=stop&site_id='.$site->id) }}">
                    {{ __('Stop Bot') }}
                </x-splade-button>
                <x-splade-button danger type="link" confirm method="POST" href="{{ route('servers.restart', $server) }}">
                    {{ __('Restart Server') }}
                </x-splade-button>
                <a class="border rounded-md shadow-sm font-bold py-2 px-4 focus:outline-none focus:ring focus:ring-opacity-50 bg-indigo-500 hover:bg-indigo-700 text-white border-transparent focus:border-indigo-300 focus:ring-indigo-200" target="_blank" href="{{ url('actions?action=export&site_id='.$site->id) }}">
                    {{ __('Export All') }}
                </a>
            </div>
        <x-splade-table :for="$accounts">
        <x-slot:empty-state>
            <x-empty-state modal :href="route('servers.sites.accounts.create', ['site'=>$site, 'server'=>$server])" icon="heroicon-o-document-plus">
                {{ __('New Account') }}
            </x-empty-state>
            <x-splade-cell points>
                @if($item->points > 13000)
                    <h1 class="text-lg font-bold text-green-600">{{$item->points }}</h1>
                @else
                    <h1 class="text-lg font-bold text-orange-500">{{$item->points ?: 0 }}</h1>
                @endif
            </x-splade-cell>
            <x-splade-cell status>
                <div class="flex justify-start space-x-4">
                    <div>
                        @if(!$item->blocked)
                            <i title="Blocked" class="bx bx-sm bx-block text-green-600"></i>
                        @else
                            <i title="Blocked" class="bx bx-sm bx-block text-red-500"></i>
                        @endif
                    </div>
                    <div>
                        @if($item->login)
                            <i title="Mobile Login" class="bx bx-sm bx-log-in text-green-600"></i>
                        @else
                            <i title="Mobile Login" class="bx bx-sm bx-log-in text-red-500"></i>
                        @endif
                    </div>
                    <div>
                        @if($item->webLogin)
                            <i title="Web Login" class="bx bx-sm bx-globe text-green-600"></i>
                        @else
                            <i title="Web Login" class="bx bx-sm bx-globe text-red-500"></i>
                        @endif
                    </div>
                    <div>
                        @if(!$item->web)
                            <i title="Web Search" class="bx bx-sm bx-search text-green-600"></i>
                        @else
                            <i title="Web Search" class="bx bx-sm bx-search text-red-600"></i>
                        @endif
                    </div>
                    <div>
                        @if(!$item->mobile)
                            <i title="Mobile Search" class="bx bx-sm bx-phone text-green-600"></i>
                        @else
                            <i title="Mobile Search" class="bx bx-sm bx-phone text-red-600"></i>
                        @endif
                    </div>
                    <div>
                        @if(!$item->links)
                            <i title="Links Clicks" class="bx bx-sm bxs-mouse text-green-600"></i>
                        @else
                            <i title="Links Clicks" class="bx bx-sm bxs-mouse text-red-600"></i>
                        @endif
                    </div>
                    <div>
                        @if(!$item->fail)
                            <i title="Account Fail" class="bx bx-sm bx-check-circle text-green-600"></i>
                        @else
                            <i title="Account Fail" class="bx bx-sm bx-x-circle text-red-600"></i>
                        @endif
                    </div>
                </div>
            </x-splade-cell>
        </x-slot>
    </x-splade-table>
</x-site-layout>
