<x-tomato-admin-container :label="__('Run on server')">
    <x-splade-form class="flex flex-col gap-4" method="POST" :action="route('admin.recipes.fire', $recipe->id)">
        <x-splade-select choices multiple name="servers" :placeholder="__('Servers')" :label="__('Servers')">
            @foreach($servers as $server)
                <option value="{{$server->id}}">{{$server->name}}</option>
            @endforeach
        </x-splade-select>

        <x-tomato-admin-submit spinner :label="__('Fire')" />
    </x-splade-form>
</x-tomato-admin-container>
