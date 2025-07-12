<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Issues') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xs sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="container ">
                        @if ($items->count() > 0)
                            <p class="mb-4 text-lg font-semibold">Issues:</p>
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white border border-gray-200 rounded-md shadow-xs">
                                    <thead>
                                        <tr class="bg-gray-100 border-b border-gray-300">
                                            <th scope="col" class="text-left py-2 px-4">{{ __('Id') }}</th>
                                            <th scope="col" class="text-left py-2 px-4">{{ __('Eigenaar') }}</th>
                                            <th scope="col" class="text-left py-2 px-4">{{ __('Repository') }}</th>
                                            <th scope="col" class="text-left py-2 px-4">{{ __('Branch') }}</th>
                                            <th scope="col" class="text-left py-2 px-4">{{ __('Titel') }}</th>
                                            <th scope="col" class="text-left py-2 px-4">{{ __('Text') }}</th>
                                            <th scope="col" class="text-left py-2 px-4">Acties</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($items as $item)
                                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                                <td class="py-2 px-4">{{ $item->id }}</td>
                                                <td class="py-2 px-4">{{ $item->job->owner }}</td>
                                                <td class="py-2 px-4">{{ $item->job->repository }}</td>
                                                <td class="py-2 px-4">{{ $item->job->branch }}</td>
                                                <td class="py-2 px-4">{{ $item->title }}</td>
                                                <td class="py-2 px-4">{{ Str::limit($item->text, 50) }}</td>
                                                <td class="py-2 px-4 space-y-1">
                                                    <a href="{{ route('codeanalyzer.showissue', ['jobissues' => $item]) }}"
                                                        class="text-blue-600 hover:underline">Toon issue</a><br>
                                                    <a href="{{ $item->git_url }}"
                                                        class="text-blue-600 hover:underline" target="_blank"
                                                        rel="noopener noreferrer">Github link</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                {{ $items->links() }}
                            </div>
                        @else
                            <p class="text-gray-600">Nog geen issues aangemaakt</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
