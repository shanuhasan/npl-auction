<div x-data="auctionTimer()"
     x-on:player-changed.window="startTimer($event.detail.timerEndAt)"
     x-on:bid-placed.window="pulseBid(); startTimer($event.detail.timerEndAt)"
     class="min-h-screen bg-canvas text-ink flex flex-col relative overflow-hidden"
     style="--team-color: {{ $myTeam->primary_color }};">
    
    <!-- Header: Team Info -->
    <div class="px-4 md:px-8 py-3 md:py-4 flex items-center justify-between relative border-b border-[var(--team-color)]/30 z-10 bg-surface/90 backdrop-blur-md" style="box-shadow: 0 4px 30px rgba(0,0,0,0.5), 0 0 20px {{ $myTeam->primary_color }}33;">
        
        <!-- Left: Back Button -->
        <div class="flex-1 flex justify-start">
            <a href="{{ route('dashboard') }}" class="text-muted hover:text-ink transition p-2 bg-surface-muted rounded-full border border-line hover:bg-surface-subtle z-20" title="Back to Dashboard">
                <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
        </div>

        <!-- Center: Team Info -->
        <div class="flex-none flex items-center gap-3 md:gap-4 z-10">
            <img src="{{ $myTeam->logo ? Storage::url($myTeam->logo) : 'https://ui-avatars.com/api/?name='.urlencode($myTeam->name).'&background=random' }}" 
                 alt="{{ $myTeam->name }}" 
                 class="w-10 h-10 md:w-12 md:h-12 rounded-full border-2 border-[var(--team-color)] shadow-sm">
            <div class="text-left">
                <h1 class="text-lg md:text-xl font-semibold text-ink leading-tight tracking-tight">
                    {{ $myTeam->name }}
                </h1>
                <p class="text-[10px] md:text-xs text-[var(--team-color)] uppercase tracking-wider font-bold">Owner Dashboard</p>
            </div>
        </div>

        <!-- Right: Empty Spacer (to keep Center perfectly aligned) -->
        <div class="flex-1"></div>
    </div>

    <!-- Sub Header: Auction Info & Budget -->
    <div class="px-4 md:px-8 py-2 md:py-3 bg-surface-subtle border-b border-line flex justify-between items-center z-10 relative shadow-sm">
        <!-- Left: Auction Name -->
        <div class="text-left z-10">
            <p class="text-[10px] md:text-xs text-muted uppercase tracking-widest mb-0.5 md:mb-1">Live Bidding Room</p>
            <h2 class="text-base md:text-2xl font-semibold text-brand truncate tracking-tight">{{ $auction->title }}</h2>
        </div>

        <!-- Right: Remaining Budget -->
        <div class="text-right z-10 bg-surface-subtle px-3 md:px-6 py-1.5 md:py-2 rounded-xl border border-line">
            <p class="text-[10px] md:text-xs text-muted uppercase tracking-widest mb-0.5 md:mb-1">Remaining Budget</p>
            <p class="text-lg md:text-3xl font-bold text-success tracking-tight">₹{{ number_format($myTeam->remaining_budget) }}</p>
        </div>
    </div>

    @error('bid')
        <div class="absolute top-20 left-1/2 -translate-x-1/2 z-50 bg-red-500 text-white px-6 py-3 rounded-xl shadow-sm font-bold flex items-center gap-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ $message }}
        </div>
    @enderror

    <!-- Main Content -->
    <div class="flex-1 flex flex-col relative p-2 md:p-8 gap-4 md:gap-8 overflow-y-auto h-[calc(100dvh-130px)] md:h-[calc(100dvh-80px)] w-full custom-scrollbar">
        <!-- Background Glows -->
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] rounded-full blur-[120px] pointer-events-none" style="background-color: var(--team-color); opacity: 0.15;"></div>

        <!-- TOP: Active Bidding -->
        <div class="flex flex-col relative z-10 w-full h-auto order-1">
            @if($auction->status === 'completed')
                <div class="text-center z-10 m-auto py-10 lg:py-0">
                    <h2 class="text-3xl md:text-4xl font-semibold text-brand mb-4 tracking-tight">Auction Completed</h2>
                </div>
            @elseif($currentPlayer)
                <!-- Player Card Container -->
                <div class="w-full flex flex-col gap-4 lg:gap-6 p-4 lg:p-8 bg-surface/90 backdrop-blur-2xl rounded-2xl border border-line shadow-sm transition-all duration-500 overflow-hidden">
                    
                    <div class="flex flex-col lg:flex-row items-center justify-center gap-6 lg:gap-12 w-full flex-1 custom-scrollbar py-2 lg:py-4">
                        <!-- Player Photo -->
                        <div class="relative w-48 h-60 lg:w-[360px] lg:h-[480px] shrink-0 mx-auto lg:mx-0">
                            <img src="{{ $currentPlayer['player']['photo'] ? Storage::url($currentPlayer['player']['photo']) : 'https://ui-avatars.com/api/?name='.urlencode($currentPlayer['player']['name']).'&background=FFC800&color=0B0F19&size=512' }}" 
                                 alt="{{ $currentPlayer['player']['name'] }}" 
                                 class="relative w-full h-full object-cover rounded-2xl border-4 border-line-strong shadow-sm z-10">
                            <div class="absolute -bottom-3 lg:-bottom-4 left-1/2 -translate-x-1/2 px-4 lg:px-6 py-1 lg:py-2 bg-surface-subtle text-ink rounded-full font-bold text-xs lg:text-base uppercase tracking-widest shadow-sm z-20 whitespace-nowrap border-2 border-line-strong">
                                {{ $currentPlayer['player']['role'] }}
                            </div>
                        </div>

                        <!-- Player Details -->
                        <div class="flex-1 text-center lg:text-left space-y-4 lg:space-y-6 w-full">
                            <div>
                                <h2 class="text-3xl md:text-4xl lg:text-5xl xl:text-7xl font-semibold mb-1 lg:mb-3 tracking-tight">{{ $currentPlayer['player']['name'] }}</h2>
                                <div class="flex flex-wrap items-center justify-center lg:justify-start gap-2 lg:gap-4 text-muted mt-2 text-xs lg:text-lg">
                                    <span class="px-2 py-1 bg-surface-muted rounded">Base: ₹{{ number_format($currentPlayer['player']['base_price']) }}</span>
                                    <span class="hidden md:inline">&bull;</span>
                                    <span>{{ $currentPlayer['player']['country'] }}</span>
                                </div>
                            </div>

                            <!-- Current Bid Display -->
                            <div class="bg-surface-subtle rounded-2xl p-4 lg:p-6 border border-line relative overflow-hidden" 
                                 x-bind:class="{ 'ring-2 ring-[var(--team-color)] scale-105': isPulsing }"
                                 style="transition: all 0.3s ease-out;">
                                <p class="text-muted uppercase tracking-widest text-xs lg:text-sm font-semibold mb-2 lg:mb-3">Current Highest Bid</p>
                                
                                <div class="flex flex-col xl:flex-row xl:items-end justify-between gap-4 lg:gap-6">
                                    <div class="text-4xl lg:text-7xl font-bold text-brand tracking-tight">
                                        ₹{{ number_format($currentHighestBid > 0 ? $currentHighestBid : $currentPlayer['player']['base_price']) }}
                                    </div>
                                    
                                    @if($currentHighestTeam)
                                        <div class="flex items-center justify-center gap-2 bg-surface px-4 py-2 lg:px-6 lg:py-3 rounded-xl border border-line shadow-sm"
                                             @if($currentHighestTeam['id'] === $myTeam->id) style="border-color: var(--team-color); box-shadow: 0 0 15px var(--team-color);" @endif>
                                            <span class="font-bold text-sm lg:text-lg @if($currentHighestTeam['id'] === $myTeam->id) text-success @endif tracking-wider text-center">
                                                {{ $currentHighestTeam['id'] === $myTeam->id ? 'YOUR TEAM' : $currentHighestTeam['name'] }} 
                                                <span class="text-sm text-muted">({{ $currentHighestTeam['short_name'] }})</span>
                                            </span>
                                        </div>
                                    @else
                                        <div class="text-muted font-semibold italic text-sm">Awaiting bids...</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bidding Action Area -->
                    <div class="w-full mt-4 lg:mt-auto pt-4 lg:pt-6 border-t border-line shrink-0">
                        <!-- Timer Bar -->
                        <div class="h-2 w-full bg-surface-subtle rounded-full overflow-hidden relative mb-4 lg:mb-6 shadow-inner">
                            <div class="absolute top-0 left-0 h-full transition-all duration-100 ease-linear rounded-full"
                                 x-bind:style="'width: ' + timerProgress + '%'"
                                 x-bind:class="timerProgress < 20 ? 'bg-red-500' 'bg-[var(--team-color)]'">
                            </div>
                        </div>

                        <!-- Bidding Button -->
                        @if($statusOverlay)
                            <div class="w-full py-4 lg:py-5 rounded-2xl font-bold text-xl lg:text-2xl uppercase tracking-widest text-center text-ink shadow-sm"
                                 style="background-color: {{ $statusOverlay === 'sold' ? '#00C853' : '#ef4444' }};">
                                Player {{ strtoupper($statusOverlay) }}
                            </div>
                        @else
                            <button wire:click="placeBid"
                                    wire:loading.attr="disabled"
                                    @if(!$canBid) disabled @endif
                                    class="w-full py-4 lg:py-5 rounded-xl font-semibold text-xl lg:text-2xl transition-all duration-200 flex items-center justify-center gap-2 lg:gap-4 group disabled:opacity-60 disabled:cursor-not-allowed hover:brightness-110 text-white"
                                    style="background-color: {{ $canBid ? 'var(--team-color)' : '#94A3B8' }}; box-shadow: {{ $canBid ? '0 8px 20px -8px var(--team-color)' : 'none' }};">
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
                                    <svg class="animate-spin -ml-1 mr-3 h-8 w-8 text-ink inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Processing...
                                </span>
                            </button>
                        @endif
                    </div>

                </div>
            @else
                <div class="text-center z-10 animate-pulse">
                    <h2 class="text-3xl lg:text-4xl font-semibold text-muted tracking-tight">Awaiting Next Player...</h2>
                    <p class="text-faint mt-4">The auctioneer will call the next player soon.</p>
                </div>
            @endif
        </div>

        <!-- BOTTOM: My Squad -->
        <div class="w-full bg-surface/80 backdrop-blur-xl border border-line rounded-2xl shadow-sm flex flex-col z-10 overflow-hidden h-[400px] md:h-[500px] order-2">
            <div class="p-4 md:p-6 border-b border-line bg-surface-muted flex justify-between items-center">
                <h3 class="font-semibold text-lg text-ink tracking-tight">My Squad</h3>
                <span class="bg-[var(--team-color)]/20 text-[var(--team-color)] px-3 py-1 rounded-full text-xs font-bold border border-[var(--team-color)]/30">
                    {{ count($squad) }} Players
                </span>
            </div>
            
            <div class="flex-1 overflow-y-auto p-4 space-y-3 custom-scrollbar">
                @forelse($squad as $boughtPlayer)
                    <div class="bg-surface-muted border border-line p-3 rounded-xl flex items-center justify-between hover:bg-surface-subtle transition">
                        <div class="flex items-center gap-3">
                            <img src="{{ $boughtPlayer['player']['photo'] ? Storage::url($boughtPlayer['player']['photo']) : 'https://ui-avatars.com/api/?name='.urlencode($boughtPlayer['player']['name']).'&background=random' }}" 
                                 class="w-12 h-12 rounded-lg object-cover border border-line">
                            <div>
                                <h4 class="font-semibold text-sm text-ink tracking-tight">{{ $boughtPlayer['player']['name'] }}</h4>
                                <p class="text-xs text-muted uppercase">{{ $boughtPlayer['player']['role'] }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] text-muted uppercase tracking-widest">Bought For</p>
                            <p class="font-bold text-success text-sm">₹{{ number_format($boughtPlayer['final_price']) }}</p>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center h-full text-center opacity-50 space-y-4 py-10">
                        <svg class="w-16 h-16 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <div>
                            <p class="text-ink-soft font-bold">No Players Bought Yet</p>
                            <p class="text-muted text-xs mt-1">Start bidding to build your squad!</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="w-full py-3 text-center text-xs md:text-sm text-muted border-t border-line bg-surface/80 backdrop-blur-md relative z-20 mt-auto">
        &copy; {{ date('Y') }} {{ setting('app_name', 'NPLT20') }}. All rights reserved. 
        @if(setting('developer_name'))
            <span class="mx-1">|</span> Developed by <a href="{{ setting('developer_url', '#') }}" target="_blank" class="text-brand hover:underline">{{ setting('developer_name') }}</a>
        @endif
    </footer>

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
