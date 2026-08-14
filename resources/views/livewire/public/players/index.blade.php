<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    
    <div class="mb-10 flex flex-col md:flex-row justify-between items-center">
        <div class="text-center md:text-left mb-6 md:mb-0">
            <h1 class="text-4xl md:text-5xl font-black text-white uppercase tracking-widest mb-4">Players Directory</h1>
            <p class="text-gray-400 text-lg">Search, filter, and track all players in the auction.</p>
        </div>
        <div>
            <a href="{{ route('public.players.register') }}" class="bg-accent-gold text-primary-bg px-6 py-3 rounded-xl font-bold hover:bg-yellow-400 transition shadow-lg text-lg uppercase tracking-wider">
                Register as Player
            </a>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-[#141B2D] p-4 rounded-2xl shadow-lg border border-white/5 mb-10 flex flex-col md:flex-row items-center gap-4">
        
        <!-- Search -->
        <div class="relative w-full md:flex-1">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search players by name..." class="w-full pl-10 pr-4 py-3 bg-black/30 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#FFC800] focus:border-transparent transition">
        </div>

        <!-- Role Filter -->
        <x-select2 id="publicRole" wire:model.live="role" placeholder="All Roles">
            <option value="batsman">Batsman</option>
            <option value="bowler">Bowler</option>
            <option value="all-rounder">All-Rounder</option>
            <option value="wicketkeeper">Wicketkeeper</option>
        </x-select2>

        <!-- Status Filter -->
        <x-select2 id="publicStatus" wire:model.live="status" placeholder="All Statuses">
            <option value="available">Available</option>
            <option value="sold">Sold</option>
            <option value="unsold">Unsold</option>
        </x-select2>
    </div>

    <!-- Players Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
        @forelse($players as $player)
            <div class="bg-[#141B2D] rounded-2xl border border-white/5 overflow-hidden group hover:border-[#FFC800] transition duration-300 relative flex flex-col h-full">
                
                @if($player->status === 'sold')
                    <div class="absolute top-0 right-0 p-2 z-20">
                        <div class="bg-[#00C853] text-white text-[10px] font-black uppercase px-3 py-1 rounded-full shadow-lg border border-white/20">
                            SOLD
                        </div>
                    </div>
                @elseif($player->status === 'unsold')
                    <div class="absolute top-0 right-0 p-2 z-20">
                        <div class="bg-gray-600 text-white text-[10px] font-black uppercase px-3 py-1 rounded-full shadow-lg border border-white/20">
                            UNSOLD
                        </div>
                    </div>
                @endif

                <div class="h-56 relative overflow-hidden bg-black/50 flex justify-center items-end pt-4 cursor-pointer" wire:click="showPlayer({{ $player->id }})">
                    <img src="{{ $player->photo ? Storage::url($player->photo) : 'https://ui-avatars.com/api/?name='.urlencode($player->name).'&background=374151&color=fff&size=512' }}" 
                         class="h-full w-auto object-cover group-hover:scale-105 transition duration-500">
                    
                    <div class="absolute top-3 left-3 bg-white/10 backdrop-blur-md px-3 py-1 rounded-full text-[10px] font-bold text-white uppercase border border-white/20">
                        {{ $player->country }}
                    </div>
                </div>

                <div class="p-5 flex flex-col flex-1">
                    <h4 class="text-xl font-black text-white mb-1">{{ $player->name }}</h4>
                    <p class="text-xs text-gray-400 font-semibold uppercase tracking-widest mb-4">{{ $player->role }}</p>
                    
                    <div class="mt-auto space-y-4">
                        @if($player->status === 'sold' && $player->currentTeam)
                            <div class="bg-white/5 p-3 rounded-xl border border-[var(--team-color)]/30 relative overflow-hidden group-hover:bg-white/10 transition" style="--team-color: {{ $player->currentTeam->primary_color }};">
                                <div class="absolute inset-0 opacity-10" style="background-color: var(--team-color);"></div>
                                <div class="flex justify-between items-center relative z-10">
                                    <div class="flex items-center gap-2">
                                        <img src="{{ $player->currentTeam->logo ? Storage::url($player->currentTeam->logo) : 'https://ui-avatars.com/api/?name='.urlencode($player->currentTeam->name).'&background=random' }}" class="w-6 h-6 rounded-full">
                                        <span class="text-xs font-bold text-white">{{ $player->currentTeam->short_name }}</span>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[9px] text-gray-400 uppercase tracking-widest">Bought For</p>
                                        <p class="font-black text-[#FFC800]">
                                            @php
                                                $bought = $player->auctionPlayers->first();
                                            @endphp
                                            ₹{{ $bought ? number_format($bought->final_price) : number_format($player->base_price) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="flex justify-between items-end h-8">
                                <!-- Empty space where base price used to be -->
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 text-center">
                <div class="w-20 h-20 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-300">No players found</h3>
                <p class="text-gray-500 mt-2">Try adjusting your filters or search query.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-12">
        {{ $players->links(data: ['scrollTo' => false]) }}
    </div>

    <style>
        /* Custom Pagination Styling for Dark Theme */
        nav[role="navigation"] {
            @apply flex justify-between items-center gap-4;
        }
        nav[role="navigation"] p {
            @apply text-gray-400 text-sm;
        }
        nav[role="navigation"] span.relative.z-0.inline-flex.shadow-sm.rounded-md {
            @apply shadow-none flex gap-1;
        }
        nav[role="navigation"] a, nav[role="navigation"] span[aria-disabled="true"] span {
            @apply bg-[#141B2D] border border-white/10 text-gray-300 hover:text-white hover:bg-white/10 px-4 py-2 text-sm font-bold transition rounded-lg;
        }
        nav[role="navigation"] span[aria-current="page"] span {
            @apply bg-[#FFC800] text-[#0B0F19] border-[#FFC800];
        }
    </style>

    <!-- Player Modal -->
    @if($showModal && $selectedPlayer)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm px-4 py-6" x-data="{ open: true }" x-show="open" @keydown.escape.window="$wire.closeModal()">
        
        <!-- Prev Button -->
        <button wire:click.stop="prevPlayer" class="absolute top-1/2 -translate-y-1/2 bg-black/60 border border-white/20 hover:border-[#FFC800] hover:bg-[#FFC800] text-white hover:text-black rounded-full flex items-center justify-center transition-all shadow-[0_4px_10px_rgba(0,0,0,0.5)]" style="z-index: 10000; left: 1rem; width: 3rem; height: 3rem;">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </button>

        <!-- Next Button -->
        <button wire:click.stop="nextPlayer" class="absolute top-1/2 -translate-y-1/2 bg-black/60 border border-white/20 hover:border-[#FFC800] hover:bg-[#FFC800] text-white hover:text-black rounded-full flex items-center justify-center transition-all shadow-[0_4px_10px_rgba(0,0,0,0.5)]" style="z-index: 10000; right: 1rem; width: 3rem; height: 3rem;">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>

        <div class="bg-[#141B2D] border border-white/10 rounded-3xl w-full max-w-3xl max-h-[90vh] overflow-y-auto shadow-2xl relative flex flex-col md:flex-row" @click.away="$wire.closeModal()">
            <button wire:click="closeModal" class="absolute top-4 right-4 bg-black/50 text-white rounded-full p-2 hover:bg-red-500 transition z-50">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            
            <div class="w-full md:w-1/2 bg-black/50 flex justify-center items-end p-6 md:p-8 relative min-h-[200px] md:min-h-[300px]">
                <img src="{{ $selectedPlayer->photo ? Storage::url($selectedPlayer->photo) : 'https://ui-avatars.com/api/?name='.urlencode($selectedPlayer->name).'&background=374151&color=fff&size=512' }}" 
                     class="max-h-56 md:max-h-80 w-auto object-contain z-10 drop-shadow-2xl">
                     
                @if($selectedPlayer->currentTeam)
                    <div class="absolute inset-0 opacity-20 pointer-events-none" style="background-color: {{ $selectedPlayer->currentTeam->primary_color }}"></div>
                @endif
            </div>
            
            <div class="w-full md:w-1/2 p-6 md:p-8 flex flex-col justify-center">
                <div class="mb-4">
                    <!-- <span class="inline-block px-3 py-1 bg-white/10 rounded-full text-xs font-bold text-gray-300 uppercase tracking-widest mb-2 border border-white/10">{{ $selectedPlayer->country }}</span> -->
                    <h2 class="text-3xl font-black text-white leading-tight">{{ $selectedPlayer->name }}</h2>
                    <p class="text-[#FFC800] font-bold uppercase tracking-widest text-sm mt-1">{{ $selectedPlayer->role }}</p>
                </div>
                
                <div class="space-y-4 mb-8">
                    <!-- @if($selectedPlayer->batting_style)
                    <div>
                        <p class="text-gray-500 text-xs uppercase font-bold tracking-wider">Batting Style</p>
                        <p class="text-white font-medium">{{ $selectedPlayer->batting_style }}</p>
                    </div>
                    @endif
                    
                    @if($selectedPlayer->bowling_style)
                    <div>
                        <p class="text-gray-500 text-xs uppercase font-bold tracking-wider">Bowling Style</p>
                        <p class="text-white font-medium">{{ $selectedPlayer->bowling_style }}</p>
                    </div>
                    @endif -->
                    
                    @if($selectedPlayer->city)
                    <div>
                        <p class="text-gray-500 text-xs uppercase font-bold tracking-wider">Address</p>
                        <p class="text-white font-medium capitalize">{{ $selectedPlayer->city }}</p>
                    </div>
                    @endif
                </div>
                
                <div class="mt-auto border-t border-white/10 pt-6">
                    @if($selectedPlayer->status === 'sold' && $selectedPlayer->currentTeam)
                        <div class="flex items-center gap-4">
                            <img src="{{ $selectedPlayer->currentTeam->logo ? Storage::url($selectedPlayer->currentTeam->logo) : 'https://ui-avatars.com/api/?name='.urlencode($selectedPlayer->currentTeam->name).'&background=random' }}" class="w-12 h-12 rounded-full border-2 border-white/20">
                            <div>
                                <p class="text-gray-400 text-xs uppercase font-bold">Bought By</p>
                                <p class="text-white font-black">{{ $selectedPlayer->currentTeam->name }}</p>
                                <p class="text-[#00C853] font-bold mt-1 text-lg">
                                    @php
                                        $bought = $selectedPlayer->auctionPlayers->first();
                                    @endphp
                                    ₹{{ $bought ? number_format($bought->final_price) : number_format($selectedPlayer->base_price) }}
                                </p>
                            </div>
                        </div>
                    @else
                        <!-- <div class="flex justify-between items-center">
                            <div>
                                <p class="text-gray-400 text-xs uppercase font-bold">Base Price</p>
                                <p class="text-white font-black text-xl">₹{{ number_format($selectedPlayer->base_price) }}</p>
                            </div>
                            <div>
                                @if($selectedPlayer->status === 'unsold')
                                    <span class="bg-gray-600 text-white text-xs font-black uppercase px-3 py-1 rounded-full border border-white/20">Unsold</span>
                                @else
                                    <span class="bg-blue-600 text-white text-xs font-black uppercase px-3 py-1 rounded-full border border-white/20">Available</span>
                                @endif
                            </div>
                        </div> -->
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

</div>
