@props(['fixture', 'highlightTeam' => null])
@php
    $f = $fixture;
    $statusClass = [
        'scheduled' => 'bg-surface-muted text-ink-soft',
        'live' => 'bg-red-600 text-white animate-pulse',
        'completed' => 'bg-success/20 text-success',
        'abandoned' => 'bg-amber-50 text-amber-600',
    ][$f->status];
    $logo = fn ($t) => $t->logo ? Storage::url($t->logo) : 'https://ui-avatars.com/api/?name=' . urlencode($t->name) . '&background=random';
@endphp
<div class="bg-surface border border-line rounded-2xl p-5 shadow-sm hover:border-brand/30 transition">
    <div class="flex justify-between items-center text-xs uppercase tracking-widest text-muted mb-4">
        <span>Match {{ $f->match_no }} &middot; {{ $f->stage }}</span>
        <span class="px-2 py-0.5 rounded-full font-bold {{ $statusClass }}">{{ $f->status === 'abandoned' ? 'No Result' : ucfirst($f->status) }}</span>
    </div>

    <div class="flex items-center justify-between gap-3">
        @foreach([[$f->teamA, $f->team_a_score, 'a'], [$f->teamB, $f->team_b_score, 'b']] as $i => [$team, $score, $side])
            @if($i === 1)
                <div class="text-muted font-bold text-sm">VS</div>
            @endif
            @if($team)
                <a href="{{ route('public.teams.show', $team->guid) }}" class="flex-1 flex flex-col items-center text-center group {{ $highlightTeam && $highlightTeam != $team->id ? 'opacity-80' : '' }}">
                    <img src="{{ $logo($team) }}" alt="{{ $team->name }}" class="w-14 h-14 rounded-full object-cover bg-white border-2 mb-2" style="border-color: {{ $team->primary_color ?? '#fff' }}">
                    <span class="font-bold text-ink group-hover:text-brand leading-tight {{ $f->winner_team_id == $team->id ? 'text-success' : '' }}">{{ $team->short_name ?: $team->name }}</span>
                    <span class="text-xs text-muted hidden sm:block truncate max-w-full">{{ $team->name }}</span>
                    @if($score)<span class="text-sm font-bold text-ink mt-1">{{ $score }}</span>@endif
                </a>
            @else
                {{-- Team not decided yet (playoffs) --}}
                <div class="flex-1 flex flex-col items-center text-center">
                    <div class="w-14 h-14 rounded-full bg-surface-muted border-2 border-dashed border-line mb-2 flex items-center justify-center text-muted font-bold text-sm">?</div>
                    <span class="font-bold text-ink leading-tight">TBC</span>
                    @if($f->{"team_{$side}_label"})
                        <span class="text-xs text-muted truncate max-w-full">{{ $f->placeholder($side) }}</span>
                    @endif
                </div>
            @endif
        @endforeach
    </div>

    <div class="mt-4 pt-3 border-t border-line text-center text-sm">
        @if($f->result || $f->winner)
            <p class="font-semibold text-brand">{{ $f->winner ? $f->winner->name . ' ' : '' }}{{ $f->result }}</p>
        @endif
        <p class="text-muted">
            {{ $f->match_date ? $f->match_date->format('D, d M Y · h:i A') : 'Date TBD' }}
            @if($f->venue) &middot; {{ $f->venue }} @endif
        </p>
    </div>
</div>
