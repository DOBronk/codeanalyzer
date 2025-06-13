@extends('layouts.master')

@section('page', 'Code analyse')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-lg">
    <h1 class="text-3xl font-bold mb-6">Code analyse nieuwe job aanmaken</h1>
    <p class="mb-4">{{ __('Geef een git repository op') }}</p>

    @if (Session('error'))
        <p class="mb-4 text-red-600 font-semibold">Foutmelding: {{ session('error') }}</p>
    @endif

    <form action="{{ route('codeanalyzer.create.step.one.post') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        <div>
            <label for="owner" class="block mb-1 font-semibold">Eigenaar</label>
            <input type="text" id="owner" name="owner" value="{{ old('owner') }}" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            <x-input-error :messages="$errors->get('owner')" class="mt-2" />
        </div>
        <div>
            <label for="repository" class="block mb-1 font-semibold">Repository</label>
            <input type="text" id="repository" name="repository" value="{{ old('repository') }}" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            <x-input-error :messages="$errors->get('repository')" class="mt-2" />
        </div>
        <div>
            <label for="branch" class="block mb-1 font-semibold">Branch</label>
            <input type="text" id="branch" name="branch" value="{{ old('branch') }}" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            <p class="text-sm text-gray-500 mt-1">(Optioneel veld, wanneer niet ingevuld wordt de standaard hoofdbranch gekozen)</p>
        </div>
        <div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Volgende stap</button>
        </div>
    </form>
</div>
@endsection
