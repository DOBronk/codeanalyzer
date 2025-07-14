    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xs sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div {{ $attributes->merge(['class' => 'container mx-auto px-4 py-6']) }}>
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </div>
