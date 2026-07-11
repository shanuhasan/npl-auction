<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    
    <div class="mb-10 text-center">
        <h1 class="text-4xl md:text-5xl font-black text-white uppercase tracking-widest mb-4">Franchises</h1>
        <p class="text-gray-400 text-lg">Explore the official teams and their squads.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
        @foreach($teams as $team)
            <a href="{{ route('public.teams.show', $team->id) }}" class="block group">
                <div class="bg-[#141B2D] border border-white/5 rounded-3xl overflow-hidden shadow-lg transition-all duration-300 transform group-hover:-translate-y-2 group-hover:shadow-[0_10px_30px_rgba(0,0,0,0.5)] group-hover:border-[var(--team-color)]" style="--team-color: {{ $team->primary_color }};">
                    
                    <!-- Team Header -->
                    <div class="h-24 relative overflow-hidden flex items-center justify-center border-b border-white/5 bg-gradient-to-br from-black/50 to-black/10">
                        <div class="absolute inset-0 opacity-20 transition-opacity duration-300 group-hover:opacity-40" style="background-color: {{ $team->primary_color }}"></div>
                        <img src="{{ $team->logo ? Storage::url($team->logo) : 'https://ui-avatars.com/api/?name='.urlencode($team->name).'&background=random' }}" 
                             alt="{{ $team->name }}" 
                             class="w-20 h-20 rounded-full object-cover relative z-10 border-2 border-white/20 shadow-xl bg-white">
                    </div>
                    
                    <!-- Team Body -->
                    <div class="p-6 text-center">
                        <h2 class="text-xl font-bold text-white tracking-wide group-hover:text-[var(--team-color)] transition-colors">{{ $team->name }}</h2>
                        <p class="text-sm font-semibold text-gray-400 uppercase tracking-widest mt-1">{{ $team->short_name }}</p>
                        
                        <div class="mt-6 grid grid-cols-2 gap-4">
                            <div class="bg-black/20 rounded-xl p-3 border border-white/5">
                                <p class="text-[10px] text-gray-500 uppercase tracking-widest">Spent</p>
                                <p class="font-bold text-red-400">₹{{ number_format($team->budget - $team->remaining_budget) }}</p>
                            </div>
                            <div class="bg-black/20 rounded-xl p-3 border border-white/5">
                                <p class="text-[10px] text-gray-500 uppercase tracking-widest">Purse</p>
                                <p class="font-bold text-[#00C853]">₹{{ number_format($team->remaining_budget) }}</p>
                            </div>
                        </div>

                        <div class="mt-4 pt-4 border-t border-white/5 flex justify-between items-center text-sm">
                            <span class="text-gray-400">Squad Size</span>
                            <span class="font-bold text-white bg-white/10 px-3 py-1 rounded-full">{{ $team->players_count }} Players</span>
                        </div>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</div>
