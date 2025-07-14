@props(['message', 'error' => false])
@if ($message)
    <p class="mb-4 text-red-600 font-semibold">
        @if ($error)
            Foutmelding:
        @endif {{ $message }}
    </p>
@endif
