<x-tomato-admin-container label="{{__('Server Count')}}">
    <x-splade-form class="flex flex-col gap-4" :default="['count' => 1]" action="{{route('admin.site-templates.server', $model->id)}}" method="post">

        <x-splade-input :label="__('Server Count')" name="count" type="number"  :placeholder="__('Server Count')" />
        <div class="flex justify-start gap-2 pt-3">
            <x-tomato-admin-submit  label="{{__('Save')}}" :spinner="true" />
            <x-tomato-admin-button secondary :href="route('admin.site-templates.index')" label="{{__('Cancel')}}"/>
        </div>
    </x-splade-form>
</x-tomato-admin-container>
