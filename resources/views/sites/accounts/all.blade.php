@seoTitle(__('Accounts'))

<x-app-layout>
    <x-slot:header>
        {{ __('Accounts') }}
        </x-slot>

        <x-slot:description>
            {{ __('Manage your accounts.') }}
            </x-slot>

            <x-splade-rehydrate poll="60000">
                <div class="my-4 grid grid-cols-1 gap-4 mb-4 filament-widgets-container lg:grid-cols-3">
                    <x-widget label="Accounts" :counter="$counters['total']" />
                    <x-widget label="Web Login" :counter="$counters['web']" />
                    <x-widget label="Mobile Login" :counter="$counters['mobile']" />
                    <x-widget label="Not Login" :counter="$counters['not']" />
                    <x-widget label="Fails Account" :counter="$counters['fail']" />
                    <x-widget label="Blocked Account" :counter="$counters['blocked']" />
                    <x-widget label="Points" :counter="$counters['points']" />
                    <x-widget label="On Queue" :counter="\App\Models\Job::count()" />
                    <x-widget label="Failed Jobs" :counter="\App\Models\FailedJob::count()" />
                    <x-widget label="Running Jobs" :counter="\App\Models\Job::where('reserved_at', '!=' , null)->get()->count()" />
                    <x-widget label="Money" :counter="'$'.number_format((int)$counters['money'])" />
                    <x-widget label="Cards" :counter="number_format($counters['cards'])" />
                    <x-widget label="Suspended" :counter="$counters['suspended']" style="col-span-3" />
                </div>
                <div class="my-4 grid grid-cols-2 gap-4 mb-4 filament-widgets-container lg:grid-cols-3">
                    <x-splade-button type="link" confirm href="{{ url('actions?action=runAll') }}">
                        {{ __('Run All') }}
                    </x-splade-button>
                    <x-splade-button type="link" confirm href="{{ url('actions?action=runAllSearch') }}">
                        {{ __('Search All') }}
                    </x-splade-button>
                    <x-splade-button type="link" confirm href="{{ url('actions?action=runAllLinks') }}">
                        {{ __('Links All') }}
                    </x-splade-button>
                </div>
                <div class="my-4 grid grid-cols-2 gap-4 mb-4 filament-widgets-container lg:grid-cols-4">
                    <x-splade-button type="link" confirm href="{{ url('actions?action=redeem') }}">
                        {{ __('Redeem All') }}
                    </x-splade-button>
                    <x-splade-button type="link" confirm  href="{{ url('actions?action=checkAll') }}">
                        {{ __('Check All') }}
                    </x-splade-button>
                    <x-splade-button type="link" confirm  href="{{ url('actions?action=checkAllWeb') }}">
                        {{ __('Check Web All') }}
                    </x-splade-button>
                    <x-splade-button type="link" confirm  href="{{ url('actions?action=checkAllMobile') }}">
                        {{ __('Check Mobile All') }}
                    </x-splade-button>
                    <x-splade-button type="link" confirm  href="{{ url('actions?action=gmail') }}">
                        {{ __('Gmail Login All') }}
                    </x-splade-button>
                    <x-splade-button type="link" confirm  href="{{ url('actions?action=goal') }}">
                        {{ __('Set Goal All') }}
                    </x-splade-button>
                    <x-splade-button type="link" confirm  href="{{ url('actions?action=card') }}">
                        {{ __('Card List All') }}
                    </x-splade-button>
                    <x-splade-button type="link" confirm  href="{{ url('actions?action=switch') }}">
                        {{ __('Switch Ips All') }}
                    </x-splade-button>
                    <x-splade-button class="col-span-2" danger  type="link" confirm  href="{{ url('actions?action=hold') }}">
                        {{ __('Hold All') }}
                    </x-splade-button>
                    <x-splade-button class="col-span-2" type="link" confirm  href="{{ url('actions?action=unhold') }}">
                        {{ __('Un Hold All') }}
                    </x-splade-button>
                </div>
                <div class="my-4 grid grid-cols-2 gap-4 mb-4 filament-widgets-container lg:grid-cols-3">
                    <x-splade-button secondary type="link" confirm  href="{{ url('actions?action=cookiesAll') }}">
                        {{ __('Clear Cookies All') }}
                    </x-splade-button>
                    <x-splade-button secondary type="link" confirm  href="{{ url('actions?action=cookiesAll') }}">
                        {{ __('Clear Cookies & Check Blocked') }}
                    </x-splade-button>
                    <x-splade-button secondary type="link" confirm  href="{{ url('actions?action=clear-mobile') }}">
                        {{ __('Clear Mobile All') }}
                    </x-splade-button>
                    <x-splade-button secondary type="link" confirm  href="{{ url('actions?action=clear-suspended') }}">
                        {{ __('Clear Suspended All') }}
                    </x-splade-button>
                    <x-splade-button secondary type="link" confirm  href="{{ url('actions?action=clear-password') }}">
                        {{ __('Clear Password All') }}
                    </x-splade-button>
                    <x-splade-button secondary type="link" confirm  href="{{ url('actions?action=clear-failed') }}">
                        {{ __('Clear Failed All') }}
                    </x-splade-button>
                </div>
                <div class="my-4 grid grid-cols-2 gap-4 mb-4 filament-widgets-container lg:grid-cols-4">
                    <button class="border rounded-md shadow-sm font-bold py-2 px-4 focus:outline-none focus:ring focus:ring-opacity-50 bg-indigo-500 hover:bg-indigo-700 text-white border-transparent focus:border-indigo-300 focus:ring-indigo-200" onclick="
                    @foreach(\App\Models\Server::all() as $server)
                        @php
                            $projectID = \App\Models\Credentials::where('provider', 'hetzner_cloud')->first()->credentials['hetzner_cloud_project_id'];
                            $vncLink = "https://console.hetzner.cloud/console/".$projectID."/".$server->provider_id;
                        @endphp
                        @if($vncLink)
                            window.open('{{$vncLink}}', '_blank');
                        @endif
                    @endforeach

                ">
                        {{ __('Open VNC All') }}
                    </button>
                    <x-splade-button danger type="link" confirm  href="{{ url('actions?action=stop') }}">
                        {{ __('Stop Bot') }}
                    </x-splade-button>
                    <x-splade-button danger type="link" confirm  href="{{ route('servers.restart.all') }}">
                        {{ __('Restart Servers') }}
                    </x-splade-button>
                    <a class="border rounded-md shadow-sm font-bold py-2 px-4 focus:outline-none focus:ring focus:ring-opacity-50 bg-indigo-500 hover:bg-indigo-700 text-white border-transparent focus:border-indigo-300 focus:ring-indigo-200" target="_blank"   href="{{ url('actions?action=export') }}">
                        {{ __('Export All') }}
                    </a>
                </div>

                <div class="my-4">
                    <x-splade-form method="GET" action="{{url()->current()}}" :default="['tag'=> $getTag?->id]" submit-on-change>
                        <x-splade-select choices :placeholder="__('Select Tag To Filter')" :label="__('Filter By Tag')"  name="tag" :options="\App\Models\Tag::all()->toArray()" option-label="name" option-value="id" />
                    </x-splade-form>
                </div>

                <x-splade-table :for="$accounts">
                    <x-splade-cell tag>
                        <div class="flex justify-start">
                            @foreach($item->site->server->tags as $tag)
                                <a href="/accounts?tag={{$tag->id}}" class="mx-2 px-2 py-1 text-xs font-medium leading-4 text-white bg-indigo-500 rounded-full">
                                    {{$tag->name}}
                                </a>
                            @endforeach
                        </div>
                    </x-splade-cell>
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
                </x-splade-table>
            </x-splade-rehydrate>
</x-app-layout>
