<div x-data="auctionTimer()"
     x-on:player-changed.window="startTimer($event.detail.timerEndAt)"
     x-on:bid-placed.window="pulseBid(); startTimer($event.detail.timerEndAt)"
     class="min-h-screen bg-[#0B0F19] text-white flex flex-col relative overflow-hidden"
     style="--team-color: {{ $myTeam->primary_color }};">
    
    <!-- Header: Team Info -->
    <div class="px-8 py-4 flex justify-between items-center border-b border-[var(--team-color)]/30 z-10 bg-[#141B2D]/90 backdrop-blur-md shadow-[0_0_20px_var(--team-color)]" style="box-shadow: 0 4px 30px rgba(0,0,0,0.5), 0 0 20px {{ $myTeam->primary_color }}33;">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-white transition p-2 bg-white/5 rounded-full border border-white/10 hover:bg-white/10" title="Back to Dashboard">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div class="flex items-center gap-4 border-l border-white/10 pl-4">
                <img src="{{ $myTeam->logo ? Storage::url($myTeam->logo) : 'https://ui-avatars.com/api/?name='.urlencode($myTeam->name).'&background=random' }}" 
                     alt="{{ $myTeam->name }}" 
                     class="w-12 h-12 rounded-full border-2 border-[var(--team-color)]">
                <div>
                    <h1 class="text-xl font-bold tracking-wider uppercase" style="color: var(--team-color);">
                        {{ $myTeam->name }}
                    </h1>
                    <p class="text-xs text-gray-400">Owner Dashboard</p>
                </div>
            </div>
        </div>
        <div class="text-right">
            <p class="text-sm text-gray-400 uppercase tracking-widest">Remaining Budget</p>
            <p class="text-3xl font-black text-[#00C853]">₹{{ number_format($myTeam->remaining_budget) }}</p>
        </div>
    </div>

    @error('bid')
        <div class="absolute top-20 left-1/2 -translate-x-1/2 z-50 bg-red-500 text-white px-6 py-3 rounded-xl shadow-2xl font-bold flex items-center gap-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ $message }}
        </div>
    @enderror

    <!-- Main Content -->
    <div class="flex-1 grid grid-cols-2 relative p-2 md:p-8 gap-4 md:gap-8 overflow-hidden h-[calc(100vh-80px)] w-full">
        <!-- Background Glows -->
        <div class="absolute top-1/2 left-1/4 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] rounded-full blur-[120px] pointer-events-none" style="background-color: var(--team-color); opacity: 0.15;"></div>

        <!-- LEFT COLUMN: My Squad -->
        <div class="w-full bg-[#141B2D]/80 backdrop-blur-xl border border-white/10 rounded-3xl shadow-2xl flex flex-col z-10 overflow-hidden h-full">
            <div class="p-6 border-b border-white/10 bg-black/20 flex justify-between items-center">
                <h3 class="font-bold text-lg uppercase tracking-wider text-white">My Squad</h3>
                <span class="bg-[var(--team-color)]/20 text-[var(--team-color)] px-3 py-1 rounded-full text-xs font-bold border border-[var(--team-color)]/30">
                    {{ count($squad) }} Players
                </span>
            </div>
            
            <div class="flex-1 overflow-y-auto p-4 space-y-3 custom-scrollbar">
                @forelse($squad as $boughtPlayer)
                    <div class="bg-black/30 border border-white/5 p-3 rounded-xl flex items-center justify-between hover:bg-black/50 transition">
                        <div class="flex items-center gap-3">
                            <img src="{{ $boughtPlayer['player']['photo'] ? Storage::url($boughtPlayer['player']['photo']) : 'https://ui-avatars.com/api/?name='.urlencode($boughtPlayer['player']['name']).'&background=random' }}" 
                                 class="w-12 h-12 rounded-lg object-cover border border-white/10">
                            <div>
                                <h4 class="font-bold text-sm text-white">{{ $boughtPlayer['player']['name'] }}</h4>
                                <p class="text-xs text-gray-500 uppercase">{{ $boughtPlayer['player']['role'] }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] text-gray-500 uppercase tracking-widest">Bought For</p>
                            <p class="font-black text-[#00C853] text-sm">₹{{ number_format($boughtPlayer['final_price']) }}</p>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center h-full text-center opacity-50 space-y-4 py-10">
                        <svg class="w-16 h-16 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <div>
                            <p class="text-gray-300 font-bold">No Players Bought Yet</p>
                            <p class="text-gray-500 text-xs mt-1">Start bidding to build your squad!</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- RIGHT COLUMN: Active Bidding -->
        <div class="flex flex-col justify-center items-center relative z-10 w-full h-full overflow-y-auto">
            @if($auction->status === 'completed')
                <div class="text-center z-10">
                    <h2 class="text-5xl font-bold text-[#FFC800] mb-4">Auction Completed</h2>
                </div>
            @elseif($currentPlayer)
                <!-- Player Card Container -->
                <div class="w-full flex flex-col gap-6 p-6 lg:p-10 bg-[#141B2D]/90 backdrop-blur-2xl rounded-3xl border border-white/10 shadow-2xl transition-all duration-500">
                    
                    <div class="flex flex-col md:flex-row items-center gap-8 w-full">
                        <!-- Player Photo -->
                        <div class="relative w-48 h-48 lg:w-56 lg:h-56 shrink-0 mx-auto md:mx-0">
                            <img src="{{ $currentPlayer['player']['photo'] ? Storage::url($currentPlayer['player']['photo']) : 'https://ui-avatars.com/api/?name='.urlencode($currentPlayer['player']['name']).'&background=FFC800&color=0B0F19&size=512' }}" 
                                 alt="{{ $currentPlayer['player']['name'] }}" 
                                 class="relative w-full h-full object-cover rounded-full border-4 border-gray-600 shadow-2xl z-10">
                            <div class="absolute bottom-2 left-1/2 -translate-x-1/2 px-4 py-1 bg-gray-700 text-white rounded-full font-bold text-xs uppercase tracking-wider shadow-lg z-20 whitespace-nowrap">
                                {{ $currentPlayer['player']['role'] }}
                            </div>
                        </div>

                        <!-- Player Details -->
                        <div class="flex-1 text-center md:text-left space-y-4 w-full">
                            <div>
                                <h2 class="text-3xl lg:text-5xl font-extrabold tracking-tight mb-2">{{ $currentPlayer['player']['name'] }}</h2>
                                <div class="flex items-center justify-center md:justify-start gap-3 text-gray-400 mt-1 text-sm lg:text-base">
                                    <span class="px-2 py-1 bg-white/5 rounded">Base: ₹{{ number_format($currentPlayer['player']['base_price']) }}</span>
                                    <span>&bull;</span>
                                    <span>{{ $currentPlayer['player']['country'] }}</span>
                                </div>
                            </div>

                            <!-- Current Bid Display -->
                            <div class="bg-black/50 rounded-2xl p-6 border border-white/5 relative overflow-hidden" 
                                 x-bind:class="{ 'ring-2 ring-[var(--team-color)] shadow-[0_0_30px_var(--team-color)] scale-105': isPulsing }"
                                 style="transition: all 0.3s ease-out;">
                                <p class="text-gray-400 uppercase tracking-widest text-xs font-semibold mb-2">Current Highest Bid</p>
                                
                                <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                                    <div class="text-4xl lg:text-6xl font-black text-[#FFC800] tracking-tight">
                                        ₹{{ number_format($currentHighestBid > 0 ? $currentHighestBid : $currentPlayer['player']['base_price']) }}
                                    </div>
                                    
                                    @if($currentHighestTeam)
                                        <div class="flex items-center gap-2 bg-[#141B2D] px-4 py-2 rounded-xl border border-white/10 shadow-lg"
                                             @if($currentHighestTeam['id'] === $myTeam->id) style="border-color: var(--team-color); box-shadow: 0 0 15px var(--team-color);" @endif>
                                            <span class="font-bold text-sm @if($currentHighestTeam['id'] === $myTeam->id) text-[#00C853] @endif tracking-wider">
                                                {{ $currentHighestTeam['id'] === $myTeam->id ? 'YOUR TEAM' : $currentHighestTeam['short_name'] }}
                                            </span>
                                        </div>
                                    @else
                                        <div class="text-gray-500 font-semibold italic text-sm">Awaiting bids...</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bidding Action Area -->
                    <div class="w-full mt-4 pt-6 border-t border-white/10">
                        <!-- Timer Bar -->
                        <div class="h-2 w-full bg-gray-800 rounded-full overflow-hidden relative mb-6 shadow-inner">
                            <div class="absolute top-0 left-0 h-full transition-all duration-100 ease-linear rounded-full"
                                 x-bind:style="'width: ' + timerProgress + '%'"
                                 x-bind:class="timerProgress < 20 ? 'bg-red-500 shadow-[0_0_15px_#ef4444]' : 'bg-[var(--team-color)] shadow-[0_0_15px_var(--team-color)]'">
                            </div>
                        </div>

                        <!-- Bidding Button -->
                        @if($statusOverlay)
                            <div class="w-full py-5 rounded-2xl font-black text-2xl uppercase tracking-widest text-center text-white shadow-2xl"
                                 style="background-color: {{ $statusOverlay === 'sold' ? '#00C853' : '#ef4444' }};">
                                Player {{ strtoupper($statusOverlay) }}
                            </div>
                        @else
                            <button wire:click="placeBid"
                                    wire:loading.attr="disabled"
                                    @if(!$canBid) disabled @endif
                                    class="w-full py-5 rounded-2xl font-black text-2xl lg:text-3xl uppercase tracking-widest transition-all duration-200 flex items-center justify-center gap-4 group disabled:opacity-50 disabled:cursor-not-allowed hover:scale-[1.02]"
                                    style="background-color: {{ $canBid ? 'var(--team-color)' : '#374151' }}; box-shadow: {{ $canBid ? '0 10px 30px var(--team-color)' : 'none' }}; color: #0B0F19;">
                                <span wire:loading.remove>
                                    @if($currentHighestTeam && $currentHighestTeam['id'] === $myTeam->id)
                                        Highest Bidder
                                    @elseif($myTeam->remaining_budget < $nextValidBid)
                                        Insufficient Budget
                                    @else
                                        Place Bid (₹{{ number_format($nextValidBid) }})
                                    @endif
                                </span>
                                <span wire:loading>
                                    <svg class="animate-spin -ml-1 mr-3 h-8 w-8 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Processing...
                                </span>
                            </button>
                        @endif
                    </div>

                </div>
            @else
                <div class="text-center z-10 animate-pulse">
                    <h2 class="text-3xl lg:text-5xl font-bold text-gray-400">Awaiting Next Player...</h2>
                    <p class="text-gray-600 mt-4">The auctioneer will call the next player soon.</p>
                </div>
            @endif
        </div>
    </div>
    
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }
    </style>

    <!-- Alpine Script -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('auctionTimer', () => ({
                timerProgress: 100,
                interval: null,
                isPulsing: false,
                totalSeconds: {{ $state ? $state->timer_seconds : 15 }},

                init() {
                    @if($timerEndAt && !$statusOverlay)
                        this.startTimer('{{ $timerEndAt->toISOString() }}');
                    @endif
                },

                startTimer(endAtStr) {
                    if(!endAtStr) {
                        this.timerProgress = 0;
                        return;
                    }
                    if(this.interval) clearInterval(this.interval);

                    const endAt = new Date(endAtStr).getTime();
                    
                    this.interval = setInterval(() => {
                        const now = new Date().getTime();
                        const remainingMs = endAt - now;
                        
                        if (remainingMs <= 0) {
                            this.timerProgress = 0;
                            clearInterval(this.interval);
                        } else {
                            const totalMs = this.totalSeconds * 1000;
                            this.timerProgress = (remainingMs / totalMs) * 100;
                        }
                    }, 50);
                },

                pulseBid() {
                    this.isPulsing = true;
                    setTimeout(() => { this.isPulsing = false; }, 300);
                    // Play bid sound
                    let audio = new Audio('https://actions.google.com/sounds/v1/alarms/beep_short.ogg');
                    audio.volume = 0.5;
                    audio.play().catch(e => console.log('Audio play blocked:', e));
                }
            }))
        })
    </script>
</div>
