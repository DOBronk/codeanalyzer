<x-app-layout>
        <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Issue tonen') }}
        </h2>
    </x-slot>

        <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xs sm:rounded-lg">
                <div class="p-6 text-gray-900">

<div class="container mx-auto px-4 py-6">
    <table class="min-w-full bg-white border border-gray-200 rounded-md shadow-xs">
        <thead>
            <tr class="bg-gray-100 border-b border-gray-300">
                <th class="text-left py-2 px-4">{{ __('Eigenaar') }}</th>
                <th class="text-left py-2 px-4">{{ __('Repository') }}</th>
                <th class="text-left py-2 px-4">{{ __('Branch') }}</th>
            </tr>
        </thead>
        <tbody>
            <tr class="border-b border-gray-200 hover:bg-gray-50">
                <td class="py-2 px-4">{{ $job->owner }}</td>
                <td class="py-2 px-4">{{ $job->repo }}</td>
                <td class="py-2 px-4">{{ $job->branch }}</td>
            </tr>
        </tbody>
    </table>

    <div class="container mx-auto px-4 py-6">
    <p class="mb-1 text-lg font-semibold">Titel:</p>
    {{ $item->title }}
    <p class="mb-1 text-lg font-semibold">Text:</p>
    <div style="white-space: pre-wrap;">{{ $item->text }}</div><br>
    <a href="{{ $item->git_url }}">Link naar issue in github</a>
    </div>
</div></div></div></div><div>
</x-app-layout>