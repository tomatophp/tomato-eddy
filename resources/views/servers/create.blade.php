<x-tomato-admin-container :label="__('New Server')">
    <x-splade-form :action="route('admin.servers.store')" class="space-y-4" :default="[
        'name' => '',
        'credentials_id' => $defaultCredentials,
        'custom_server' => $credentials->isEmpty(),
        'ssh_keys' => [],
        'type' => 'cpx31',
        'region' => '5',
        'add_key_to_github' => true
    ]">
        <x-splade-input name="name" :label="__('Name')" />

        <x-splade-checkbox
            name="custom_server"
            :disabled="$credentials->isEmpty()"
            :label="__('Use a custom server provider')"
            :help="__('Provision a fresh Ubuntu 22.04 server that you have root access to.')"
        />

        <x-splade-checkbox
            name="multi"
            :label="__('Create Multiple Servers')"
            :help="__('You can create multi server with the same configuration')"
        />

        <x-splade-input v-if="form.multi" name="count" type="number" :label="__('Server Count')" />

        @if($credentials->isNotEmpty())
            <x-splade-select v-if="!form.custom_server" name="credentials_id" :label="__('Provider')" :options="$credentials" />
        @endif

        <x-splade-input v-if="form.custom_server" name="public_ipv4" :label="__('Public IPv4')" />

        <div v-if="form.credentials_id && !form.custom_server" class="space-y-4">
            <x-splade-select name="region" :label="__('Region')" remote-url="`/admin/servers/provider/${form.credentials_id}/regions`" />
            <x-splade-select v-if="form.region" name="type" :label="__('Type')" remote-url="`/admin/servers/provider/${form.credentials_id}/types/${form.region}`" />
            <x-splade-select v-if="form.region" name="image" :label="__('Image')" remote-url="`/admin/servers/provider/${form.credentials_id}/images/${form.region}`" />
        </div>

        <x-splade-select
            name="ssh_keys[]"
            multiple
            :label="__('SSH Keys')"
            :options="$sshKeys"
            :help="__('Select the keys that should be added to the server so you can access it via SSH.')"
        />

        @if($hasGithubCredentials)
            <x-splade-checkbox
                name="add_key_to_github"
                :label="__('Add Server\'s SSH Key to Github')"
                :help="__('If you want this server to be able to access your Github repositories, you can add the server\'s SSH key to your Github account.')"
            />
        @endif

        <x-splade-submit />
    </x-splade-form>
</x-tomato-admin-container>
