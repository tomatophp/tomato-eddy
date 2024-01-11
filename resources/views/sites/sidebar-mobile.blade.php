<x-tomato-admin-dropdown>
    <x-slot:button>
        <div class="text-primary-600 bg-gray-50 hover:bg-white group flex items-center rounded-md px-3 py-2 text-sm font-medium flex justify-start gap-2">
        <div class="flex flex-col justify-center items-center">
            <i class="bx bx-menu"></i>
        </div>
        <div>
            {{__('Menus')}}
        </div>
        </div>
    </x-slot:button>

    @include('tomato-eddy::sites.sidebar')
</x-tomato-admin-dropdown>
