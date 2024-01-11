<x-tomato-admin-container :label="$file->nameWithContext()">
    <x-splade-form method="PATCH" :action="$file->updateRoute($server)" :default="[
                'contents' => $contents,
            ]" class="space-y-4">
        <x-tomato-admin-code name="contents" :ex="$file->prismLanguage->value" />
        <x-splade-submit :label="__('Save')" />
    </x-splade-form>
</x-tomato-admin-container>
