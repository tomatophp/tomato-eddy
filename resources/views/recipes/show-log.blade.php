<x-tomato-admin-container :label="__('Show Recipe Log')">
    <div class="w-full overflow-x-scroll">
        <code>
        <pre>
             {!! $model->task?->output !!}
        </pre>
        </code>
    </div>
    <div class="flex justify-start gap-2 pt-3">
        <x-tomato-admin-button secondary type="button" @click.prevent="modal.close" label="{{__('Cancel')}}"/>
    </div>
</x-tomato-admin-container>
