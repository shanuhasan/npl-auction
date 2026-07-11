<div x-data="auctionTimer()"
     x-on:player-changed.window="startTimer($event.detail.timerEndAt)"
     x-on:bid-placed.window="pulseBid(); startTimer($event.detail.timerEndAt)"
     x-on:player-sold.window="triggerSold()"
     x-on:player-unsold.window="triggerUnsold()"
     class="min-h-screen bg-[#0B0F19] text-white flex flex-col relative overflow-hidden">
    
    <!-- Header -->
    <div class="px-4 md:px-8 py-4 flex flex-wrap gap-3 justify-between items-center border-b border-white/10 z-10 bg-[#141B2D]/80 backdrop-blur-md">
        <a href="{{ url('/') }}" class="text-xl md:text-2xl font-bold tracking-wider text-[#FFC800] uppercase hover:text-yellow-400 transition-colors">
            {{ $auction->title }}
        </a>
        <div class="flex items-center space-x-2 md:space-x-4">
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
    <div class="flex-1 flex flex-col lg:grid lg:grid-cols-2 relative p-4 md:p-8 gap-6 md:gap-8 overflow-y-auto lg:overflow-hidden h-auto lg:h-[calc(100vh-80px)] w-full custom-scrollbar">
        
        <!-- LEFT COLUMN: Active Auction -->
        <div class="flex flex-col justify-center items-center relative z-10 w-full lg:h-full lg:overflow-y-auto min-h-[60vh] lg:min-h-0 py-4 lg:py-0">
            
            <!-- Background Glows -->
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-[#FFC800]/5 rounded-full blur-[100px] pointer-events-none"></div>

            @if($auction->status === 'completed')
                <div class="text-center z-10">
                    <h2 class="text-5xl font-bold text-[#FFC800] mb-4">Auction Completed</h2>
                    <p class="text-gray-400 text-xl">Thank you for joining the auction.</p>
                </div>
            @elseif($currentPlayer)
                <!-- Player Card Container -->
                <div class="relative z-10 w-full max-w-4xl flex flex-col md:flex-row items-center md:items-stretch gap-6 md:gap-8 p-4 md:p-8 bg-[#141B2D]/80 backdrop-blur-xl rounded-3xl border border-white/10 shadow-2xl transition-all duration-500">
                    
                    <!-- Player Photo -->
                    <div class="relative w-48 h-64 sm:w-56 sm:h-72 md:w-64 md:h-80 shrink-0 mx-auto md:mx-0">
                        <div class="absolute inset-0 rounded-2xl bg-gradient-to-tr from-[#FFC800]/20 to-transparent blur-xl"></div>
                        <img src="{{ $currentPlayer['player']['photo'] ? Storage::url($currentPlayer['player']['photo']) : 'https://ui-avatars.com/api/?name='.urlencode($currentPlayer['player']['name']).'&background=FFC800&color=0B0F19&size=512' }}" 
                             alt="{{ $currentPlayer['player']['name'] }}" 
                             class="relative w-full h-full object-cover rounded-2xl border-4 border-[#FFC800]/30 shadow-2xl z-10 bg-[#0B0F19]">
                        
                        <!-- Role Badge -->
                        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 px-6 py-2 bg-gradient-to-r from-[#FFC800] to-yellow-600 text-[#0B0F19] rounded-full font-bold text-sm uppercase tracking-wider shadow-lg z-20 whitespace-nowrap">
                            {{ $currentPlayer['player']['role'] }}
                        </div>
                    </div>

                    <!-- Player Details & Bidding -->
                    <div class="flex-1 w-full text-center md:text-left space-y-4 md:space-y-6 flex flex-col justify-center">
                        <div>
                            <div class="flex flex-wrap items-center justify-center md:justify-start gap-2 md:gap-4 mb-2">
                                <h2 class="text-3xl sm:text-4xl md:text-5xl font-extrabold tracking-tight">{{ $currentPlayer['player']['name'] }}</h2>
                            </div>
                            <div class="flex items-center justify-center md:justify-start gap-3 text-gray-400 text-lg mb-3">
                                <span>Base: ₹{{ number_format($currentPlayer['player']['base_price']) }}</span>
                            </div>

                            @if($statusOverlay === 'sold')
                                <div class="inline-flex flex-col items-center md:items-start animate-in fade-in zoom-in duration-500 mb-4">
                                    <div class="flex flex-col md:flex-row items-center justify-center md:justify-start gap-3 bg-[#00C853] px-6 py-3 rounded-2xl border-2 border-white/50 shadow-[0_0_40px_rgba(0,200,83,0.8)] animate-pulse">
                                        <span class="text-3xl md:text-4xl font-black text-white tracking-widest uppercase drop-shadow-lg">SOLD</span>
                                        @if($currentHighestTeam)
                                            <div class="flex flex-wrap items-center justify-center gap-1.5 md:gap-2 border-t md:border-t-0 md:border-l border-white/40 pt-2 md:pt-0 md:pl-4">
                                                <span class="text-sm md:text-lg text-green-100 font-medium">To</span>
                                                <span class="text-base md:text-2xl font-extrabold text-white">{{ $currentHighestTeam['name'] }}</span>
                                                <span class="text-sm md:text-lg text-green-100 font-medium">for</span>
                                                <span class="text-xl md:text-3xl font-black text-[#FFC800] drop-shadow-md">₹{{ number_format($currentHighestBid) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @elseif($statusOverlay === 'unsold')
                                <div class="inline-flex flex-col items-center md:items-start animate-in fade-in zoom-in duration-500 mb-4">
                                    <div class="bg-red-600 px-8 py-3 rounded-2xl border-2 border-white/50 shadow-[0_0_40px_rgba(220,38,38,0.8)]">
                                        <span class="text-3xl md:text-4xl font-black text-white tracking-widest uppercase drop-shadow-lg">UNSOLD</span>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Current Bid Display -->
                        <div class="bg-black/40 rounded-2xl p-6 border border-white/5 relative overflow-hidden" 
                             x-bind:class="{ 'ring-2 ring-[#FFC800] shadow-[0_0_30px_rgba(255,200,0,0.3)] scale-105': isPulsing }"
                             style="transition: all 0.3s ease-out;">
                            <p class="text-gray-400 uppercase tracking-widest text-sm font-semibold mb-2">Current Bid</p>
                            
                            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                                <div class="text-4xl sm:text-5xl md:text-7xl font-black text-[#FFC800]">
                                    ₹{{ number_format($currentHighestBid > 0 ? $currentHighestBid : $currentPlayer['player']['base_price']) }}
                                </div>
                                
                                @if($currentHighestTeam)
                                    <div class="flex items-center gap-3 bg-[#141B2D] p-3 rounded-xl border border-white/10">
                                        <img src="{{ $currentHighestTeam['logo'] ? Storage::url($currentHighestTeam['logo']) : 'https://ui-avatars.com/api/?name='.urlencode($currentHighestTeam['name']).'&background=random' }}" 
                                             alt="{{ $currentHighestTeam['name'] }}" 
                                             class="w-10 h-10 rounded-full object-cover">
                                        <div class="flex flex-col text-left">
                                            <span class="font-bold text-sm leading-tight">{{ $currentHighestTeam['name'] }}</span>
                                            <span class="text-xs text-[#FFC800] font-black uppercase tracking-wider">{{ $currentHighestTeam['short_name'] }}</span>
                                        </div>
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
                        
                    </div>

                    <!-- Overlays (Moved inline below player name) -->
                </div>
            @else
                <div class="text-center z-10 animate-pulse">
                    <div class="w-12 h-12 md:w-16 md:h-16 border-4 border-[#FFC800] border-t-transparent rounded-full animate-spin mx-auto mb-4 md:mb-6"></div>
                    <h2 class="text-xl md:text-3xl font-bold text-gray-300">Waiting for next player...</h2>
                </div>
            @endif
        </div>
<!-- RIGHT COLUMN: Teams and Squads -->
        <div class="w-full bg-[#141B2D]/80 backdrop-blur-xl border border-white/10 rounded-3xl shadow-2xl flex flex-col z-10 overflow-hidden min-h-[500px] lg:h-full mt-4 lg:mt-0">
            <h3 class="font-black text-lg md:text-xl text-white uppercase tracking-widest sticky top-0 bg-[#0B0F19]/90 backdrop-blur-sm p-4 md:p-6 z-10 border-b border-white/10">Teams & Squads</h3>
            
            <div class="flex-1 overflow-y-auto p-6 space-y-6 custom-scrollbar">
                @foreach($teams as $team)
                <div class="bg-[#141B2D] border border-white/10 rounded-2xl overflow-hidden shadow-lg transition hover:border-white/20">
                    <!-- Team Header -->
                    <div class="p-4 border-b border-white/5 flex items-center justify-between" style="background: linear-gradient(to right, {{ $team['primary_color'] ?? '#333' }}22, transparent);">
                        <div class="flex items-center gap-3">
                            <img src="{{ $team['logo'] ? Storage::url($team['logo']) : 'https://ui-avatars.com/api/?name='.urlencode($team['name']).'&background=random' }}" 
                                 class="w-10 h-10 rounded-full border border-white/20 object-cover">
                            <div class="flex flex-col text-left">
                                <h4 class="font-bold text-white text-sm leading-tight">{{ $team['name'] }}</h4>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="text-[10px] text-[#FFC800] font-black uppercase tracking-wider">{{ $team['short_name'] }}</span>
                                    <span class="text-gray-500 text-[10px]">&bull;</span>
                                    <p class="text-xs text-[#00C853] font-black">₹{{ number_format($team['remaining_budget']) }}</p>
                                </div>
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
