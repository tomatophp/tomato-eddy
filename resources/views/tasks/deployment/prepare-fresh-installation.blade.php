cd {!! $site->path !!}

{{-- For example: tasks/deployment/prepare-fresh-installation/laravel.blade.php --}}
@includeIf('tomato-eddy::tasks.deployment.prepare-fresh-installation.'.$site->type->value, ['status' => 'complete'])

cd {!! $site->path !!}
