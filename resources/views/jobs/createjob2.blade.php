<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Job aanmaken') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xs sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="container mx-auto px-4 py-6 max-w-lg">
                        @if (Session('error'))
                            <p class="mb-4 text-red-600 font-semibold">Foutmelding: {{ session('error') }}</p>
                        @endif
                        <p>{{ __('Selecteer bestanden voor analyse') }}</p>
                        <form action="{{ route('codeanalyzer.create.step.two.post') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <x-input-error :messages="$errors->get('selectedItems')" class="mt-2" />
                            <x-rendertree2 :tree="$items" namecheckbox="selectedItems[]" /><br>
                            <button type="submit"
                                class="bg-blue-600 text-white px-4 py-2 rounded-sm hover:bg-blue-700 transition">Job
                                aanmaken</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
