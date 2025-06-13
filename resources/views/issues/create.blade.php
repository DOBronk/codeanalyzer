@extends('layouts.master')

@section('page', 'Code analyse')

@section('content')

<div class="container mx-auto px-4 py-6">
    @if (Session('error'))
        <p class="mb-4 text-red-600 font-semibold">Foutmelding: {{ session('error') }}</p>
    @endif

    <form action="{{ route('codeanalyzer.storeissue', ['id' => $item->id]) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        <h1 class="text-3xl font-bold mb-6">Issue aanmaken</h1>

        <p class="mb-2 font-semibold">Bestand: {{ $item->path }}</p>
        <p class="mb-2 font-semibold">Titel: <input type="text" name="title" /></p>
        <p class="mb-2 font-semibold">Text:</p>
        <textarea rows="10" cols="50" name="issuetext" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">@foreach($item->results as $name => $value){{ $name }}:{{ $value }}@endforeach</textarea>
        <x-primary-button>Aanmaken</x-primary-button>
    </form>
</div>

@endsection
