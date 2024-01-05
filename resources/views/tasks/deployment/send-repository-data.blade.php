cd {!! $repositoryDirectory !!}

GIT_HASH=$(git rev-list {!! $site->repository_branch !!} -1);

<x-eddy-task-callback :url="$callbackUrl()" raw="git_hash=$GIT_HASH" />
