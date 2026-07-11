<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-poppins font-bold text-white">Manage Teams</h1>
        <button wire:click="create" class="bg-accent-gold text-primary-bg px-4 py-2 rounded font-semibold hover:bg-yellow-400 transition">Add New Team</button>
    </div>

    @if (session()->has('message'))
        <div class="bg-success-green text-white px-4 py-3 rounded mb-4 shadow">
            {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($teams as $team)
            <div class="bg-card-bg rounded-lg shadow border-t-4 p-6" style="border-top-color: {{ $team->primary_color ?? '#FFC800' }}">
                <div class="flex items-center space-x-4 mb-4">
                    @if($team->logo)
                        <img src="{{ asset('storage/' . $team->logo) }}" alt="Logo" class="w-16 h-16 rounded-full object-cover bg-gray-800">
                    @else
                        <div class="w-16 h-16 rounded-full bg-gray-800 flex items-center justify-center text-accent-gold font-bold text-xl">{{ substr($team->name, 0, 1) }}</div>
                    @endif
                    <div>
                        <h2 class="text-xl font-poppins font-bold text-white">{{ $team->name }}</h2>
                        <span class="text-sm font-inter text-gray-400">{{ $team->short_name }}</span>
                    </div>
                </div>
                <div class="space-y-2 mb-4 text-sm font-inter">
                    <p class="text-gray-300">Owner: <span class="text-white">{{ $team->owner->name ?? 'N/A' }}</span></p>
                    <p class="text-gray-300">Budget: <span class="text-white">₹{{ number_format($team->budget, 2) }}</span></p>
                    <p class="text-gray-300">Remaining: <span class="text-success-green">₹{{ number_format($team->remaining_budget, 2) }}</span></p>
                </div>
                <div class="flex justify-end space-x-2 items-center">
                    <a href="{{ route('teams.pdf', $team->id) }}" target="_blank" class="text-blue-400 hover:text-blue-300 transition p-2" title="Download Squad PDF">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </a>
                    <button wire:click="edit({{ $team->id }})" class="bg-gray-700 hover:bg-gray-600 text-white px-3 py-1 rounded text-sm transition">Edit</button>
                    <button wire:click="delete({{ $team->id }})" wire:confirm="Are you sure you want to delete this team?" class="bg-accent-red hover:bg-red-500 text-white px-3 py-1 rounded text-sm transition">Delete</button>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Modal -->
    @if($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 overflow-y-auto">
            <div class="bg-card-bg rounded-lg w-full max-w-lg p-6 my-8">
                <h2 class="text-2xl font-poppins font-bold text-white mb-4">{{ $team_id ? 'Edit Team' : 'Create Team' }}</h2>
                <form wire:submit.prevent="store">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-gray-300 text-sm font-bold mb-2">Team Name</label>
                            <input type="text" wire:model="name" class="w-full bg-primary-bg border border-gray-700 rounded py-2 px-3 text-white focus:outline-none focus:border-accent-gold" required>
                            @error('name') <span class="text-accent-red text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-gray-300 text-sm font-bold mb-2">Short Name</label>
                            <input type="text" wire:model="short_name" class="w-full bg-primary-bg border border-gray-700 rounded py-2 px-3 text-white focus:outline-none focus:border-accent-gold" required>
                            @error('short_name') <span class="text-accent-red text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-gray-300 text-sm font-bold mb-2">Logo</label>
                            <input type="file" wire:model="logo" class="w-full bg-primary-bg border border-gray-700 rounded py-2 px-3 text-white focus:outline-none focus:border-accent-gold">
                            @error('logo') <span class="text-accent-red text-xs">{{ $message }}</span> @enderror
                            @if($logo)
                                <img src="{{ $logo->temporaryUrl() }}" class="mt-2 w-16 h-16 object-cover rounded-full">
                            @elseif($existing_logo)
                                <img src="{{ asset('storage/' . $existing_logo) }}" class="mt-2 w-16 h-16 object-cover rounded-full">
                            @endif
                        </div>
                        <div>
                            <label class="block text-gray-300 text-sm font-bold mb-2">Primary Color</label>
                            <input type="color" wire:model="primary_color" class="w-full h-10 bg-primary-bg border border-gray-700 rounded p-1 text-white focus:outline-none focus:border-accent-gold">
                            @error('primary_color') <span class="text-accent-red text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-gray-300 text-sm font-bold mb-2">Budget (₹)</label>
                            <input type="number" step="0.01" wire:model="budget" class="w-full bg-primary-bg border border-gray-700 rounded py-2 px-3 text-white focus:outline-none focus:border-accent-gold" required>
                            @error('budget') <span class="text-accent-red text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-gray-300 text-sm font-bold mb-2">Owner</label>
                            <select wire:model="owner_id" class="w-full bg-primary-bg border border-gray-700 rounded py-2 px-3 text-white focus:outline-none focus:border-accent-gold" required>
                                <option value="">Select Owner</option>
                                @foreach($owners as $owner)
                                    <option value="{{ $owner->id }}">{{ $owner->name }}</option>
                                @endforeach
                            </select>
                            @error('owner_id') <span class="text-accent-red text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" wire:click="closeModal" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded transition">Cancel</button>
                        <button type="submit" class="bg-accent-gold hover:bg-yellow-400 text-primary-bg px-4 py-2 rounded font-semibold transition">Save</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
