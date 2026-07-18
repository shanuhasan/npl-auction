<div>
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <h1 class="text-3xl font-poppins font-bold text-white uppercase tracking-wider">Auction Reports</h1>
        
        <div class="w-full md:w-64">
            <select wire:model.live="selectedAuctionId" class="w-full bg-card-bg border border-gray-700 text-white rounded-lg py-2 px-4 focus:ring-accent-gold focus:border-accent-gold shadow">
                <option value="">Select Season/Auction</option>
                @foreach($auctions as $auction)
                    <option value="{{ $auction->id }}">{{ $auction->title }} ({{ \Carbon\Carbon::parse($auction->auction_date)->format('M d, Y') }})</option>
                @endforeach
            </select>
        </div>
    </div>

    @if($selectedAuctionId && !empty($stats))
        <!-- Statistics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-card-bg rounded-xl shadow border border-gray-800 p-6 flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400 font-bold uppercase tracking-wider">Total Spent</p>
                    <p class="text-3xl font-black text-accent-gold mt-1">₹{{ number_format($stats['total_spent']) }}</p>
                </div>
                <div class="p-3 bg-accent-gold/10 rounded-lg">
                    <svg class="w-8 h-8 text-accent-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            
            <div class="bg-card-bg rounded-xl shadow border border-gray-800 p-6 flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400 font-bold uppercase tracking-wider">Players Sold</p>
                    <p class="text-3xl font-black text-green-500 mt-1">{{ $stats['total_sold'] }} <span class="text-sm text-gray-500">/ {{ $stats['total_players'] }}</span></p>
                </div>
                <div class="p-3 bg-green-500/10 rounded-lg">
                    <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>

            <div class="bg-card-bg rounded-xl shadow border border-gray-800 p-6 flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400 font-bold uppercase tracking-wider">Unsold Players</p>
                    <p class="text-3xl font-black text-red-500 mt-1">{{ $stats['total_unsold'] }}</p>
                </div>
                <div class="p-3 bg-red-500/10 rounded-lg">
                    <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6"></path></svg>
                </div>
            </div>

            <div class="bg-card-bg rounded-xl shadow border border-gray-800 p-6 flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400 font-bold uppercase tracking-wider">Highest Bid</p>
                    <p class="text-2xl font-black text-accent-gold mt-1">₹{{ number_format($stats['highest_bid']) }}</p>
                    <p class="text-xs text-gray-500 mt-1 truncate">{{ $stats['highest_bid_player'] }} ({{ $stats['highest_bid_team'] }})</p>
                </div>
                <div class="p-3 bg-yellow-500/10 rounded-lg">
                    <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Team Wise Squads -->
            <div class="lg:col-span-2 space-y-6">
                <h2 class="text-xl font-bold text-white uppercase tracking-wider border-b border-gray-700 pb-2">Team Squads & Spending</h2>
                
                @forelse($teamsData as $teamId => $auctionPlayers)
                    @php $team = $auctionPlayers->first()->soldToTeam; @endphp
                    @if($team)
                    <div class="bg-card-bg rounded-xl shadow border border-gray-800 overflow-hidden">
                        <div class="p-4 border-b border-gray-700 flex justify-between items-center bg-gray-900">
                            <div class="flex items-center gap-3">
                                <img src="{{ $team->logo ? Storage::url($team->logo) : 'https://ui-avatars.com/api/?name='.urlencode($team->name).'&background=random' }}" class="w-10 h-10 rounded-full object-cover border border-gray-700">
                                <div>
                                    <h3 class="font-bold text-white text-lg">{{ $team->name }}</h3>
                                    <p class="text-xs text-gray-400">{{ $auctionPlayers->count() }} Players</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="text-right">
                                    <p class="text-sm text-gray-400 uppercase tracking-wider">Total Spent</p>
                                    <p class="font-black text-accent-gold text-lg">₹{{ number_format($auctionPlayers->sum('final_price')) }}</p>
                                </div>
                                <a href="{{ route('teams.pdf', ['team' => $team->id, 'auction_id' => $selectedAuctionId]) }}" target="_blank" class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded transition flex items-center gap-1 text-sm font-bold shadow-md border border-red-500" title="Download Squad PDF">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    PDF
                                </a>
                            </div>
                        </div>
                        <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($auctionPlayers as $ap)
                                <div class="flex items-center justify-between bg-primary-bg p-3 rounded border border-gray-800">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $ap->player->photo ? Storage::url($ap->player->photo) : 'https://ui-avatars.com/api/?name='.urlencode($ap->player->name).'&background=random' }}" class="w-8 h-8 rounded-full object-cover">
                                        <div>
                                            <p class="text-sm font-bold text-white">{{ $ap->player->name }}</p>
                                            <p class="text-[10px] text-gray-500 uppercase">{{ $ap->player->role }}</p>
                                        </div>
                                    </div>
                                    <p class="text-sm font-bold text-accent-gold">₹{{ number_format($ap->final_price) }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                @empty
                    <div class="bg-card-bg p-8 text-center rounded-xl border border-gray-800">
                        <p class="text-gray-500 italic">No players sold in this auction yet.</p>
                    </div>
                @endforelse
            </div>

            <!-- Unsold Players -->
            <div>
                <h2 class="text-xl font-bold text-gray-400 uppercase tracking-wider border-b border-gray-800 pb-2 mb-6">Unsold Players</h2>
                
                <div class="bg-card-bg rounded-xl shadow border border-gray-800 p-4">
                    @if($unsoldPlayers->count() > 0)
                        <div class="space-y-3 max-h-[800px] overflow-y-auto custom-scrollbar pr-2">
                            @foreach($unsoldPlayers as $up)
                                <div class="flex items-center justify-between bg-primary-bg p-3 rounded border border-gray-800 opacity-70">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-gray-800 flex items-center justify-center">
                                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6"></path></svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-300">{{ $up->player->name }}</p>
                                            <p class="text-[10px] text-gray-500 uppercase">{{ $up->player->role }} &bull; Base: ₹{{ number_format($up->player->base_price) }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 italic text-center py-4">No unsold players in this auction.</p>
                    @endif
                </div>
            </div>
        </div>
    @elseif(!$selectedAuctionId)
        <div class="flex justify-center items-center h-64 bg-card-bg rounded-xl border border-gray-800">
            <p class="text-gray-400 text-lg">Please select an auction to view the report.</p>
        </div>
    @endif
</div>
