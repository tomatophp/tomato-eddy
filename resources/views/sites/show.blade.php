@extends('tomato-eddy::sites.layout')

@section('title', __('Site Overview'))

@section('content')
    <x-splade-event private channel="teams.{{ auth()->user()->currentTeam->id }}" listen="SiteUpdated" preserve-scroll />

    <x-tomato-admin-row inline :label="__('Address')" type="copy" :value="$site->url"/>
    <x-tomato-admin-row inline :label="__('Server')" type="copy" :value="$server->name"/>
    <x-tomato-admin-row inline :label="__('Path')" type="copy" :value="$site->path"/>
    <x-tomato-admin-row inline :label="__('PHP Version')" :value="$site->php_version->getDisplayName()"/>
    <x-tomato-admin-row inline :label="__('Type')"  :value="$site->type->getDisplayName()"/>
    <x-tomato-admin-row inline :label="__('SSL')"  :value="$site->tls_setting->getDisplayName()"/>
    <x-tomato-admin-row inline :label="__('Repository')"  type="copy" :value="$site->repository_url"/>
    <x-tomato-admin-row inline :label="__('Repository Branch')"   :value="$site->repository_branch"/>

    <div class="my-4 border-b border-gray-200 py-4 font-bold text-lg">
        {{ __('Deployment') }}
    </div>

    <x-tomato-admin-row inline :label="__('Zero Downtime Deployment')" :value="$site->zero_downtime_deployment ? __('Yes') : __('No')"/>
    @if($site->latestDeployment)
        <x-tomato-admin-row inline :label="__('Latest Deployment')" :href="route('admin.servers.sites.deployments.show', [$server, $site, $site->latestDeployment])" :value="$site->latestDeployment->updated_at"/>
    @endif
    <x-tomato-admin-row inline :label="__('Deploy URL')" type="copy" :value="route('site.deployWithToken', [$site, $site->deploy_token])"/>

    <div class="flex justify-start gap-4">
        <x-tomato-admin-button danger confirm-danger :label="__('Delete Site')" method="DELETE" :href="route('admin.servers.sites.destroy', [$server, $site])" />
        <x-tomato-admin-button
            confirm="{{ __('Are you sure you want to regenerate the deploy token?') }}"
            confirm-text="{{ __('This will invalidate the current deploy token.') }}"
            :label="__('Refresh Deploy Token')"
            method="POST"
            :href="route('admin.servers.sites.refresh-deploy-token', [$server, $site])"
        />
    </div>
@endsection


