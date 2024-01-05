@include('tomato-eddy::servers.menu-item', [
    'href' => route('admin.servers.show', $server->id),
    'icon' => 'bx bx-server',
    'label' => __('Overview'),
])

@include('tomato-eddy::servers.menu-item', [
    'href' => route('admin.servers.sites.index', $server->id),
    'icon' => 'bx bx-globe',
    'label' => __('Sites'),
])

@include('tomato-eddy::servers.menu-item', [
    'href' => route('admin.servers.databases.index', $server->id),
    'icon' => 'bx bx-data',
    'label' => __('Databases'),
])

@include('tomato-eddy::servers.menu-item', [
    'href' => route('admin.servers.crons.index', $server->id),
    'icon' => 'bx bx-time',
    'label' => __('Cronjobs'),
])

@include('tomato-eddy::servers.menu-item', [
    'href' => route('admin.servers.daemons.index', $server->id),
    'icon' => 'bx bx-refresh',
    'label' => __('Daemons'),
])

@include('tomato-eddy::servers.menu-item', [
    'href' => route('admin.servers.firewall-rules.index', $server->id),
    'icon' => 'bx bx-shield',
    'label' => __('Firewall Rules'),
])

@include('tomato-eddy::servers.menu-item', [
    'href' => route('admin.servers.software.index', $server->id),
    'icon' => 'bx bx-code',
    'label' => __('Software'),
])

@include('tomato-eddy::servers.menu-item', [
    'href' => route('admin.servers.files.index', $server->id),
    'icon' => 'bx bx-file',
    'label' => __('Files'),
])

@include('tomato-eddy::servers.menu-item', [
    'href' => route('admin.servers.logs.index', $server->id),
    'icon' => 'bx bx-history',
    'label' => __('Logs'),
])
