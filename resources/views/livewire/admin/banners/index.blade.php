<div>
    {{-- Do your work, then step back. --}}

    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
        <h1 class="text-3xl font-poppins font-bold text-white">Manage Banners</h1>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-500 text-white p-4 rounded-lg font-bold mb-6" role="alert">
            <p>{{ session('message') }}</p>
        </div>
    @endif

    <!-- Upload Form -->
    <div class="bg-card-bg p-6 rounded-xl shadow-lg border border-gray-800 mb-8">
        <h2 class="text-xl font-bold mb-4 text-white font-poppins">{{ $editId ? 'Edit Banner' : 'Add New Banner' }}</h2>
        <form wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Banner Image {{ $editId ? '' : '*' }}</label>
                    <input type="file" wire:model="image" id="image-{{ $editId }}" class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-gray-800 file:text-white hover:file:bg-gray-700 bg-primary-bg border border-gray-700 rounded-md">
                    <p class="text-xs text-gray-500 mt-1">Recommended size: 1920x600 pixels (or 16:9 aspect ratio) for best fit.</p>
                    @error('image') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    <div wire:loading wire:target="image" class="text-xs text-accent-gold mt-1 block">Uploading...</div>
                    @if ($image)
                        <img src="{{ $image->temporaryUrl() }}" class="mt-3 h-32 object-cover rounded shadow-sm border border-gray-700">
                    @elseif($editId)
                        <span class="text-xs text-gray-500 mt-1 block">Leave empty to keep current image.</span>
                    @endif
                </div>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Title</label>
                        <input type="text" wire:model="title" class="block w-full rounded-md border-gray-700 bg-primary-bg text-white shadow-sm focus:border-accent-gold focus:ring-accent-gold sm:text-sm">
                        @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Description</label>
                        <textarea wire:model="description" rows="2" class="block w-full rounded-md border-gray-700 bg-primary-bg text-white shadow-sm focus:border-accent-gold focus:ring-accent-gold sm:text-sm"></textarea>
                        @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Link URL</label>
                            <input type="url" wire:model="link" placeholder="https://" class="block w-full rounded-md border-gray-700 bg-primary-bg text-white shadow-sm focus:border-accent-gold focus:ring-accent-gold sm:text-sm">
                            @error('link') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Order</label>
                            <input type="number" wire:model="order" class="block w-full rounded-md border-gray-700 bg-primary-bg text-white shadow-sm focus:border-accent-gold focus:ring-accent-gold sm:text-sm">
                            @error('order') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end mt-6 gap-3">
                @if($editId)
                    <button type="button" wire:click="cancelEdit" class="bg-gray-700 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded shadow transition-colors">
                        Cancel
                    </button>
                @endif
                <button type="submit" class="bg-accent-gold hover:bg-yellow-500 text-black font-bold py-2 px-6 rounded shadow transition-colors">
                    {{ $editId ? 'Update Banner' : 'Upload Banner' }}
                </button>
            </div>
        </form>
    </div>

    <!-- Banners List -->
    <div class="bg-card-bg rounded-xl shadow-lg border border-gray-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-800">
                <thead class="bg-gray-800/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Order</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Image</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Details</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    @forelse ($banners as $banner)
                        <tr class="hover:bg-gray-800/30 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="number" value="{{ $banner->order }}" wire:change="updateOrder({{ $banner->id }}, $event.target.value)" class="w-16 rounded border-gray-700 bg-primary-bg text-white text-sm focus:border-accent-gold focus:ring-accent-gold">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <img src="{{ asset('storage/' . $banner->image_path) }}" alt="Banner" class="h-16 w-32 object-cover rounded shadow border border-gray-700">
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-white">{{ $banner->title ?: 'No Title' }}</div>
                                <div class="text-sm text-gray-400 line-clamp-1">{{ $banner->description }}</div>
                                @if($banner->link)
                                    <a href="{{ $banner->link }}" target="_blank" class="text-xs text-accent-gold hover:underline mt-1 inline-block">Link &nearr;</a>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <button wire:click="toggleActive({{ $banner->id }})" class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full {{ $banner->is_active ? 'bg-green-500/20 text-green-400 border border-green-500/30' : 'bg-red-500/20 text-red-400 border border-red-500/30' }} transition-colors hover:opacity-80">
                                    {{ $banner->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button wire:click="edit({{ $banner->id }})" class="text-blue-500 hover:text-blue-400 font-bold transition-colors mr-3">Edit</button>
                                <button wire:click="delete({{ $banner->id }})" wire:confirm="Are you sure you want to delete this banner?" class="text-red-500 hover:text-red-400 font-bold transition-colors">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">No banners found. Upload one above.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
