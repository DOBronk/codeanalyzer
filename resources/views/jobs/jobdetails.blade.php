<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Job details') }}
        </h2>
    </x-slot>

    <x-page-container>
        <x-message :message="Session('message')" />

        <p class="mb-2 font-semibold">Job:</p>
        <div class="overflow-x-auto mb-6">
            <x-data-table :headers="[__('Id'), __('Eigenaar'), __('Repository'), __('Branch'), __('Status')]">
                <x-row-table>
                    <x-column-table>{{ $job->id }}</x-column-table>
                    <x-column-table>{{ $job->owner }}</x-column-table>
                    <x-column-table>{{ $job->repository }}</x-column-table>
                    <x-column-table>{{ $job->branch }}</x-column-table>
                    <x-column-table>{{ $job->active }}</x-column-table>
                </x-row-table>
            </x-data-table>
        </div>

        <p class="mb-2 font-semibold">Items</p>
        <div class="overflow-x-auto">
            <x-data-table :headers="[__('Bestand'), __('Status'), __('Resultaat'), __('Acties')]">
                @foreach ($job->items as $item)
                    <x-row-table class="hover:bg-gray-50">
                        <x-column-table>{{ $item->path }}</x-column-table>
                        <x-column-table>{{ $item->status->name }}</x-column-table>
                        <x-column-table>
                            @if ($item->filteredResults)
                                <x-text rows='4'>{{ $item->resultsToString() }}</x-text>
                            @elseif ($item->status_id != 0)
                                Geen aanmerkingen
                            @endif
                        </x-column-table>
                        <x-column-table>
                            @if ($item->status_id == 1 && $item->filteredResults)
                                <form action="{{ route('codeanalyzer.createissue', ['jobitems' => $item]) }}">
                                    <x-button-blue>Issue aanmaken</x-button-blue>
                                </form>
                            @endif
                        </x-column-table>
                    </x-row-table>
                @endforeach
            </x-data-table>
        </div>
    </x-page-container>
</x-app-layout>
