<x-tomato-admin-container label="{{trans('tomato-admin::global.crud.create')}} {{__('Recipe')}}">
    <x-splade-form :default="['user'=>'root', 'type'=>'software']" class="flex flex-col space-y-4" action="{{route('admin.recipes.store')}}" method="post">
        <x-splade-select choices :label="__('User')" name="user" type="text"  :placeholder="__('User')">
            <option value="root">Root</option>
            <option value="eddy">Eddy</option>
            <option value="server">Server</option>
        </x-splade-select>
        <x-splade-select choices :label="__('Type')" name="type" type="text"  :placeholder="__('Type')">
            <option value="software">Software</option>
            <option value="update">Update</option>
            <option value="cron">Cron</option>
        </x-splade-select>

        <x-splade-input :label="__('Name')" name="name" type="text"  :placeholder="__('Name')" />
        <x-splade-textarea :label="__('Description')" name="description" type="text"  :placeholder="__('Description')" />
        <x-tomato-admin-code :label="__('Script')" name="script"  :placeholder="__('Script')" />

        <div class="flex justify-start gap-2 pt-3">
            <x-tomato-admin-submit  label="{{__('Save')}}" :spinner="true" />
            <x-tomato-admin-button secondary :href="route('admin.recipes.index')" label="{{__('Cancel')}}"/>
        </div>
    </x-splade-form>
</x-tomato-admin-container>
