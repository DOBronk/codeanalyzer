@props(['type' => 'submit'])
<button type="{{ $type }}"
    {{ $attributes->merge(['class' => 'bg-blue-600 text-white px-4 py-2 rounded-sm hover:bg-blue-700 transition']) }}>{{ $slot }}</button>
