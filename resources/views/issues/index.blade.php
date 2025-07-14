<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Issues') }}
        </h2>
    </x-slot>

    <x-page-container>
        @if ($items->count() > 0)
            <p class="mb-4 text-lg font-semibold">Issues:</p>
            <div class="overflow-x-auto">
                <x-data-table :headers="[
                    __('Id'),
                    __('Eigenaar'),
                    __('Repository'),
                    __('Branch'),
                    __('Titel'),
                    __('Text'),
                    'Acties',
                ]">
                    @foreach ($items as $item)
                        <x-row-table class="hover:bg-gray-50">
                            <x-column-table :multi="[
                                $item->id,
                                $item->job->owner,
                                $item->job->repository,
                                $item->job->branch,
                                $item->title,
                                Str::limit($item->text, 50),
                            ]" />
                            <x-column-table>
                                <x-link href="{{ route('codeanalyzer.showissue', ['jobissues' => $item]) }}">Toon
                                    issue</x-link><br>
                                <x-link href="{{ $item->git_url }}" target="_blank" rel="noopener noreferrer">Github
                                    link</x-link>
                            </x-column-table>
                        </x-row-table>
                    @endforeach
                </x-data-table>
                {{ $items->links() }}
            </div>
        @else
            <p class="text-gray-600">Nog geen issues aangemaakt</p>
        @endif
    </x-page-container>

</x-app-layout>
