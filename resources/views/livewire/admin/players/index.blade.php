<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-poppins font-bold text-white">Manage Players</h1>
        <button wire:click="create" class="bg-accent-gold text-primary-bg px-4 py-2 rounded font-semibold hover:bg-yellow-400 transition">Add New Player</button>
    </div>

    @if (session()->has('message'))
        <div class="bg-success-green text-white px-4 py-3 rounded mb-4 shadow">
            {{ session('message') }}
        </div>
    @endif

    <div class="bg-card-bg p-4 rounded-lg shadow mb-6 border border-gray-800 flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4">
        <input type="text" wire:model.live="search" placeholder="Search by name..." class="w-full md:flex-1 bg-primary-bg border border-gray-700 rounded py-2 px-3 text-white focus:outline-none focus:border-accent-gold">
        <select wire:model.live="filterRole" class="w-full md:w-48 bg-primary-bg border border-gray-700 rounded py-2 px-3 text-white focus:outline-none focus:border-accent-gold">
            <option value="">All Roles</option>
            <option value="batsman">Batsman</option>
            <option value="bowler">Bowler</option>
            <option value="all-rounder">All-Rounder</option>
            <option value="wicketkeeper">Wicketkeeper</option>
        </select>
        <select wire:model.live="filterStatus" class="w-full md:w-48 bg-primary-bg border border-gray-700 rounded py-2 px-3 text-white focus:outline-none focus:border-accent-gold">
            <option value="">All Statuses</option>
            <option value="available">Available</option>
            <option value="sold">Sold</option>
            <option value="unsold">Unsold</option>
        </select>
    </div>

    <div class="bg-card-bg rounded-lg shadow overflow-x-auto border border-gray-800">
        <table class="min-w-full divide-y divide-gray-800">
            <thead class="bg-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Player</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Base Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-card-bg divide-y divide-gray-800">
                @foreach($players as $player)
                <tr class="hover:bg-gray-800 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                @if($player->photo)
                                    <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $player->photo) }}" alt="">
                                @else
                                    <div class="h-10 w-10 rounded-full bg-gray-700 flex items-center justify-center text-accent-gold font-bold">{{ substr($player->name, 0, 1) }}</div>
                                @endif
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-white">{{ $player->name }}</div>
                                <div class="text-sm text-gray-400">{{ $player->country }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300 capitalize">{{ $player->role }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300 capitalize">{{ str_replace('-', ' ', $player->category) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-accent-gold font-semibold">₹{{ number_format($player->base_price, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $player->status === 'sold' ? 'bg-success-green text-white' : ($player->status === 'unsold' ? 'bg-accent-red text-white' : 'bg-gray-600 text-white') }}">
                            {{ ucfirst($player->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        @if($player->status === 'available')
                            <button wire:click="openAssignModal({{ $player->id }})" class="text-green-400 hover:text-green-300 mr-3 font-bold uppercase tracking-wider text-xs">Assign</button>
                        @endif
                        <button wire:click="edit({{ $player->id }})" class="text-indigo-400 hover:text-indigo-300 mr-3">Edit</button>
                        <button wire:click="delete({{ $player->id }})" wire:confirm="Are you sure you want to delete this player?" class="text-accent-red hover:text-red-400">Delete</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4 border-t border-gray-800">
            {{ $players->links() }}
        </div>
    </div>

    <!-- Modal -->
    @if($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 overflow-y-auto pt-10 pb-10">
            <div class="bg-card-bg rounded-lg w-full max-w-3xl p-6 my-8 overflow-y-auto max-h-[90vh]">
                <h2 class="text-2xl font-poppins font-bold text-white mb-4">{{ $player_id ? 'Edit Player' : 'Create Player' }}</h2>
                <form wire:submit.prevent="store">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-300 text-sm font-bold mb-2">Name</label>
                            <input type="text" wire:model="name" class="w-full bg-primary-bg border border-gray-700 rounded py-2 px-3 text-white focus:outline-none focus:border-accent-gold" required>
                            @error('name') <span class="text-accent-red text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-gray-300 text-sm font-bold mb-2">Country</label>
                            <input type="text" wire:model="country" class="w-full bg-primary-bg border border-gray-700 rounded py-2 px-3 text-white focus:outline-none focus:border-accent-gold" required>
                            @error('country') <span class="text-accent-red text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-gray-300 text-sm font-bold mb-2">Role</label>
                            <select wire:model="role" class="w-full bg-primary-bg border border-gray-700 rounded py-2 px-3 text-white focus:outline-none focus:border-accent-gold" required>
                                <option value="batsman">Batsman</option>
                                <option value="bowler">Bowler</option>
                                <option value="all-rounder">All-Rounder</option>
                                <option value="wicketkeeper">Wicketkeeper</option>
                            </select>
                            @error('role') <span class="text-accent-red text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-gray-300 text-sm font-bold mb-2">Category</label>
                            <select wire:model="category" class="w-full bg-primary-bg border border-gray-700 rounded py-2 px-3 text-white focus:outline-none focus:border-accent-gold" required>
                                <option value="marquee">Marquee</option>
                                <option value="set-a">Set A</option>
                                <option value="set-b">Set B</option>
                                <option value="set-c">Set C</option>
                            </select>
                            @error('category') <span class="text-accent-red text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-gray-300 text-sm font-bold mb-2">Base Price (₹)</label>
                            <input type="number" step="0.01" wire:model="base_price" class="w-full bg-primary-bg border border-gray-700 rounded py-2 px-3 text-white focus:outline-none focus:border-accent-gold" required>
                            @error('base_price') <span class="text-accent-red text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-gray-300 text-sm font-bold mb-2">Status</label>
                            <select wire:model="status" class="w-full bg-primary-bg border border-gray-700 rounded py-2 px-3 text-white focus:outline-none focus:border-accent-gold" required>
                                <option value="available">Available</option>
                                <option value="sold">Sold</option>
                                <option value="unsold">Unsold</option>
                            </select>
                            @error('status') <span class="text-accent-red text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-gray-300 text-sm font-bold mb-2">Batting Style</label>
                            <input type="text" wire:model="batting_style" class="w-full bg-primary-bg border border-gray-700 rounded py-2 px-3 text-white focus:outline-none focus:border-accent-gold">
                            @error('batting_style') <span class="text-accent-red text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-gray-300 text-sm font-bold mb-2">Bowling Style</label>
                            <input type="text" wire:model="bowling_style" class="w-full bg-primary-bg border border-gray-700 rounded py-2 px-3 text-white focus:outline-none focus:border-accent-gold">
                            @error('bowling_style') <span class="text-accent-red text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-gray-300 text-sm font-bold mb-2">Photo</label>
                            <input type="file" wire:model="photo" class="w-full bg-primary-bg border border-gray-700 rounded py-2 px-3 text-white focus:outline-none focus:border-accent-gold">
                            @error('photo') <span class="text-accent-red text-xs">{{ $message }}</span> @enderror
                            @if($photo)
                                <img src="{{ $photo->temporaryUrl() }}" class="mt-2 w-16 h-16 object-cover rounded-full">
                            @elseif($existing_photo)
                                <img src="{{ asset('storage/' . $existing_photo) }}" class="mt-2 w-16 h-16 object-cover rounded-full">
                            @endif
                        </div>

                        <div class="md:col-span-2 mt-4">
                            <h3 class="text-lg text-accent-gold font-bold mb-2 border-b border-gray-700 pb-2">Stats (JSON)</h3>
                            <div class="grid grid-cols-2 md:grid-cols-5 gap-2">
                                <div>
                                    <label class="block text-gray-400 text-xs font-bold mb-1">Matches</label>
                                    <input type="number" wire:model="stats.matches" class="w-full bg-primary-bg border border-gray-700 rounded py-1 px-2 text-white focus:outline-none focus:border-accent-gold">
                                </div>
                                <div>
                                    <label class="block text-gray-400 text-xs font-bold mb-1">Runs</label>
                                    <input type="number" wire:model="stats.runs" class="w-full bg-primary-bg border border-gray-700 rounded py-1 px-2 text-white focus:outline-none focus:border-accent-gold">
                                </div>
                                <div>
                                    <label class="block text-gray-400 text-xs font-bold mb-1">Wickets</label>
                                    <input type="number" wire:model="stats.wickets" class="w-full bg-primary-bg border border-gray-700 rounded py-1 px-2 text-white focus:outline-none focus:border-accent-gold">
                                </div>
                                <div>
                                    <label class="block text-gray-400 text-xs font-bold mb-1">Average</label>
                                    <input type="number" step="0.01" wire:model="stats.average" class="w-full bg-primary-bg border border-gray-700 rounded py-1 px-2 text-white focus:outline-none focus:border-accent-gold">
                                </div>
                                <div>
                                    <label class="block text-gray-400 text-xs font-bold mb-1">Strike Rate</label>
                                    <input type="number" step="0.01" wire:model="stats.strike_rate" class="w-full bg-primary-bg border border-gray-700 rounded py-1 px-2 text-white focus:outline-none focus:border-accent-gold">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3 border-t border-gray-700 pt-4">
                        <button type="button" wire:click="closeModal" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded transition">Cancel</button>
                        <button type="submit" class="bg-accent-gold hover:bg-yellow-400 text-primary-bg px-4 py-2 rounded font-semibold transition">Save Player</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Assign Modal -->
    @if($isAssignModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 overflow-y-auto pt-10 pb-10">
            <div class="bg-card-bg rounded-lg w-full max-w-lg p-6 my-8 overflow-y-auto shadow-2xl border border-gray-700">
                <h2 class="text-2xl font-poppins font-bold text-accent-gold mb-4 uppercase tracking-wider border-b border-gray-700 pb-2">Assign Player Manually</h2>
                <form wire:submit.prevent="assignPlayer">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-gray-300 text-sm font-bold mb-2">Select Team</label>
                            <select wire:model="assignTeamId" class="w-full bg-primary-bg border border-gray-700 rounded py-2 px-3 text-white focus:outline-none focus:border-accent-gold" required>
                                <option value="">-- Choose a Team --</option>
                                @foreach($teams as $team)
                                    <option value="{{ $team->id }}">{{ $team->name }} (Remaining: ₹{{ number_format($team->remaining_budget) }})</option>
                                @endforeach
                            </select>
                            @error('assignTeamId') <span class="text-accent-red text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-gray-300 text-sm font-bold mb-2">Assigned Price (₹)</label>
                            <input type="number" step="0.01" wire:model="assignPrice" class="w-full bg-primary-bg border border-gray-700 rounded py-2 px-3 text-white focus:outline-none focus:border-accent-gold" required>
                            <p class="text-xs text-gray-500 mt-1">You can set this to 0 or any amount. It will be deducted from the selected team's budget.</p>
                            @error('assignPrice') <span class="text-accent-red text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="mt-8 flex justify-end space-x-3 border-t border-gray-700 pt-4">
                        <button type="button" wire:click="closeAssignModal" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded transition font-semibold uppercase tracking-wider text-sm">Cancel</button>
                        <button type="submit" class="bg-green-600 hover:bg-green-500 text-white px-6 py-2 rounded font-bold transition shadow uppercase tracking-wider text-sm">Confirm Assignment</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
