<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @livewireScripts
    @stack('scripts')
</head>

<body class="font-sans antialiased text-gray-900">
    <div
        class="flex flex-col items-center min-h-screen pt-6 bg-gray-100 sm:justify-center sm:pt-0 {{ request()->routeIs('login') ? 'bg-login' : (request()->routeIs('register') ? 'bg-register' : 'bg-general') }}">

        <div
            class="w-11/12 px-6 py-4 mt-6 overflow-hidden shadow-md sm:max-w-md md:max-w-lg bg-transparent/50 sm:rounded-lg">
            {{ $slot }}
        </div>
    </div>
</body>

</html>