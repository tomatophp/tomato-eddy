@extends('tomato-eddy::servers.layout')

@section('title', __('Databases'))

@section('description')
    {{ __("Manage the databases on server ':server'.", ['server' => $server->name]) }}
@endsection

@section('buttons')
    <div class="flex flex-end gap-4">
        <x-tomato-admin-button type="link" modal href="{{ route('admin.servers.databases.create', $server) }}">
            {{ __('Add Database') }}
        </x-tomato-admin-button>

        <x-tomato-admin-button type="link" modal href="{{ route('admin.servers.database-users.create', $server) }}">
            {{ __('Add User') }}
        </x-tomato-admin-button>
    </div>
@endsection

@section('content')
    <x-splade-event private channel="teams.{{ auth()->user()->currentTeam->id }}" listen="DatabaseUpdated, DatabaseDeleted, DatabaseUserUpdated, DatabaseUserDeleted" />

    <div dusk="databases">
        <x-splade-table dusk="databases" :for="$databases">
            <x-splade-cell status>
                @include('tomato-eddy::servers.install-status', ['item' => $item])
            </x-splade-cell>
        </x-splade-table>
    </div>

    @if($users->for()->count())
        <div dusk="users">
            <h1 class="text-base font-semibold leading-6 text-gray-900 ml-4 sm:ml-0 mt-8">{{ __('Users') }}</h1>

            <x-splade-table :for="$users" class="mt-4">
                <x-splade-cell status>
                    @include('tomato-eddy::servers.install-status', ['item' => $item])
                </x-splade-cell>
            </x-splade-table>
        </div>
    @endif
@endsection
