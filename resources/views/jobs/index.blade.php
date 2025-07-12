<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Jobs') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xs sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="container mx-auto px-4 py-6">
                        @if ($items->count() > 0)
                            <p class="mb-4 text-lg font-semibold">Aangemaakte jobs:</p>
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white border border-gray-200 rounded-md shadow-xs">
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
                                                <td class="py-2 px-4">{{ $item->repository }}</td>
                                                <td class="py-2 px-4">{{ $item->branch }}</td>
                                                <td class="py-2 px-4">{{ count($item->items) }}</td>
                                                <td class="py-2 px-4">{{ $item->active }}</td>
                                                <td class="py-2 px-4">
                                                    <a href="{{ route('codeanalyzer.job', ['jobs' => $item]) }}"
                                                        class="text-blue-600 hover:underline">Toon details</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                {{ $items->links() }}
                            </div>
                        @else
                            <p class="mb-4 text-gray-600">Nog geen jobs aangemaakt</p>
                        @endif

                        @can('hasAPI')
                            @can('noActiveJobs', 'App\\Models\Jobs')
                                <p class="mb-2 text-green-700 font-semibold">Er zijn geen actieve jobs, u kunt een nieuwe job
                                    toevoegen</p>
                                <a href="{{ route('codeanalyzer.create.step.one') }}"
                                    class="inline-block bg-blue-600 text-white px-4 py-2 rounded-sm hover:bg-blue-700 transition">Nieuwe
                                    job aanmaken</a>
                            @else
                                <p class="mb-2 text-red-600 font-semibold">Er staat nog een job in de wacht, u kunt geen nieuwe
                                    jobs aanmaken</p>
                            @endcan
                        @else
                            <p class="mb-2 text-red-600 font-semibold">Er is nog geen github API key ingesteld. Jobs
                                aanmaken is niet mogelijk</p>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
