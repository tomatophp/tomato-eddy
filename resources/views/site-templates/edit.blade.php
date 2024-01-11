<x-tomato-admin-container label="{{trans('tomato-admin::global.crud.edit')}} {{__('Template')}} #{{$model->id}}">
    <x-splade-script>
        $splade.generatePassword = function () {
        const charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        return Array.from(crypto.getRandomValues(new Uint32Array(32)))
        .map((x) => charset[x % charset.length])
        .join('')
        };
    </x-splade-script>
    <x-splade-form class="flex flex-col space-y-4" action="{{route('admin.site-templates.update', $model->id)}}" method="post" :default="$model">
        <x-splade-input :label="__('Name')" name="name" type="text"  :placeholder="__('Name')" />
        <div class="grid grid-cols-2 gap-4">
            <x-splade-select name="php_version" :label="__('PHP Version')" :options="$phpVersions" />
            <x-splade-select name="type" :label="__('Site Type')" :options="$types" />
        </div>
        <x-splade-checkbox :label="__('Zero downtime deployment')" name="zero_downtime_deployment" label="Zero downtime deployment" />

        <div v-if="form.type != 'wordpress'" class="flex flex-col gap-4">
            @if($hasGithubCredentials)
                <x-splade-select choices name="repository_url" :label="__('Github Repository')" :placeholder="__('Search By Repo')" :remote-url="route('admin.github.repositories')" />
            @endif
            <x-splade-input :label="__('Repository url')" name="repository_url" type="text"  :placeholder="__('Repository url')" />
            <x-splade-input :label="__('Repository branch')" name="repository_branch" type="text"  :placeholder="__('Repository branch')" />

        </div>

        <x-splade-checkbox :label="__('Add server ssh key to github')" name="add_server_ssh_key_to_github" label="Add server ssh key to github" />
        <x-splade-checkbox :label="__('Add dns zone to cloudflare')" name="add_dns_zone_to_cloudflare" label="Add dns zone to cloudflare" />
        <x-splade-checkbox :label="__('Has queue')" name="has_queue" label="Has queue" />
        <x-splade-checkbox :label="__('Has schedule')" name="has_schedule" label="Has schedule" />
        <x-splade-checkbox :label="__('Has database')" name="has_database" label="Has database" />
        <div v-if="form.has_database" class="flex flex-col gap-4">
            <x-splade-input :label="__('Database name')" name="database_name" type="text"  :placeholder="__('Database name')" />
            <x-splade-input :label="__('Database user')" name="database_user" type="text"  :placeholder="__('Database user')" />
            <x-splade-input name="database_password" :label="__('Database Password')">
                <x-slot:append>
                    <button @click="form.database_password = $splade.generatePassword()" type="button" class="text-gray-500 hover:text-gray-700 focus:outline-none focus:text-gray-700">
                        @svg('heroicon-o-sparkles', 'h-5 w-5')
                    </button>
                </x-slot:append>
            </x-splade-input>
        </div>

        <div class="my-4 border-b"></div>
        <div class="mb-4">
            <h1>{{__('Deployment Scripts')}}</h1>
        </div>
        <x-tomato-admin-code :label="__('Hook before updating repository')" name="hook_before_updating_repository" :placeholder="__('Hook before updating repository')" />
        <x-tomato-admin-code :label="__('Hook after updating repository')" name="hook_after_updating_repository" :placeholder="__('Hook after updating repository')" />
        <x-tomato-admin-code :label="__('Hook before making current')" name="hook_before_making_current" :placeholder="__('Hook before making current')" />
        <x-tomato-admin-code :label="__('Hook after making current')" name="hook_after_making_current" :placeholder="__('Hook after making current')" />


        <div class="flex justify-start gap-2 pt-3">
            <x-tomato-admin-submit  label="{{__('Save')}}" :spinner="true" />
            <x-tomato-admin-button danger :href="route('admin.site-templates.destroy', $model->id)"
                                   confirm="{{trans('tomato-admin::global.crud.delete-confirm')}}"
                                   confirm-text="{{trans('tomato-admin::global.crud.delete-confirm-text')}}"
                                   confirm-button="{{trans('tomato-admin::global.crud.delete-confirm-button')}}"
                                   cancel-button="{{trans('tomato-admin::global.crud.delete-confirm-cancel-button')}}"
                                   method="delete"  label="{{__('Delete')}}" />
            <x-tomato-admin-button secondary :href="route('admin.site-templates.index')" label="{{__('Cancel')}}"/>
        </div>
    </x-splade-form>
</x-tomato-admin-container>
