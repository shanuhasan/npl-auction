<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-ink">Manage Teams</h1>
        <button wire:click="create" class="bg-brand text-white px-4 py-2 rounded-lg font-medium hover:bg-brand-hover transition text-sm inline-flex items-center justify-center gap-2">Add New Team</button>
    </div>

    @if (session()->has('message'))
        <div class="alert-success mb-4">
            {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($teams as $team)
            <div class="bg-surface rounded-lg shadow-sm border-t-4 p-6" style="border-top-color: {{ $team->primary_color ?? '#FFC800' }}">
                <div class="flex items-center space-x-4 mb-4">
                    @if($team->logo)
                        <img src="{{ asset('storage/' . $team->logo) }}" alt="Logo" class="w-16 h-16 rounded-full object-cover bg-surface-subtle">
                    @else
                        <div class="w-16 h-16 rounded-full bg-surface-subtle flex items-center justify-center text-brand font-bold text-xl">{{ substr($team->name, 0, 1) }}</div>
                    @endif
                    <div>
                        <h2 class="text-xl font-semibold text-ink">{{ $team->name }}</h2>
                        <span class="text-sm font-inter text-muted">{{ $team->short_name }}</span>
                    </div>
                </div>
                <div class="space-y-2 mb-4 text-sm font-inter">
                    <p class="text-ink-soft">Owner: <span class="text-ink">{{ $team->owner->name ?? 'N/A' }}</span></p>
                    <p class="text-ink-soft">Budget: <span class="text-ink">₹{{ number_format($team->budget, 2) }}</span></p>
                    <p class="text-ink-soft">Remaining: <span class="text-success">₹{{ number_format($team->remaining_budget, 2) }}</span></p>
                </div>
                <div class="flex justify-end space-x-2 items-center">
                    <button wire:click="toggleStatus({{ $team->id }})" 
                            class="mr-2 relative inline-flex items-center h-6 rounded-full w-11 focus:outline-none transition-colors ease-in-out duration-200 {{ $team->is_approved ? 'bg-green-500' : 'bg-slate-200' }}" title="{{ $team->is_approved ? 'Approved' : 'Not Approved' }}">
                        <span class="inline-block w-4 h-4 transform bg-white rounded-full transition ease-in-out duration-200 {{ $team->is_approved ? 'translate-x-6' : 'translate-x-1' }}"></span>
                    </button>
                    <a href="{{ route('teams.pdf', $team->guid) }}" target="_blank" class="text-blue-600 hover:text-blue-700 transition p-2" title="Download Squad PDF">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </a>
                    <button wire:click="edit({{ $team->id }})" class="bg-surface-subtle hover:bg-slate-200 text-ink px-3 py-1 rounded text-sm transition">Edit</button>
                    <button wire:click="delete({{ $team->id }})" wire:confirm="Are you sure you want to delete this team?" class="bg-danger hover:bg-red-500 text-white px-3 py-1 rounded text-sm transition">Delete</button>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Modal -->
    @if($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 overflow-y-auto">
            <div class="bg-surface rounded-lg w-full max-w-lg p-6 my-8">
                <h2 class="text-lg font-semibold text-ink mb-4">{{ $team_id ? 'Edit Team' : 'Create Team' }}</h2>
                <form wire:submit.prevent="store">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-ink-soft text-sm font-bold mb-2">Team Name</label>
                            <input type="text" wire:model="name" class="w-full bg-canvas border border-line rounded py-2 px-3 text-ink focus:outline-none focus:border-brand" required>
                            @error('name') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-ink-soft text-sm font-bold mb-2">Short Name</label>
                            <input type="text" wire:model="short_name" class="w-full bg-canvas border border-line rounded py-2 px-3 text-ink focus:outline-none focus:border-brand" required>
                            @error('short_name') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-ink-soft text-sm font-bold mb-2">Logo</label>
                            <input type="file" wire:model="logo" class="w-full bg-canvas border border-line rounded py-2 px-3 text-ink focus:outline-none focus:border-brand">
                            @error('logo') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                            @if($logo)
                                <img src="{{ $logo->temporaryUrl() }}" class="mt-2 w-16 h-16 object-cover rounded-full">
                            @elseif($existing_logo)
                                <img src="{{ asset('storage/' . $existing_logo) }}" class="mt-2 w-16 h-16 object-cover rounded-full">
                            @endif
                        </div>
                        <div>
                            <label class="block text-ink-soft text-sm font-bold mb-2">Primary Color</label>
                            <input type="color" wire:model="primary_color" class="w-full h-10 bg-canvas border border-line rounded p-1 text-ink focus:outline-none focus:border-brand">
                            @error('primary_color') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-ink-soft text-sm font-bold mb-2">Budget (₹)</label>
                            <input type="number" step="0.01" wire:model="budget" class="w-full bg-canvas border border-line rounded py-2 px-3 text-ink focus:outline-none focus:border-brand" required>
                            @error('budget') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-ink-soft text-sm font-bold mb-2">Owner</label>
                            <x-select2 id="owner_id" wire:model="owner_id" placeholder="Select Owner" required>
                                @foreach($owners as $owner)
                                    <option value="{{ $owner->id }}">{{ $owner->name }}</option>
                                @endforeach
                            </x-select2>
                            @error('owner_id') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" wire:click="closeModal" class="bg-surface-subtle hover:bg-slate-200 text-ink px-4 py-2 rounded transition">Cancel</button>
                        <button type="submit" class="bg-brand hover:bg-brand-hover text-white px-4 py-2 rounded-lg font-medium transition text-sm inline-flex items-center justify-center gap-2">Save</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
