<x-app-layout>
        <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Issue aanmaken') }}
        </h2>
    </x-slot>

        <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xs sm:rounded-lg">
                <div class="p-6 text-gray-900">

<div class="container mx-auto px-4 py-6">
    @if (Session('error'))
        <p class="mb-4 text-red-600 font-semibold">Foutmelding: {{ session('error') }}</p>
    @endif

    <form action="{{ route('codeanalyzer.storeissue', ['jobitems' => $item]) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        <p class="mb-2 font-semibold">Bestand: {{ $item->path }}</p>
        <x-input-error :messages="$errors->get('title')" class="mt-2" />
        <p class="mb-2 font-semibold">Titel:  <x-text-input id="name" class="block mt-1 w-full border border-gray-300" type="text" name="title" /></p>
        <x-input-error :messages="$errors->get('issuetext')" class="mt-2" />
        <p class="mb-2 font-semibold">Text:</p>
        <textarea rows="10" cols="50" name="issuetext" class="w-full border border-gray-300 rounded-sm px-3 py-2 focus:outline-hidden focus:ring-2 focus:ring-blue-500">{{ $item->resultsToString() }}</textarea>
        <x-primary-button>Aanmaken</x-primary-button>
    </form>
</div>

</div></div></div></div>
</x-app-layout>
