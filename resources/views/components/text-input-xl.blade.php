@props(['header', 'headerClass' => 'block mb-1 font-semibold', 'messages'])

<label for="owner" class="{{ $headerClass }}">{{ $header }}</label>
<input type="text"
    {{ $attributes->merge(['class' => 'w-full border border-gray-300 rounded-sm px-3 py-2 focus:outline-hidden focus:ring-2 focus:ring-blue-500', 'value' => old($attributes['name'])]) }} />
<x-input-error :messages="$messages" class="mt-2" />
