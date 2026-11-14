<div>
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
        <h1 class="text-2xl font-semibold text-ink">Manage Guests</h1>
    </div>

    @if (session()->has('message'))
        <div class="alert-success mb-6" role="alert">
            <p>{{ session('message') }}</p>
        </div>
    @endif

    <!-- Upload Form -->
    <div class="bg-surface p-6 rounded-xl shadow-sm border border-line mb-8">
        <h2 class="text-xl font-semibold mb-4 text-ink">{{ $editId ? 'Edit Guest' : 'Add New Guest' }}</h2>
        <form wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-muted mb-1">Guest Photo</label>
                    <input type="file" wire:model="image" id="image-{{ $editId }}" class="block w-full text-sm text-muted file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-surface-subtle file:text-ink hover:file:bg-slate-200 bg-canvas border border-line rounded-md">
                    @error('image') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
                    <div wire:loading wire:target="image" class="text-xs text-brand mt-1 block">Uploading...</div>
                    @if ($image)
                        <img src="{{ $image->temporaryUrl() }}" class="mt-3 h-32 object-cover rounded shadow-sm border border-line">
                    @elseif($editId)
                        <span class="text-xs text-muted mt-1 block">Leave empty to keep current image.</span>
                    @endif
                </div>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Name *</label>
                        <input type="text" wire:model="name" class="block w-full rounded-md border-line bg-canvas text-ink shadow-sm focus:border-brand focus:ring-brand sm:text-sm" required>
                        @error('name') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Information / Designation</label>
                        <input type="text" wire:model="designation" placeholder="e.g. MLA, Chairman, Mayor" class="block w-full rounded-md border-line bg-canvas text-ink shadow-sm focus:border-brand focus:ring-brand sm:text-sm">
                        @error('designation') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Type *</label>
                        <x-select2 id="guestType" wire:model="type" placeholder="Select Type">
                            <option value="guest">Guest</option>
                            <option value="chief_guest">Chief Guest</option>
                        </x-select2>
                        @error('type') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Order</label>
                        <input type="number" wire:model="order" class="block w-full rounded-md border-line bg-canvas text-ink shadow-sm focus:border-brand focus:ring-brand sm:text-sm">
                        @error('order') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="flex justify-end mt-6 gap-3">
                @if($editId)
                    <button type="button" wire:click="cancelEdit" class="bg-surface-subtle hover:bg-slate-200 text-ink font-bold py-2 px-6 rounded shadow-sm transition-colors">
                        Cancel
                    </button>
                @endif
                <button type="submit" class="bg-brand hover:bg-brand-hover text-white font-medium py-2 px-6 rounded-lg shadow-sm transition-colors text-sm inline-flex items-center justify-center gap-2">
                    {{ $editId ? 'Update Guest' : 'Add Guest' }}
                </button>
            </div>
        </form>
    </div>

    <!-- Guests List -->
    <div class="bg-surface rounded-xl shadow-sm border border-line overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-line">
                <thead class="bg-surface-subtle">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-muted uppercase tracking-wider">Order</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-muted uppercase tracking-wider">Image</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-muted uppercase tracking-wider">Details</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-muted uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-muted uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($guests as $guest)
                        <tr class="hover:bg-surface-muted transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="number" value="{{ $guest->order }}" wire:change="updateOrder({{ $guest->id }}, $event.target.value)" class="w-16 rounded border-line bg-canvas text-ink text-sm focus:border-brand focus:ring-brand">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($guest->image_path)
                                    <img src="{{ asset('storage/' . $guest->image_path) }}" alt="{{ $guest->name }}" class="h-16 w-16 object-cover rounded-full shadow-sm border border-line">
                                @else
                                    <div class="h-16 w-16 rounded-full bg-surface-subtle flex items-center justify-center text-muted font-bold border border-line-strong">
                                        {{ substr($guest->name, 0, 1) }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-ink">{{ $guest->name }}</div>
                                @if($guest->designation)
                                    <div class="text-xs text-muted mb-1">{{ $guest->designation }}</div>
                                @endif
                                <div class="text-[10px] uppercase font-bold text-muted">{{ $guest->type === 'chief_guest' ? 'Chief Guest' : 'Guest' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <button wire:click="toggleActive({{ $guest->id }})" class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full {{ $guest->is_active ? 'bg-green-50 text-green-600 border border-green-200' : 'bg-red-50 text-red-600 border border-red-200' }} transition-colors hover:opacity-80">
                                    {{ $guest->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button wire:click="edit({{ $guest->id }})" class="text-blue-600 hover:text-blue-700 font-bold transition-colors mr-3">Edit</button>
                                <button wire:click="delete({{ $guest->id }})" wire:confirm="Are you sure you want to delete this guest?" class="text-red-600 hover:text-red-700 font-bold transition-colors">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-muted">No guests found. Add one above.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
