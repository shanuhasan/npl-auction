<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ setting('app_name', 'Naugawan Premier League') }}</title>
        <link rel="icon" href="{{ setting('favicon') ? asset('storage/' . setting('favicon')) : asset('favicon.ico') }}">

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="theme-app min-h-screen flex flex-col relative overflow-x-hidden">
        
        <!-- Navigation / Header -->
        <header class="relative z-10 w-full p-6 flex justify-end">
            @if (Route::has('login'))
                <div class="flex gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-secondary">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn-secondary">Log in</a>
                        @if (Route::has('register'))
                            <!-- <a href="{{ route('register') }}" class="text-sm font-semibold text-ink-soft hover:text-ink transition-colors duration-200">Register</a> -->
                        @endif
                    @endauth
                </div>
            @endif
        </header>

        <!-- Main Content -->
        <main class="relative z-10 flex-1 flex flex-col items-center justify-center p-6 text-center">
            
            <div class="card p-10 md:p-16 max-w-3xl w-full">
                <div class="mb-6 inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-brand-soft text-sm font-medium text-brand">
                    <span class="relative flex h-3 w-3">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-3 w-3 bg-brand"></span>
                    </span>
                    {{ setting('season', 'Season ' . date('Y')) }}
                </div>
                
                <h1 class="text-4xl md:text-5xl font-semibold tracking-tight mb-5 text-ink">
                    <span>{{ setting('app_name', 'Naugawan Premier League') }}</span>
                </h1>
                
                <p class="text-base md:text-lg text-muted mb-10 max-w-xl mx-auto">
                    Experience the thrill of the ultimate cricket championship. Witness the best players battle it out for glory.
                </p>

                @php
                    $auction = \App\Models\Auction::whereIn('status', ['live', 'paused'])->first() ?? \App\Models\Auction::where('status', 'completed')->latest()->first();
                @endphp

                <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
                    @if($auction)
                        <a href="{{ route('auction.live', $auction->guid) }}" class="btn-primary px-6 py-3">
                            <span class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Live auction
                            </span>
                        </a>
                    @else
                        <button disabled class="btn-secondary px-6 py-3" >
                            <span class="flex items-center gap-2">
                                No active auction
                            </span>
                        </button>
                    @endif
                    
                    <a href="{{ route('home') }}" class="btn-secondary px-6 py-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Home page
                    </a>
                </div>
            </div>
        </main>
        
        <footer class="relative z-10 w-full py-6 mt-auto border-t border-line bg-surface">
            <div class="flex flex-wrap items-center justify-center gap-x-3 gap-y-2 text-sm text-muted text-center px-4">
                <span>&copy; {{ date('Y') }} {{ setting('app_name', 'Naugawan Premier League') }}. All rights reserved.</span>
                
                @if(setting('developer_name'))
                    <span>|</span>
                    <span>
                        Developed by <a href="{{ setting('developer_url', '#') }}" target="_blank" class="font-medium text-ink-soft hover:text-brand">{{ setting('developer_name') }}</a>
                    </span>
                @endif
            </div>
        </footer>
    </body>
</html>
