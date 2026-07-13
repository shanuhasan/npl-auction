<div>
    <!-- Hero Section -->
    <div class="relative w-full py-16 md:py-24 bg-[#0B0F19] overflow-hidden border-b border-[#FFC800]/20">
        <!-- Background Pattern/Gradient -->
        <div class="absolute inset-0 bg-gradient-to-r from-[#0B0F19] to-[#141B2D]">
            <div class="absolute inset-0 opacity-10 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGcgc3Ryb2tlPSIjZmZmIiBzdHJva2Utd2lkdGg9IjEiIGZpbGw9Im5vbmUiIGZpbGwtcnVsZT0iZXZlbm9kZCI+PHBhdGggZD0iTTAgNDBoNDBWMEgweiIvPjwvZz48L3N2Zz4=')]"></div>
        </div>
        
        <div class="relative z-10 max-w-[1400px] mx-auto px-4 md:px-8 flex flex-col md:flex-row justify-between items-center text-center md:text-left gap-8">
            <div>
                <h1 class="text-5xl md:text-7xl text-white heading-font uppercase tracking-widest drop-shadow-lg">
                    Player <span class="text-[#FFC800]">Directory</span>
                </h1>
                <p class="text-gray-300 mt-4 text-lg uppercase tracking-wide font-medium">
                    Search, filter, and track all players in the auction.
                </p>
            </div>
            <div>
                <a href="{{ route('public.players.register') }}" class="inline-block bg-[#FFC800] text-[#0B0F19] px-8 py-4 rounded-sm font-bold hover:bg-[#D4A000] transition shadow-lg text-lg uppercase tracking-widest">
                    Register as Player
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content Section -->
    <div class="bg-gray-50 min-h-screen">
        <div class="max-w-[1400px] mx-auto px-4 md:px-8 py-16">
            
            <!-- Filters Section -->
            <div class="bg-white p-4 rounded-xl shadow-md border-t-4 border-[#FFC800] mb-12 flex flex-col md:flex-row items-center gap-4 border-x border-b border-gray-200">
                
                <!-- Search -->
                <div class="relative w-full md:flex-1">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search players by name..." class="w-full pl-12 pr-4 py-4 bg-gray-50 border border-gray-200 rounded-sm text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#FFC800] transition font-medium">
                </div>

                <!-- Role Filter -->
                <select wire:model.live="role" class="w-full md:w-56 px-4 py-4 bg-gray-50 border border-gray-200 rounded-sm text-gray-700 font-bold uppercase focus:outline-none focus:ring-2 focus:ring-[#FFC800] transition appearance-none cursor-pointer">
                    <option value="">All Roles</option>
                    <option value="batsman">Batsman</option>
                    <option value="bowler">Bowler</option>
                    <option value="all-rounder">All-Rounder</option>
                    <option value="wicketkeeper">Wicketkeeper</option>
                </select>

                <!-- Status Filter -->
                <select wire:model.live="status" class="w-full md:w-56 px-4 py-4 bg-gray-50 border border-gray-200 rounded-sm text-gray-700 font-bold uppercase focus:outline-none focus:ring-2 focus:ring-[#FFC800] transition appearance-none cursor-pointer">
                    <option value="">All Statuses</option>
                    <option value="available">Available</option>
                    <option value="sold">Sold</option>
                    <option value="unsold">Unsold</option>
                </select>
            </div>

            <!-- Players Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                @forelse($players as $player)
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 relative flex flex-col h-full border-b-4 hover:-translate-y-1" style="border-bottom-color: {{ $player->currentTeam ? $player->currentTeam->primary_color : '#FFC800' }};">
                        
                        <!-- Status Badge -->
                        @if($player->status === 'sold')
                            <div class="absolute top-3 right-3 z-20">
                                <div class="bg-green-500 text-white text-[10px] font-black uppercase px-3 py-1.5 rounded-sm shadow-md border border-green-600">
                                    SOLD
                                </div>
                            </div>
                        @elseif($player->status === 'unsold')
                            <div class="absolute top-3 right-3 z-20">
                                <div class="bg-gray-500 text-white text-[10px] font-black uppercase px-3 py-1.5 rounded-sm shadow-md border border-gray-600">
                                    UNSOLD
                                </div>
                            </div>
                        @endif

                        <!-- Player Image Area -->
                        <div class="h-64 relative overflow-hidden bg-gradient-to-b from-[#141B2D] to-[#0B0F19] flex justify-center items-end pt-6">
                            <img src="{{ $player->photo ? Storage::url($player->photo) : 'https://ui-avatars.com/api/?name='.urlencode($player->name).'&background=141B2D&color=fff&size=512' }}" 
                                 class="h-full w-auto object-cover group-hover:scale-105 transition-transform duration-500" alt="{{ $player->name }}">
                            
                            <!-- Country Badge -->
                            <div class="absolute bottom-3 left-3 bg-white/20 backdrop-blur-md px-3 py-1 rounded-sm text-[10px] font-bold text-white uppercase border border-white/30 shadow-lg">
                                {{ $player->country }}
                            </div>
                        </div>

                        <!-- Player Details -->
                        <div class="p-6 flex flex-col flex-1 bg-white">
                            <h4 class="text-2xl heading-font text-[#0B0F19] mb-1 uppercase">{{ $player->name }}</h4>
                            <p class="text-xs text-gray-500 font-bold uppercase tracking-widest mb-6">{{ $player->role }}</p>
                            
                            <div class="mt-auto space-y-4">
                                @if($player->status === 'sold' && $player->currentTeam)
                                    <div class="bg-gray-50 p-4 rounded-sm border border-gray-200 relative overflow-hidden group-hover:bg-gray-100 transition-colors">
                                        <!-- Subtle Team Color Banner -->
                                        <div class="absolute top-0 left-0 w-1.5 h-full" style="background-color: {{ $player->currentTeam->primary_color }};"></div>
                                        
                                        <div class="flex justify-between items-center ml-2">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-white shadow-sm border border-gray-200 p-1">
                                                    <img src="{{ $player->currentTeam->logo ? Storage::url($player->currentTeam->logo) : 'https://ui-avatars.com/api/?name='.urlencode($player->currentTeam->name).'&background=random' }}" class="w-full h-full object-cover rounded-full">
                                                </div>
                                                <span class="text-xs font-black text-[#0B0F19] uppercase">{{ $player->currentTeam->short_name }}</span>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-[9px] text-gray-500 uppercase tracking-widest font-bold">Bought For</p>
                                                <p class="font-black text-xl text-[#0B0F19]">
                                                    @php
                                                        $bought = $player->auctionPlayers->first();
                                                    @endphp
                                                    ₹{{ $bought ? number_format($bought->final_price) : number_format($player->base_price) }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="p-4 border border-dashed border-gray-300 rounded-sm text-center">
                                        <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold">Base Price</p>
                                        <p class="font-black text-lg text-gray-700">
                                            ₹{{ number_format($player->base_price) }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-20 text-center bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <h3 class="text-2xl heading-font text-[#0B0F19] uppercase mb-2">No players found</h3>
                        <p class="text-gray-500 font-medium">Try adjusting your filters or search query.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-12">
                {{ $players->links(data: ['scrollTo' => false]) }}
            </div>

            <style>
                /* Custom Pagination Styling for Light Theme / IPL Aesthetic */
                nav[role="navigation"] {
                    @apply flex justify-between items-center gap-4;
                }
                nav[role="navigation"] p {
                    @apply text-gray-600 text-sm font-medium;
                }
                nav[role="navigation"] span.relative.z-0.inline-flex.shadow-sm.rounded-md {
                    @apply shadow-none flex gap-1;
                }
                nav[role="navigation"] a, nav[role="navigation"] span[aria-disabled="true"] span {
                    @apply bg-white border border-gray-200 text-gray-600 hover:text-black hover:bg-gray-50 px-4 py-2 text-sm font-bold transition rounded-sm shadow-sm;
                }
                nav[role="navigation"] span[aria-current="page"] span {
                    @apply bg-[#FFC800] text-black border-[#FFC800] hover:bg-[#FFC800];
                }
            </style>

        </div>
    </div>
</div>
