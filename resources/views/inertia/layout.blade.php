<x-app-layout loadVue='true' :page="$page">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $vueHeader }}
        </h2>
    </x-slot>

    <x-page-container>
        @inertia
    </x-page-container>

</x-app-layout>