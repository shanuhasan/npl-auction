<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    
    <div class="mb-10 flex flex-col md:flex-row justify-between items-center">
        <div class="text-center md:text-left mb-6 md:mb-0">
            <h1 class="text-4xl md:text-3xl font-semibold text-ink mb-4 tracking-tight">Players Directory</h1>
            <p class="text-muted text-lg">Search, filter, and track all players in the auction.</p>
        </div>
        <div>
            <a href="{{ route('public.players.register') }}" class="bg-brand text-white px-6 py-3 rounded-xl font-medium hover:bg-brand-hover transition shadow-sm text-lg">
                Register as Player
            </a>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-surface p-4 rounded-2xl shadow-sm border border-line mb-10 flex flex-col md:flex-row items-center gap-4">
        
        <!-- Search -->
        <div class="relative w-full md:flex-1">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search players by name..." class="w-full pl-10 pr-4 py-3 bg-surface-muted border border-line rounded-xl text-ink placeholder-faint focus:outline-none focus:ring-2 focus:ring-brand focus:border-transparent transition">
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
            <option value="unavailable">Unavailable</option>
        </x-select2>
    </div>

    <!-- Players Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
        @forelse($players as $player)
            <div class="bg-surface rounded-2xl border border-line overflow-hidden group hover:border-brand transition duration-300 relative flex flex-col h-full">
                
                @if($player->status === 'sold')
                    <div class="absolute top-0 right-0 p-2 z-20">
                        <div class="bg-success text-white text-[10px] font-bold uppercase px-3 py-1 rounded-full shadow-sm border border-line">
                            SOLD
                        </div>
                    </div>
                @elseif($player->status === 'unsold')
                    <div class="absolute top-0 right-0 p-2 z-20">
                        <div class="bg-slate-200 text-ink text-[10px] font-bold uppercase px-3 py-1 rounded-full shadow-sm border border-line">
                            UNSOLD
                        </div>
                    </div>
                @elseif($player->status === 'unavailable')
                    <div class="absolute top-0 right-0 p-2 z-20">
                        <div class="bg-surface-subtle text-muted text-[10px] font-bold uppercase px-3 py-1 rounded-full shadow-sm border border-line">
                            UNAVAILABLE
                        </div>
                    </div>
                @endif

                <div class="h-56 relative overflow-hidden bg-surface-subtle flex justify-center items-end pt-4 cursor-pointer" wire:click="showPlayer({{ $player->id }})">
                    <img src="{{ $player->photo ? Storage::url($player->photo) : 'https://ui-avatars.com/api/?name='.urlencode($player->name).'&background=374151&color=fff&size=512' }}" 
                         class="h-full w-auto object-cover group-hover:scale-105 transition duration-500">
                    
                    <div class="absolute top-3 left-3 bg-surface-muted backdrop-blur-md px-3 py-1 rounded-full text-[10px] font-bold text-ink uppercase border border-line">
                        {{ $player->country }}
                    </div>
                </div>

                <div class="p-5 flex flex-col flex-1">
                    <h4 class="text-xl font-semibold text-ink mb-1 tracking-tight">
                        {{ $player->name }}
                        @if($player->is_captain) <span class="badge-brand align-middle ml-1" title="Captain/Owner">C</span> @endif
                        @if($player->is_favorite) <span class="badge bg-pink-50 text-pink-700 ring-1 ring-inset ring-pink-600/20 align-middle ml-1" title="Favorite">★</span> @endif
                    </h4>
                    <p class="text-xs text-muted font-semibold uppercase tracking-widest mb-4">{{ $player->role }}</p>
                    
                    <div class="mt-auto space-y-4">
                        @if($player->status === 'sold' && $player->currentTeam)
                            <div class="bg-surface-muted p-3 rounded-xl border border-[var(--team-color)]/30 relative overflow-hidden group-hover:bg-surface-subtle transition" style="--team-color: {{ $player->currentTeam->primary_color }};">
                                <div class="absolute inset-0 opacity-10" style="background-color: var(--team-color);"></div>
                                <div class="flex justify-between items-center relative z-10">
                                    <div class="flex items-center gap-2">
                                        <img src="{{ $player->currentTeam->logo ? Storage::url($player->currentTeam->logo) : 'https://ui-avatars.com/api/?name='.urlencode($player->currentTeam->name).'&background=random' }}" class="w-6 h-6 rounded-full">
                                        <span class="text-xs font-bold text-ink">{{ $player->currentTeam->short_name }}</span>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[9px] text-muted uppercase tracking-widest">Bought For</p>
                                        <p class="font-bold text-brand">
                                            @php
                                                $bought = $player->auctionPlayers->first();
                                            @endphp
                                            ₹{{ $bought ? number_format($bought->final_price) : ($player->status === 'sold' ? '0' : number_format($player->base_price)) }}
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
                <div class="w-20 h-20 bg-surface-muted rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <h3 class="text-2xl font-semibold text-ink-soft tracking-tight">No players found</h3>
                <p class="text-muted mt-2">Try adjusting your filters or search query.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-12">
        {{ $players->links(data: ['scrollTo' => false]) }}
    </div>


    <!-- Player Modal -->
    @if($showModal && $selectedPlayer)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm px-4 py-6" x-data="{ open: true }" x-show="open" @keydown.escape.window="$wire.closeModal()">
        
        <!-- Prev Button -->
        <button wire:click.stop="prevPlayer" class="absolute top-1/2 -translate-y-1/2 bg-black/60 border border-line hover:border-brand hover:bg-brand text-white hover:text-white rounded-full flex items-center justify-center transition-all shadow-[0_4px_10px_rgba(0,0,0,0.5)]" style="z-index: 10000; left: 1rem; width: 3rem; height: 3rem;">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </button>

        <!-- Next Button -->
        <button wire:click.stop="nextPlayer" class="absolute top-1/2 -translate-y-1/2 bg-black/60 border border-line hover:border-brand hover:bg-brand text-white hover:text-white rounded-full flex items-center justify-center transition-all shadow-[0_4px_10px_rgba(0,0,0,0.5)]" style="z-index: 10000; right: 1rem; width: 3rem; height: 3rem;">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>

        <div class="w-full max-w-5xl h-full max-h-[80vh] flex items-center justify-center">
            <div class="relative w-full h-full max-w-4xl mx-auto bg-canvas/90 backdrop-blur-md rounded-2xl shadow-[0_0_40px_rgba(255,200,0,0.2)] border border-brand/30 flex items-center justify-center overflow-hidden" @click.away="$wire.closeModal()">
                
                <!-- Close Button -->
                <button wire:click="closeModal" style="position: absolute; top: 20px; right: 20px; z-index: 10000; width: 50px; height: 50px; background: #ffffff; border-radius: 50%; color: #172554; border: 1px solid #E2E8F0; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 10px 30px -10px rgba(15,23,42,0.25); transition: all 0.2s;" onmouseover="this.style.background='#F1F5F9';" onmouseout="this.style.background='#ffffff';">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>

                <!-- Image and Text Content -->
                <div class="relative w-full h-full flex items-center justify-center p-2 md:p-6">
                    <img src="{{ $selectedPlayer->photo ? Storage::url($selectedPlayer->photo) : 'https://ui-avatars.com/api/?name='.urlencode($selectedPlayer->name).'&background=374151&color=fff&size=512' }}" 
                         class="max-w-full max-h-[70vh] object-contain rounded-lg relative z-10">
                    
                    <div class="absolute bottom-0 left-0 w-full p-4 md:p-5 bg-surface border-t-2 border-brand text-center z-50 rounded-b-2xl shadow-[0_-4px_20px_rgba(0,0,0,0.5)]">
                        <h3 class="text-2xl md:text-3xl text-ink font-semibold mb-1 tracking-tight">
                            {{ $selectedPlayer->name }}
                            @if($selectedPlayer->is_captain) <span class="badge-brand align-middle ml-1" title="Captain/Owner">C</span> @endif
                            @if($selectedPlayer->is_favorite) <span class="badge bg-pink-50 text-pink-700 ring-1 ring-inset ring-pink-600/20 align-middle ml-1" title="Favorite">★</span> @endif
                        </h3>
                        <p class="text-brand text-sm md:text-lg font-bold uppercase tracking-widest">{{ $selectedPlayer->role }}</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
    @endif

</div>
