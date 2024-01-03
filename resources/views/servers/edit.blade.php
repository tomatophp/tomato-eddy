<x-splade-modal class="font-main">
    <h1 class="text-2xl font-bold mb-4">Edit Server #{{$model->id}}</h1>

    <x-splade-form class="flex flex-col space-y-4" action="{{route('servers.actions.update', $model->id)}}" method="post" :default="$model">
        <x-splade-textarea name="description" type="text"  placeholder="Description" />
        <x-splade-select placeholder="Tags" name="tags[]" :options="$tags" option-label="name" option-value="id" choices relation multiple/>

        <x-splade-submit label="Submit" :spinner="true" />
    </x-splade-form>
</x-splade-modal>
