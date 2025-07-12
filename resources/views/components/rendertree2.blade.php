@props(['tree', 'namecheckbox'])

<ul id="treeView" class="list-disc pl-5 space-y-1">
    @foreach ($tree as $key => $subTree)
        @if (is_array($subTree))
            <li>
                <details
                    class="group [&_summary::-webkit-details-marker]:hidden cursor-pointer rounded-md border border-gray-300 p-2 hover:bg-gray-100">
                    <summary class="flex items-center justify-between font-medium text-gray-900">
                        <span>{{ $key }}</span>
                        <svg class="w-4 h-4 text-gray-500 group-open:rotate-90 transition-transform" fill="none"
                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </summary>
                    <x-rendertree2 :tree="$subTree" :namecheckbox="$namecheckbox" />
                </details>
            </li>
        @else
            <li class="flex items-center space-x-2">
                <input type='checkbox' value="{{ $subTree }}" name="{{ $namecheckbox }}"
                    class="form-checkbox h-4 w-4 text-blue-600" />
                <span class="select-none">{{ $key }}</span>
            </li>
        @endif
    @endforeach
</ul>
