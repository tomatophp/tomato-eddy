<x-tomato-admin-container label="{{trans('tomato-admin::global.crud.view')}} {{__('Template')}} #{{$model->id}}">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <x-tomato-admin-row :label="__('Id')" :value="$model->id" type="string" />

          <x-tomato-admin-row :label="__('Name')" :value="$model->name" type="string" />

          <x-tomato-admin-row :label="__('Type')" :value="$model->type" type="string" />

          <x-tomato-admin-row :label="__('Zero downtime deployment')" :value="$model->zero_downtime_deployment" type="bool" />

          <x-tomato-admin-row :label="__('Repository url')" :value="$model->repository_url" type="string" />

          <x-tomato-admin-row :label="__('Repository branch')" :value="$model->repository_branch" type="string" />

          <x-tomato-admin-row :label="__('Web folder')" :value="$model->web_folder" type="string" />

          <x-tomato-admin-row :label="__('Php version')" :value="$model->php_version" type="string" />

          <x-tomato-admin-row :label="__('Hook before updating repository')" :value="$model->hook_before_updating_repository" type="rich" />

          <x-tomato-admin-row :label="__('Hook after updating repository')" :value="$model->hook_after_updating_repository" type="rich" />

          <x-tomato-admin-row :label="__('Hook before making current')" :value="$model->hook_before_making_current" type="rich" />

          <x-tomato-admin-row :label="__('Hook after making current')" :value="$model->hook_after_making_current" type="rich" />

          <x-tomato-admin-row :label="__('Add server ssh key to github')" :value="$model->add_server_ssh_key_to_github" type="bool" />

          <x-tomato-admin-row :label="__('Add dns zone to cloudflare')" :value="$model->add_dns_zone_to_cloudflare" type="bool" />

          <x-tomato-admin-row :label="__('Has queue')" :value="$model->has_queue" type="bool" />

        <x-tomato-admin-row :label="__('queue command')" :value="$model->queue_command" type="bool" />

          <x-tomato-admin-row :label="__('Has schedule')" :value="$model->has_schedule" type="bool" />

        <x-tomato-admin-row :label="__('schedule command')" :value="$model->schedule_command" type="bool" />

          <x-tomato-admin-row :label="__('Has database')" :value="$model->has_database" type="bool" />

          <x-tomato-admin-row :label="__('Database name')" :value="$model->database_name" type="string" />

          <x-tomato-admin-row :label="__('Database user')" :value="$model->database_user" type="string" />

          <x-tomato-admin-row :label="__('Database password')" :value="$model->database_password" type="password" />


    </div>
    <div class="flex justify-start gap-2 pt-3">
        <x-tomato-admin-button warning label="{{__('Edit')}}" :href="route('admin.site-templates.edit', $model->id)"/>
        <x-tomato-admin-button danger :href="route('admin.site-templates.destroy', $model->id)"
                               confirm="{{trans('tomato-admin::global.crud.delete-confirm')}}"
                               confirm-text="{{trans('tomato-admin::global.crud.delete-confirm-text')}}"
                               confirm-button="{{trans('tomato-admin::global.crud.delete-confirm-button')}}"
                               cancel-button="{{trans('tomato-admin::global.crud.delete-confirm-cancel-button')}}"
                               method="delete"  label="{{__('Delete')}}" />
        <x-tomato-admin-button secondary :href="route('admin.site-templates.index')" label="{{__('Cancel')}}"/>
    </div>
</x-tomato-admin-container>
