<div>
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <h1 class="text-2xl font-semibold text-ink">Auction Reports</h1>
        
        <div class="w-full md:w-64">
            <x-select2 id="selectedAuctionId" wire:model.live="selectedAuctionId" placeholder="Select Season/Auction">
                @foreach($auctions as $auction)
                    <option value="{{ $auction->id }}">{{ $auction->title }} ({{ \Carbon\Carbon::parse($auction->auction_date)->format('M d, Y') }})</option>
                @endforeach
            </x-select2>
        </div>
    </div>

    @if($selectedAuctionId && !empty($stats))
        <!-- Statistics: same headline cards as the dashboard -->
        <div class="stat-strip mb-8">
            <div>
                <p class="stat-label">Total spent</p>
                <p class="stat-value">₹{{ number_format($stats['total_spent']) }}</p>
            </div>
            <div>
                <p class="stat-label">Players sold</p>
                <p class="stat-value">{{ $stats['total_sold'] }}<span class="stat-of">/ {{ $stats['total_players'] }}</span></p>
            </div>
            <div>
                <p class="stat-label">Unsold players</p>
                <p class="stat-value {{ $stats['total_unsold'] > 0 ? 'text-danger' : '' }}">{{ $stats['total_unsold'] }}</p>
            </div>
            <div>
                <p class="stat-label">Highest bid</p>
                <p class="stat-value">₹{{ number_format($stats['highest_bid']) }}</p>
                @if($stats['highest_bid'] > 0)
                    <p class="stat-note">{{ $stats['highest_bid_player'] }} · {{ $stats['highest_bid_team'] }}</p>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Team Wise Squads -->
            <div class="lg:col-span-2 space-y-6">
                <h2 class="text-xl font-semibold text-ink border-b border-line pb-2">Team Squads & Spending</h2>
                
                @forelse($teamsData as $teamId => $auctionPlayers)
                    @php $team = $auctionPlayers->first()->soldToTeam; @endphp
                    @if($team)
                    <div class="bg-surface rounded-xl shadow-sm border border-line overflow-hidden">
                        <div class="p-4 border-b border-line flex justify-between items-center bg-surface-muted">
                            <div class="flex items-center gap-3">
                                <img src="{{ $team->logo ? Storage::url($team->logo) : 'https://ui-avatars.com/api/?name='.urlencode($team->name).'&background=random' }}" class="w-10 h-10 rounded-full object-cover border border-line">
                                <div>
                                    <h3 class="font-bold text-ink text-lg">{{ $team->name }}</h3>
                                    <p class="text-xs text-muted">{{ $auctionPlayers->count() }} Players</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="text-right">
                                    <p class="text-sm text-muted uppercase tracking-wider">Total Spent</p>
                                    <p class="font-semibold text-brand text-lg">₹{{ number_format($auctionPlayers->sum('final_price')) }}</p>
                                </div>
                                <a href="{{ route('teams.pdf', ['team' => $team->guid, 'auction_id' => $selectedAuctionId]) }}" target="_blank" class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded transition flex items-center gap-1 text-sm font-bold shadow-sm border border-red-500" title="Download Squad PDF">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    PDF
                                </a>
                            </div>
                        </div>
                        <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($auctionPlayers as $ap)
                                <div class="flex items-center justify-between bg-canvas p-3 rounded border border-line">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $ap->player->photo ? Storage::url($ap->player->photo) : 'https://ui-avatars.com/api/?name='.urlencode($ap->player->name).'&background=random' }}" class="w-8 h-8 rounded-full object-cover">
                                        <div>
                                            <p class="text-sm font-bold text-ink">{{ $ap->player->name }}</p>
                                            <p class="text-[10px] text-muted uppercase">{{ $ap->player->role }}</p>
                                        </div>
                                    </div>
                                    <p class="text-sm font-bold text-brand">₹{{ number_format($ap->final_price) }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                @empty
                    <div class="bg-surface p-8 text-center rounded-xl border border-line">
                        <p class="text-muted italic">No players sold in this auction yet.</p>
                    </div>
                @endforelse
            </div>

            <!-- Unsold Players -->
            <div>
                <h2 class="text-xl font-semibold text-muted border-b border-line pb-2 mb-6">Unsold Players</h2>
                
                <div class="bg-surface rounded-xl shadow-sm border border-line p-4">
                    @if($unsoldPlayers->count() > 0)
                        <div class="space-y-3 max-h-[800px] overflow-y-auto custom-scrollbar pr-2">
                            @foreach($unsoldPlayers as $up)
                                <div class="flex items-center justify-between bg-canvas p-3 rounded border border-line opacity-70">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-surface-subtle flex items-center justify-center">
                                            <svg class="w-4 h-4 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6"></path></svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-ink-soft">{{ $up->player->name }}</p>
                                            <p class="text-[10px] text-muted uppercase">{{ $up->player->role }} &bull; Base: ₹{{ number_format($up->player->base_price) }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted italic text-center py-4">No unsold players in this auction.</p>
                    @endif
                </div>
            </div>
        </div>
    @elseif(!$selectedAuctionId)
        <div class="flex justify-center items-center h-64 bg-surface rounded-xl border border-line">
            <p class="text-muted text-lg">Please select an auction to view the report.</p>
        </div>
    @endif
</div>
