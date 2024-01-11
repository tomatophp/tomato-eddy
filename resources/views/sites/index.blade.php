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
        <x-splade-cell latestDeployment.updated_at>
            <x-tomato-admin-row table type="datetime" :value="$item->latestDeployment?->updated_at" />
        </x-splade-cell>
    </x-splade-table>
@endsection
