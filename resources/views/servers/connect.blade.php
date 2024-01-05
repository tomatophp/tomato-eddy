<x-tomato-admin-container :label="__('Connect Server')">
    <x-splade-form :action="route('admin.servers.connect')" class="space-y-4" :default="[
        'name' => '',
        'credentials_id' => $defaultCredentials,
        'custom_server' => $credentials->isEmpty(),
        'ssh_keys' => [],
        'type' => 'cpx31',
        'region' => '5',
        'add_key_to_github' => true
    ]">
        <x-splade-input name="name" :label="__('Name')" />
        
        @if($credentials->isNotEmpty())
            <x-splade-select v-if="!form.custom_server" name="credentials_id" :label="__('Provider')" :options="$credentials" />
        @endif

        <x-splade-input name="public_ipv4" :label="__('Public IPv4')" />

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
