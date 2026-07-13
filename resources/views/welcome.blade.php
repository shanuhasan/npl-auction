<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ setting('app_name', 'Naugawan Premier League') }}</title>
        <link rel="icon" href="{{ setting('favicon') ? asset('storage/' . setting('favicon')) : asset('favicon.ico') }}">
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body {
                font-family: 'Poppins', sans-serif;
            }
            .glass-card {
                background: #141B2D;
                border: 1px solid rgba(255, 200, 0, 0.2);
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
            }
            .btn-primary {
                background: linear-gradient(135deg, #FFC800 0%, #D4A000 100%);
                color: #0B0F19;
            }
            .btn-primary:hover {
                background: linear-gradient(135deg, #FFE040 0%, #FFC800 100%);
                box-shadow: 0 0 20px rgba(255, 200, 0, 0.4);
            }
            .btn-outline {
                border: 2px solid #FFC800;
                color: #FFC800;
            }
            .btn-outline:hover {
                background: rgba(255, 200, 0, 0.1);
            }
            .text-gold {
                color: #FFC800;
            }
        </style>
    </head>
    <body class="antialiased bg-[#0B0F19] text-white min-h-screen flex flex-col relative overflow-hidden">
        
        <!-- Navigation / Header -->
        <header class="relative z-10 w-full p-6 flex justify-end">
            @if (Route::has('login'))
                <div class="flex gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-sm font-semibold text-gray-300 hover:text-white transition-colors duration-200">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-300 hover:text-white transition-colors duration-200">Log in</a>
                        @if (Route::has('register'))
                            <!-- <a href="{{ route('register') }}" class="text-sm font-semibold text-gray-300 hover:text-white transition-colors duration-200">Register</a> -->
                        @endif
                    @endauth
                </div>
            @endif
        </header>

        <!-- Main Content -->
        <main class="relative z-10 flex-1 flex flex-col items-center justify-center p-6 text-center">
            
            <div class="glass-card p-10 md:p-16 rounded-2xl max-w-4xl w-full transform transition-all duration-500 hover:scale-105">
                <div class="mb-6 inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#FFC800]/10 border border-[#FFC800]/30 text-sm font-bold text-[#FFC800]">
                    <span class="relative flex h-3 w-3">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#FFC800] opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-3 w-3 bg-[#FFC800]"></span>
                    </span>
                    {{ setting('season', 'Season ' . date('Y')) }}
                </div>
                
                <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight mb-6 text-white uppercase" style="text-shadow: 0 4px 20px rgba(255,200,0,0.3);">
                    <span class="text-gold">{{ setting('app_name', 'Naugawan Premier League') }}</span>
                </h1>
                
                <p class="text-lg md:text-xl text-gray-400 mb-10 max-w-2xl mx-auto">
                    Experience the thrill of the ultimate cricket championship. Witness the best players battle it out for glory.
                </p>

                @php
                    $auction = \App\Models\Auction::whereIn('status', ['live', 'paused'])->first() ?? \App\Models\Auction::where('status', 'completed')->latest()->first();
                @endphp

                <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
                    @if($auction)
                        <a href="{{ route('auction.live', $auction->guid) }}" class="btn-primary relative inline-flex items-center justify-center px-8 py-4 font-extrabold transition-all duration-300 rounded-full focus:outline-none hover:-translate-y-1">
                            <span class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                LIVE AUCTION
                            </span>
                        </a>
                    @else
                        <button disabled class="group relative inline-flex items-center justify-center px-8 py-4 font-bold text-gray-500 transition-all duration-200 bg-gray-800 border border-gray-700 rounded-full cursor-not-allowed">
                            <span class="flex items-center gap-2">
                                NO ACTIVE AUCTION
                            </span>
                        </button>
                    @endif
                    
                    <a href="{{ route('public.teams') }}" class="btn-outline relative inline-flex items-center justify-center px-8 py-4 font-extrabold transition-all duration-300 rounded-full focus:outline-none hover:-translate-y-1">
                        VIEW TEAMS
                    </a>
                </div>
            </div>
        </main>
        
        <footer class="relative z-10 w-full py-6 mt-auto text-center text-sm text-gray-500 border-t border-white/5 bg-[#141B2D]/50 backdrop-blur-md">
            &copy; {{ date('Y') }} {{ setting('app_name', 'Naugawan Premier League') }}. All rights reserved.
            @if(setting('developer_name'))
                <span class="mx-1">|</span>
                Developed by <a href="{{ setting('developer_url', '#') }}" target="_blank" class="text-[#FFC800] hover:underline">{{ setting('developer_name') }}</a>
            @endif
            <span class="mx-1">|</span>
            <span class="font-semibold text-gray-400">Total Visitors: {{ \App\Models\Visitor::count() }}</span>
        </footer>
    </body>
</html>
