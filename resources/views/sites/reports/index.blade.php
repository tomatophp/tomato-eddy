<x-splade-event private channel="teams.{{ auth()->user()->currentTeam->id }}" listen="SiteUpdated" preserve-scroll />

<x-site-layout :site="$site" :title="__('Reports')">

</x-site-layout>
