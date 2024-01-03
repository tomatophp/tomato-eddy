<x-tomato-admin-layout>
    <x-slot:header>
        {{ __('Credentials') }}
    </x-slot:header>
    <x-slot:buttons>
        <x-tomato-admin-button type="link" modal href="{{ route('admin.credentials.create') }}">
            {{ __('Add Credentials') }}
        </x-tomato-admin-button>
    </x-slot:buttons>

    <div class="pb-12">
        <div class="mx-auto">
            <x-splade-table :for="$table" striped>
                <x-splade-cell actions>
                    <div class="my-4">
                        <x-splade-button secondary type="link" modal href="{{ route('admin.servers.create', ['credentials' => $item->id]) }}">
                            {{ __('Create Server') }}
                        </x-splade-button>
                    </div>
                </x-splade-cell>
            </x-splade-table>
        </div>
    </div>
</x-tomato-admin-layout>


{{--@seoTitle(__('Credentials'))--}}

{{--<x-app-layout>--}}
{{--    <x-slot:header>--}}
{{--        {{ __('Credentials') }}--}}
{{--    </x-slot>--}}

{{--    <x-slot:description>--}}
{{--        {{ __('Manage your credentials.') }}--}}
{{--    </x-slot>--}}

{{--    @if($credentials->isNotEmpty())--}}
{{--        <x-slot:actions>--}}
{{--            <x-splade-button type="link" modal href="{{ route('credentials.create') }}">--}}
{{--                {{ __('Add Credentials') }}--}}
{{--            </x-splade-button>--}}
{{--        </x-slot>--}}
{{--    @endif--}}

{{--    <x-splade-table :for="$credentials">--}}
{{--        <x-splade-cell actions>--}}
{{--            @if($item->canBeUsedByTeam(auth()->user()->currentTeam))--}}
{{--                <x-splade-button secondary type="link" modal href="{{ route('servers.create', ['credentials' => $item->id]) }}">--}}
{{--                    {{ __('Create Server') }}--}}
{{--                </x-splade-button>--}}
{{--            @endif--}}
{{--        </x-splade-cell>--}}

{{--        <x-slot:empty-state>--}}
{{--            <x-empty-state modal :href="route('credentials.create')">--}}
{{--                {{ __('Add Credentials') }}--}}
{{--            </x-empty-state>--}}
{{--        </x-slot>--}}
{{--    </x-splade-table>--}}
{{--</x-app-layout>--}}
