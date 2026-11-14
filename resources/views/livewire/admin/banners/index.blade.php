<div>
    {{-- Do your work, then step back. --}}

    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
        <h1 class="text-2xl font-semibold text-ink">Manage Banners</h1>
    </div>

    @if (session()->has('message'))
        <div class="alert-success mb-6" role="alert">
            <p>{{ session('message') }}</p>
        </div>
    @endif

    <!-- Upload Form -->
    <div class="bg-surface p-6 rounded-xl shadow-sm border border-line mb-8">
        <h2 class="text-xl font-semibold mb-4 text-ink">{{ $editId ? 'Edit Banner' : 'Add New Banner' }}</h2>
        <form wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-muted mb-1">Banner Image {{ $editId ? '' : '*' }}</label>
                    <input type="file" wire:model="image" id="image-{{ $editId }}" class="block w-full text-sm text-muted file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-surface-subtle file:text-ink hover:file:bg-slate-200 bg-canvas border border-line rounded-md">
                    <p class="text-xs text-muted mt-1">Recommended size: 1920x600 pixels (or 16:9 aspect ratio) for best fit.</p>
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
                        <label class="block text-sm font-medium text-muted mb-1">Title</label>
                        <input type="text" wire:model="title" class="block w-full rounded-md border-line bg-canvas text-ink shadow-sm focus:border-brand focus:ring-brand sm:text-sm">
                        @error('title') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Description</label>
                        <textarea wire:model="description" rows="2" class="block w-full rounded-md border-line bg-canvas text-ink shadow-sm focus:border-brand focus:ring-brand sm:text-sm"></textarea>
                        @error('description') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-muted mb-1">Link URL</label>
                            <input type="url" wire:model="link" placeholder="https://" class="block w-full rounded-md border-line bg-canvas text-ink shadow-sm focus:border-brand focus:ring-brand sm:text-sm">
                            @error('link') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-muted mb-1">Button Name</label>
                            <input type="text" wire:model="button_name" placeholder="Learn More" class="block w-full rounded-md border-line bg-canvas text-ink shadow-sm focus:border-brand focus:ring-brand sm:text-sm">
                            @error('button_name') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-muted mb-1">Order</label>
                            <input type="number" wire:model="order" class="block w-full rounded-md border-line bg-canvas text-ink shadow-sm focus:border-brand focus:ring-brand sm:text-sm">
                            @error('order') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </div>
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
                    {{ $editId ? 'Update Banner' : 'Upload Banner' }}
                </button>
            </div>
        </form>
    </div>

    <!-- Banners List -->
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
                    @forelse ($banners as $banner)
                        <tr class="hover:bg-surface-muted transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="number" value="{{ $banner->order }}" x-on:change="$wire.updateOrder({{ $banner->id }}, $event.target.value)" class="w-16 rounded border-line bg-canvas text-ink text-sm focus:border-brand focus:ring-brand">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <img src="{{ asset('storage/' . $banner->image_path) }}" alt="Banner" class="h-16 w-32 object-cover rounded shadow-sm border border-line">
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-ink">{{ $banner->title ?: 'No Title' }}</div>
                                <div class="text-sm text-muted line-clamp-1">{{ $banner->description }}</div>
                                @if($banner->link)
                                    <a href="{{ $banner->link }}" target="_blank" class="text-xs text-brand hover:underline mt-1 inline-block">Link &nearr;</a>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <button wire:click="toggleActive({{ $banner->id }})" class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full {{ $banner->is_active ? 'bg-green-50 text-green-600 border border-green-200' : 'bg-red-50 text-red-600 border border-red-200' }} transition-colors hover:opacity-80">
                                    {{ $banner->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button wire:click="edit({{ $banner->id }})" class="text-blue-600 hover:text-blue-700 font-bold transition-colors mr-3">Edit</button>
                                <button wire:click="delete({{ $banner->id }})" wire:confirm="Are you sure you want to delete this banner?" class="text-red-600 hover:text-red-700 font-bold transition-colors">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-muted">No banners found. Upload one above.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
