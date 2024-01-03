<x-splade-event private channel="teams.{{ auth()->user()->currentTeam->id }}" listen="SiteUpdated" preserve-scroll />

<x-site-layout :site="$site" :title="__('Activities')">
    <x-splade-table :for="$logs">
    </x-splade-table>
</x-site-layout>
