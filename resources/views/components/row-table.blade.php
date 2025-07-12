@props(['rowClass' => 'border-b border-gray-200'])

<tr {{ $attributes->merge(['class' => $rowClass]) }}>
    {{ $slot }}
</tr>
