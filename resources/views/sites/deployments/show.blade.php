@extends('tomato-eddy::sites.layout')

@section('title', __('Deployment at :at', ['at' => $deployment->created_at]))

@section('buttons')
    <x-splade-button type="link" :href="route('admin.servers.sites.deployments.index', [$server, $site])" class="flex items-center justify-center">
        @svg('heroicon-s-arrow-left-circle', 'h-5 w-5 -ml-1 mr-2')
        <span class="text-center">{{ __('All Deployments') }}</span>
    </x-splade-button>
@endsection

@section('content')


<x-splade-event private channel="teams.{{ auth()->user()->currentTeam->id }}" listen="DeploymentUpdated" preserve-scroll />

<div class="flex flex-row items-center">
    {{ __('Output Log') }}

    @if($deployment->status == \TomatoPHP\TomatoEddy\Enums\Models\DeploymentStatus::Pending)
        @svg('heroicon-s-cog-6-tooth', 'h-5 w-5 text-gray-400 ml-2 animate-spin')
    @endif
</div>

<div class="overflow-x-auto max-w-full">
    <pre class="bg-gray-900 text-white p-4 rounded-lg my-4">
        {{ $deployment->task?->output ?: '...' }}
    </pre>
</div>

@endsection
