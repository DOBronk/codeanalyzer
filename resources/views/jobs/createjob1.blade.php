<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Job aanmaken') }}
        </h2>
    </x-slot>

    <x-page-container class="max-w-lg">
        <p class="mb-4">{{ __('Geef een git repository op') }}</p>
        <x-message :message="Session('error')" error="1" />

        <form action="{{ route('codeanalyzer.create.step.one.post') }}" method="POST" enctype="multipart/form-data"
            class="space-y-6">
            @csrf
            <div>
                <x-text-input-xl header="Eigenaar" name="owner" :messages="$errors->get('owner')" />
            </div>
            <div>
                <x-text-input-xl header="Repository" name="repository" :messages="$errors->get('repository')" />
            </div>
            <div>
                <x-text-input-xl header="Branch" name="branch" :messages="$errors->get('branch')" />
                <p class="text-sm text-gray-500 mt-1">(Optioneel laat leeg voor main branch)</p>
            </div>
            <div>
                <x-button-blue>Volgende stap</x-button-blue>
            </div>
        </form>
    </x-page-container>

</x-app-layout>
