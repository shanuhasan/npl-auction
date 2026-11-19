<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    
    <div class="page-intro mb-10 flex flex-col md:flex-row md:items-end justify-between text-center md:text-left gap-4">
        <div>
            <h1 class="text-4xl md:text-3xl font-semibold text-ink mb-2 md:mb-4 tracking-tight">Franchises</h1>
            <p class="text-muted text-lg">Explore the official teams and their squads.</p>
        </div>
        <div>
            <a href="{{ route('public.teams.register') }}" class="inline-block bg-brand hover:bg-brand-hover text-white font-medium py-3 px-6 rounded-lg transition-colors shadow-sm">Register Your Team</a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
        @foreach($teams as $team)
            <a href="{{ route('public.teams.show', $team->id) }}" class="block group">
                <div class="bg-surface border border-line rounded-2xl overflow-hidden shadow-sm transition-all duration-300 transform group-hover:-translate-y-2 group-hover:shadow-[0_10px_30px_rgba(0,0,0,0.5)] group-hover:border-[var(--team-color)]" style="--team-color: {{ $team->primary_color }};">
                    
                    <!-- Team Header -->
                    <div class="h-24 relative overflow-hidden flex items-center justify-center border-b border-line bg-gradient-to-br from-black/50 to-black/10">
                        <div class="absolute inset-0 opacity-20 transition-opacity duration-300 group-hover:opacity-40" style="background-color: {{ $team->primary_color }}"></div>
                        <img src="{{ $team->logo ? Storage::url($team->logo) : 'https://ui-avatars.com/api/?name='.urlencode($team->name).'&background=random' }}" 
                             alt="{{ $team->name }}" 
                             class="w-20 h-20 rounded-full object-cover relative z-10 border-2 border-line shadow-sm bg-white">
                    </div>
                    
                    <!-- Team Body -->
                    <div class="p-6 text-center">
                        <h2 class="text-xl font-semibold text-ink group-hover:text-[var(--team-color)] transition-colors tracking-tight">{{ $team->name }} ({{ $team->short_name }})</h2>
                        @if($team->owner)
                            <p class="text-sm font-medium text-ink-soft mt-2">Owner: {{ $team->owner->name }}</p>
                        @endif
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</div>
