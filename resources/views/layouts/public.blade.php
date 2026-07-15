<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ setting('app_name', config('app.name', 'Naugawan Premier League (NPLT20)')) }} | Naugawan Premier League</title>
        <link rel="icon" href="{{ setting('favicon') ? asset('storage/' . setting('favicon')) : asset('favicon.ico') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        
        <style>
            body { font-family: 'Poppins', sans-serif; background-color: #0B0F19; color: #ffffff; }
        </style>
    </head>
    <body class="antialiased min-h-screen flex flex-col">
        
        <!-- Navbar -->
        <nav class="bg-[#141B2D]/90 backdrop-blur-md border-b border-white/10 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <div class="flex items-center gap-8">
                        <a href="/" class="text-2xl font-black text-[#FFC800] tracking-wider uppercase">
                            {{ setting('app_name', 'Naugawan Premier League (NPLT20)') }}
                        </a>
                        <div class="hidden md:flex space-x-6">
                            <a href="{{ route('public.teams') }}" class="text-gray-300 hover:text-white font-semibold uppercase tracking-wider {{ request()->routeIs('public.teams*') ? 'text-[#FFC800]' : '' }}">Teams</a>
                            <a href="{{ route('public.players') }}" class="text-gray-300 hover:text-white font-semibold uppercase tracking-wider {{ request()->routeIs('public.players') ? 'text-[#FFC800]' : '' }}">Players</a>
                        </div>
                    </div>
                    <div class="flex items-center">
                        @auth
                            <a href="{{ route('dashboard') }}" class="text-sm text-gray-300 hover:text-white font-semibold">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm text-gray-300 hover:text-white font-semibold">Log in</a>
                        @endauth
                    </div>
                </div>
            </div>
            
            <!-- Mobile Menu -->
            <div class="md:hidden flex bg-[#141B2D] border-t border-white/5">
                <a href="{{ route('public.teams') }}" class="flex-1 text-center py-3 text-sm font-semibold uppercase tracking-wider {{ request()->routeIs('public.teams*') ? 'text-[#FFC800] bg-white/5' : 'text-gray-400' }}">Teams</a>
                <a href="{{ route('public.players') }}" class="flex-1 text-center py-3 text-sm font-semibold uppercase tracking-wider border-l border-white/5 {{ request()->routeIs('public.players') ? 'text-[#FFC800] bg-white/5' : 'text-gray-400' }}">Players</a>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="flex-1 relative">
            {{ $slot }}
        </main>

        <footer class="bg-card-bg border-t border-gray-800 py-6 text-center text-sm text-gray-500 font-medium">
            &copy; {{ date('Y') }} {{ setting('app_name', 'Naugawan Premier League (NPLT20)') }}. All rights reserved.
            @if(setting('developer_name'))
                <span class="mx-1">|</span>
                Developed by <a href="{{ setting('developer_url', '#') }}" target="_blank" class="text-accent-gold hover:underline">{{ setting('developer_name') }}</a>
            @endif
        </footer>

        @livewireScripts
    </body>
</html>
