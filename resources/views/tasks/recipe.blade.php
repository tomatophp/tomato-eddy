@if($recipe->script && !empty($recipe->script))
    @if(Str::of($recipe->script)->contains('apt'))

        @include('tomato-eddy::tasks.apt-functions')

        waitForAptUnlock

    @endif
@endif

{!! $recipe->script !!}
