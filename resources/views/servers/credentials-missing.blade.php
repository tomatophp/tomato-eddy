<x-tomato-admin-container :label="__('New Server')">
    <p>{{ __('Whoops! You haven\'t configured your DigitalOcean or Hetzner Cloud credentials yet. Don\'t worry, you can still create a new server. However, keep in mind that you\'ll only be able to provision a server at a custom provider.') }}</p>

    <div class="space-x-4">
        <x-splade-button type="link" modal href="{{ route('admin.credentials.create', ['forServer' => true]) }}" class="inline-block mt-4">
            {{ __('Create Credentials') }}
        </x-splade-button>

        <x-splade-button type="link" secondary keep-modal href="{{ route('admin.servers.create', ['withoutCredentials' => true]) }}" class="inline-block mt-4">
            {{ __('Continue without credentials') }}
        </x-splade-button>
    </div>
</x-tomato-admin-container>
