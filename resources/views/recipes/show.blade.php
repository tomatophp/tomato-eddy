<x-tomato-admin-container label="{{trans('tomato-admin::global.crud.view')}} {{__('Recipe')}} #{{$model->id}}">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 ">

          <x-tomato-admin-row :label="__('Name')" :value="$model->name" type="string" />

          <x-tomato-admin-row :label="__('Description')" :value="$model->description" type="string" />

          <x-tomato-admin-row :label="__('User')" :value="$model->user" type="string" />

          <x-tomato-admin-row :label="__('Type')" :value="$model->type" type="string" />

          <div class="col-span-2 bg-gray-900 text-white p-2 rounded-lg w-full overflow-x-scroll ">
              <code>
                  <pre>
                      {{$model->script}}
                  </pre>
              </code>
          </div>


    </div>
    <div class="flex justify-start gap-2 pt-3">
        <x-tomato-admin-button warning label="{{__('Edit')}}" :href="route('admin.recipes.edit', $model->id)"/>
        <x-tomato-admin-button danger :href="route('admin.recipes.destroy', $model->id)"
                               confirm="{{trans('tomato-admin::global.crud.delete-confirm')}}"
                               confirm-text="{{trans('tomato-admin::global.crud.delete-confirm-text')}}"
                               confirm-button="{{trans('tomato-admin::global.crud.delete-confirm-button')}}"
                               cancel-button="{{trans('tomato-admin::global.crud.delete-confirm-cancel-button')}}"
                               method="delete"  label="{{__('Delete')}}" />
        <x-tomato-admin-button secondary :href="route('admin.recipes.index')" label="{{__('Cancel')}}"/>
    </div>


    <x-tomato-admin-relations-group :relations="['logs' => __('Logs')]">
        <x-tomato-admin-relations
            :model="$model"
            name="logs"
            :table="\TomatoPHP\TomatoEddy\Tables\RecipeLogTable::class"
        />
    </x-tomato-admin-relations-group>
</x-tomato-admin-container>
