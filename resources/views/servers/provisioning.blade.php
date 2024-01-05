@seoTitle($server->name)

<x-splade-event private channel="teams.{{ auth()->user()->currentTeam->id }}" listen="ServerUpdated, ServerDeleted" preserve-scroll />

@php
    $isNew = $server->status === \TomatoPHP\TomatoEddy\Enums\Infrastructure\ServerStatus::New;
    $isStarting = $server->status === \TomatoPHP\TomatoEddy\Enums\Infrastructure\ServerStatus::Starting;
    $isProvisioning = $server->status === \TomatoPHP\TomatoEddy\Enums\Infrastructure\ServerStatus::Provisioning;
@endphp

@if($server->provider === \TomatoPHP\TomatoEddy\Enums\Services\Provider::CustomServer && !$isProvisioning)
    <x-splade-modal opened name="provision-script">
        <x-slot:title>
            {{ __('Provisioning Script') }}
        </x-slot:title>

        <p>{{ __('Run this script as root on your server to start the provisioning process:') }}</p>

        <div class="mt-4 pr-8 break-all font-mono text-sm relative">
            <div>
                {{ $server->provisionCommand() }}
            </div>
            <div class="flex flex-col justify-center items-center">
                <x-tomato-admin-copy class="top-0 right-0 w-5 h-5 absolute" :text="$server->provisionCommand()">
                    <x-tomato-admin-tooltip :text="__('Copy')">
                        <i class="bx bx-copy"></i>
                    </x-tomato-admin-tooltip>
                </x-tomato-admin-copy>
            </div>
        </div>
    </x-splade-modal>
@endif

<x-tomato-admin-container :label="$server->name">
    <div>
        @if($server->status === \TomatoPHP\TomatoEddy\Enums\Infrastructure\ServerStatus::Provisioning)
            {{ __('The server is currently being provisioned.') }}
        @elseif($server->status === \TomatoPHP\TomatoEddy\Enums\Infrastructure\ServerStatus::Starting)
            {{ __('The server is created at the provider and is currently starting up.') }}
        @else
            {{ __('The server is currently being created at the provider.') }}
        @endif

        {{ __('This page will automatically refresh on updates.') }}

        <x-splade-form
            confirm-danger
            method="DELETE"
            :action="route('admin.servers.destroy', $server)"
            :confirm-text="__('Deleting a server will remove all settings. We will delete it for you, but you might have to manually remove it from your provider.')"
            class="mt-4">
            <x-splade-submit danger :label="__('Delete Server')" />
        </x-splade-form>

        @if($server->provider === \TomatoPHP\TomatoEddy\Enums\Services\Provider::CustomServer && !$isProvisioning)
            <p class="mt-4">{{ __('Need to see the provisioning script again?') }}</p>

            <x-splade-button type="link" secondary href="#provision-script" class="mt-4 inline-flex">
                {{ __('View Provisioning Script') }}
            </x-splade-button>
        @endif
    </div>

    <div>
        <ol role="list" class="space-y-6">
            <x-dynamic-component
                :component="$isNew ? 'tomato-eddy::step.current' : 'tomato-eddy::step.complete'">
                {{ __('Create the server at the provider') }}
            </x-dynamic-component>

            <x-dynamic-component
                :component="$isStarting ? 'tomato-eddy::step.current' : ($isNew ? 'tomato-eddy::step.upcoming' : 'tomato-eddy::step.complete')">
                {{ __('Wait for the server to start up') }}
            </x-dynamic-component>

            @php
                $lastStepWasCompleted = $isProvisioning;
                $completedSteps = $server->completed_provision_steps->toArray();
            @endphp

            @foreach(\TomatoPHP\TomatoEddy\Enums\Server\ProvisionStep::forFreshServer() as $step)
                @php
                    $completed = in_array($step->value, $completedSteps);
                    $current = !$completed && $lastStepWasCompleted;
                    $lastStepWasCompleted = $completed;
                @endphp

                <x-dynamic-component
                    :component="$completed ? 'tomato-eddy::step.complete' : ($current ? 'tomato-eddy::step.current' : 'tomato-eddy::step.upcoming')">
                    {{ $step->getDescription() }}
                </x-dynamic-component>
            @endforeach

            @php
                $installedSoftware = $server->installed_software->toArray();
            @endphp

            @foreach(\TomatoPHP\TomatoEddy\Enums\Server\Software::defaultStack() as $software)
                @php
                    $completed = in_array($software->value, $installedSoftware);
                    $current = !$completed && $lastStepWasCompleted;
                    $lastStepWasCompleted = $completed;
                @endphp

                <x-dynamic-component
                    :component="$completed ? 'tomato-eddy::step.complete' : ($current ? 'tomato-eddy::step.current' : 'tomato-eddy::step.upcoming')">
                    {{ __('Install :software', ['software' => $software->getDisplayName()]) }}
                </x-dynamic-component>
            @endforeach
        </ol>
    </div>
</x-tomato-admin-container>
