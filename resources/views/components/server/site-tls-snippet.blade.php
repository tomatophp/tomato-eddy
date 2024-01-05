(tls-{!! $site->id !!}) {
    @if($tlsSetting === \TomatoPHP\TomatoEddy\Enums\Models\TlsSetting::Custom && $certificate)
        tls {!! $certificate->certificatePath() !!} {!! $certificate->privateKeyPath() !!}
    @elseif($tlsSetting === \TomatoPHP\TomatoEddy\Enums\Models\TlsSetting::Internal)
        tls internal
    @else
        #
    @endif
}
