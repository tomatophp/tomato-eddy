<x-eddy-task-shell-defaults />

@include('tomato-eddy::tasks.common-functions')

@include('tomato-eddy::tasks.apt-functions')

@foreach($provisionSteps() as $step)
    @include($step->getViewName())

    <x-eddy-task-callback :url="$callbackUrl()" :data="['provision_step_completed' => $step]" />
@endforeach

@foreach($softwareStack() as $software)
    @include('tomato-eddy::'.$software->getInstallationViewName())

    <x-eddy-task-callback :url="$callbackUrl()" :data="['software_installed' => $software]" />
@endforeach

# See 'apt-update-upgrade'
waitForAptUnlock
apt-mark unhold cloud-init
