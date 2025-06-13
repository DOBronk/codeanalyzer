@extends('layouts.master')

@section('page', 'Jobs')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-3xl font-bold mb-6">Code analyse startpagina</h1>

    @if($items->count() > 0)
        <p class="mb-4 text-lg font-semibold">Aangemaakte jobs:</p>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 rounded-md shadow-sm">
                <thead>
                    <tr class="bg-gray-100 border-b border-gray-300">
                        <th class="text-left py-2 px-4">{{ __('Id') }}</th>
                        <th class="text-left py-2 px-4">{{ __('Eigenaar') }}</th>
                        <th class="text-left py-2 px-4">{{ __('Repository') }}</th>
                        <th class="text-left py-2 px-4">{{ __('Branch') }}</th>
                        <th class="text-left py-2 px-4">{{ __('Aantal items') }}</th>
                        <th class="text-left py-2 px-4">{{ __('Status') }}</th>
                        <th class="text-left py-2 px-4">Acties</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="py-2 px-4">{{ $item->id }}</td>
                            <td class="py-2 px-4">{{ $item->owner }}</td>
                            <td class="py-2 px-4">{{ $item->repo }}</td>
                            <td class="py-2 px-4">{{ $item->branch }}</td>
                            <td class="py-2 px-4">{{ count($item->items) }}</td>
                            <td class="py-2 px-4">{{ $item->active }}</td>
                            <td class="py-2 px-4">
                                <a href="{{ route('codeanalyzer.job', ['id' => $item->id]) }}" class="text-blue-600 hover:underline">Toon details</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="mb-4 text-gray-600">Nog geen jobs aangemaakt</p>
    @endif

    @can('noActiveJobs')
        <p class="mb-2 text-green-700 font-semibold">Er zijn geen actieve jobs, u kunt een nieuwe job toevoegen</p>
        <a href="{{ route('codeanalyzer.create.step.one') }}" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Nieuwe job aanmaken</a>
    @else
        <p class="mb-2 text-red-600 font-semibold">Er staat nog een job in de wacht, u kunt geen nieuwe jobs aanmaken</p>
    @endcan
</div>
@endsection
