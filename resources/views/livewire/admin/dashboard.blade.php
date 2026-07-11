<div>
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
        <h1 class="text-3xl font-poppins font-bold text-white">Dashboard</h1>
        <button wire:click="resetSeason" wire:confirm="WARNING: This will reset ALL Teams' remaining budgets back to full, and set ALL players to 'pending' (unsold). ONLY DO THIS IF YOU ARE STARTING A BRAND NEW AUCTION / NEW SEASON. Are you sure?" class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg shadow uppercase tracking-wider text-sm transition">
            Reset Data for New Season
        </button>
    </div>

    @if (session()->has('success'))
        <div class="bg-green-500 text-white p-4 rounded-lg font-bold mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Players -->
        <div class="bg-card-bg rounded-lg p-6 shadow border border-gray-800">
            <h2 class="text-sm font-inter text-gray-400 uppercase tracking-wide">Total Players</h2>
            <p class="text-4xl font-poppins font-bold text-accent-gold mt-2">{{ $totalPlayers }}</p>
        </div>

        <!-- Total Teams -->
        <div class="bg-card-bg rounded-lg p-6 shadow border border-gray-800">
            <h2 class="text-sm font-inter text-gray-400 uppercase tracking-wide">Total Teams</h2>
            <p class="text-4xl font-poppins font-bold text-accent-gold mt-2">{{ $totalTeams }}</p>
        </div>

        <!-- Upcoming Auctions -->
        <div class="bg-card-bg rounded-lg p-6 shadow border border-gray-800">
            <h2 class="text-sm font-inter text-gray-400 uppercase tracking-wide">Upcoming Auctions</h2>
            <p class="text-4xl font-poppins font-bold text-accent-gold mt-2">{{ $upcomingAuctions }}</p>
        </div>

        <!-- Completed Auctions -->
        <div class="bg-card-bg rounded-lg p-6 shadow border border-gray-800">
            <h2 class="text-sm font-inter text-gray-400 uppercase tracking-wide">Completed Auctions</h2>
            <p class="text-4xl font-poppins font-bold text-accent-gold mt-2">{{ $completedAuctions }}</p>
        </div>
    </div>
</div>
