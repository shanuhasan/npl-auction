<div>
    <x-slot name="header">
        <h1>{{ __('Auction Analytics') }}</h1>
        <p class="mt-1 text-sm font-normal text-muted">Highlights and spending across the latest auctions.</p>
    </x-slot>

    <div>
        <div class="space-y-6">
            
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <!-- Top Player Card -->
                <div class="card p-6">
                    <p class="text-sm font-medium text-muted">Most expensive player</p>
                    @if($topPlayer)
                        <div class="mt-4 flex items-center gap-4">
                            <img src="{{ $topPlayer->photo ? Storage::url($topPlayer->photo) : 'https://ui-avatars.com/api/?name='.urlencode($topPlayer->name).'&background=random' }}" class="h-16 w-16 rounded-full object-cover ring-1 ring-line">
                            <div class="min-w-0">
                                <h4 class="truncate text-lg font-semibold text-ink">{{ $topPlayer->name }}</h4>
                                <p class="mt-0.5 text-2xl font-semibold tracking-tight text-brand">₹{{ number_format($topPlayer->final_price) }}</p>
                            </div>
                        </div>
                    @else
                        <p class="mt-4 text-sm text-muted">No players sold yet.</p>
                    @endif
                </div>

                <!-- Top Spender Card -->
                <div class="card p-6">
                    <p class="text-sm font-medium text-muted">Highest spending team</p>
                    @if($topSpender && ($topSpender->budget - $topSpender->remaining_budget) > 0)
                        <div class="mt-4 flex items-center gap-4">
                            <img src="{{ $topSpender->logo ? Storage::url($topSpender->logo) : 'https://ui-avatars.com/api/?name='.urlencode($topSpender->name).'&background=random' }}" class="h-16 w-16 rounded-full object-cover ring-1 ring-line">
                            <div class="min-w-0">
                                <h4 class="truncate text-lg font-semibold text-ink">{{ $topSpender->name }}</h4>
                                <p class="mt-0.5 text-2xl font-semibold tracking-tight text-success">₹{{ number_format($topSpender->budget - $topSpender->remaining_budget) }}</p>
                            </div>
                        </div>
                    @else
                        <p class="mt-4 text-sm text-muted">No spending data available yet.</p>
                    @endif
                </div>
            </div>

            <!-- Chart Section -->
            <div class="card">
                <div class="card-header">
                    <div>
                        <h3 class="card-title">Category-wise spending</h3>
                        <p class="text-xs text-muted mt-0.5">Total amount (₹) spent on sold players per category</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mx-auto w-full max-w-lg" style="height: 360px;">
                        <canvas id="spendingChart"></canvas>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            const ctx = document.getElementById('spendingChart');
            
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($categoryLabels) !!},
                    datasets: [{
                        data: {!! json_encode($categoryData) !!},
                        backgroundColor: [
                            '#172554',
                            '#1D4ED8',
                            '#60A5FA',
                            '#BFDBFE'
                        ],
                        borderWidth: 2,
                        borderColor: '#FFFFFF',
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#475569',
                                font: {
                                    family: "'Inter', sans-serif",
                                    size: 13
                                },
                                usePointStyle: true,
                                pointStyle: 'circle',
                                padding: 18
                            }
                        }
                    },
                    cutout: '72%'
                }
            });
        });
    </script>
</div>
