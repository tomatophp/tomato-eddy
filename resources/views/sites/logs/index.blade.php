@extends('tomato-eddy::sites.layout')

@section('title', __('Logs'))


@section('content')
    <x-splade-table :for="$logs">
        <x-splade-cell description>
            <p class="whitespace-pre-line">{{ $item->description }}</p>
        </x-splade-cell>
    </x-splade-table>
@endsection
