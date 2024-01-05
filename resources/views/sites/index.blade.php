@extends('tomato-eddy::servers.layout')

@section('title', __('Sites'))

@section('description', __("Manage the sites on server ':server'.", ['server' => $server->name]))

@section('buttons')
    <x-tomato-admin-button type="link" modal href="{{ route('admin.servers.sites.create', $server) }}">
        {{ __('New Site') }}
    </x-tomato-admin-button>
@endsection

@section('content')
    <x-splade-table :for="$sites">

    </x-splade-table>
@endsection
{{--<x-server-layout :$server>--}}
{{--    <x-slot:title>--}}
{{--        {{ __('Sites') }}--}}
{{--    </x-slot>--}}

{{--    <x-slot:description>--}}
{{--        {{ __("Manage the sites on server ':server'.", ['server' => $server->name]) }}--}}
{{--    </x-slot>--}}

{{--    <x-slot:actions>--}}
{{--        <x-splade-button type="link" modal href="{{ route('servers.sites.create', $server) }}">--}}
{{--            {{ __('New Site') }}--}}
{{--        </x-splade-button>--}}
{{--    </x-slot>--}}

{{--    @if($sites->isNotEmpty())--}}
{{--        <x-slot:actions>--}}
{{--            <x-splade-button type="link" modal href="{{ route('servers.sites.create', $server) }}">--}}
{{--                {{ __('New Site') }}--}}
{{--            </x-splade-button>--}}
{{--        </x-slot>--}}
{{--    @endif--}}


{{--</x-server-layout>--}}
