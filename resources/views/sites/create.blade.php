<x-tomato-admin-container :label="__('Add Site')">
    <x-splade-script>
        $splade.generatePassword = function () {
        const charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        return Array.from(crypto.getRandomValues(new Uint32Array(32)))
        .map((x) => charset[x % charset.length])
        .join('')
        };
    </x-splade-script>
    <x-splade-form :action="route('admin.servers.sites.store', $server)" :default="[
                'php_version' => array_keys($phpVersions)[0],
                'zero_downtime_deployment' => true,
                'type' => 'laravel',
                'web_folder' => '/public',
                'repository_branch' => 'main',
                'deploy_key_uuid' => $deployKeyUuid,
                'has_database' => false,
                'has_queue' => false,
                'has_schedule' => false,
                'add_server_ssh_key_to_github' => true,
                'site_template' => false
            ]">
        <div class="space-y-4">
            <x-splade-input v-if="!form.site_template" name="address" :label="__('Hostname')" autofocus>
                <x-slot:prepend>
                    <span class="text-gray-900">https://</span>
                </x-slot:prepend>
            </x-splade-input>

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <x-splade-select
                        choices
                        :label="__('Create From Template')"
                        :placeholder="__('Select Template')"
                        name="site_template"
                        remote-root="data"
                        :remote-url="route('admin.site-templates.api')"
                        option-label="name"
                        option-value="id"
                    />
                </div>
                <x-splade-select v-if="!form.site_template" name="php_version" :label="__('PHP Version')" :options="$phpVersions" />
                <x-splade-select v-if="!form.site_template" name="type" :label="__('Site Type')" :options="$types" />
            </div>


            <div v-if="form.type != 'wordpress' && !form.site_template" class="flex flex-col gap-4">
                <x-splade-input name="web_folder" :label="__('Web Folder')" />
                <x-splade-checkbox name="zero_downtime_deployment" :label="__('Enable Zero Downtime Deployment')" />
                @if($hasCloudflareCredential)
                    <x-splade-checkbox name="add_server_ssh_key_to_github" :label="__('Add Server SSH Key To Github')" />
                @endif
                <x-splade-checkbox name="add_dns_zone_to_cloudflare" :label="__('Add Domain DNS to Cloudflare')" />
                <x-splade-checkbox v-if="form.type === 'laravel'" name="has_queue" :label="__('Install Laravel Queue?')" />
                <x-splade-checkbox v-if="form.type === 'laravel'" name="has_schedule" :label="__('Install Laravel Schedule?')" />
                <x-splade-checkbox name="has_database" :label="__('Create Database?')" />

                <div v-if="form.has_database" class="flex flex-col gap-4">
                    <x-splade-input name="database_name" :label="__('Database Name')"  />
                    <x-splade-input name="database_user" :label="__('Database User')"  />
                    <x-splade-input name="database_password" :label="__('Database Password')">
                        <x-slot:append>
                            <button @click="form.database_password = $splade.generatePassword()" type="button" class="text-gray-500 hover:text-gray-700 focus:outline-none focus:text-gray-700">
                                @svg('heroicon-o-sparkles', 'h-5 w-5')
                            </button>
                        </x-slot:append>
                    </x-splade-input>
                </div>
            </div>
        </div>

        <div v-if="form.type != 'wordpress' && !form.site_template" class="space-y-4">
            <div class="my-8 h-px bg-slate-200" />

            @if($hasGithubCredentials)
                <x-splade-select choices name="repository_url" :label="__('Github Repository')" :placeholder="__('Search By Repo')" :remote-url="route('admin.github.repositories')" />
            @endif

            <x-splade-input name="deploy_key_uuid" type="hidden" />
            <x-splade-input name="repository_url" :label="__('Repository URL')" />
            <x-splade-input name="repository_branch" :label="__('Repository Branch')" />
        </div>

        <x-tomato-admin-submit spinner class="mt-8" :label="__('Deploy Now')" />
    </x-splade-form>

</x-tomato-admin-container>
