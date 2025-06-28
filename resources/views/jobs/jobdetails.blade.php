<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Job details') }}
        </h2>
    </x-slot>
<div class="container mx-auto px-4 py-6">
    @if(Session('message'))
        <p class="mb-4 text-red-600 font-semibold">{{ Session('message') }}</p>
    @endif

    <p class="mb-2 font-semibold">Job:</p>
    <div class="overflow-x-auto mb-6">
        <table class="min-w-full bg-white border border-gray-200 rounded-md shadow-sm">
            <thead>
                <tr class="bg-gray-100 border-b border-gray-300">
                    <th class="text-left py-2 px-4">{{ __('Id') }}</th>
                    <th class="text-left py-2 px-4">{{ __('Eigenaar') }}</th>
                    <th class="text-left py-2 px-4">{{ __('Repository') }}</th>
                    <th class="text-left py-2 px-4">{{ __('Branch') }}</th>
                    <th class="text-left py-2 px-4">{{ __('Status') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b border-gray-200">
                    <td class="py-2 px-4">{{ $job->id }}</td>
                    <td class="py-2 px-4">{{ $job->owner }}</td>
                    <td class="py-2 px-4">{{ $job->repo }}</td>
                    <td class="py-2 px-4">{{ $job->branch }}</td>
                    <td class="py-2 px-4">{{ $job->active }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <p class="mb-2 font-semibold">Items</p>
    <div class="overflow-x-auto">
        <table class="w-full bg-white border border-gray-200 rounded-md shadow-sm">
            <thead>
                <tr class="bg-gray-100 border-b border-gray-300">
                    <th class="text-left py-2 px-4">{{ __('Bestand') }}</th>
                    <th class="text-left py-2 px-4">{{ __('Status') }}</th>
                    <th class="text-left py-2 px-4">{{ __('Resultaat') }}</th>
                    <th class="text-left py-2 px-4">{{ __('Acties') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($job->items as $item)
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="py-2 px-4">{{ $item->path }}</td>
                        <td class="py-2 px-4">{{ $item->status->name }}</td>
                        @if($item->status_id == 1 || $item->status_id == 3)
                            @if($item->results != null)
                                <td class="py-2 px-4">
                                    <textarea rows="5" cols="50" name="result{{ $item->id }}" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $item->resultsToString() }}</textarea>
                                </td>
                            @else
                                <td class="py-2 px-4">Geen aanmerkingen</td>
                            @endif
                        @else
                            <td class="py-2 px-4">-</td>
                        @endif

                        @if($item->status_id == 1 && $item->results != null)
                            <td class="py-2 px-4">
                                <form action="{{ route('codeanalyzer.createissue', ['id' => $item->id]) }}">
                                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Issue aanmaken</button>
                                </form>
                            </td>
                        @else
                            <td class="py-2 px-4">-</td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
</x-app-layout>
