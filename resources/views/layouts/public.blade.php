<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'IPL Auction') }}</title>

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
                            IPL AUCTION
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

        <footer class="border-t border-white/10 py-8 text-center text-gray-500 text-sm bg-[#0B0F19]">
            &copy; {{ date('Y') }} IPL Auction Simulator. All rights reserved.
        </footer>

        @livewireScripts
    </body>
</html>
