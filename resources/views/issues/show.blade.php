<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Issue tonen') }}
        </h2>
    </x-slot>

    <x-page-container>
        <x-data-table :headers="[__('Eigenaar'), __('Repository'), __('Branch')]">
            <x-row-table>
                <x-column-table :multi="[$job->owner, $job->repository, $job->branch]" />
            </x-row-table>
        </x-data-table>

        <div class="container mx-auto px-4 py-6">
            <p class="mb-1 text-lg font-semibold">Titel:</p>
            {{ $item->title }}
            <p class="mb-1 text-lg font-semibold">Text:</p>
            <div style="white-space: pre-wrap;">{{ $item->text }}</div><br>
            <x-link href="{{ $item->git_url }}">Link naar issue in github</x-link>
        </div>
    </x-page-container>
</x-app-layout>
