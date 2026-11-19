<div x-data="auctionTimer()"
     x-on:player-changed.window="startTimer($event.detail.timerEndAt)"
     x-on:bid-placed.window="pulseBid(); startTimer($event.detail.timerEndAt)"
     x-on:player-sold.window="triggerSold()"
     x-on:player-unsold.window="triggerUnsold()"
     class="relative flex min-h-screen flex-col overflow-hidden bg-brand-deep text-white"
     style="background: radial-gradient(1100px 500px at 85% -10%, rgb(var(--color-accent) / .14), transparent 60%), radial-gradient(900px 500px at 0% 110%, rgb(var(--color-brand) / .9), transparent 60%), rgb(var(--color-brand-deep));">

    <!-- Header -->
    <header class="relative z-10 flex items-center justify-between gap-4 border-b border-white/10 px-4 py-4 md:px-10">
        <a href="{{ url('/') }}" class="flex min-w-0 items-center gap-3">
            @if(setting('logo'))
                <img src="{{ asset('storage/' . setting('logo')) }}" alt="{{ setting('app_name', 'NPL') }}" class="h-10 w-auto max-w-[110px] object-contain md:h-12">
            @endif
            <div class="min-w-0">
                <p class="eyebrow">Player Auction</p>
                <h1 class="truncate text-lg font-semibold tracking-tight md:text-2xl">{{ $auction->title }}</h1>
            </div>
        </a>

        @if($auction->status === 'live')
            <span class="inline-flex shrink-0 items-center gap-2 rounded-full border border-red-400/40 bg-red-500/15 px-3 py-1.5 text-xs font-semibold uppercase tracking-[0.18em] text-red-100 md:px-4 md:text-sm">
                <span class="relative flex h-2.5 w-2.5">
                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-red-500"></span>
                </span>
                Live
            </span>
        @else
            <span class="shrink-0 rounded-full border border-white/15 bg-white/5 px-3 py-1.5 text-xs font-semibold uppercase tracking-[0.18em] text-white/70 md:text-sm">{{ ucfirst($auction->status) }}</span>
        @endif
    </header>

    <!-- Stage -->
    <main class="relative z-10 flex flex-1 items-center justify-center px-4 py-8 md:px-10 md:py-10">
        @if($auction->status === 'completed')
            <div class="text-center">
                <div class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-accent text-brand-deep">
                    <x-icon name="trophy" class="h-8 w-8" stroke="1.8" />
                </div>
                <h2 class="text-3xl font-semibold tracking-tight md:text-5xl">Auction Completed</h2>
                <p class="mt-3 text-lg text-white/65">Thank you for joining the auction.</p>
            </div>

        @elseif($currentPlayer)
            @php
                $p = $currentPlayer['player'];
                $displayBid = $currentHighestBid > 0 ? $currentHighestBid : $p['base_price'];
            @endphp
            <div class="grid w-full max-w-6xl grid-cols-1 items-stretch gap-6 lg:grid-cols-[minmax(0,5fr)_minmax(0,6fr)] lg:gap-8">

                <!-- Player -->
                <section class="relative overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04] p-6 md:p-8">
                    <div class="flex flex-col items-center gap-6 sm:flex-row sm:items-end lg:flex-col lg:items-center">
                        <div class="relative w-52 shrink-0 md:w-64">
                            <div class="aspect-[4/5] overflow-hidden rounded-2xl bg-white/5 ring-2 ring-accent/70 ring-offset-4 ring-offset-brand-deep">
                                <img src="{{ $p['photo'] ? Storage::url($p['photo']) : 'https://ui-avatars.com/api/?name='.urlencode($p['name']).'&background=C9A227&color=0B1432&size=512' }}"
                                     alt="{{ $p['name'] }}" class="h-full w-full object-cover object-top">
                            </div>
                            <span class="absolute -bottom-3 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-full bg-accent px-4 py-1 text-sm font-semibold capitalize text-brand-deep shadow-sm">
                                {{ $p['role'] }}
                            </span>
                        </div>

                        <div class="w-full text-center sm:text-left lg:text-center">
                            <h2 class="text-3xl font-semibold leading-tight tracking-tight md:text-4xl">{{ $p['name'] }}</h2>
                            <div class="mt-3 flex flex-wrap items-center justify-center gap-2 sm:justify-start lg:justify-center">
                                @if($p['is_captain'] ?? false)
                                    <span class="rounded-full bg-white/10 px-2.5 py-0.5 text-xs font-medium text-white ring-1 ring-inset ring-white/20">Captain / Owner</span>
                                @endif
                                @if($p['is_favorite'] ?? false)
                                    <span class="rounded-full bg-pink-500/15 px-2.5 py-0.5 text-xs font-medium text-pink-200 ring-1 ring-inset ring-pink-400/30">Favorite</span>
                                @endif
                                @if(!empty($p['country']))
                                    <span class="rounded-full bg-white/5 px-2.5 py-0.5 text-xs font-medium text-white/70 ring-1 ring-inset ring-white/10">{{ $p['country'] }}</span>
                                @endif
                            </div>
                            @php $styles = collect([$p['batting_style'] ?? null, $p['bowling_style'] ?? null])->filter()->implode(' · '); @endphp
                            <dl class="mt-5 grid {{ $styles ? 'grid-cols-2' : 'grid-cols-1' }} gap-3 text-left">
                                <div class="rounded-xl border border-white/10 bg-white/[0.03] px-4 py-3">
                                    <dt class="text-xs uppercase tracking-wider text-white/50">Base price</dt>
                                    <dd class="mt-1 text-lg font-semibold">₹{{ number_format($p['base_price']) }}</dd>
                                </div>
                                @if($styles)
                                    <div class="rounded-xl border border-white/10 bg-white/[0.03] px-4 py-3">
                                        <dt class="text-xs uppercase tracking-wider text-white/50">Style</dt>
                                        <dd class="mt-1 truncate text-sm font-medium text-white/85">{{ $styles }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    </div>
                </section>

                <!-- Bid panel -->
                <section class="flex flex-col gap-6">
                    @if($statusOverlay === 'sold')
                        <div class="relative overflow-hidden rounded-3xl bg-success p-6 text-center shadow-pop md:p-8">
                            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-white/80">Sold</p>
                            <p class="mt-2 text-5xl font-bold tracking-tight md:text-7xl">₹{{ number_format($currentHighestBid) }}</p>
                            @if($currentHighestTeam)
                                <div class="mt-5 inline-flex items-center gap-3 rounded-full bg-white/15 py-1.5 pl-1.5 pr-5">
                                    <img src="{{ $currentHighestTeam['logo'] ? Storage::url($currentHighestTeam['logo']) : 'https://ui-avatars.com/api/?name='.urlencode($currentHighestTeam['name']).'&background=random' }}"
                                         alt="" class="h-10 w-10 rounded-full bg-white object-cover">
                                    <span class="text-lg font-semibold">{{ $currentHighestTeam['name'] }}</span>
                                </div>
                            @endif
                        </div>
                    @elseif($statusOverlay === 'unsold')
                        <div class="rounded-3xl border border-red-400/30 bg-red-500/15 p-8 text-center md:p-10">
                            <p class="text-5xl font-bold uppercase tracking-[0.2em] text-red-200 md:text-6xl">Unsold</p>
                            <p class="mt-3 text-white/60">No team bid for this player.</p>
                        </div>
                    @else
                        <!-- Current bid -->
                        <div class="relative overflow-hidden rounded-3xl border border-accent/30 bg-white/[0.04] p-6 transition-all duration-300 md:p-8"
                             x-bind:class="isPulsing ? 'scale-[1.02] border-accent bg-accent/10' : ''">
                            <p class="eyebrow">{{ $currentHighestBid > 0 ? 'Current bid' : 'Opening bid' }}</p>
                            <p class="mt-3 text-5xl font-bold tracking-tight text-accent sm:text-6xl xl:text-7xl">₹{{ number_format($displayBid) }}</p>

                            <!-- Timer -->
                            <div class="mt-6">
                                <div class="mb-2 flex items-center justify-between text-sm">
                                    <span class="text-white/60">Time left</span>
                                    <span class="font-semibold tabular-nums" x-bind:class="timerProgress < 20 ? 'text-red-300' : 'text-white'" x-text="secondsLeft + 's'"></span>
                                </div>
                                <div class="h-2.5 w-full overflow-hidden rounded-full bg-white/10">
                                    <div class="h-full rounded-full transition-[width] duration-100 ease-linear"
                                         x-bind:style="'width: ' + timerProgress + '%'"
                                         x-bind:class="timerProgress < 20 ? 'bg-red-500' : 'bg-accent'"></div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Leading team -->
                    <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 md:p-8 {{ $statusOverlay ? 'hidden' : '' }}">
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-white/50">Leading team</p>
                        @if($currentHighestTeam)
                            <div class="mt-4 flex items-center gap-4">
                                <img src="{{ $currentHighestTeam['logo'] ? Storage::url($currentHighestTeam['logo']) : 'https://ui-avatars.com/api/?name='.urlencode($currentHighestTeam['name']).'&background=random' }}"
                                     alt="{{ $currentHighestTeam['name'] }}"
                                     class="h-16 w-16 rounded-2xl bg-white object-cover md:h-20 md:w-20"
                                     style="box-shadow: 0 0 0 3px {{ $currentHighestTeam['primary_color'] ?? '#C9A227' }};">
                                <div class="min-w-0">
                                    <p class="truncate text-2xl font-semibold tracking-tight md:text-3xl">{{ $currentHighestTeam['name'] }}</p>
                                    <p class="mt-0.5 text-sm text-white/60">{{ $currentHighestTeam['short_name'] }} · Purse ₹{{ number_format($currentHighestTeam['remaining_budget']) }}</p>
                                </div>
                            </div>
                        @else
                            <p class="mt-4 text-2xl font-semibold text-white/40 md:text-3xl">Waiting for the first bid…</p>
                        @endif
                    </div>
                </section>
            </div>

        @else
            <div class="text-center">
                <div class="mx-auto mb-6 h-14 w-14 animate-spin rounded-full border-4 border-accent border-t-transparent"></div>
                <h2 class="text-2xl font-semibold tracking-tight md:text-4xl">Waiting for the next player…</h2>
                <p class="mt-2 text-white/60">The auctioneer will bring the next player up shortly.</p>
            </div>
        @endif
    </main>

    <!-- Footer -->
    <footer class="relative z-10 border-t border-white/10 px-4 py-3 text-center text-xs text-white/45 md:text-sm">
        &copy; {{ date('Y') }} {{ setting('app_name', 'NPLT20') }}. All rights reserved.
        @if(setting('developer_name'))
            <span class="mx-1">·</span> Developed by <a href="{{ setting('developer_url', '#') }}" target="_blank" class="font-medium text-white/70 hover:text-accent">{{ setting('developer_name') }}</a>
        @endif
    </footer>

    <!-- Alpine Script -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('auctionTimer', () => ({
                timerProgress: 100,
                secondsLeft: {{ $state ? $state->timer_seconds : 15 }},
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
                        this.secondsLeft = 0;
                        return;
                    }

                    if(this.interval) clearInterval(this.interval);

                    const endAt = new Date(endAtStr).getTime();

                    this.interval = setInterval(() => {
                        const now = new Date().getTime();
                        const remainingMs = endAt - now;

                        if (remainingMs <= 0) {
                            this.timerProgress = 0;
                            this.secondsLeft = 0;
                            clearInterval(this.interval);
                        } else {
                            const totalMs = this.totalSeconds * 1000;
                            this.timerProgress = Math.min(100, (remainingMs / totalMs) * 100);
                            this.secondsLeft = Math.ceil(remainingMs / 1000);
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
                    this.secondsLeft = 0;

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
                            colors: ['#C9A227', '#16A34A', '#ffffff']
                        });
                        confetti({
                            particleCount: 5,
                            angle: 120,
                            spread: 55,
                            origin: { x: 1 },
                            colors: ['#C9A227', '#16A34A', '#ffffff']
                        });

                        if (Date.now() < end) {
                            requestAnimationFrame(frame);
                        }
                    }());
                },

                triggerUnsold() {
                    if (this.interval) clearInterval(this.interval);
                    this.timerProgress = 0;
                    this.secondsLeft = 0;
                }
            }))
        })
    </script>
</div>
