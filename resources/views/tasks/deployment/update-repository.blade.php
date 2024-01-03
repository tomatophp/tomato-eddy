# Check if the repository exists and if the remote URL is correct, if not, delete it
if [ -f "{!! $repositoryDirectory !!}/HEAD" ]; then
    cd {!! $repositoryDirectory !!}
    CURRENT_REMOTE_URL=$(git config --get remote.origin.url || echo '');

    if [ "$CURRENT_REMOTE_URL" != '{!! $site->repository_url !!}' ]; then
        @if($site->zero_downtime_deployment)
            rm -rf {!! $repositoryDirectory !!}
            cd {!! $site->path !!}
            mkdir -p {!! $repositoryDirectory !!}
        @else
            git remote set-url origin {!! $site->repository_url !!}
        @endif

    fi
fi

cd {!! $site->path !!}

# Clone the repository if it doesn't exist
@if($site->zero_downtime_deployment)
    if [ ! -f "{!! $repositoryDirectory !!}/HEAD" ]; then
        git clone --mirror {!! $site->repository_url !!} {!! $repositoryDirectory !!}
    fi
@else
    if [ ! -f "{!! $repositoryDirectory !!}/.git/HEAD" ]; then
        git clone {!! $site->repository_url !!} {!! $repositoryDirectory !!}
    fi
@endif

# Fetch the latest changes from the repository
cd {!! $repositoryDirectory !!}

@if($site->zero_downtime_deployment)
    git remote update
@else
    git reset --hard HEAD
    git pull origin {!! $site->repository_branch !!}
    cd {!! $repositoryDirectory !!}/playwright
    npm install -D @playwright/test@latest
    npx playwright install
    npm i
@endif

@if($site->zero_downtime_deployment)
    # Clone the repository into the release directory
    cd {!! $releaseDirectory !!}
    git clone -l {!! $repositoryDirectory !!} .
    git checkout --force {!! $site->repository_branch !!}
@endif

cd {!! $site->path !!}
