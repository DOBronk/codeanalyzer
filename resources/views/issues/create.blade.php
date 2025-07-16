<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Issue aanmaken') }}
        </h2>
    </x-slot>

    <x-page-container>
        <x-message :message="Session('error')" error="1" />

        <form action="{{ route('codeanalyzer.storeissue', ['jobitem' => $item]) }}" method="POST"
            enctype="multipart/form-data" class="space-y-6">
            @csrf
            <p class="mb-2 font-semibold">Bestand: {{ $item->path }}</p>
            <x-input-error :messages="$errors->get('title')" class="mt-2" />
            <p class="mb-2 font-semibold">Titel:</p>
            <x-text-input class="block mt-1 w-full border border-gray-300" type="text" id="title"
                name="title" />
            <x-input-error :messages="$errors->get('text')" class="mt-2" />
            <p class="mb-2 font-semibold">Text:</p>
            <x-text rows="10" name="text">{{ $item->resultsToString() }}</x-text>
            <x-button-blue>Aanmaken</x-button-blue>
        </form>
    </x-page-container>

</x-app-layout>
