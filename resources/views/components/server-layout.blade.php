<x-tomato-admin-layout>
    <x-slot:header>
        <div class="flex justify-between">
            <div>
                {{ $server->name }} @isset($title) - {{ $title }} @endisset
            </div>
        </div>
    </x-slot:header>

    <x-slot:description>
        <div class="flex flex-row items-center space-x-2">
            <span>{{ $server->provider_name }}</span>

            <div class='w-1 h-1 bg-gray-400 rounded-full' />

            <p class="flex flex-row items-center">
                <span>{{ $server->public_ipv4 }}</span>
{{--                <x-clipboard class="ml-1 w-4 h-4">{{ $server->public_ipv4 }}</x-clipboard>--}}
            </p>
        </div>
    </x-slot:description>

    @isset($actions)
        <x-slot:actions> {{ $actions }} </x-slot:actions>
    @endisset

    {{ $slot }}
</x-tomato-admin-layout>
