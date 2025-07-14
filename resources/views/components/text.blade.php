<textarea
    {{ $attributes->merge([
        'class' =>
            'w-full border border-gray-300 rounded-sm px-3 py-2 focus:outline-hidden focus:ring-2 focus:ring-blue-500',
        'rows' => '5',
        'cols' => '50',
    ]) }}>{{ $slot }}</textarea>
