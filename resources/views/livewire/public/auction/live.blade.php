<div x-data="auctionTimer()"
     x-on:player-changed.window="startTimer($event.detail.timerEndAt)"
     x-on:bid-placed.window="pulseBid(); startTimer($event.detail.timerEndAt)"
     x-on:player-sold.window="triggerSold()"
     x-on:player-unsold.window="triggerUnsold()"
     class="min-h-screen bg-[#0B0F19] text-white flex flex-col relative overflow-hidden">
    
    <!-- Header -->
    <div class="px-8 py-4 flex justify-between items-center border-b border-white/10 z-10 bg-[#141B2D]/80 backdrop-blur-md">
        <h1 class="text-2xl font-bold tracking-wider text-[#FFC800] uppercase">
            {{ $auction->title }}
        </h1>
        <div class="flex items-center space-x-4">
            @if($auction->status === 'live')
                <span class="flex h-3 w-3 relative">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                </span>
                <span class="text-red-500 font-semibold tracking-widest uppercase text-sm">Live</span>
            @else
                <span class="text-gray-400 font-semibold tracking-widest uppercase text-sm">{{ $auction->status }}</span>
            @endif
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 grid grid-cols-2 relative p-2 md:p-8 gap-4 md:gap-8 overflow-hidden h-[calc(100vh-80px)] w-full">
        
        <!-- LEFT COLUMN: Active Auction -->
        <div class="flex flex-col justify-center items-center relative z-10 w-full h-full overflow-y-auto">
            
            <!-- Background Glows -->
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-[#FFC800]/5 rounded-full blur-[100px] pointer-events-none"></div>

            @if($auction->status === 'completed')
                <div class="text-center z-10">
                    <h2 class="text-5xl font-bold text-[#FFC800] mb-4">Auction Completed</h2>
                    <p class="text-gray-400 text-xl">Thank you for joining the auction.</p>
                </div>
            @elseif($currentPlayer)
                <!-- Player Card Container -->
                <div class="relative z-10 w-full max-w-4xl flex flex-col md:flex-row items-center gap-8 p-8 bg-[#141B2D]/80 backdrop-blur-xl rounded-3xl border border-white/10 shadow-2xl transition-all duration-500">
                    
                    <!-- Player Photo -->
                    <div class="relative w-64 h-64 md:w-80 md:h-80 shrink-0">
                        <div class="absolute inset-0 rounded-full bg-gradient-to-tr from-[#FFC800]/20 to-transparent blur-xl"></div>
                        <img src="{{ $currentPlayer['player']['photo'] ? Storage::url($currentPlayer['player']['photo']) : 'https://ui-avatars.com/api/?name='.urlencode($currentPlayer['player']['name']).'&background=FFC800&color=0B0F19&size=512' }}" 
                             alt="{{ $currentPlayer['player']['name'] }}" 
                             class="relative w-full h-full object-cover rounded-full border-4 border-[#FFC800]/30 shadow-2xl z-10">
                        
                        <!-- Role Badge -->
                        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 px-6 py-2 bg-gradient-to-r from-[#FFC800] to-yellow-600 text-[#0B0F19] rounded-full font-bold text-sm uppercase tracking-wider shadow-lg z-20 whitespace-nowrap">
                            {{ $currentPlayer['player']['role'] }}
                        </div>
                    </div>

                    <!-- Player Details & Bidding -->
                    <div class="flex-1 text-center md:text-left space-y-6">
                        <div>
                            <div class="flex items-center justify-center md:justify-start gap-4 mb-2">
                                <h2 class="text-4xl md:text-5xl font-extrabold tracking-tight">{{ $currentPlayer['player']['name'] }}</h2>
                                @if($currentPlayer['player']['country'])
                                    <span class="px-3 py-1 bg-white/10 rounded text-sm uppercase tracking-widest">{{ $currentPlayer['player']['country'] }}</span>
                                @endif
                            </div>
                            <div class="flex items-center justify-center md:justify-start gap-3 text-gray-400 text-lg">
                                <span>Base: ₹{{ number_format($currentPlayer['player']['base_price']) }}</span>
                                <span>&bull;</span>
                                <span class="uppercase">{{ str_replace('-', ' ', $currentPlayer['player']['category']) }}</span>
                            </div>
                        </div>

                        <!-- Current Bid Display -->
                        <div class="bg-black/40 rounded-2xl p-6 border border-white/5 relative overflow-hidden" 
                             x-bind:class="{ 'ring-2 ring-[#FFC800] shadow-[0_0_30px_rgba(255,200,0,0.3)] scale-105': isPulsing }"
                             style="transition: all 0.3s ease-out;">
                            <p class="text-gray-400 uppercase tracking-widest text-sm font-semibold mb-2">Current Bid</p>
                            
                            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                                <div class="text-5xl md:text-7xl font-black text-[#FFC800]">
                                    ₹{{ number_format($currentHighestBid > 0 ? $currentHighestBid : $currentPlayer['player']['base_price']) }}
                                </div>
                                
                                @if($currentHighestTeam)
                                    <div class="flex items-center gap-3 bg-[#141B2D] p-3 rounded-xl border border-white/10">
                                        <img src="{{ $currentHighestTeam['logo'] ? Storage::url($currentHighestTeam['logo']) : 'https://ui-avatars.com/api/?name='.urlencode($currentHighestTeam['name']).'&background=random' }}" 
                                             alt="{{ $currentHighestTeam['name'] }}" 
                                             class="w-10 h-10 rounded-full object-cover">
                                        <span class="font-bold">{{ $currentHighestTeam['short_name'] }}</span>
                                    </div>
                                @else
                                    <div class="text-gray-500 font-semibold italic">Awaiting bids...</div>
                                @endif
                            </div>

                            <!-- Timer Bar -->
                            <div class="mt-6 h-2 w-full bg-gray-800 rounded-full overflow-hidden relative">
                                <div class="absolute top-0 left-0 h-full transition-all duration-100 ease-linear rounded-full"
                                     x-bind:style="'width: ' + timerProgress + '%'"
                                     x-bind:class="timerProgress < 20 ? 'bg-red-500 shadow-[0_0_10px_#ef4444]' : 'bg-[#00C853] shadow-[0_0_10px_#00c853]'">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Stats Grid -->
                        @if($currentPlayer['player']['stats'])
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @foreach($currentPlayer['player']['stats'] as $key => $val)
                                    <div class="bg-white/5 rounded-xl p-3 text-center border border-white/5">
                                        <div class="text-xs text-gray-400 uppercase tracking-wider mb-1">{{ str_replace('_', ' ', $key) }}</div>
                                        <div class="font-bold text-lg">{{ $val }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Overlays -->
                    @if($statusOverlay === 'sold')
                        <div class="absolute inset-0 z-50 bg-[#00C853]/90 backdrop-blur-sm rounded-3xl flex flex-col items-center justify-center shadow-[0_0_100px_rgba(0,200,83,0.5)] animate-in fade-in zoom-in duration-300">
                            <div class="text-8xl font-black text-white tracking-widest uppercase drop-shadow-2xl mb-4">SOLD</div>
                            @if($currentHighestTeam)
                                <div class="text-3xl font-bold text-white drop-shadow-lg bg-black/30 px-6 py-2 rounded-full">
                                    To {{ $currentHighestTeam['name'] }} for ₹{{ number_format($currentHighestBid) }}
                                </div>
                            @endif
                        </div>
                    @elseif($statusOverlay === 'unsold')
                        <div class="absolute inset-0 z-50 bg-gray-900/95 backdrop-blur-md rounded-3xl flex items-center justify-center animate-in fade-in duration-500">
                            <div class="text-7xl font-black text-gray-500 tracking-widest uppercase drop-shadow-2xl opacity-50">UNSOLD</div>
                        </div>
                    @endif
                </div>
            @else
                <div class="text-center z-10 animate-pulse">
                    <div class="w-16 h-16 border-4 border-[#FFC800] border-t-transparent rounded-full animate-spin mx-auto mb-6"></div>
                    <h2 class="text-3xl font-bold text-gray-300">Waiting for next player...</h2>
                </div>
            @endif
        </div>
<!-- RIGHT COLUMN: Teams and Squads -->
        <div class="w-full bg-[#141B2D]/80 backdrop-blur-xl border border-white/10 rounded-3xl shadow-2xl flex flex-col z-10 overflow-hidden h-full">
            <h3 class="font-black text-xl text-white uppercase tracking-widest sticky top-0 bg-[#0B0F19]/90 backdrop-blur-sm p-6 z-10 border-b border-white/10">Teams & Squads</h3>
            
            <div class="flex-1 overflow-y-auto p-6 space-y-6 custom-scrollbar">
                @foreach($teams as $team)
                <div class="bg-[#141B2D] border border-white/10 rounded-2xl overflow-hidden shadow-lg transition hover:border-white/20">
                    <!-- Team Header -->
                    <div class="p-4 border-b border-white/5 flex items-center justify-between" style="background: linear-gradient(to right, {{ $team['primary_color'] ?? '#333' }}22, transparent);">
                        <div class="flex items-center gap-3">
                            <img src="{{ $team['logo'] ? Storage::url($team['logo']) : 'https://ui-avatars.com/api/?name='.urlencode($team['name']).'&background=random' }}" 
                                 class="w-10 h-10 rounded-full border border-white/20 object-cover">
                            <div>
                                <h4 class="font-bold text-white text-sm uppercase">{{ $team['short_name'] }}</h4>
                                <p class="text-xs text-[#00C853] font-black">₹{{ number_format($team['remaining_budget']) }}</p>
                            </div>
                        </div>
                        <span class="bg-black/50 text-gray-300 text-[10px] uppercase font-bold px-2 py-1 rounded-full">
                            {{ count($team['auction_players'] ?? []) }} Players
                        </span>
                    </div>
                    
                    <!-- Team Players List -->
                    @if(!empty($team['auction_players']))
                        <div class="p-3 bg-black/20 max-h-[200px] overflow-y-auto custom-scrollbar space-y-2">
                            @foreach($team['auction_players'] as $ap)
                                <div class="flex items-center justify-between bg-white/5 rounded-lg p-2">
                                    <div class="flex items-center gap-2">
                                        <img src="{{ $ap['player']['photo'] ? Storage::url($ap['player']['photo']) : 'https://ui-avatars.com/api/?name='.urlencode($ap['player']['name']).'&background=random' }}" 
                                             class="w-6 h-6 rounded-full object-cover">
                                        <div>
                                            <p class="text-xs text-white font-bold leading-tight">{{ $ap['player']['name'] }}</p>
                                            <p class="text-[9px] text-gray-400 uppercase leading-tight">{{ $ap['player']['role'] }}</p>
                                        </div>
                                    </div>
                                    <div class="text-[10px] font-black text-[#FFC800]">
                                        ₹{{ number_format($ap['final_price'] ?? 0) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-4 text-center text-xs text-gray-500 italic">No players bought yet.</div>
                    @endif
                </div>
            @endforeach
            </div>
        </div>

            </div>

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
