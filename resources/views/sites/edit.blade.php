@extends('tomato-eddy::sites.layout')

@section('title', __('Site Settings'))

@section('content')
    @if($site->pending_caddyfile_update_since)
        {{ __('The Caddyfile for this site is currently being updated. This may take a few minutes.') }}
    @else
        <x-splade-form
            method="PATCH"
            :action="route('admin.servers.sites.update', [$server, $site])"
            :default="$site"
            class="space-y-4"
            :confirm="__('Are you sure you want to update the site?')"
            :confirm-text="__('After updating the site, it will be redeployed.')"
        >
            <div class="grid grid-cols-2 gap-4">
                <x-splade-select name="php_version" :label="__('PHP Version')" :options="$phpVersions" />
                <x-splade-input name="web_folder" :label="__('Web Folder')" />
            </div>

            @if($site->repository_url)
                <x-splade-input name="repository_url" :label="__('Repository URL')" />
                <x-splade-input name="repository_branch" :label="__('Repository Branch')" />
            @endif

            <x-tomato-admin-submit spinner :label="__('Save')" />
        </x-splade-form>
    @endif
@endsection
