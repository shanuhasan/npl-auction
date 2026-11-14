@props(['fixture', 'highlightTeam' => null])
@php
    $f = $fixture;
    $statusClass = [
        'scheduled' => 'bg-white/10 text-gray-300',
        'live' => 'bg-red-600 text-white animate-pulse',
        'completed' => 'bg-[#00C853]/20 text-[#00C853]',
        'abandoned' => 'bg-yellow-600/20 text-yellow-400',
    ][$f->status];
    $logo = fn ($t) => $t->logo ? Storage::url($t->logo) : 'https://ui-avatars.com/api/?name=' . urlencode($t->name) . '&background=random';
@endphp
<div class="bg-[#141B2D] border border-white/5 rounded-2xl p-5 shadow-lg hover:border-[#FFC800]/40 transition">
    <div class="flex justify-between items-center text-xs uppercase tracking-widest text-gray-500 mb-4">
        <span>Match {{ $f->match_no }} &middot; {{ $f->stage }}</span>
        <span class="px-2 py-0.5 rounded-full font-bold {{ $statusClass }}">{{ $f->status === 'abandoned' ? 'No Result' : ucfirst($f->status) }}</span>
    </div>

    <div class="flex items-center justify-between gap-3">
        @foreach([[$f->teamA, $f->team_a_score], [$f->teamB, $f->team_b_score]] as $i => [$team, $score])
            @if($i === 1)
                <div class="text-gray-500 font-black text-sm">VS</div>
            @endif
            <a href="{{ route('public.teams.show', $team->id) }}" class="flex-1 flex flex-col items-center text-center group {{ $highlightTeam && $highlightTeam != $team->id ? 'opacity-80' : '' }}">
                <img src="{{ $logo($team) }}" alt="{{ $team->name }}" class="w-14 h-14 rounded-full object-cover bg-white border-2 mb-2" style="border-color: {{ $team->primary_color ?? '#fff' }}">
                <span class="font-bold text-white group-hover:text-[#FFC800] leading-tight {{ $f->winner_team_id == $team->id ? 'text-[#00C853]' : '' }}">{{ $team->short_name ?: $team->name }}</span>
                <span class="text-xs text-gray-500 hidden sm:block truncate max-w-full">{{ $team->name }}</span>
                @if($score)<span class="text-sm font-bold text-gray-200 mt-1">{{ $score }}</span>@endif
            </a>
        @endforeach
    </div>

    <div class="mt-4 pt-3 border-t border-white/5 text-center text-sm">
        @if($f->result || $f->winner)
            <p class="font-semibold text-[#FFC800]">{{ $f->winner ? $f->winner->name . ' ' : '' }}{{ $f->result }}</p>
        @endif
        <p class="text-gray-400">
            {{ $f->match_date ? $f->match_date->format('D, d M Y · h:i A') : 'Date TBD' }}
            @if($f->venue) &middot; {{ $f->venue }} @endif
        </p>
    </div>
</div>
