@props(['headers', 'rowClass' => 'bg-gray-100 border-b border-gray-300', 'headerClass' => 'text-left py-2 px-4'])

<table {{ $attributes->merge(['class' => 'min-w-full bg-white border border-gray-200 rounded-md shadow-xs']) }}>
    <thead>
        <x-row-table :rowClass="$rowClass">
            @foreach ($headers as $header)
                <th class="{{ $headerClass }}">{{ $header }}</th>
            @endforeach
        </x-row-table>
    </thead>
    <tbody>
        {{ $slot }}
    </tbody>
</table>
