@extends('tomato-eddy::servers.layout')

@section('title', __('Firewall Rules'))
@section('description', __('Manage your Firewall Rules.'))

@section('buttons')
    <x-tomato-admin-button type="link" modal href="{{ route('admin.servers.firewall-rules.create', $server) }}">
        {{ __('Add Firewall Rule') }}
    </x-tomato-admin-button>
@endsection

@section('content')
    <x-splade-event private channel="teams.{{ auth()->user()->currentTeam->id }}" listen="FirewallRuleUpdated, FirewallRuleDeleted" />

    <x-splade-table :for="$firewallRules">
        <x-splade-cell status>
            @include('tomato-eddy::servers.install-status', ['item' => $item])
        </x-splade-cell>
    </x-splade-table>
@endsection
