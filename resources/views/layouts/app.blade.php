<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'NPL Auction') }}</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-inter antialiased bg-primary-bg text-white">
        <div class="flex h-screen overflow-hidden">
            <!-- Sidebar -->
            <aside class="w-64 bg-card-bg shadow-lg border-r border-gray-800 hidden md:flex flex-col">
                <div class="p-6 border-b border-gray-800">
                    <h2 class="text-2xl font-poppins font-bold text-accent-gold">NPL Auction</h2>
                </div>
                <nav class="flex-1 mt-6 space-y-1">
                    <a href="{{ route('dashboard') }}" class="block px-6 py-3 hover:bg-gray-800 transition">Dashboard</a>
                    @auth
                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}" class="block px-6 py-3 hover:bg-gray-800 transition text-accent-red font-semibold">Dashboard (Admin)</a>
                            <a href="{{ route('admin.auctions') }}" class="block px-6 py-3 hover:bg-gray-800 transition">Manage Auctions</a>
                            <a href="{{ route('admin.teams') }}" class="block px-6 py-3 hover:bg-gray-800 transition">Manage Teams</a>
                            <a href="{{ route('admin.players') }}" class="block px-6 py-3 hover:bg-gray-800 transition">Manage Players</a>
                            <a href="{{ route('admin.users') }}" class="block px-6 py-3 hover:bg-gray-800 transition">Manage Users</a>
                            <a href="{{ route('admin.analytics') }}" class="block px-6 py-3 hover:bg-gray-800 transition text-[#FFC800]">Analytics</a>
                        @elseif(auth()->user()->role === 'team_owner')
                            @php
                                $myTeam = \App\Models\Team::where('owner_id', auth()->id())->first();
                                $activeAuction = \App\Models\Auction::whereIn('status', ['live', 'upcoming'])->first();
                            @endphp
                            @if($myTeam)
                                <a href="{{ route('public.teams.show', $myTeam->id) }}" class="block px-6 py-3 hover:bg-gray-800 transition text-success-green font-semibold">My Team</a>
                            @endif
                            @if($activeAuction)
                                <a href="{{ route('team.auction.bidding', $activeAuction->id) }}" class="block px-6 py-3 hover:bg-gray-800 transition">Bidding Room</a>
                            @endif
                        @endif
                        <a href="{{ route('public.players') }}" class="block px-6 py-3 hover:bg-gray-800 transition">All Players</a>
                        <a href="{{ route('reports.auction') }}" class="block px-6 py-3 hover:bg-gray-800 transition text-[#00C853] font-bold">Reports</a>
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
