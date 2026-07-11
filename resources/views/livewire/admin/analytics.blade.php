<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Auction Analytics') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <!-- Top Player Card -->
                <div class="bg-[#141B2D] border border-white/5 rounded-2xl p-6 shadow-xl relative overflow-hidden group">
                    <div class="absolute inset-0 bg-gradient-to-br from-[#FFC800]/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-4">Most Expensive Player</h3>
                    @if($topPlayer)
                        <div class="flex items-center gap-6 relative z-10">
                            <img src="{{ $topPlayer->photo ? Storage::url($topPlayer->photo) : 'https://ui-avatars.com/api/?name='.urlencode($topPlayer->name).'&background=random' }}" class="w-24 h-24 rounded-full border-2 border-[#FFC800]">
                            <div>
                                <h4 class="text-3xl font-black text-white">{{ $topPlayer->name }}</h4>
                                <p class="text-2xl font-bold text-[#FFC800] mt-2">₹{{ number_format($topPlayer->final_price) }}</p>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-400">No players sold yet.</p>
                    @endif
                </div>

                <!-- Top Spender Card -->
                <div class="bg-[#141B2D] border border-white/5 rounded-2xl p-6 shadow-xl relative overflow-hidden group">
                    <div class="absolute inset-0 bg-gradient-to-br from-[#00C853]/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-4">Highest Spending Team</h3>
                    @if($topSpender && ($topSpender->budget - $topSpender->remaining_budget) > 0)
                        <div class="flex items-center gap-6 relative z-10">
                            <img src="{{ $topSpender->logo ? Storage::url($topSpender->logo) : 'https://ui-avatars.com/api/?name='.urlencode($topSpender->name).'&background=random' }}" class="w-24 h-24 rounded-full border-2 border-[#00C853]">
                            <div>
                                <h4 class="text-3xl font-black text-white">{{ $topSpender->name }}</h4>
                                <p class="text-2xl font-bold text-[#00C853] mt-2">₹{{ number_format($topSpender->budget - $topSpender->remaining_budget) }}</p>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-400">No spending data available yet.</p>
                    @endif
                </div>
            </div>

            <!-- Chart Section -->
            <div class="bg-[#141B2D] border border-white/5 rounded-2xl p-6 shadow-xl">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-6">Category-wise Spending (₹)</h3>
                
                <div class="w-full max-w-lg mx-auto" style="height: 400px;">
                    <canvas id="spendingChart"></canvas>
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
                            '#FFC800',
                            '#E63946',
                            '#00C853',
                            '#4A90E2'
                        ],
                        borderWidth: 0,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#ccc',
                                font: {
                                    family: "'Poppins', sans-serif",
                                    size: 14
                                },
                                padding: 20
                            }
                        }
                    },
                    cutout: '70%'
                }
            });
        });
    </script>
</div>
