<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Instellingen') }}
        </h2>
    </x-slot>

    <x-page-container>
        <x-message :message="Session('error')" error="1" />
        <x-message :message="Session('message')" />

        <form action="{{ route('codeanalyzer.postsettings') }}" method="POST" enctype="multipart/form-data"
            class="space-y-6">
            @csrf
            <div>
                <x-text-input-xl header="Github API Key" :messages="$errors->get('gh_api_key')" name="gh_api_key"
                    value="{{ old('gh_api_key', Auth::user()->settings->gh_api_key) }}" />
            </div>
            <br>
            <div>
                <x-button-blue>Opslaan</x-button-blue>
            </div>
        </form>
    </x-page-container>

</x-app-layout>
