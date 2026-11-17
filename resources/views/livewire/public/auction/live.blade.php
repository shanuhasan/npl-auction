<div x-data="auctionTimer()"
     x-on:player-changed.window="startTimer($event.detail.timerEndAt)"
     x-on:bid-placed.window="pulseBid(); startTimer($event.detail.timerEndAt)"
     x-on:player-sold.window="triggerSold()"
     x-on:player-unsold.window="triggerUnsold()"
     class="min-h-screen bg-canvas text-ink flex flex-col relative overflow-hidden">
    
    <!-- Header -->
    <div class="px-4 md:px-8 py-4 flex flex-wrap gap-3 justify-between items-center border-b border-line z-10 bg-surface/80 backdrop-blur-md">
        <a href="{{ url('/') }}" class="text-xl md:text-2xl font-bold tracking-wider text-brand uppercase hover:text-amber-700 transition-colors">
            {{ $auction->title }}
        </a>
        <div class="flex items-center space-x-2 md:space-x-4">
            @if($auction->status === 'live')
                <span class="flex h-3 w-3 relative">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                </span>
                <span class="text-red-600 font-semibold tracking-widest uppercase text-sm">Live</span>
            @else
                <span class="text-muted font-semibold tracking-widest uppercase text-sm">{{ $auction->status }}</span>
            @endif
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col lg:grid lg:grid-cols-2 relative p-4 md:p-8 gap-6 md:gap-8 overflow-y-auto lg:overflow-hidden h-auto lg:h-[calc(100vh-80px)] w-full custom-scrollbar">
        
        <!-- LEFT COLUMN: Active Auction -->
        <div class="flex flex-col justify-center items-center relative z-10 w-full lg:h-full lg:overflow-y-auto min-h-[60vh] lg:min-h-0 py-4 lg:py-0">
            
            <!-- Background Glows -->
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-brand/5 rounded-full blur-[100px] pointer-events-none"></div>

            @if($auction->status === 'completed')
                <div class="text-center z-10">
                    <h2 class="text-3xl font-semibold text-brand mb-4 tracking-tight">Auction Completed</h2>
                    <p class="text-muted text-xl">Thank you for joining the auction.</p>
                </div>
            @elseif($currentPlayer)
                <!-- Player Card Container -->
                <div class="relative z-10 w-full max-w-4xl flex flex-col md:flex-row items-center md:items-stretch gap-6 md:gap-8 p-4 md:p-8 bg-surface/80 backdrop-blur-xl rounded-2xl border border-line shadow-pop transition-all duration-500">
                    
                    <!-- Player Photo -->
                    <div class="relative w-48 h-64 sm:w-56 sm:h-72 md:w-64 md:h-80 shrink-0 mx-auto md:mx-0">
                        <div class="absolute inset-0 rounded-2xl bg-gradient-to-tr from-brand/5 to-transparent blur-xl"></div>
                        <img src="{{ $currentPlayer['player']['photo'] ? Storage::url($currentPlayer['player']['photo']) : 'https://ui-avatars.com/api/?name='.urlencode($currentPlayer['player']['name']).'&background=FFC800&color=0B0F19&size=512' }}" 
                             alt="{{ $currentPlayer['player']['name'] }}" 
                             class="relative w-full h-full object-cover rounded-2xl border-4 border-brand/30 shadow-sm z-10 bg-canvas">
                        
                        <!-- Role Badge -->
                        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 px-5 py-1.5 bg-brand text-white rounded-full font-semibold text-sm shadow-sm capitalize z-20 whitespace-nowrap">
                            {{ $currentPlayer['player']['role'] }}
                        </div>
                    </div>

                    <!-- Player Details & Bidding -->
                    <div class="flex-1 w-full text-center md:text-left space-y-4 md:space-y-6 flex flex-col justify-center">
                        <div>
                            <div class="flex flex-wrap items-center justify-center md:justify-start gap-2 md:gap-4 mb-2">
                                <h2 class="text-3xl sm:text-4xl md:text-4xl font-semibold tracking-tight">
                                    {{ $currentPlayer['player']['name'] }}
                                    @if($currentPlayer['player']['is_captain'] ?? false) <span class="badge-brand align-middle ml-2" title="Captain/Owner">(Captain/Owner)</span> @endif
                                    @if($currentPlayer['player']['is_favorite'] ?? false) <span class="badge bg-pink-50 text-pink-700 ring-1 ring-inset ring-pink-600/20 align-middle ml-2" title="Favorite">(Favorite)</span> @endif
                                </h2>
                            </div>
                            <div class="flex items-center justify-center md:justify-start gap-3 text-muted text-lg mb-3">
                                <span>Base: ₹{{ number_format($currentPlayer['player']['base_price']) }}</span>
                            </div>

                            @if($statusOverlay === 'sold')
                                <div class="inline-flex flex-col items-center md:items-start animate-in fade-in zoom-in duration-500 mb-4">
                                    <div class="flex flex-col md:flex-row items-center justify-center md:justify-start gap-3 bg-success px-6 py-3 rounded-2xl border-2 border-line shadow-[0_0_40px_rgba(0,200,83,0.8)] animate-pulse">
                                        <span class="text-3xl md:text-4xl font-bold text-ink tracking-widest uppercase">SOLD</span>
                                        @if($currentHighestTeam)
                                            <div class="flex flex-wrap items-center justify-center gap-1.5 md:gap-2 border-t md:border-t-0 md:border-l border-line pt-2 md:pt-0 md:pl-4">
                                                <span class="text-sm md:text-lg text-green-600 font-medium">To</span>
                                                <span class="text-base md:text-2xl font-semibold text-ink">{{ $currentHighestTeam['name'] }}</span>
                                                <span class="text-sm md:text-lg text-green-600 font-medium">for</span>
                                                <span class="text-xl md:text-3xl font-bold text-brand">₹{{ number_format($currentHighestBid) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @elseif($statusOverlay === 'unsold')
                                <div class="inline-flex flex-col items-center md:items-start animate-in fade-in zoom-in duration-500 mb-4">
                                    <div class="bg-red-600 px-8 py-3 rounded-2xl border-2 border-line shadow-[0_0_40px_rgba(220,38,38,0.8)]">
                                        <span class="text-3xl md:text-4xl font-bold text-ink tracking-widest uppercase">UNSOLD</span>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Current Bid & Leading Team Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Current Bid Display -->
                            <div class="bg-surface-subtle rounded-2xl p-8 lg:p-12 min-h-[160px] md:min-h-[200px] lg:min-h-[240px] border border-line relative overflow-hidden flex flex-col justify-center" 
                                 x-bind:class="{ 'ring-2 ring-brand shadow-[0_0_30px_rgba(255,200,0,0.3)] scale-105 z-10': isPulsing }"
                                 style="transition: all 0.3s ease-out;">
                                <p class="text-muted uppercase tracking-widest text-sm md:text-lg font-semibold mb-2 lg:mb-4 text-center md:text-left">Current Bid</p>
                                <div style="font-size: 40px" class="text-4xl md:text-5xl lg:text-7xl xl:text-8xl font-bold text-brand text-center md:text-left tracking-tight">
                                    ₹{{ number_format($currentHighestBid > 0 ? $currentHighestBid : $currentPlayer['player']['base_price']) }}
                                </div>
                            </div>
                            
                            <!-- Leading Team Display -->
                            <div class="bg-surface-subtle rounded-2xl p-8 lg:p-12 min-h-[160px] md:min-h-[200px] lg:min-h-[240px] border border-line relative overflow-hidden flex flex-col justify-center"
                                 x-bind:class="{ 'ring-2 ring-brand shadow-[0_0_30px_rgba(255,200,0,0.3)] scale-105 z-10': isPulsing }"
                                 style="transition: all 0.3s ease-out;">
                                <p class="text-muted uppercase tracking-widest text-sm font-semibold mb-2 text-center md:text-left">Leading Team</p>
                                
                                @if($currentHighestTeam)
                                    <div class="flex items-center justify-center md:justify-start gap-4">
                                        <img src="{{ $currentHighestTeam['logo'] ? Storage::url($currentHighestTeam['logo']) : 'https://ui-avatars.com/api/?name='.urlencode($currentHighestTeam['name']).'&background=random' }}" 
                                             alt="{{ $currentHighestTeam['name'] }}" 
                                             class="w-16 h-16 md:w-20 md:h-20 rounded-full object-cover border-2 border-brand shadow-[0_0_15px_rgba(255,200,0,0.4)]">
                                        <div class="flex flex-col text-left">
                                            <span class="font-bold text-2xl md:text-4xl text-ink leading-tight uppercase">{{ $currentHighestTeam['short_name'] }}</span>
                                            <span class="text-sm md:text-base text-muted font-semibold">{{ $currentHighestTeam['name'] }}</span>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex h-full items-center justify-center md:justify-start">
                                        <span class="text-xl md:text-3xl font-bold text-faint uppercase tracking-widest animate-pulse">Waiting for bids...</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Timer Bar -->
                        <div class="mt-4 bg-surface-subtle rounded-2xl p-4 border border-line relative overflow-hidden">
                            <div class="h-3 w-full bg-surface-muted rounded-full overflow-hidden relative shadow-inner">
                                <div class="absolute top-0 left-0 h-full transition-all duration-100 ease-linear rounded-full"
                                     x-bind:style="'width: ' + timerProgress + '%'"
                                     x-bind:class="timerProgress < 20 ? 'bg-red-500' 'bg-success'">
                                </div>
                            </div>
                        </div>
                        
                    </div>

                    <!-- Overlays (Moved inline below player name) -->
                </div>
            @else
                <div class="text-center z-10 animate-pulse">
                    <div class="w-12 h-12 md:w-16 md:h-16 border-4 border-brand border-t-transparent rounded-full animate-spin mx-auto mb-4 md:mb-6"></div>
                    <h2 class="text-xl md:text-3xl font-semibold text-ink-soft tracking-tight">Waiting for next player...</h2>
                </div>
            @endif
        </div>
<!-- RIGHT COLUMN: Teams and Squads -->
        <div class="w-full bg-surface/80 backdrop-blur-xl border border-line rounded-2xl shadow-sm flex flex-col z-10 overflow-hidden min-h-[500px] lg:h-full mt-4 lg:mt-0">
            <h3 class="font-semibold text-lg md:text-xl text-ink sticky top-0 bg-canvas/90 backdrop-blur-sm p-4 md:p-6 z-10 border-b border-line tracking-tight">Teams & Squads</h3>
            
            <div class="flex-1 overflow-y-auto p-6 space-y-6 custom-scrollbar">
                @foreach($teams as $team)
                <div class="bg-surface border border-line rounded-2xl overflow-hidden shadow-sm transition hover:border-line-strong">
                    <!-- Team Header -->
                    <div class="p-4 border-b border-line flex items-center justify-between" style="background: linear-gradient(to right, {{ $team['primary_color'] ?? '#333' }}22, transparent);">
                        <div class="flex items-center gap-3">
                            <img src="{{ $team['logo'] ? Storage::url($team['logo']) : 'https://ui-avatars.com/api/?name='.urlencode($team['name']).'&background=random' }}" 
                                 class="w-10 h-10 rounded-full border border-line object-cover">
                            <div class="flex flex-col text-left">
                                <h4 class="font-semibold text-ink text-sm leading-tight tracking-tight">{{ $team['name'] }}</h4>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="text-[10px] text-brand font-bold uppercase tracking-wider">{{ $team['short_name'] }}</span>
                                    <span class="text-muted text-[10px]">&bull;</span>
                                    <p class="text-xs text-success font-bold">₹{{ number_format($team['remaining_budget']) }}</p>
                                </div>
                            </div>
                        </div>
                        <span class="bg-surface-subtle text-ink-soft text-[10px] uppercase font-bold px-2 py-1 rounded-full">
                            {{ count($team['auction_players'] ?? []) }} Players
                        </span>
                    </div>
                    
                    <!-- Team Players List -->
                    @if(!empty($team['auction_players']))
                        <div class="p-3 bg-surface-muted max-h-[200px] overflow-y-auto custom-scrollbar space-y-2">
                            @foreach($team['auction_players'] as $ap)
                                <div class="flex items-center justify-between bg-surface-muted rounded-lg p-2">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold text-muted w-4">{{ $loop->iteration }}.</span>
                                        <img src="{{ $ap['player']['photo'] ? Storage::url($ap['player']['photo']) : 'https://ui-avatars.com/api/?name='.urlencode($ap['player']['name']).'&background=random' }}" 
                                             class="w-6 h-6 rounded-full object-cover">
                                        <div>
                                            <p class="text-xs text-ink font-bold leading-tight">
                                                {{ $ap['player']['name'] }}
                                                @if($ap['player']['is_captain'] ?? false) <span class="align-middle ml-0.5 rounded-full bg-brand-soft px-1 py-0.5 text-[9px] font-medium text-brand" title="Captain/Owner">(Captain/Owner)</span> @endif
                                                @if($ap['player']['is_favorite'] ?? false) <span class="badge bg-pink-50 text-pink-700 ring-1 ring-inset ring-pink-600/20 align-middle ml-0.5" title="Favorite">(Favorite)</span> @endif
                                            </p>
                                            <p class="text-[9px] text-muted uppercase leading-tight">{{ $ap['player']['role'] }}</p>
                                        </div>
                                    </div>
                                    <div class="text-[10px] font-bold text-brand">
                                        ₹{{ number_format($ap['final_price'] ?? 0) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-4 text-center text-xs text-muted italic">No players bought yet.</div>
                    @endif
                </div>
            @endforeach
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

    <!-- Alpine Script -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('auctionTimer', () => ({
                timerProgress: 100,
                interval: null,
                isPulsing: false,
                totalSeconds: {{ $state ? $state->timer_seconds : 15 }},

                init() {
                    // Initialize timer if page loads with an active timer
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
                            // Calculate percentage
                            const totalMs = this.totalSeconds * 1000;
                            this.timerProgress = (remainingMs / totalMs) * 100;
                        }
                    }, 50); // 50ms for smooth animation
                },

                pulseBid() {
                    this.isPulsing = true;
                    setTimeout(() => { this.isPulsing = false; }, 300);
                    // Play bid sound
                    let audio = new Audio('https://actions.google.com/sounds/v1/alarms/beep_short.ogg');
                    audio.volume = 0.5;
                    audio.play().catch(e => console.log('Audio play blocked:', e));
                },

                triggerSold() {
                    if (this.interval) clearInterval(this.interval);
                    this.timerProgress = 0;
                    
                    // Play cheer sound
                    let audio = new Audio('https://actions.google.com/sounds/v1/crowds/crowd_cheering.ogg');
                    audio.volume = 0.8;
                    audio.play().catch(e => console.log('Audio play blocked:', e));
                    
                    // Fire Confetti
                    const duration = 3000;
                    const end = Date.now() + duration;

                    (function frame() {
                        confetti({
                            particleCount: 5,
                            angle: 60,
                            spread: 55,
                            origin: { x: 0 },
                            colors: ['#FFC800', '#00C853', '#ffffff']
                        });
                        confetti({
                            particleCount: 5,
                            angle: 120,
                            spread: 55,
                            origin: { x: 1 },
                            colors: ['#FFC800', '#00C853', '#ffffff']
                        });

                        if (Date.now() < end) {
                            requestAnimationFrame(frame);
                        }
                    }());
                },

                triggerUnsold() {
                    if (this.interval) clearInterval(this.interval);
                    this.timerProgress = 0;
                }
            }))
        })
    </script>
</div>
