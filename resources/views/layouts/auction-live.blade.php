<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ setting('app_name', config('app.name', 'Naugawan Premier League (NPLT20)')) }} - Live</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        <script src="{{ asset('js/confetti.browser.min.js') }}"></script>
    </head>
    <body class="theme-app min-h-screen flex flex-col overflow-x-hidden">
        {{ $slot }}

        @livewireScripts
    </body>
</html>
