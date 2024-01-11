@extends('tomato-eddy::servers.layout')

@section('title', __('Crons'))

@section('description')
    {{ __("Manage the crons on server ':server'.", ['server' => $server->name]) }}
@endsection

@section('buttons')
    <x-tomato-admin-button type="link" modal href="{{ route('admin.servers.crons.create', $server) }}">
        {{ __('Add Cron') }}
    </x-tomato-admin-button>
@endsection

@section('content')
    <x-splade-event private channel="teams.{{ auth()->user()->currentTeam->id }}" listen="CronUpdated, CronDeleted" />
    <x-splade-table :for="$crons">
        <x-splade-cell status>
            @include('tomato-eddy::servers.install-status', ['item' => $item])
        </x-splade-cell>
    </x-splade-table>
@endsection
