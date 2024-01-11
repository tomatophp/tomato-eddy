@extends('tomato-eddy::servers.layout')

@section('title', __('Daemons'))

@section('description')
    {{ __("Manage the daemons on server ':server'.", ['server' => $server->name]) }}
@endsection

@section('buttons')
    <div class="flex flex-end gap-4">
        <x-tomato-admin-button type="link" modal href="{{ route('admin.servers.daemons.create', $server) }}">
            {{ __('Add Daemon') }}
        </x-tomato-admin-button>
    </div>
@endsection

@section('content')
    <x-splade-event private channel="teams.{{ auth()->user()->currentTeam->id }}" listen="DaemonUpdated, DaemonDeleted" />

    <x-splade-table :for="$daemons">
        <x-splade-cell status>
            @include('tomato-eddy::servers.install-status', ['item' => $item])
        </x-splade-cell>
    </x-splade-table>
@endsection
