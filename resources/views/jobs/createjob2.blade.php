<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('job.create') }}
        </h2>
    </x-slot>

    <x-page-container class="max-w-lg">
        <x-message :message="Session('error')" error="1" />

        <p>{{ __('Selecteer bestanden voor analyse') }}</p>
        <form action="{{ route('codeanalyzer.create.step.two.post') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <x-input-error :messages="$errors->get('selectedItems')" class="mt-2" />
            <x-rendertree2 :tree="$items" namecheckbox="selectedItems[]" /><br>
            <x-button-blue>{{ __('job.create') }}</x-button-blue>
        </form>
    </x-page-container>

</x-app-layout>
