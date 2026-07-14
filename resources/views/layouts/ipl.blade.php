<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ setting('app_name', 'Naugawan Premier League') }} | Naugawan Premier League</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=bebas-neue:400|roboto:400,500,700,900&display=swap" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Roboto', sans-serif; }
        h1, h2, h3, .heading-font { font-family: 'Bebas Neue', sans-serif; letter-spacing: 1px; }
        .ipl-blue { background-color: #0B0F19; }
        .ipl-gradient { background: linear-gradient(90deg, #141B2D 0%, #0B0F19 100%); }
        .nav-gradient { background: linear-gradient(180deg, #141B2D 0%, #0B0F19 100%); }
        .ipl-orange { color: #FFC800; }
        .bg-ipl-orange { background-color: #FFC800; }
        .bg-ipl-orange-hover:hover { background-color: #D4A000; }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #0B0F19; }
        ::-webkit-scrollbar-thumb { background: #141B2D; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #1A233A; }
    </style>
</head>
<body class="bg-[#0B0F19] text-white antialiased overflow-x-hidden">

    <!-- Top Bar (BCCI/WPL links) -->
    <div class="bg-[#0B0F19] text-gray-400 text-xs font-medium py-1.5 px-4 md:px-8 flex justify-between items-center hidden sm:flex">
        <div class="flex space-x-4">
            <!-- <a href="#" class="hover:text-white transition-colors">BCCI.TV</a>
            <span>|</span>
            <a href="#" class="hover:text-white transition-colors">WOMEN'S PREMIER LEAGUE</a> -->
        </div>
        <div class="flex space-x-4 items-center">
            <span>Follow Us:</span>
            <!-- Social Icons Placeholders -->
            <div class="flex space-x-2">
                <div class="w-4 h-4 bg-gray-500 rounded-full hover:bg-white cursor-pointer transition-colors"></div>
                <div class="w-4 h-4 bg-gray-500 rounded-full hover:bg-white cursor-pointer transition-colors"></div>
                <div class="w-4 h-4 bg-gray-500 rounded-full hover:bg-white cursor-pointer transition-colors"></div>
            </div>
        </div>
    </div>

    <!-- Main Navigation -->
    <nav class="nav-gradient sticky top-0 z-50 border-b border-gray-700 shadow-xl">
        <div class="max-w-[1400px] mx-auto px-4 md:px-8 flex justify-between items-center h-20">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center space-x-2 group">
                @if(setting('logo'))
                    <img src="{{ asset('storage/' . setting('logo')) }}" alt="{{ setting('app_name', 'NPL') }}" class="h-16 w-auto max-w-[150px] object-contain transform group-hover:scale-105 transition-transform drop-shadow-xl">
                @else
                    <!-- Mock Logo -->
                    <div class="w-12 h-14 bg-gradient-to-br from-[#FFC800] to-[#D4A000] rounded-b-xl flex items-center justify-center transform group-hover:scale-105 transition-transform shadow-lg border border-[#FFC800]/30">
                        <span class="text-black font-black text-xl heading-font transform -skew-x-6">NPL</span>
                    </div>
                @endif
                <div class="flex flex-col ml-2">
                    <span class="text-white heading-font text-2xl leading-none uppercase">{{ setting('app_name', 'Naugawan Premier League') }}</span>
                </div>
            </a>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center space-x-1 h-full">
                <a href="{{ route('home') }}" class="h-full flex items-center px-4 font-bold text-sm tracking-wide uppercase transition-colors border-b-4 {{ request()->routeIs('home') ? 'text-[#FFC800] border-[#FFC800]' : 'text-white border-transparent hover:text-[#FFC800] hover:border-[#FFC800]' }}">Home</a>
                <a href="{{ route('public.teams') }}" class="h-full flex items-center px-4 font-bold text-sm tracking-wide uppercase transition-colors border-b-4 {{ request()->routeIs('public.teams*') ? 'text-[#FFC800] border-[#FFC800]' : 'text-white border-transparent hover:text-[#FFC800] hover:border-[#FFC800]' }}">Teams</a>
                <a href="{{ route('public.players') }}" class="h-full flex items-center px-4 font-bold text-sm tracking-wide uppercase transition-colors border-b-4 {{ request()->routeIs('public.players*') ? 'text-[#FFC800] border-[#FFC800]' : 'text-white border-transparent hover:text-[#FFC800] hover:border-[#FFC800]' }}">Players</a>
                <a href="#" class="h-full flex items-center px-4 font-bold text-sm tracking-wide uppercase transition-colors border-b-4 text-white border-transparent hover:text-[#FFC800] hover:border-[#FFC800]">Matches</a>
                <a href="#" class="h-full flex items-center px-4 font-bold text-sm tracking-wide uppercase transition-colors border-b-4 text-white border-transparent hover:text-[#FFC800] hover:border-[#FFC800]">Videos</a>
                <a href="#" class="h-full flex items-center px-4 font-bold text-sm tracking-wide uppercase transition-colors border-b-4 text-white border-transparent hover:text-[#FFC800] hover:border-[#FFC800]">Stats</a>
                <a href="#" class="h-full flex items-center px-4 font-bold text-sm tracking-wide uppercase transition-colors border-b-4 text-white border-transparent hover:text-[#FFC800] hover:border-[#FFC800]">Point Table</a>
            </div>

            <!-- Right Actions -->
            <div class="flex items-center space-x-4">
                @php
                    $auction = \App\Models\Auction::whereIn('status', ['live', 'paused'])->first();
                @endphp
                @if($auction)
                    <a href="{{ route('auction.live', $auction->guid) }}" class="inline-flex items-center bg-red-600 text-white px-3 py-1.5 md:px-4 md:py-2 font-bold text-xs md:text-sm uppercase rounded hover:bg-red-700 transition-colors whitespace-nowrap shadow-lg">
                        <span class="w-2 h-2 bg-white rounded-full mr-2 animate-pulse"></span>
                        Live Auction
                    </a>
                @endif
                
                @auth
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="hidden md:flex items-center text-white hover:text-[#FFC800] font-bold text-sm tracking-wide uppercase transition-colors">
                            Dashboard
                        </a>
                    @elseif(auth()->user()->role === 'team_owner')
                        <a href="{{ route('team_owner.my_team') }}" class="hidden md:flex items-center text-white hover:text-[#FFC800] font-bold text-sm tracking-wide uppercase transition-colors">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="hidden md:flex items-center text-white hover:text-[#FFC800] font-bold text-sm tracking-wide uppercase transition-colors">
                            Dashboard
                        </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="hidden md:flex m-0 p-0 items-center">
                        @csrf
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="text-red-500 hover:text-red-400 font-bold text-sm tracking-wide uppercase transition-colors">
                            Logout
                        </a>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="hidden md:flex items-center text-white hover:text-[#FFC800] font-bold text-sm tracking-wide uppercase transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" /></svg>
                        Login
                    </a>
                @endauth
                <button id="mobile-menu-button" class="md:hidden text-white hover:text-[#FFC800] focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                </button>
            </div>
        </div>
        
        <!-- Mobile Menu (Hidden by default) -->
        <div id="mobile-menu" class="hidden md:hidden bg-[#0B0F19] border-t border-gray-700 pb-4">
            <a href="{{ route('home') }}" class="block px-6 py-3 font-bold text-sm tracking-wide uppercase {{ request()->routeIs('home') ? 'text-[#FFC800]' : 'text-white' }} border-b border-gray-800">Home</a>
            <a href="{{ route('public.teams') }}" class="block px-6 py-3 font-bold text-sm tracking-wide uppercase {{ request()->routeIs('public.teams*') ? 'text-[#FFC800]' : 'text-white' }} border-b border-gray-800">Teams</a>
            <a href="{{ route('public.players') }}" class="block px-6 py-3 font-bold text-sm tracking-wide uppercase {{ request()->routeIs('public.players*') ? 'text-[#FFC800]' : 'text-white' }} border-b border-gray-800">Players</a>
            <a href="#" class="block px-6 py-3 font-bold text-sm tracking-wide uppercase text-white border-b border-gray-800">Matches</a>
            <a href="#" class="block px-6 py-3 font-bold text-sm tracking-wide uppercase text-white border-b border-gray-800">Videos</a>
            <a href="#" class="block px-6 py-3 font-bold text-sm tracking-wide uppercase text-white border-b border-gray-800">Stats</a>
            <a href="#" class="block px-6 py-3 font-bold text-sm tracking-wide uppercase text-white border-b border-gray-800">Point Table</a>
            @if($auction)
                <a href="{{ route('auction.live', $auction->guid) }}" class="block px-6 py-3 font-bold text-sm tracking-wide uppercase text-red-500 border-b border-gray-800">Live Auction</a>
            @endif
            @auth
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="block px-6 py-3 font-bold text-sm tracking-wide uppercase text-[#FFC800] border-b border-gray-800">Dashboard</a>
                @elseif(auth()->user()->role === 'team_owner')
                    <a href="{{ route('team_owner.my_team') }}" class="block px-6 py-3 font-bold text-sm tracking-wide uppercase text-[#FFC800] border-b border-gray-800">My Team</a>
                @else
                    <a href="{{ route('dashboard') }}" class="block px-6 py-3 font-bold text-sm tracking-wide uppercase text-[#FFC800] border-b border-gray-800">Dashboard</a>
                @endif
                <form method="POST" action="{{ route('logout') }}" class="block m-0 p-0">
                    @csrf
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="block px-6 py-3 font-bold text-sm tracking-wide uppercase text-red-500 hover:text-red-400 border-b border-gray-800">
                        Logout
                    </a>
                </form>
            @else
                <a href="{{ route('login') }}" class="block px-6 py-3 font-bold text-sm tracking-wide uppercase text-white border-b border-gray-800">Login</a>
            @endauth
        </div>
    </nav>

    <!-- Mobile Menu Script -->
    <script>
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
            } else {
                menu.classList.add('hidden');
            }
        });
    </script>

    <main>
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-[#0B0F19] text-white pt-20 pb-10 border-t-4 border-[#FFC800]">
        <div class="max-w-[1400px] mx-auto px-4 md:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 lg:gap-16 mb-16 pt-8">
                <div class="pr-0 lg:pr-4">
                    @if(setting('logo'))
                        <img src="{{ asset('storage/' . setting('logo')) }}" alt="{{ setting('app_name', 'NPLT20') }}" class="h-24 w-auto max-w-[200px] object-contain mb-6 drop-shadow-xl">
                    @else
                        <div class="w-16 h-20 bg-gradient-to-br from-[#FFC800] to-[#D4A000] rounded-b-xl flex items-center justify-center mb-6 shadow-lg border border-[#FFC800]/30">
                            <span class="text-black font-black text-2xl heading-font transform -skew-x-6">NPL</span>
                        </div>
                    @endif
                    <div class="text-3xl heading-font text-white uppercase mb-4 tracking-wide">{{ setting('app_name', 'Naugawan Premier League') }}</div>
                    <p class="text-gray-400 text-sm leading-relaxed">The official home of the premier league. Get live scores, news, and exclusive videos directly from the field.</p>
                </div>
                <div>
                    <h4 class="font-bold uppercase mb-4 text-[#FFC800]">Quick Links</h4>
                    <ul class="space-y-3 text-sm text-gray-400">
                        <li><a href="#" class="hover:text-white">Home</a></li>
                        <li><a href="#" class="hover:text-white">About Us</a></li>
                        <li><a href="{{ route('public.teams') }}" class="hover:text-[#FFC800] transition-colors">Teams</a></li>
                        <li><a href="{{ route('public.players') }}" class="hover:text-[#FFC800] transition-colors">Players</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold uppercase mb-4 text-[#FFC800]">Guidelines</h4>
                    <ul class="space-y-3 text-sm text-gray-400">
                        <li><a href="#" class="hover:text-white">Code of Conduct</a></li>
                        <li><a href="#" class="hover:text-white">Privacy Policy</a></li>
                        <li><a href="#" class="hover:text-white">Terms of Use</a></li>
                        <li><a href="#" class="hover:text-white">Contact Us</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold uppercase mb-4 text-[#FFC800]">Connect</h4>
                    <p class="text-sm text-gray-400 mb-4">Follow us on social media for the latest updates.</p>
                    <div class="flex space-x-5">
                        <div class="w-10 h-10 bg-gray-800 rounded flex items-center justify-center group hover:bg-[#FFC800] transition-colors cursor-pointer">
                            <span class="group-hover:text-black font-bold">FB</span>
                        </div>
                        <div class="w-10 h-10 bg-gray-800 rounded flex items-center justify-center group hover:bg-[#FFC800] transition-colors cursor-pointer">
                            <span class="group-hover:text-black font-bold">TW</span>
                        </div>
                        <div class="w-10 h-10 bg-gray-800 rounded flex items-center justify-center group hover:bg-[#FFC800] transition-colors cursor-pointer">
                            <span class="group-hover:text-black font-bold">IG</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-white/10 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-500 text-xs text-center md:text-left">&copy; {{ date('Y') }} {{ setting('app_name', 'Naugawan Premier League') }}. All Rights Reserved.</p>
                @if(setting('developer_name'))
                    <p class="text-gray-500 text-xs mt-2 md:mt-0">
                        Developed by <a href="{{ setting('developer_url', '#') }}" target="_blank" class="text-[#FFC800] hover:underline">{{ setting('developer_name') }}</a>
                    </p>
                @endif
            </div>
        </div>
    </footer>

</body>
</html>
