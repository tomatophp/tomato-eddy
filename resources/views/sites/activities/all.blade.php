@seoTitle(__('Accounts'))

<x-app-layout>
    <x-slot:header>
        {{ __('Accounts') }}
    </x-slot:header>

    <x-slot:description>
        {{ __('Manage your accounts.') }}
    </x-slot:description>

    <x-splade-table :for="$logs">

    </x-splade-table>
</x-app-layout>
