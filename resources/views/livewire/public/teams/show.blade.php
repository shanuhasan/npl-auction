<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12" style="--team-color: {{ $team->primary_color }};">
    
    <!-- Team Header -->
    <div class="bg-[#141B2D] border border-white/10 rounded-3xl p-8 mb-12 shadow-[0_10px_40px_rgba(0,0,0,0.5)] relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 pointer-events-none" style="background: radial-gradient(circle at top right, var(--team-color), transparent 50%);"></div>
        
        <div class="flex flex-col md:flex-row items-center gap-8 relative z-10">
            <img src="{{ $team->logo ? Storage::url($team->logo) : 'https://ui-avatars.com/api/?name='.urlencode($team->name).'&background=random' }}" 
                 alt="{{ $team->name }}" 
                 class="w-40 h-40 rounded-full border-4 shadow-2xl bg-white" style="border-color: var(--team-color);">
            
            <div class="flex-1 text-center md:text-left">
                <h1 class="text-4xl md:text-6xl font-black text-white tracking-widest uppercase mb-2">{{ $team->name }}</h1>
                <p class="text-xl font-bold text-gray-400 tracking-widest">{{ $team->short_name }}</p>
            </div>

                <div class="grid grid-cols-2 gap-4 w-full md:w-auto mt-6 md:mt-0">
                    <div class="bg-black/30 rounded-xl p-4 border border-white/5 text-center">
                        <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">Total Spent</p>
                        <p class="text-2xl font-black text-red-400">₹{{ number_format($totalSpent) }}</p>
                    </div>
                    <div class="bg-black/30 rounded-xl p-4 border border-white/5 text-center">
                        <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">Purse Remaining</p>
                        <p class="text-2xl font-black text-[#00C853]">₹{{ number_format($remainingBudget) }}</p>
                    </div>
                </div>
            </div>
        </div>

    <!-- Squad Listing -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <h2 class="text-3xl font-black text-white uppercase tracking-widest text-center md:text-left">Official Squad ({{ $totalPlayers }})</h2>
        
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
            <div class="w-full sm:w-64">
                <select wire:model.live="selectedAuctionId" class="w-full bg-[#141B2D] border border-white/20 text-white rounded-lg py-2 px-4 focus:ring-[var(--team-color)] focus:border-[var(--team-color)] shadow">
                    <option value="">Select Season/Auction</option>
                    @foreach($auctions as $auction)
                        <option value="{{ $auction->id }}">{{ $auction->title }} ({{ \Carbon\Carbon::parse($auction->auction_date)->format('Y') }})</option>
                    @endforeach
                </select>
            </div>
            
            <a href="{{ route('teams.pdf', ['team' => $team->id, 'auction_id' => $selectedAuctionId]) }}" target="_blank" class="w-full sm:w-auto text-center bg-[var(--team-color)] text-white px-4 py-2 rounded-lg font-bold uppercase tracking-wider text-sm whitespace-nowrap hover:opacity-80 transition shadow-lg border border-white/20" title="Download Squad PDF">
                Download PDF
            </a>
        </div>
    </div>

    @forelse(['batsman', 'all-rounder', 'wicketkeeper', 'bowler'] as $role)
        @if(isset($playersByRole[$role]))
            <div class="mb-12">
                <h3 class="text-xl font-bold text-[var(--team-color)] uppercase tracking-widest mb-6 border-b border-white/10 pb-2">
                    {{ str_replace('-', ' ', $role) }}s ({{ $playersByRole[$role]->count() }})
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($playersByRole[$role] as $player)
                        <div class="bg-[#141B2D] rounded-2xl border border-white/5 overflow-hidden group hover:border-[var(--team-color)] transition duration-300">
                            <div class="h-48 relative overflow-hidden bg-black/50 flex justify-center items-end pt-4">
                                <img src="{{ $player->photo ? Storage::url($player->photo) : 'https://ui-avatars.com/api/?name='.urlencode($player->name).'&background=374151&color=fff&size=512' }}" 
                                     class="h-full w-auto object-cover group-hover:scale-105 transition duration-500">
                                <div class="absolute top-3 right-3 bg-white/10 backdrop-blur-md px-3 py-1 rounded-full text-xs font-bold text-white uppercase border border-white/20">
                                    {{ $player->country }}
                                </div>
                            </div>
                            <div class="p-5">
                                <h4 class="text-lg font-black text-white truncate">{{ $player->name }}</h4>
                                <div class="mt-4 flex justify-between items-end">
                                    <div>
                                        <p class="text-[10px] text-gray-500 uppercase tracking-widest">Bought For</p>
                                        <p class="font-bold text-[#FFC800] text-lg">
                                            @php
                                                $bought = $player->auctionPlayers->first();
                                            @endphp
                                            ₹{{ $bought ? number_format($bought->final_price) : number_format($player->base_price) }}
                                        </p>
                                    </div>
                                    <div class="text-[10px] text-gray-400 uppercase bg-white/5 px-2 py-1 rounded">
                                        {{ $player->category }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @empty
        <div class="text-center py-20 text-gray-500 text-xl font-semibold">
            No players bought yet.
        </div>
    @endforelse

</div>
