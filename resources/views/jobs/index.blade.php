<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Job') }}
        </h2>
    </x-slot>

    <x-page-container>
        @if ($items->count() > 0)
            <p class="mb-4 text-lg font-semibold">{{ __('Aangemaakte jobs')  }}:</p>
            <div class="overflow-x-auto">
                <x-data-table :headers="[
                    __('Id'),
                    __('Eigenaar'),
                    __('Repository'),
                    __('Branch'),
                    __('Aantal items'),
                    __('Status'),
                    __('Acties'),
                ]">
                    @foreach ($items as $item)
                        <x-row-table class="hover:bg-gray-50">
                            <x-column-table :multi="[
                                $item->id,
                                $item->owner,
                                $item->repository,
                                $item->branch,
                                count($item->items),
                                $item->active,
                            ]" />
                            <x-column-table>
                                <x-link
                                    href="{{ route('codeanalyzer.job', ['job' => $item]) }}">{{ __('messages.details') }}
                                </x-link>
                                @if ($item->active)
                                    <x-link href="{{ route('job.cancel', ['job' => $item]) }}"
                                        onclick="return confirm('Zeker weten?');">{{ __('messages.cancel') }}
                                    </x-link>
                                @endif
                            </x-column-table>
                        </x-row-table>
                    @endforeach
                </x-data-table>
                {{ $items->links() }}
            </div>
        @else
            <p class="mb-2 text-gray-600">{{ __('job.none') }}</p>
        @endif

        @cannot('hasAPI')
            <p class="mb-2 text-red-600 font-semibold">{{ __('messages.noapi') }}</p>
        @elsecannot('noActiveJobs', 'App\\Models\Job')
            <p class="mb-2 text-red-600 font-semibold">{{ __('job.busy') }}</p>
        @else
            <p class="mb-2 text-green-700 font-semibold">{{ __('job.ready') }}</p>
            <x-button-blue href="{{ route('codeanalyzer.create.step.one') }}"
                type="link">{{ __('job.create') }}</x-button-blue>
        @endcan
    </x-page-container>

</x-app-layout>
