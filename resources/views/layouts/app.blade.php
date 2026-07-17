<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ setting('app_name', config('app.name', 'Naugawan Premier League (NPLT20)')) }} | Naugawan Premier League</title>
        <link rel="icon" href="{{ setting('favicon') ? asset('storage/' . setting('favicon')) : asset('favicon.ico') }}">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Quill Editor -->
        <link href="{{ asset('css/quill.snow.css') }}" rel="stylesheet">
        <script src="{{ asset('js/quill.min.js') }}"></script>
    </head>
    <body class="font-inter antialiased bg-primary-bg text-white">
        <div class="flex h-screen overflow-hidden">
            <!-- Sidebar -->
            <aside class="w-64 bg-card-bg shadow-lg border-r border-gray-800 hidden md:flex flex-col">
                <div class="p-6 border-b border-gray-800 flex items-center gap-3">
                    @if(setting('logo'))
                        <img src="{{ asset('storage/' . setting('logo')) }}" alt="Logo" class="h-10 w-10 object-contain">
                    @endif
                    <h2 class="text-xl font-poppins font-bold text-accent-gold">{{ setting('app_name', 'NPLT20') }}</h2>
                </div>
                <nav class="flex-1 mt-6 space-y-1">
                    @auth
                        @if(in_array(auth()->user()->role, ['admin', 'sub_admin']))
                            <a href="{{ route('admin.dashboard') }}" class="block px-6 py-3 transition {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800 border-l-4 border-accent-red text-accent-red font-bold' : 'hover:bg-gray-800 text-accent-red font-semibold' }}">Dashboard (Admin)</a>
                            
                            @if(auth()->user()->hasPermission('manage_users'))
                            <a href="{{ route('admin.users') }}" class="block px-6 py-3 transition {{ request()->routeIs('admin.users*') ? 'bg-gray-800 border-l-4 border-accent-gold text-white font-semibold' : 'hover:bg-gray-800' }}">Manage Users</a>
                            @endif
                            
                            @if(auth()->user()->hasPermission('manage_teams'))
                            <a href="{{ route('admin.teams') }}" class="block px-6 py-3 transition {{ request()->routeIs('admin.teams*') ? 'bg-gray-800 border-l-4 border-accent-gold text-white font-semibold' : 'hover:bg-gray-800' }}">Manage Teams</a>
                            @endif
                            
                            @if(auth()->user()->hasPermission('manage_players'))
                            <a href="{{ route('admin.players') }}" class="block px-6 py-3 transition {{ request()->routeIs('admin.players*') ? 'bg-gray-800 border-l-4 border-accent-gold text-white font-semibold' : 'hover:bg-gray-800' }}">Manage Players</a>
                            @endif
                            
                            @if(auth()->user()->hasPermission('manage_auctions'))
                            <a href="{{ route('admin.auctions') }}" class="block px-6 py-3 transition {{ request()->routeIs('admin.auctions*') || request()->routeIs('admin.auction.*') ? 'bg-gray-800 border-l-4 border-accent-gold text-white font-semibold' : 'hover:bg-gray-800' }}">Manage Auctions</a>
                            @endif
                            
                            @if(auth()->user()->hasPermission('view_analytics'))
                            <a href="{{ route('admin.analytics') }}" class="block px-6 py-3 transition {{ request()->routeIs('admin.analytics') ? 'bg-gray-800 border-l-4 border-accent-gold text-white font-semibold' : 'hover:bg-gray-800' }}">Analytics</a>
                            @endif
                            
                            @if(auth()->user()->hasPermission('manage_banners'))
                            <a href="{{ route('admin.banners') }}" class="block px-6 py-3 transition {{ request()->routeIs('admin.banners') ? 'bg-gray-800 border-l-4 border-accent-gold text-white font-semibold' : 'hover:bg-gray-800' }}">Manage Banners</a>
                            @endif
                            
                            @if(auth()->user()->hasPermission('manage_core_committees'))
                            <a href="{{ route('admin.core-committees') }}" class="block px-6 py-3 transition {{ request()->routeIs('admin.core-committees') ? 'bg-gray-800 border-l-4 border-accent-gold text-white font-semibold' : 'hover:bg-gray-800' }}">Core Committee</a>
                            <a href="{{ route('admin.guests') }}" class="block px-6 py-3 transition {{ request()->routeIs('admin.guests') ? 'bg-gray-800 border-l-4 border-accent-gold text-white font-semibold' : 'hover:bg-gray-800' }}">Manage Guests</a>
                            @endif
                            
                            @if(auth()->user()->hasPermission('manage_sponsors'))
                            <a href="{{ route('admin.sponsors') }}" class="block px-6 py-3 transition {{ request()->routeIs('admin.sponsors') ? 'bg-gray-800 border-l-4 border-accent-gold text-white font-semibold' : 'hover:bg-gray-800' }}">Manage Sponsors</a>
                            @endif
                            
                            @if(auth()->user()->hasPermission('manage_pages'))
                            <a href="{{ route('admin.pages.index') }}" class="block px-6 py-3 transition {{ request()->routeIs('admin.pages*') ? 'bg-gray-800 border-l-4 border-accent-gold text-white font-semibold' : 'hover:bg-gray-800' }}">Manage Pages</a>
                            @endif
                            
                            @if(auth()->user()->hasPermission('manage_gallery'))
                            <a href="{{ route('admin.gallery') }}" class="block px-6 py-3 transition {{ request()->routeIs('admin.gallery*') ? 'bg-gray-800 border-l-4 border-accent-gold text-white font-semibold' : 'hover:bg-gray-800' }}">Manage Gallery</a>
                            @endif
                            
                            @if(auth()->user()->hasPermission('manage_settings'))
                            <a href="{{ route('admin.settings') }}" class="block px-6 py-3 transition {{ request()->routeIs('admin.settings') ? 'bg-gray-800 border-l-4 border-accent-gold text-white font-semibold' : 'hover:bg-gray-800' }}">Settings</a>
                            @endif
                        @elseif(auth()->user()->role === 'team_owner')
                            @php
                                $myTeam = \App\Models\Team::where('owner_id', auth()->id())->first();
                                $activeAuction = \App\Models\Auction::whereIn('status', ['live', 'upcoming'])->first();
                            @endphp
                            @if($myTeam)
                                <a href="{{ route('team_owner.my_team') }}" class="block px-6 py-3 transition {{ request()->routeIs('team_owner.my_team') ? 'bg-gray-800 border-l-4 border-success-green text-success-green font-bold' : 'hover:bg-gray-800 text-success-green font-semibold' }}">My Team</a>
                            @endif
                            @if($activeAuction)
                                <a href="{{ route('team.auction.bidding', $activeAuction->guid) }}" class="block px-6 py-3 transition {{ request()->routeIs('team.auction.bidding') ? 'bg-gray-800 border-l-4 border-accent-gold text-white font-semibold' : 'hover:bg-gray-800' }}">Bidding Room</a>
                            @endif
                        @endif
                        <a href="{{ route('public.players') }}" target="_blank" class="block px-6 py-3 transition {{ request()->routeIs('public.players') ? 'bg-gray-800 border-l-4 border-accent-gold text-white font-semibold' : 'hover:bg-gray-800' }}">All Players</a>
                        <a href="{{ route('reports.auction') }}" class="block px-6 py-3 transition {{ request()->routeIs('reports.auction') ? 'bg-gray-800 border-l-4 border-accent-gold text-white font-semibold' : 'hover:bg-gray-800' }}">Reports</a>
                    @endauth
                </nav>
            </aside>

            <!-- Main Content -->
            <div class="flex-1 flex flex-col h-screen overflow-hidden">
                <!-- Navbar -->
                <livewire:layout.navigation />

                <!-- Page Heading -->
                @if (isset($header))
                    <header class="bg-card-bg shadow border-b border-gray-800">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 font-poppins">
                            {{ $header }}
                        </div>
                    </header>
                @endif

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto bg-primary-bg p-4 sm:p-6 lg:p-8">
                    {{ $slot }}
                </main>
                
                <!-- Footer -->
                <footer class="bg-card-bg border-t border-gray-800 py-3 text-center text-sm text-gray-500 w-full">
                    &copy; {{ date('Y') }} {{ setting('app_name', 'NPLT20') }}. All rights reserved. 
                    @if(setting('developer_name'))
                        | Developed by <a href="{{ setting('developer_url', '#') }}" target="_blank" class="text-accent-gold hover:underline">{{ setting('developer_name') }}</a>
                    @endif
                </footer>
            </div>
        </div>
        
        <!-- Livewire Scripts -->
        @livewireScripts

        <!-- Test Echo Listener -->
        <script type="module">
            document.addEventListener('DOMContentLoaded', () => {
                if (window.Echo) {
                    window.Echo.channel('auction.1')
                        .listen('PlayerOnAuction', (e) => {
                            console.log('✅ [Reverb Test] PlayerOnAuction received:', e);
                        });
                }
            });
        </script>
    </body>
</html>
