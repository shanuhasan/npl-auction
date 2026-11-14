<div>
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6">
        <div>
            <a href="{{ route('admin.auctions') }}" class="text-sm text-gray-400 hover:text-accent-gold" wire:navigate>&larr; Back to Auctions</a>
            <h1 class="text-3xl font-poppins font-bold text-white mt-1">Match Schedule</h1>
            <p class="text-gray-400">{{ $auction->title }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('public.schedule', ['auction' => $auction->guid]) }}" target="_blank" class="bg-gray-700 text-white px-4 py-2 rounded font-semibold hover:bg-gray-600 transition">View Public Page</a>
            <button wire:click="openCreate" class="bg-accent-gold text-primary-bg px-4 py-2 rounded font-semibold hover:bg-yellow-400 transition">+ Add Match</button>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="bg-success-green text-white px-4 py-3 rounded mb-4 shadow">{{ session('message') }}</div>
    @endif

    <!-- Generator -->
    <div class="bg-card-bg rounded-lg shadow border border-gray-800 p-6 mb-8" x-data="{ open: {{ $totalFixtures === 0 ? 'true' : 'false' }} }">
        <button type="button" @click="open = !open" class="w-full flex justify-between items-center text-left">
            <h2 class="text-xl font-bold text-white">Auto-Generate League Schedule (Round Robin)</h2>
            <span class="text-accent-gold text-sm" x-text="open ? 'Hide' : 'Show'"></span>
        </button>

        <form wire:submit="generate" x-show="open" x-cloak class="mt-6 space-y-6">
            <div>
                <div class="flex justify-between items-center mb-2">
                    <label class="block text-sm font-medium text-gray-300">Teams ({{ count($selectedTeams) }} selected)</label>
                    <div class="text-xs space-x-3">
                        <button type="button" wire:click="$set('selectedTeams', {{ json_encode($teams->pluck('id')->map(fn($id) => (string) $id)) }})" class="text-accent-gold hover:underline">Auction teams</button>
                        <button type="button" wire:click="$set('selectedTeams', [])" class="text-gray-400 hover:underline">Clear</button>
                    </div>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                    @foreach($allTeams as $team)
                        <label class="flex items-center gap-2 bg-gray-900 border border-gray-700 rounded px-3 py-2 cursor-pointer hover:border-accent-gold">
                            <input type="checkbox" value="{{ $team->id }}" wire:model.live="selectedTeams" class="rounded bg-gray-800 border-gray-600 text-accent-gold focus:ring-accent-gold">
                            <span class="w-3 h-3 rounded-full flex-shrink-0" style="background: {{ $team->primary_color ?? '#888' }}"></span>
                            <span class="text-sm text-white truncate">{{ $team->name }}</span>
                        </label>
                    @endforeach
                </div>
                @error('selectedTeams') <span class="text-accent-red text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Format</label>
                    <select wire:model.live="format" class="w-full bg-gray-900 border-gray-700 rounded text-white">
                        <option value="single">Single round robin (each pair once)</option>
                        <option value="double">Double round robin (home &amp; away)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Start Date</label>
                    <input type="date" wire:model="startDate" class="w-full bg-gray-900 border-gray-700 rounded text-white">
                    @error('startDate') <span class="text-accent-red text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Match times per day</label>
                    <input type="text" wire:model="matchTimes" placeholder="10:00, 14:00" class="w-full bg-gray-900 border-gray-700 rounded text-white">
                    <p class="text-xs text-gray-500 mt-1">One match per time slot, 24h format.</p>
                    @error('matchTimes') <span class="text-accent-red text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Days between match days</label>
                    <input type="number" min="1" wire:model="dayGap" class="w-full bg-gray-900 border-gray-700 rounded text-white">
                    @error('dayGap') <span class="text-accent-red text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-300 mb-1">Venue (optional)</label>
                    <input type="text" wire:model="venue" placeholder="e.g. Naugawan Cricket Ground" class="w-full bg-gray-900 border-gray-700 rounded text-white">
                </div>
                <div class="md:col-span-2 flex items-end">
                    <label class="flex items-center gap-2 text-sm text-gray-300">
                        <input type="checkbox" wire:model="replace" class="rounded bg-gray-800 border-gray-600 text-accent-gold">
                        Replace the existing schedule (deletes all current matches of this auction)
                    </label>
                </div>
            </div>

            @php
                $n = count($selectedTeams);
                $perTeam = max(0, $n - 1) * ($format === 'double' ? 2 : 1);
                $totalMatches = $n * max(0, $n - 1) / 2 * ($format === 'double' ? 2 : 1);
            @endphp
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gray-900 rounded p-4">
                <p class="text-gray-300 text-sm">
                    <strong class="text-white">{{ $totalMatches }}</strong> matches in total &middot;
                    <strong class="text-white">{{ $perTeam }}</strong> matches per team.
                    A team never plays twice on the same day.
                </p>
                <button type="submit"
                        @if($replace && $totalFixtures > 0) wire:confirm="This will delete the {{ $totalFixtures }} existing matches and create a new schedule. Continue?" @endif
                        class="bg-accent-gold text-primary-bg px-6 py-2 rounded font-bold hover:bg-yellow-400 transition">
                    <span wire:loading.remove wire:target="generate">Generate Schedule</span>
                    <span wire:loading wire:target="generate">Generating...</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Schedule list -->
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3 mb-4">
        <h2 class="text-2xl font-bold text-white">Matches ({{ $totalFixtures }})</h2>
        <div class="flex gap-3 items-center">
            <select wire:model.live="filterTeam" class="bg-gray-900 border-gray-700 rounded text-white text-sm">
                <option value="">All teams</option>
                @foreach($allTeams as $team)
                    <option value="{{ $team->id }}">{{ $team->name }}</option>
                @endforeach
            </select>
            @if($totalFixtures > 0 && !$filterTeam)
                <button wire:click="clearAll" wire:confirm="Delete ALL matches of this auction?" class="text-accent-red hover:text-red-400 text-sm font-semibold whitespace-nowrap">Delete all</button>
            @endif
        </div>
    </div>

    @forelse($fixturesByDay as $day => $dayFixtures)
        <div class="mb-6">
            <h3 class="text-sm font-bold uppercase tracking-wider text-accent-gold mb-2">
                {{ $day === 'tbd' ? 'Date to be decided' : \Carbon\Carbon::parse($day)->format('l, d M Y') }}
            </h3>
            <div class="bg-card-bg rounded-lg shadow overflow-x-auto border border-gray-800">
                <table class="min-w-full divide-y divide-gray-800">
                    <tbody class="divide-y divide-gray-800">
                        @foreach($dayFixtures as $f)
                            <tr class="hover:bg-gray-800 transition">
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-400 w-28">
                                    <div class="font-bold text-white">Match {{ $f->match_no }}</div>
                                    <div class="text-xs">{{ $f->stage }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-300 w-24">{{ $f->match_date?->format('h:i A') ?? '—' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm">
                                    <span class="font-semibold {{ $f->winner_team_id == $f->team_a_id ? 'text-success-green' : 'text-white' }}">{{ $f->teamA->name }}</span>
                                    @if($f->team_a_score)<span class="text-gray-400 text-xs ml-1">{{ $f->team_a_score }}</span>@endif
                                    <span class="text-gray-500 mx-2">vs</span>
                                    <span class="font-semibold {{ $f->winner_team_id == $f->team_b_id ? 'text-success-green' : 'text-white' }}">{{ $f->teamB->name }}</span>
                                    @if($f->team_b_score)<span class="text-gray-400 text-xs ml-1">{{ $f->team_b_score }}</span>@endif
                                    @if($f->result)<div class="text-xs text-gray-400 mt-1">{{ $f->result }}</div>@endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-400">{{ $f->venue ?? '' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @php
                                        $badge = ['scheduled' => 'bg-gray-600', 'live' => 'bg-accent-red animate-pulse', 'completed' => 'bg-success-green', 'abandoned' => 'bg-yellow-600'][$f->status];
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full text-white {{ $badge }}">{{ ucfirst($f->status) }}</span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                    <button wire:click="edit({{ $f->id }})" class="text-accent-gold hover:text-yellow-400 font-bold mr-3">Edit / Result</button>
                                    <button wire:click="delete({{ $f->id }})" wire:confirm="Delete Match {{ $f->match_no }}?" class="text-accent-red hover:text-red-400 font-bold">Delete</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="bg-card-bg rounded-lg border border-gray-800 p-10 text-center text-gray-400">
            No matches scheduled yet. Use the generator above or add matches manually.
        </div>
    @endforelse

    <!-- Add / Edit Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4" wire:keydown.escape.window="closeModal">
            <div class="bg-card-bg border border-gray-700 rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center px-6 py-4 border-b border-gray-800">
                    <h3 class="text-xl font-bold text-white">{{ $editId ? 'Edit Match' : 'Add Match' }}</h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-white text-2xl leading-none">&times;</button>
                </div>
                <form wire:submit="save" class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm text-gray-300 mb-1">Match No.</label>
                            <input type="number" wire:model="match_no" class="w-full bg-gray-900 border-gray-700 rounded text-white">
                            @error('match_no') <span class="text-accent-red text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm text-gray-300 mb-1">Stage</label>
                            <input type="text" wire:model="stage" list="stage-options" class="w-full bg-gray-900 border-gray-700 rounded text-white">
                            <datalist id="stage-options">
                                <option value="League"><option value="Qualifier 1"><option value="Eliminator"><option value="Qualifier 2"><option value="Semi Final"><option value="Final">
                            </datalist>
                            @error('stage') <span class="text-accent-red text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm text-gray-300 mb-1">Status</label>
                            <select wire:model.live="status" class="w-full bg-gray-900 border-gray-700 rounded text-white">
                                <option value="scheduled">Scheduled</option>
                                <option value="live">Live</option>
                                <option value="completed">Completed</option>
                                <option value="abandoned">Abandoned / No result</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-300 mb-1">Team A</label>
                            <select wire:model.live="team_a_id" class="w-full bg-gray-900 border-gray-700 rounded text-white">
                                <option value="">Select team</option>
                                @foreach($allTeams as $team)<option value="{{ $team->id }}">{{ $team->name }}</option>@endforeach
                            </select>
                            @error('team_a_id') <span class="text-accent-red text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm text-gray-300 mb-1">Team B</label>
                            <select wire:model.live="team_b_id" class="w-full bg-gray-900 border-gray-700 rounded text-white">
                                <option value="">Select team</option>
                                @foreach($allTeams as $team)<option value="{{ $team->id }}">{{ $team->name }}</option>@endforeach
                            </select>
                            @error('team_b_id') <span class="text-accent-red text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm text-gray-300 mb-1">Date &amp; Time</label>
                            <input type="datetime-local" wire:model="match_date" class="w-full bg-gray-900 border-gray-700 rounded text-white">
                            @error('match_date') <span class="text-accent-red text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm text-gray-300 mb-1">Venue</label>
                            <input type="text" wire:model="match_venue" class="w-full bg-gray-900 border-gray-700 rounded text-white">
                        </div>
                    </div>

                    @if(in_array($status, ['live', 'completed', 'abandoned']))
                        <div class="border-t border-gray-800 pt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm text-gray-300 mb-1">Team A Score</label>
                                <input type="text" wire:model="team_a_score" placeholder="156/7 (20)" class="w-full bg-gray-900 border-gray-700 rounded text-white">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-300 mb-1">Team B Score</label>
                                <input type="text" wire:model="team_b_score" placeholder="150/9 (20)" class="w-full bg-gray-900 border-gray-700 rounded text-white">
                            </div>
                            @if($status === 'completed')
                                <div>
                                    <label class="block text-sm text-gray-300 mb-1">Winner</label>
                                    <select wire:model="winner_team_id" class="w-full bg-gray-900 border-gray-700 rounded text-white">
                                        <option value="">Tie / No winner</option>
                                        @foreach($allTeams->whereIn('id', [$team_a_id, $team_b_id]) as $team)
                                            <option value="{{ $team->id }}">{{ $team->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('winner_team_id') <span class="text-accent-red text-xs">{{ $message }}</span> @enderror
                                </div>
                            @endif
                            <div class="{{ $status === 'completed' ? '' : 'md:col-span-2' }}">
                                <label class="block text-sm text-gray-300 mb-1">Result summary</label>
                                <input type="text" wire:model="result" placeholder="won by 6 runs" class="w-full bg-gray-900 border-gray-700 rounded text-white">
                            </div>
                        </div>
                    @endif

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 rounded bg-gray-700 text-white hover:bg-gray-600">Cancel</button>
                        <button type="submit" class="px-6 py-2 rounded bg-accent-gold text-primary-bg font-bold hover:bg-yellow-400">Save</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
