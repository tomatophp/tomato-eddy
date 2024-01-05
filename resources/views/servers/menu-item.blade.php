<Link href="{{$href}}"
    class="@if(url()->current() === $href) text-primary-600 @else text-gray-600 @endif bg-gray-50 hover:bg-white group flex items-center rounded-md px-3 py-2 text-sm font-medium flex justify-start gap-2">
    <div class="flex flex-col justify-center items-center">
        <i class="{{$icon}}"></i>
    </div>
    <div>
        {{$label}}
    </div>
</Link>
