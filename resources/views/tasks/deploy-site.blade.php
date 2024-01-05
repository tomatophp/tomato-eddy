<x-eddy-task-shell-defaults />

@include('tomato-eddy::tasks.deployment.shell-variables')

# Create the necessary directories
mkdir -p {!! $repositoryDirectory !!}
mkdir -p {!! $logsDirectory !!}

@if($site->installed_at && $site->hook_before_updating_repository)
    echo "Running hook before updating repository"
    cd {!! $repositoryDirectory !!}
    {!! $site->hook_before_updating_repository !!}
@endif

@if($site->repository_url)
    @include('tomato-eddy::tasks.deployment.update-repository')

    @unless($site->installed_at)
        @include('tomato-eddy::tasks.deployment.prepare-fresh-installation')
    @endunless

    @if($site->hook_after_updating_repository)
        echo "Running hook after updating repository"
        cd {!! $repositoryDirectory !!}
        {!! $site->hook_after_updating_repository !!}
    @endif

    @include('tomato-eddy::tasks.deployment.send-repository-data')
@endif

@if($site->installed_at && $site->type === \TomatoPHP\TomatoEddy\Enums\Models\SiteType::Wordpress)
    echo "Wordpress already installed!"
@endif

echo "Done!"
