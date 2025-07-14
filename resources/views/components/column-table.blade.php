@props(['multi'])

@isset($multi)
    @foreach ($multi as $column)
        <td {{ $attributes->merge(['class' => 'py-2 px-4']) }}>{{ $column }}</td>
    @endforeach
@else
    <td {{ $attributes->merge(['class' => 'py-2 px-4']) }}>{{ $slot }}</td>
@endisset
