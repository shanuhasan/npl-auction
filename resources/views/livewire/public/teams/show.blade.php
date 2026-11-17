<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 {{ $this instanceof \App\Livewire\TeamOwner\MyTeam ? 'bg-canvas rounded-2xl text-ink' : '' }}" style="--team-color: {{ $team->primary_color }};">
    
    <!-- Team Header -->
    <div class="bg-surface border border-line rounded-2xl p-8 mb-12 shadow-[0_10px_40px_rgba(0,0,0,0.5)] relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 pointer-events-none" style="background: radial-gradient(circle at top right, var(--team-color), transparent 50%);"></div>
        
        <div class="flex flex-col md:flex-row items-center gap-8 relative z-10">
            <img src="{{ $team->logo ? Storage::url($team->logo) : 'https://ui-avatars.com/api/?name='.urlencode($team->name).'&background=random' }}" 
                 alt="{{ $team->name }}" 
                 class="w-40 h-40 rounded-full border-4 shadow-sm bg-white" style="border-color: var(--team-color);">
            
            <div class="flex-1 text-center md:text-left">
                <h1 class="text-4xl md:text-4xl font-semibold text-ink mb-2 tracking-tight">{{ $team->name }}</h1>
                <p class="text-xl font-bold text-muted tracking-widest">{{ $team->short_name }}</p>
                @if($team->owner)
                    <div class="mt-4 inline-flex items-center gap-3 bg-surface-subtle border border-line rounded-full py-1.5 px-4 shadow-sm">
                        @if($team->owner->image)
                            <img src="{{ asset('storage/' . $team->owner->image) }}" alt="{{ $team->owner->name }}" class="w-8 h-8 rounded-full border border-[var(--team-color)] object-cover">
                        @else
                            <div class="w-8 h-8 rounded-full bg-[var(--team-color)] flex items-center justify-center text-white font-bold text-xs">
                                {{ substr($team->owner->name, 0, 1) }}
                            </div>
                        @endif
                        <span class="text-sm font-semibold text-ink-soft uppercase tracking-wide">
                            Owner: <span class="text-ink">{{ $team->owner->name }}</span>
                        </span>
                    </div>
                @endif
            </div>

                <div class="grid grid-cols-2 gap-4 w-full md:w-auto mt-6 md:mt-0">
                    <div class="bg-surface-muted rounded-xl p-4 border border-line text-center">
                        <p class="text-xs text-muted uppercase tracking-widest mb-1">Total Spent</p>
                        <p class="text-2xl font-bold text-red-600">₹{{ number_format($totalSpent) }}</p>
                    </div>
                    <div class="bg-surface-muted rounded-xl p-4 border border-line text-center">
                        <p class="text-xs text-muted uppercase tracking-widest mb-1">Purse Remaining</p>
                        <p class="text-2xl font-bold text-success">₹{{ number_format($remainingBudget) }}</p>
                    </div>
                </div>
            </div>
        </div>

    <!-- Squad Listing -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <h2 class="text-3xl font-semibold text-ink text-center md:text-left tracking-tight">Official Squad ({{ $totalPlayers }})</h2>
        
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
            <div class="w-full sm:w-64">
                <x-select2 id="teamShowAuctionId" wire:model.live="selectedAuctionId" placeholder="Select Season/Auction">
                    @foreach($auctions as $auction)
                        <option value="{{ $auction->id }}">{{ $auction->title }} ({{ \Carbon\Carbon::parse($auction->auction_date)->format('Y') }})</option>
                    @endforeach
                </x-select2>
            </div>
            
            <a href="{{ route('teams.pdf', ['team' => $team->id, 'auction_id' => $selectedAuctionId]) }}" target="_blank" class="w-full sm:w-auto text-center bg-[var(--team-color)] text-white px-4 py-2 rounded-lg font-bold uppercase tracking-wider text-sm whitespace-nowrap hover:opacity-80 transition shadow-sm border border-line" title="Download Squad PDF">
                Download PDF
            </a>
        </div>
    </div>

    @forelse(['batsman', 'all-rounder', 'wicketkeeper', 'bowler'] as $role)
        @if(isset($playersByRole[$role]))
            <div class="mb-12">
                <h3 class="text-xl font-semibold text-[var(--team-color)] mb-6 border-b border-line pb-2 tracking-tight">
                    {{ str_replace('-', ' ', $role) }}s ({{ $playersByRole[$role]->count() }})
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($playersByRole[$role] as $player)
                        <div class="bg-surface rounded-2xl border border-line overflow-hidden group hover:border-[var(--team-color)] transition duration-300">
                            <div class="h-48 relative overflow-hidden bg-surface-subtle flex justify-center items-end pt-4">
                                <img src="{{ $player->photo ? Storage::url($player->photo) : 'https://ui-avatars.com/api/?name='.urlencode($player->name).'&background=374151&color=fff&size=512' }}" 
                                     class="h-full w-auto object-cover group-hover:scale-105 transition duration-500">
                                <div class="absolute top-3 right-3 bg-surface-muted backdrop-blur-md px-3 py-1 rounded-full text-xs font-bold text-ink uppercase border border-line">
                                    {{ $player->country }}
                                </div>
                            </div>
                            <div class="p-5">
                                <h4 class="text-lg font-semibold text-ink truncate tracking-tight">
                                    {{ $player->name }}
                                    @if($player->is_captain) <span class="badge-brand align-middle ml-1" title="Captain/Owner">(Captain/Owner)</span> @endif
                                    @if($player->is_favorite) <span class="badge bg-pink-50 text-pink-700 ring-1 ring-inset ring-pink-600/20 align-middle ml-1" title="Favorite">(Favorite)</span> @endif
                                </h4>
                                <div class="mt-4 flex justify-between items-end">
                                    <div>
                                        <p class="text-[10px] text-muted uppercase tracking-widest">Bought For</p>
                                        <p class="font-bold text-brand text-lg">
                                            @php
                                                $bought = $player->auctionPlayers->first();
                                            @endphp
                                            ₹{{ $bought ? number_format($bought->final_price) : ($player->status === 'sold' ? '0' : number_format($player->base_price)) }}
                                        </p>
                                    </div>
                                    <!-- <div class="text-[10px] text-muted uppercase bg-surface-muted px-2 py-1 rounded">
                                        {{ $player->category }}
                                    </div> -->
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @empty
        <div class="text-center py-20 text-muted text-xl font-semibold">
            No players bought yet.
        </div>
    @endforelse


    <!-- Team Match Schedule -->
    <div class="mt-16">
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
            <h2 class="text-3xl font-semibold text-ink text-center md:text-left tracking-tight">Match Schedule ({{ $fixtures->count() }})</h2>
            @php $selectedAuction = $auctions->firstWhere('id', $selectedAuctionId); @endphp
            @if($selectedAuction)
                <a href="{{ route('public.schedule', ['auction' => $selectedAuction->guid]) }}" class="text-brand font-bold uppercase tracking-wider text-sm hover:underline">Full schedule &amp; points table &rarr;</a>
            @endif
        </div>
        @if($fixtures->isNotEmpty())
            @php
                $won = $fixtures->where('status', 'completed')->where('winner_team_id', $team->id)->count();
                $lost = $fixtures->where('status', 'completed')->whereNotNull('winner_team_id')->where('winner_team_id', '!=', $team->id)->count();
            @endphp
            <p class="text-muted mb-6 text-center md:text-left">
                Played {{ $fixtures->whereIn('status', ['completed', 'abandoned'])->count() }}
                &middot; <span class="text-success">Won {{ $won }}</span>
                &middot; <span class="text-red-600">Lost {{ $lost }}</span>
                &middot; Upcoming {{ $fixtures->whereIn('status', ['scheduled', 'live'])->count() }}
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($fixtures as $fixture)
                    <x-fixture-card :fixture="$fixture" :highlight-team="$team->id" />
                @endforeach
            </div>
        @else
            <div class="text-center py-12 text-muted text-lg font-semibold bg-surface rounded-2xl border border-line">
                Match schedule not announced yet.
            </div>
        @endif
    </div>

</div>
