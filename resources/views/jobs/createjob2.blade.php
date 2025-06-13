@extends('layouts.master')

@section('page', 'Code analyse')

@section('content')
<h1>Code analyse nieuwe job aanmaken</h1>
<p>{{ __('Selecteer bestanden voor analyse') }}</p>
    <form action="{{ route('codeanalyzer.create.step.two.post') }}" method="POST"
                        enctype="multipart/form-data">
        @csrf
        <input type="hidden" value="{{ $repository }}" name="repository" />
        <input type="hidden" value="{{ $owner }}" name="owner" />
        <input type="hidden" value="{{ $branch }}" name="branch" />
        <x-rendertree :tree="$items" namecheckbox="selectedItems[]" />
        <x-primary-button>Job aanmaken</x-primary-button>
    </form>
@endsection