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
    <div class="bg-[#141B2D] p-6 rounded-3xl shadow-lg border border-white/5 mb-10">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            
            <!-- Search -->
            <div class="md:col-span-1">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Search Name</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="e.g. Virat Kohli" class="w-full pl-10 pr-4 py-3 bg-black/30 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#FFC800] focus:border-transparent transition">
                </div>
            </div>

            <!-- Role Filter -->
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Role</label>
                <select wire:model.live="role" class="w-full px-4 py-3 bg-black/30 border border-white/10 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-[#FFC800] transition appearance-none">
                    <option value="">All Roles</option>
                    <option value="batsman">Batsman</option>
                    <option value="bowler">Bowler</option>
                    <option value="all-rounder">All-Rounder</option>
                    <option value="wicketkeeper">Wicketkeeper</option>
                </select>
            </div>

            <!-- Category Filter -->
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Category</label>
                <select wire:model.live="category" class="w-full px-4 py-3 bg-black/30 border border-white/10 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-[#FFC800] transition appearance-none">
                    <option value="">All Categories</option>
                    <option value="marquee">Marquee</option>
                    <option value="set-a">Set-A</option>
                    <option value="set-b">Set-B</option>
                    <option value="set-c">Set-C</option>
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Status</label>
                <select wire:model.live="status" class="w-full px-4 py-3 bg-black/30 border border-white/10 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-[#FFC800] transition appearance-none">
                    <option value="">All Statuses</option>
                    <option value="available">Available</option>
                    <option value="sold">Sold</option>
                    <option value="unsold">Unsold</option>
                </select>
            </div>
        </div>
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

                <div class="h-56 relative overflow-hidden bg-black/50 flex justify-center items-end pt-4">
                    <img src="{{ $player->photo ? Storage::url($player->photo) : 'https://ui-avatars.com/api/?name='.urlencode($player->name).'&background=374151&color=fff&size=512' }}" 
                         class="h-full w-auto object-cover group-hover:scale-105 transition duration-500">
                    
                    <div class="absolute top-3 left-3 bg-white/10 backdrop-blur-md px-3 py-1 rounded-full text-[10px] font-bold text-white uppercase border border-white/20">
                        {{ $player->country }}
                    </div>
                </div>

                <div class="p-5 flex flex-col flex-1">
                    <h4 class="text-xl font-black text-white mb-1">{{ $player->name }}</h4>
                    <p class="text-xs text-gray-400 font-semibold uppercase tracking-widest mb-4">{{ $player->role }} &bull; {{ $player->category }}</p>
                    
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
                            <div class="flex justify-between items-end">
                                <div>
                                    <p class="text-[10px] text-gray-500 uppercase tracking-widest">Base Price</p>
                                    <p class="font-black text-white text-lg">₹{{ number_format($player->base_price) }}</p>
                                </div>
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

</div>
