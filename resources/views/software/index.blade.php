@extends('tomato-eddy::servers.layout')

@section('title', __('Services'))

@section('content')
    <x-splade-table :for="$software">
        <x-splade-cell actions use="$server">
            <div class="flex space-x-4">
                @if($item['hasUpdateAlternativesTask'])
                    <x-tomato-admin-button :label="__('Make CLI default')" method="POST" :href="route('admin.servers.software.default', [$server, $item['id']])" confirm />
                @endif

                @if($item['hasRestartTask'])
                    <x-tomato-admin-button danger :label="__('Restart')" confirm-danger method="POST" :href="route('admin.servers.software.restart', [$server, $item['id']])" />
                @endif
            </div>
        </x-splade-cell>
    </x-splade-table>
@endsection
