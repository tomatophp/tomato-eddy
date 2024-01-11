@include('tomato-eddy::servers.menu-item', [
    'href' => route('admin.servers.sites.show', [$server->id, $site->id]),
    'icon' => 'bx bx-globe',
    'label' => __('Overview'),
])

@include('tomato-eddy::servers.menu-item', [
    'href' => route('admin.servers.sites.deployments.index', [$server->id, $site->id]),
    'icon' => 'bx bx-rocket',
    'label' => __('Deployments'),
])

@include('tomato-eddy::servers.menu-item', [
    'href' => route('admin.servers.sites.edit', [$server->id, $site->id]),
    'icon' => 'bx bx-cog',
    'label' => __('Site Settings'),
])

@include('tomato-eddy::servers.menu-item', [
    'href' => route('admin.servers.sites.deployment-settings.edit', [$server->id, $site->id]),
    'icon' => 'bx bxl-github',
    'label' => __('Deployment Settings'),
])

@include('tomato-eddy::servers.menu-item', [
    'href' => route('admin.servers.sites.ssl.edit', [$server->id, $site->id]),
    'icon' => 'bx bx-lock-alt',
    'label' => __('SSL'),
])

@include('tomato-eddy::servers.menu-item', [
    'href' => route('admin.servers.sites.files.index', [$server->id, $site->id]),
    'icon' => 'bx bx-file',
    'label' => __('Files'),
])

@include('tomato-eddy::servers.menu-item', [
    'href' => route('admin.servers.sites.logs.index', [$server->id, $site->id]),
    'icon' => 'bx bx-history',
    'label' => __('Logs'),
])


