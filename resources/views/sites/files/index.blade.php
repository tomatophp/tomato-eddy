@extends('tomato-eddy::sites.layout')

@section('title', __('Files'))

@section('description')
    {{ __('Manage your Files.') }}
@endsection


@section('content')
    <x-splade-table :for="$files">
        <x-splade-cell description>
            <p class="whitespace-pre-line">{{ $item->description }}</p>
        </x-splade-cell>
    </x-splade-table>
@endsection
