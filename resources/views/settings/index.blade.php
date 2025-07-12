<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Instellingen') }}
        </h2>
    </x-slot>
    <div class="container mx-auto px-4 py-6 max-w-lg">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xs sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        @if (Session('error'))
                            <p class="mb-4 text-red-600 font-semibold">Foutmelding: {{ session('error') }}</p>
                        @elseif (Session('message'))
                            <p class="mb-4 text-red-600 font-semibold">{{ session('message') }}</p>
                        @endif

                        <form action="{{ route('codeanalyzer.postsettings') }}" method="POST"
                            enctype="multipart/form-data" class="space-y-6">
                            @csrf
                            <div>
                                <x-input-error :messages="$errors->get('gh_api_key')" class="mt-2" />
                                <label for="owner" class="block mb-1 font-semibold">Github API Key</label>
                                <input type="text" id="apikey" name="gh_api_key"
                                    value="{{ old('gh_api_key', Auth::user()->settings->gh_api_key) }}"
                                    class="w-full border border-gray-300 rounded-sm px-3 py-2 focus:outline-hidden focus:ring-2 focus:ring-blue-500" />
                            </div>
                            <br>
                            <div>
                                <button type="submit"
                                    class="bg-blue-600 text-white px-4 py-2 rounded-sm hover:bg-blue-700 transition">Opslaan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
