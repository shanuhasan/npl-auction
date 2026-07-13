<div>
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
        <h1 class="text-3xl font-poppins font-bold text-white">Dashboard</h1>
        @if(!$hasLiveAuctions)
            <button wire:click="resetSeason" wire:confirm="WARNING: This will reset ALL Teams' remaining budgets back to full, and set ALL players to 'available'. ONLY DO THIS IF YOU ARE STARTING A BRAND NEW AUCTION / NEW SEASON. Are you sure?" class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg shadow uppercase tracking-wider text-sm transition">
                Reset Data for New Season
            </button>
        @endif
    </div>

    @if (session()->has('success'))
        <div class="bg-green-500 text-white p-4 rounded-lg font-bold mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        
        <!-- Total Players -->
        <div class="bg-card-bg rounded-xl p-6 shadow-lg border border-gray-800 flex items-center gap-4 hover:border-accent-gold transition-colors">
            <div class="w-12 h-12 bg-blue-500/20 text-blue-500 rounded-lg flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <div>
                <p class="text-xs font-inter text-gray-400 uppercase tracking-wider font-bold mb-1">Players</p>
                <h3 class="text-2xl font-poppins font-black text-white leading-none">{{ $totalPlayers }}</h3>
            </div>
        </div>

        <!-- Total Teams -->
        <div class="bg-card-bg rounded-xl p-6 shadow-lg border border-gray-800 flex items-center gap-4 hover:border-accent-gold transition-colors">
            <div class="w-12 h-12 bg-purple-500/20 text-purple-500 rounded-lg flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
            </div>
            <div>
                <p class="text-xs font-inter text-gray-400 uppercase tracking-wider font-bold mb-1">Teams</p>
                <h3 class="text-2xl font-poppins font-black text-white leading-none">{{ $totalTeams }}</h3>
            </div>
        </div>

        <!-- Upcoming Auctions -->
        <div class="bg-card-bg rounded-xl p-6 shadow-lg border border-gray-800 flex items-center gap-4 hover:border-accent-gold transition-colors">
            <div class="w-12 h-12 bg-yellow-500/20 text-yellow-500 rounded-lg flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-xs font-inter text-gray-400 uppercase tracking-wider font-bold mb-1">Upcoming</p>
                <h3 class="text-2xl font-poppins font-black text-white leading-none">{{ $upcomingAuctions }}</h3>
            </div>
        </div>

        <!-- Completed Auctions -->
        <div class="bg-card-bg rounded-xl p-6 shadow-lg border border-gray-800 flex items-center gap-4 hover:border-accent-gold transition-colors">
            <div class="w-12 h-12 bg-green-500/20 text-green-500 rounded-lg flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-xs font-inter text-gray-400 uppercase tracking-wider font-bold mb-1">Completed</p>
                <h3 class="text-2xl font-poppins font-black text-white leading-none">{{ $completedAuctions }}</h3>
            </div>
        </div>

        <!-- Total Visitors -->
        <div class="bg-card-bg rounded-xl p-6 shadow-lg border border-gray-800 flex items-center gap-4 hover:border-accent-gold transition-colors">
            <div class="w-12 h-12 bg-red-500/20 text-red-500 rounded-lg flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
            </div>
            <div>
                <p class="text-xs font-inter text-gray-400 uppercase tracking-wider font-bold mb-1">Visitors</p>
                <h3 class="text-2xl font-poppins font-black text-white leading-none">{{ $totalVisitors }}</h3>
            </div>
        </div>

    </div>
</div>
