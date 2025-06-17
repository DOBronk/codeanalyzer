@extends('layouts.master')

@section('page', 'Issues')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-3xl font-bold mb-6">Code analyse issues</h1>

    @if($items->count() > 0)
        <p class="mb-4 text-lg font-semibold">Issues:</p>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 rounded-md shadow-sm">
                <thead>
                    <tr class="bg-gray-100 border-b border-gray-300">
                        <th class="text-left py-2 px-4">{{ __('Id') }}</th>
                        <th class="text-left py-2 px-4">{{ __('Eigenaar') }}</th>
                        <th class="text-left py-2 px-4">{{ __('Repository') }}</th>
                        <th class="text-left py-2 px-4">{{ __('Branch') }}</th>
                        <th class="text-left py-2 px-4">{{ __('Titel') }}</th>
                        <th class="text-left py-2 px-4">{{ __('Text') }}</th>
                        <th class="text-left py-2 px-4">Acties</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="py-2 px-4">{{ $item->id }}</td>
                            <td class="py-2 px-4">{{ $item->job->owner }}</td>
                            <td class="py-2 px-4">{{ $item->job->repo }}</td>
                            <td class="py-2 px-4">{{ $item->job->branch }}</td>
                            <td class="py-2 px-4">{{ $item->title }}</td>
                            <td class="py-2 px-4">{{ Str::limit($item->text, 50) }}</td>
                            <td class="py-2 px-4">
                                <a href="{{ route('codeanalyzer.showissue', ['id' => $item->id]) }}">Toon issue</a><br>
                                <a href="{{ $item->git_url }}">Github link</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="mb-4 text-gray-600">Nog geen issues aangemaakt</p>
    @endif


</div>
@endsection