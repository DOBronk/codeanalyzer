<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Jobs') }}
        </h2>
    </x-slot>

    <x-page-container>
        @if ($items->count() > 0)
            <p class="mb-4 text-lg font-semibold">Aangemaakte jobs:</p>
            <div class="overflow-x-auto">
                <x-data-table :headers="[
                    __('Id'),
                    __('Eigenaar'),
                    __('Repository'),
                    __('Branch'),
                    __('Aantal items'),
                    __('Status'),
                    'Acties',
                ]">
                    @foreach ($items as $item)
                        <x-row-table class="hover:bg-gray-50">
                            <x-column-table :multi="[
                                $item->id,
                                $item->owner,
                                $item->repository,
                                $item->branch,
                                count($item->items),
                                $item->active,
                            ]" />
                            <x-column-table>
                                <x-link href="{{ route('codeanalyzer.job', ['jobs' => $item]) }}">Toon details</x-link>
                            </x-column-table>
                        </x-row-table>
                    @endforeach
                </x-data-table>
                {{ $items->links() }}
            </div>
        @else
            <p class="mb-4 text-gray-600">Nog geen jobs aangemaakt</p>
        @endif

        @cannot('hasAPI')
            <p class="mb-2 text-red-600 font-semibold">Er is nog geen github API key ingesteld. Jobs
                aanmaken is niet mogelijk</p>
        @elsecannot('noActiveJobs', 'App\\Models\Jobs')
            <p class="mb-2 text-red-600 font-semibold">Er staat nog een job in de wacht, u kunt geen nieuwe
                jobs aanmaken</p>
        @else
            <p class="mb-2 text-green-700 font-semibold">Er zijn geen actieve jobs, u kunt een nieuwe job
                toevoegen</p>
            <x-button-blue href="{{ route('codeanalyzer.create.step.one') }}" type="link">Nieuwe job
                aanmaken</x-button-blue>
        @endcan
    </x-page-container>

</x-app-layout>
