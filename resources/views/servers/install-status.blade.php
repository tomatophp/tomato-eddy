<div class="flex flex-row items-center">
    @unless ($item->installation_failed_at || $item->uninstallation_failed_at)
        @if (! $item->installed_at || $item->uninstallation_requested_at)
            @svg('heroicon-s-cog-6-tooth', 'h-5 w-5 text-gray-400 mr-1.5 animate-spin')
        @endif
    @endunless

    @if ($item->installation_failed_at)
        {{ __('Installation failed') }}
    @elseif ($item->uninstallation_failed_at)
        {{ __('Uninstallation failed') }}
    @elseif ($item->uninstallation_requested_at)
        {{ __('Uninstalling') }}...
    @elseif ($item->installed_at)
        {{ __('Installed') }}
    @else
        {{ __('Installing') }}...
    @endif
</div>
