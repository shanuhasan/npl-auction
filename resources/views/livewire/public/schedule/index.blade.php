<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="page-intro mb-8 flex flex-col md:flex-row md:items-end justify-between gap-6 text-center md:text-left">
        <div>
            <h1 class="text-4xl md:text-3xl font-semibold text-ink mb-2 tracking-tight">Match Schedule</h1>
            <p class="text-muted text-lg">{{ $auction?->title ?? 'Fixtures, results and points table' }}</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <select wire:model.live="auctionGuid" class="bg-surface border-line rounded-lg text-ink">
                @foreach($auctions as $a)
                    <option value="{{ $a->guid }}">{{ $a->title }} ({{ $a->auction_date->format('Y') }})</option>
                @endforeach
            </select>
            @if($tab === 'fixtures')
                <select wire:model.live="teamId" class="bg-surface border-line rounded-lg text-ink">
                    <option value="">All teams</option>
                    @foreach($teams as $team)
                        <option value="{{ $team->id }}">{{ $team->name }}</option>
                    @endforeach
                </select>
            @endif
        </div>
    </div>

    <div class="flex gap-2 mb-8 border-b border-line">
        @foreach(['fixtures' => 'Fixtures & Results', 'points' => 'Points Table'] as $key => $label)
            <button wire:click="$set('tab', '{{ $key }}')" class="px-5 py-3 font-medium text-sm border-b-4 -mb-px transition {{ $tab === $key ? 'text-brand border-brand' : 'text-muted border-transparent hover:text-ink' }}">{{ $label }}</button>
        @endforeach
    </div>

    @if($tab === 'points')
        <div class="bg-surface border border-line rounded-2xl overflow-x-auto shadow-sm">
            <table class="min-w-full text-sm">
                <thead class="bg-surface-muted text-muted uppercase tracking-wider text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left">#</th>
                        <th class="px-4 py-3 text-left">Team</th>
                        <th class="px-4 py-3 text-center">P</th>
                        <th class="px-4 py-3 text-center">W</th>
                        <th class="px-4 py-3 text-center">L</th>
                        <th class="px-4 py-3 text-center">NR/T</th>
                        <th class="px-4 py-3 text-center">Pts</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse($pointsTable as $i => $row)
                        <tr class="hover:bg-surface-subtle">
                            <td class="px-4 py-3 text-muted font-bold">{{ $i + 1 }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('public.teams.show', $row['team']->guid) }}" class="flex items-center gap-3 font-bold text-ink hover:text-brand">
                                    <span class="w-3 h-3 rounded-full" style="background: {{ $row['team']->primary_color ?? '#888' }}"></span>
                                    {{ $row['team']->name }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-center text-ink-soft">{{ $row['played'] }}</td>
                            <td class="px-4 py-3 text-center text-success">{{ $row['won'] }}</td>
                            <td class="px-4 py-3 text-center text-red-600">{{ $row['lost'] }}</td>
                            <td class="px-4 py-3 text-center text-ink-soft">{{ $row['nr'] }}</td>
                            <td class="px-4 py-3 text-center font-bold text-brand">{{ $row['points'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-10 text-center text-muted">No schedule published yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <p class="px-4 py-3 text-xs text-muted">League matches only. Win = 2 pts, Tie / No result = 1 pt.</p>
        </div>
    @else
        @forelse($fixturesByDay as $day => $dayFixtures)
            <div class="mb-10">
                <h2 class="text-sm font-semibold text-brand mb-4 tracking-tight">
                    {{ $day === 'tbd' ? 'Date to be announced' : \Carbon\Carbon::parse($day)->format('l, d F Y') }}
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($dayFixtures as $fixture)
                        <x-fixture-card :fixture="$fixture" :highlight-team="$teamId" />
                    @endforeach
                </div>
            </div>
        @empty
            <div class="text-center py-20 text-muted text-xl font-semibold">No matches scheduled yet.</div>
        @endforelse
    @endif
</div>
