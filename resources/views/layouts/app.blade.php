@props(['loadVue' => null, 'page' => null])
<!DOCTYPE html>
<html data-theme="dark" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @empty($inertia)<meta name="csrf-token" content="{{ csrf_token() }}">@endempty

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @isset($loadVue) 
        <!-- Alleen vue, inertia en alle andere vue modules laden wanneer nodig --> 
            @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/inertiavue.js']) 
            @inertiaHead 
        @else
        <!-- Scripts -->
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endisset

    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow-sm">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
