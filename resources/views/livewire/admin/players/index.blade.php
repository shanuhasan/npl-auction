<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-ink">Manage Players</h1>
        <div class="flex space-x-3">
            <a href="{{ route('admin.players.pdf') }}" class="px-4 py-2 rounded-lg font-medium transition flex items-center text-sm bg-surface text-ink border border-line-strong hover:bg-surface-subtle shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Export PDF
            </a>
            <button wire:click="create" class="bg-brand text-white px-4 py-2 rounded-lg font-medium hover:bg-brand-hover transition text-sm inline-flex items-center justify-center gap-2">Add New Player</button>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="alert-success mb-4">
            {{ session('message') }}
        </div>
    @endif

    <div class="bg-surface p-4 rounded-lg shadow-sm mb-6 border border-line flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4">
        <input type="text" wire:model.live="search" placeholder="Search by name..." class="w-full md:flex-1 bg-canvas border border-line rounded py-2 px-3 text-ink focus:outline-none focus:border-brand">
        <x-select2 id="filterRole" wire:model.live="filterRole" placeholder="All Roles">
            <option value="batsman">Batsman</option>
            <option value="bowler">Bowler</option>
            <option value="all-rounder">All-Rounder</option>
            <option value="wicketkeeper">Wicketkeeper</option>
        </x-select2>
        <x-select2 id="filterStatus" wire:model.live="filterStatus" placeholder="All Statuses">
            <option value="available">Available</option>
            <option value="sold">Sold</option>
            <option value="unsold">Unsold</option>
            <option value="unavailable">Unavailable</option>
        </x-select2>
        <x-select2 id="filterApproval" wire:model.live="filterApproval" placeholder="All Approvals">
            <option value="1">Approved</option>
            <option value="0">Pending</option>
        </x-select2>
    </div>

    <div class="bg-surface rounded-lg shadow-sm overflow-x-auto border border-line">
        <table class="min-w-full divide-y divide-line">
            <thead class="bg-surface-muted">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Player</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Base Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-muted uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-surface divide-y divide-line">
                @foreach($players as $player)
                <tr class="hover:bg-surface-muted transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                @if($player->photo)
                                    <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $player->photo) }}" alt="">
                                @else
                                    <div class="h-10 w-10 rounded-full bg-surface-subtle flex items-center justify-center text-brand font-bold">{{ substr($player->name, 0, 1) }}</div>
                                @endif
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-ink">
                                    {{ $player->name }}
                                    @if($player->is_captain) <span class="badge-brand ml-1" title="Captain/Owner">C</span> @endif
                                    @if($player->is_favorite) <span class="badge-brand ml-1" title="Favorite">★</span> @endif
                                </div>
                                <div class="text-sm text-muted">
                                    {{ $player->city ? $player->city . ', ' : '' }}{{ $player->country }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-ink-soft capitalize">{{ $player->role }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-ink-soft capitalize">{{ str_replace('-', ' ', $player->category) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-brand font-semibold">₹{{ number_format($player->base_price, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex flex-col space-y-1">
                            <span class="w-max
                                {{ $player->status === 'sold' ? 'badge-success' : ($player->status === 'unsold' ? 'badge-danger' : ($player->status === 'unavailable' ? 'badge-neutral' : 'badge-neutral')) }}">
                                {{ ucfirst($player->status) }}
                            </span>
                            @if(!$player->is_approved)
                                <span class="badge-warning w-max">
                                    Pending
                                </span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        @if(!$player->is_approved)
                            <button wire:click="approve({{ $player->id }})" class="text-amber-600 hover:text-amber-700 mr-3 font-bold uppercase tracking-wider text-xs">Approve</button>
                        @endif
                        @if($player->status === 'available')
                            <button wire:click="openAssignModal({{ $player->id }})" class="text-green-600 hover:text-green-700 mr-3 font-bold uppercase tracking-wider text-xs">Assign</button>
                        @endif
                        <button wire:click="edit({{ $player->id }})" class="text-indigo-600 hover:text-indigo-700 mr-3">Edit</button>
                        <button wire:click="delete({{ $player->id }})" wire:confirm="Are you sure you want to delete this player?" class="text-danger hover:text-red-700">Delete</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4 border-t border-line">
            {{ $players->links() }}
        </div>
    </div>

    <!-- Modal -->
    @if($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 overflow-y-auto pt-10 pb-10">
            <div class="bg-surface rounded-lg w-full max-w-3xl p-6 my-8 overflow-y-auto max-h-[90vh]">
                <h2 class="text-lg font-semibold text-ink mb-4">{{ $player_id ? 'Edit Player' : 'Create Player' }}</h2>
                <form wire:submit.prevent="store">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-ink-soft text-sm font-bold mb-2">Name</label>
                            <input type="text" wire:model="name" class="w-full bg-canvas border border-line rounded py-2 px-3 text-ink focus:outline-none focus:border-brand" required>
                            @error('name') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-ink-soft text-sm font-bold mb-2">Country</label>
                            <input type="text" wire:model="country" class="w-full bg-canvas border border-line rounded py-2 px-3 text-ink focus:outline-none focus:border-brand" required>
                            @error('country') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-ink-soft text-sm font-bold mb-2">City/Village/Town</label>
                            <input type="text" wire:model="city" class="w-full bg-canvas border border-line rounded py-2 px-3 text-ink focus:outline-none focus:border-brand" placeholder="Optional">
                            @error('city') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-ink-soft text-sm font-bold mb-2">Contact No.</label>
                            <input type="text" wire:model="contact_no" class="w-full bg-canvas border border-line rounded py-2 px-3 text-ink focus:outline-none focus:border-brand" maxlength="10" pattern="\d{10}" required>
                            @error('contact_no') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-ink-soft text-sm font-bold mb-2">Role</label>
                            <x-select2 id="role" wire:model="role" placeholder="Select Role" required>
                                <option value="batsman">Batsman</option>
                                <option value="bowler">Bowler</option>
                                <option value="all-rounder">All-Rounder</option>
                                <option value="wicketkeeper">Wicketkeeper</option>
                            </x-select2>
                            @error('role') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-ink-soft text-sm font-bold mb-2">Category</label>
                            <x-select2 id="category" wire:model="category" placeholder="Select Category" required>
                                <option value="marquee">Marquee</option>
                                <option value="set-a">Set A</option>
                                <option value="set-b">Set B</option>
                                <option value="set-c">Set C</option>
                            </x-select2>
                            @error('category') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-ink-soft text-sm font-bold mb-2">Base Price (₹)</label>
                            <input type="number" step="0.01" wire:model="base_price" class="w-full bg-canvas border border-line rounded py-2 px-3 text-ink focus:outline-none focus:border-brand" required>
                            @error('base_price') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-ink-soft text-sm font-bold mb-2">Status</label>
                            <x-select2 id="status" wire:model="status" placeholder="Select Status" required>
                                <option value="available">Available</option>
                                <option value="sold">Sold</option>
                                <option value="unsold">Unsold</option>
                                <option value="unavailable">Unavailable</option>
                            </x-select2>
                            @error('status') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-ink-soft text-sm font-bold mb-2">Approval</label>
                            <x-select2 id="is_approved" wire:model="is_approved" placeholder="Select Approval" required>
                                <option value="1">Approved</option>
                                <option value="0">Pending</option>
                            </x-select2>
                            @error('is_approved') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-ink-soft text-sm font-bold mb-2">Captain/Owner</label>
                            <x-select2 id="is_captain" wire:model="is_captain" placeholder="Select Captain/Owner" required>
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </x-select2>
                            @error('is_captain') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-ink-soft text-sm font-bold mb-2">Favorite Player</label>
                            <x-select2 id="is_favorite" wire:model="is_favorite" placeholder="Select Favorite" required>
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </x-select2>
                            @error('is_favorite') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-ink-soft text-sm font-bold mb-2">Batting Style</label>
                            <x-select2 id="batting_style" wire:model="batting_style" placeholder="Select Batting Style">
                                <option value="Right-hand bat">Right-hand bat</option>
                                <option value="Left-hand bat">Left-hand bat</option>
                            </x-select2>
                            @error('batting_style') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-ink-soft text-sm font-bold mb-2">Bowling Style</label>
                            <x-select2 id="bowling_style" wire:model="bowling_style" placeholder="Select Bowling Style (or None)">
                                <option value="None">None</option>
                                <option value="Right-arm fast">Right-arm fast</option>
                                <option value="Right-arm medium">Right-arm medium</option>
                                <option value="Right-arm offbreak">Right-arm offbreak</option>
                                <option value="Right-arm legbreak">Right-arm legbreak</option>
                                <option value="Left-arm fast">Left-arm fast</option>
                                <option value="Left-arm medium">Left-arm medium</option>
                                <option value="Left-arm orthodox">Left-arm orthodox</option>
                                <option value="Left-arm chinaman">Left-arm chinaman</option>
                            </x-select2>
                            @error('bowling_style') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-ink-soft text-sm font-bold mb-2">Photo</label>
                            <input type="file" wire:model="photo" class="w-full bg-canvas border border-line rounded py-2 px-3 text-ink focus:outline-none focus:border-brand">
                            @error('photo') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                            @if($photo)
                                <img src="{{ $photo->temporaryUrl() }}" class="mt-2 w-16 h-16 object-cover rounded-full">
                            @elseif($existing_photo)
                                <img src="{{ asset('storage/' . $existing_photo) }}" class="mt-2 w-16 h-16 object-cover rounded-full">
                            @endif
                        </div>

                        <div class="md:col-span-2 mt-4">
                            <h3 class="text-lg text-brand font-bold mb-2 border-b border-line pb-2">Stats (JSON)</h3>
                            <div class="grid grid-cols-2 md:grid-cols-5 gap-2">
                                <div>
                                    <label class="block text-muted text-xs font-bold mb-1">Matches</label>
                                    <input type="number" wire:model="stats.matches" class="w-full bg-canvas border border-line rounded py-1 px-2 text-ink focus:outline-none focus:border-brand">
                                </div>
                                <div>
                                    <label class="block text-muted text-xs font-bold mb-1">Runs</label>
                                    <input type="number" wire:model="stats.runs" class="w-full bg-canvas border border-line rounded py-1 px-2 text-ink focus:outline-none focus:border-brand">
                                </div>
                                <div>
                                    <label class="block text-muted text-xs font-bold mb-1">Wickets</label>
                                    <input type="number" wire:model="stats.wickets" class="w-full bg-canvas border border-line rounded py-1 px-2 text-ink focus:outline-none focus:border-brand">
                                </div>
                                <div>
                                    <label class="block text-muted text-xs font-bold mb-1">Average</label>
                                    <input type="number" step="0.01" wire:model="stats.average" class="w-full bg-canvas border border-line rounded py-1 px-2 text-ink focus:outline-none focus:border-brand">
                                </div>
                                <div>
                                    <label class="block text-muted text-xs font-bold mb-1">Strike Rate</label>
                                    <input type="number" step="0.01" wire:model="stats.strike_rate" class="w-full bg-canvas border border-line rounded py-1 px-2 text-ink focus:outline-none focus:border-brand">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3 border-t border-line pt-4">
                        <button type="button" wire:click="closeModal" class="bg-surface-subtle hover:bg-slate-200 text-ink px-4 py-2 rounded transition">Cancel</button>
                        <button type="submit" class="bg-brand hover:bg-brand-hover text-white px-4 py-2 rounded-lg font-medium transition text-sm inline-flex items-center justify-center gap-2">Save Player</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Assign Modal -->
    @if($isAssignModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 overflow-y-auto pt-10 pb-10">
            <div class="bg-surface rounded-lg w-full max-w-lg p-6 my-8 overflow-y-auto shadow-pop border border-line">
                <h2 class="text-lg font-semibold text-brand mb-4 border-b border-line pb-2">Assign Player Manually</h2>
                <form wire:submit.prevent="assignPlayer">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-ink-soft text-sm font-bold mb-2">Select Team</label>
                            <x-select2 id="assignTeamId" wire:model="assignTeamId" placeholder="-- Choose a Team --" required>
                                @foreach($teams as $team)
                                    <option value="{{ $team->id }}">{{ $team->name }} (Remaining: ₹{{ number_format($team->remaining_budget) }})</option>
                                @endforeach
                            </x-select2>
                            @error('assignTeamId') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-ink-soft text-sm font-bold mb-2">Assigned Price (₹)</label>
                            <input type="number" step="0.01" wire:model="assignPrice" class="w-full bg-canvas border border-line rounded py-2 px-3 text-ink focus:outline-none focus:border-brand" required>
                            <p class="text-xs text-muted mt-1">You can set this to 0 or any amount. It will be deducted from the selected team's budget.</p>
                            @error('assignPrice') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="mt-8 flex justify-end space-x-3 border-t border-line pt-4">
                        <button type="button" wire:click="closeAssignModal" class="bg-surface-subtle hover:bg-slate-200 text-ink px-4 py-2 rounded transition font-semibold uppercase tracking-wider text-sm">Cancel</button>
                        <button type="submit" class="bg-green-600 hover:bg-green-500 text-white px-6 py-2 rounded-lg font-medium transition shadow-sm text-sm inline-flex items-center justify-center gap-2">Confirm Assignment</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
