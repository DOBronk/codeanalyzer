@extends('layouts.master')

@section('page', 'Toon issue')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-3xl font-bold mb-6">Code analyse toon issue</h1><br>
    <table class="min-w-full bg-white border border-gray-200 rounded-md shadow-sm">
        <thead>
            <tr class="bg-gray-100 border-b border-gray-300">
                <th class="text-left py-2 px-4">{{ __('Eigenaar') }}</th>
                <th class="text-left py-2 px-4">{{ __('Repository') }}</th>
                <th class="text-left py-2 px-4">{{ __('Branch') }}</th>
            </tr>
        </thead>
        <tbody>
            <tr class="border-b border-gray-200 hover:bg-gray-50">
                <td class="py-2 px-4">{{ $item->job->owner }}</td>
                <td class="py-2 px-4">{{ $item->job->repo }}</td>
                <td class="py-2 px-4">{{ $item->job->branch }}</td>
            </tr>
        </tbody>
    </table>

    <p class="mb-4 text-lg font-semibold">Titel: {{ $item->title }}</p>
    <p class="mb-4 text-lg font-semibold">Text:</p>
    <div style="white-space: pre-wrap;">{{ $item->text }}</div><br>
    <a href="{{ $item->git_url }}">Link naar issue in github</a>
@endsection