<x-app-layout>
        <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Job aanmaken') }}
        </h2>
    </x-slot>
<div class="container mx-auto px-4 py-6 max-w-lg">
      @if (Session('error'))
        <p class="mb-4 text-red-600 font-semibold">Foutmelding: {{ session('error') }}</p>
    @endif
<p>{{ __('Selecteer bestanden voor analyse') }}</p>
    <form action="{{ route('codeanalyzer.create.step.two.post') }}" method="POST"
                        enctype="multipart/form-data">
        @csrf
        <input type="hidden" value="{{ $repository }}" name="repository" />
        <input type="hidden" value="{{ $owner }}" name="owner" />
        <input type="hidden" value="{{ $branch }}" name="branch" /><br>
        <x-rendertree :tree="$items" namecheckbox="selectedItems[]" /><br>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Job aanmaken</button>
    </form>
</div>
</x-app-layout>