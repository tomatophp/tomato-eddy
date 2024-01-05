@extends('tomato-eddy::servers.layout')

@section('title', __('Server Overview'))

@section('content')
    <x-tomato-admin-row inline :label="__('Name')" :value="$server->name" />
    <x-tomato-admin-row inline type="copy" :label="__('IP Address')" :value="$server->public_ipv4" />
    <x-tomato-admin-row inline :label="__('Provider')" :value="$server->provider->getDisplayName()" />
    <x-tomato-admin-row inline type="password" :label="__('Sudo Password')" :value="$server->password" />
    <x-tomato-admin-row inline type="password" :label="__('Database Password')" :value="$server->database_password" />
    <x-tomato-admin-row inline type="password" :label="__('SSH Key')" :value="$server->user_public_key" />
    <x-tomato-admin-row inline :label="__('Server Storage')" :value="$server->storage_name" />
@endsection
