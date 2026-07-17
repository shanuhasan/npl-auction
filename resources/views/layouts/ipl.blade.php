<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ setting('app_name', 'Naugawan Premier League') }} | Naugawan Premier League {{ isset($title) ? ' | ' . $title : '' }}</title>
    <link rel="icon" href="{{ setting('favicon') ? asset('storage/' . setting('favicon')) : asset('favicon.ico') }}">
    
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
            @php
                $facebook = setting('facebook');
                $instagram = setting('instagram');
                $twitter = setting('twitter');
                $youtube = setting('youtube');
            @endphp
            @if($facebook || $instagram || $twitter || $youtube)
                <span>Follow Us:</span>
                <div class="flex space-x-3">
                    @if($facebook)
                        <a href="{{ $facebook }}" target="_blank" class="text-gray-500 hover:text-white transition-colors"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd"/></svg></a>
                    @endif
                    @if($instagram)
                        <a href="{{ $instagram }}" target="_blank" class="text-gray-500 hover:text-white transition-colors"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd"/></svg></a>
                    @endif
                    @if($twitter)
                        <a href="{{ $twitter }}" target="_blank" class="text-gray-500 hover:text-white transition-colors"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"/></svg></a>
                    @endif
                    @if($youtube)
                        <a href="{{ $youtube }}" target="_blank" class="text-gray-500 hover:text-white transition-colors"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M19.812 5.418c.861.23 1.538.907 1.768 1.768C21.998 8.746 22 12 22 12s0 3.255-.418 4.814a2.504 2.504 0 0 1-1.768 1.768c-1.56.419-7.814.419-7.814.419s-6.255 0-7.814-.419a2.505 2.505 0 0 1-1.768-1.768C2 15.255 2 12 2 12s0-3.255.417-4.814a2.507 2.507 0 0 1 1.768-1.768C5.744 5 11.998 5 11.998 5s6.255 0 7.814.418ZM15.194 12 10 15V9l5.194 3Z" clip-rule="evenodd"/></svg></a>
                    @endif
                </div>
            @endif
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
                
                @if(isset($globalPages) && $globalPages->count() > 0)
                    @foreach($globalPages as $gPage)
                        <a href="{{ route('public.page', $gPage->slug) }}" class="h-full flex items-center px-4 font-bold text-sm tracking-wide uppercase transition-colors border-b-4 {{ request()->is('pages/' . $gPage->slug) ? 'text-[#FFC800] border-[#FFC800]' : 'text-white border-transparent hover:text-[#FFC800] hover:border-[#FFC800]' }}">{{ $gPage->title }}</a>
                    @endforeach
                @endif
                <a href="{{ route('public.contact') }}" class="h-full flex items-center px-4 font-bold text-sm tracking-wide uppercase transition-colors border-b-4 {{ request()->routeIs('public.contact') ? 'text-[#FFC800] border-[#FFC800]' : 'text-white border-transparent hover:text-[#FFC800] hover:border-[#FFC800]' }}">Contact Us</a>
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
            
            @if(isset($globalPages) && $globalPages->count() > 0)
                @foreach($globalPages as $gPage)
                    <a href="{{ route('public.page', $gPage->slug) }}" class="block px-6 py-3 font-bold text-sm tracking-wide uppercase {{ request()->is('pages/' . $gPage->slug) ? 'text-[#FFC800]' : 'text-white' }} border-b border-gray-800">{{ $gPage->title }}</a>
                @endforeach
            @endif
            <a href="{{ route('public.contact') }}" class="block px-6 py-3 font-bold text-sm tracking-wide uppercase {{ request()->routeIs('public.contact') ? 'text-[#FFC800]' : 'text-white' }} border-b border-gray-800">Contact Us</a>
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
                    @endif
                    <div class="text-3xl heading-font text-white uppercase mb-4 tracking-wide">{{ setting('app_name', 'Naugawan Premier League') }}</div>
                    <p class="text-gray-400 text-sm leading-relaxed">The official home of the premier league. Get live scores, news, and exclusive videos directly from the field.</p>
                </div>
                <div>
                    <h4 class="font-bold uppercase mb-4 text-[#FFC800]">Quick Links</h4>
                    <ul class="space-y-3 text-sm text-gray-400">
                        <li><a href="#" class="hover:text-white">Home</a></li>
                        <li><a href="{{ route('public.teams') }}" class="hover:text-[#FFC800] transition-colors">Teams</a></li>
                        <li><a href="{{ route('public.players') }}" class="hover:text-[#FFC800] transition-colors">Players</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold uppercase mb-4 text-[#FFC800]">Guidelines & Pages</h4>
                    <ul class="space-y-3 text-sm text-gray-400">
                        @if(isset($globalPages) && $globalPages->count() > 0)
                            @foreach($globalPages as $gPage)
                                <li><a href="{{ route('public.page', $gPage->slug) }}" class="hover:text-white transition-colors">{{ $gPage->title }}</a></li>
                            @endforeach
                        @endif
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold uppercase mb-4 text-[#FFC800]">Connect</h4>
                    <p class="text-sm text-gray-400 mb-4">Follow us on social media for the latest updates.</p>
                    @php
                        $facebook = setting('facebook');
                        $instagram = setting('instagram');
                        $twitter = setting('twitter');
                        $youtube = setting('youtube');
                    @endphp
                    @if($facebook || $instagram || $twitter || $youtube)
                    <div class="flex space-x-3 mt-4">
                        @if($facebook)
                        <a href="{{ $facebook }}" target="_blank" class="w-10 h-10 bg-gray-800 rounded flex items-center justify-center group hover:bg-[#FFC800] transition-colors cursor-pointer text-gray-400 hover:text-black">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd"/></svg>
                        </a>
                        @endif
                        @if($instagram)
                        <a href="{{ $instagram }}" target="_blank" class="w-10 h-10 bg-gray-800 rounded flex items-center justify-center group hover:bg-[#FFC800] transition-colors cursor-pointer text-gray-400 hover:text-black">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd"/></svg>
                        </a>
                        @endif
                        @if($twitter)
                        <a href="{{ $twitter }}" target="_blank" class="w-10 h-10 bg-gray-800 rounded flex items-center justify-center group hover:bg-[#FFC800] transition-colors cursor-pointer text-gray-400 hover:text-black">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"/></svg>
                        </a>
                        @endif
                        @if($youtube)
                        <a href="{{ $youtube }}" target="_blank" class="w-10 h-10 bg-gray-800 rounded flex items-center justify-center group hover:bg-[#FFC800] transition-colors cursor-pointer text-gray-400 hover:text-black">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M19.812 5.418c.861.23 1.538.907 1.768 1.768C21.998 8.746 22 12 22 12s0 3.255-.418 4.814a2.504 2.504 0 0 1-1.768 1.768c-1.56.419-7.814.419-7.814.419s-6.255 0-7.814-.419a2.505 2.505 0 0 1-1.768-1.768C2 15.255 2 12 2 12s0-3.255.417-4.814a2.507 2.507 0 0 1 1.768-1.768C5.744 5 11.998 5 11.998 5s6.255 0 7.814.418ZM15.194 12 10 15V9l5.194 3Z" clip-rule="evenodd"/></svg>
                        </a>
                        @endif
                    </div>
                    @endif
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
