<div>
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
        <h1 class="text-3xl font-poppins font-bold text-white">Manage Core Committee</h1>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-500 text-white p-4 rounded-lg font-bold mb-6" role="alert">
            <p>{{ session('message') }}</p>
        </div>
    @endif

    <!-- Upload Form -->
    <div class="bg-card-bg p-6 rounded-xl shadow-lg border border-gray-800 mb-8">
        <h2 class="text-xl font-bold mb-4 text-white font-poppins">{{ $editId ? 'Edit Member' : 'Add New Member' }}</h2>
        <form wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Member Image</label>
                    <input type="file" wire:model="image" id="image-{{ $editId }}" class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-gray-800 file:text-white hover:file:bg-gray-700 bg-primary-bg border border-gray-700 rounded-md">
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
                        <label class="block text-sm font-medium text-gray-400 mb-1">Name *</label>
                        <input type="text" wire:model="name" class="block w-full rounded-md border-gray-700 bg-primary-bg text-white shadow-sm focus:border-accent-gold focus:ring-accent-gold sm:text-sm" required>
                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Role / Position</label>
                        <input type="text" wire:model="role" class="block w-full rounded-md border-gray-700 bg-primary-bg text-white shadow-sm focus:border-accent-gold focus:ring-accent-gold sm:text-sm">
                        @error('role') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Order</label>
                        <input type="number" wire:model="order" class="block w-full rounded-md border-gray-700 bg-primary-bg text-white shadow-sm focus:border-accent-gold focus:ring-accent-gold sm:text-sm">
                        @error('order') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Social Media Links -->
            <div class="pt-4 border-t border-gray-800 mt-4">
                <h3 class="text-sm font-bold text-gray-300 uppercase tracking-wider mb-3">Social Media Links</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1 flex items-center gap-1">
                            <span class="text-blue-500 font-bold">Facebook</span>
                        </label>
                        <input type="url" wire:model="facebook" placeholder="https://facebook.com/username" class="block w-full rounded-md border-gray-700 bg-primary-bg text-white text-xs shadow-sm focus:border-accent-gold focus:ring-accent-gold">
                        @error('facebook') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1 flex items-center gap-1">
                            <span class="text-pink-500 font-bold">Instagram</span>
                        </label>
                        <input type="url" wire:model="instagram" placeholder="https://instagram.com/username" class="block w-full rounded-md border-gray-700 bg-primary-bg text-white text-xs shadow-sm focus:border-accent-gold focus:ring-accent-gold">
                        @error('instagram') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1 flex items-center gap-1">
                            <span class="text-sky-400 font-bold">Twitter / X</span>
                        </label>
                        <input type="url" wire:model="twitter" placeholder="https://x.com/username" class="block w-full rounded-md border-gray-700 bg-primary-bg text-white text-xs shadow-sm focus:border-accent-gold focus:ring-accent-gold">
                        @error('twitter') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1 flex items-center gap-1">
                            <span class="text-blue-400 font-bold">LinkedIn</span>
                        </label>
                        <input type="url" wire:model="linkedin" placeholder="https://linkedin.com/in/username" class="block w-full rounded-md border-gray-700 bg-primary-bg text-white text-xs shadow-sm focus:border-accent-gold focus:ring-accent-gold">
                        @error('linkedin') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1 flex items-center gap-1">
                            <span class="text-green-500 font-bold">WhatsApp</span>
                        </label>
                        <input type="text" wire:model="whatsapp" placeholder="https://wa.me/1234567890 or +1234567890" class="block w-full rounded-md border-gray-700 bg-primary-bg text-white text-xs shadow-sm focus:border-accent-gold focus:ring-accent-gold">
                        @error('whatsapp') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
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
                    {{ $editId ? 'Update Member' : 'Add Member' }}
                </button>
            </div>
        </form>
    </div>

    <!-- Members List -->
    <div class="bg-card-bg rounded-xl shadow-lg border border-gray-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-800">
                <thead class="bg-gray-800/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Order</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Image</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Details</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Social Links</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    @forelse ($members as $member)
                        <tr class="hover:bg-gray-800/30 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="number" value="{{ $member->order }}" wire:change="updateOrder({{ $member->id }}, $event.target.value)" class="w-16 rounded border-gray-700 bg-primary-bg text-white text-sm focus:border-accent-gold focus:ring-accent-gold">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($member->image_path)
                                    <img src="{{ asset('storage/' . $member->image_path) }}" alt="{{ $member->name }}" class="h-16 w-16 object-cover rounded-full shadow border border-gray-700">
                                @else
                                    <div class="h-16 w-16 rounded-full bg-gray-700 flex items-center justify-center text-gray-400 font-bold border border-gray-600">
                                        {{ substr($member->name, 0, 1) }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-white">{{ $member->name }}</div>
                                <div class="text-sm text-gray-400">{{ $member->role }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    @if($member->facebook)
                                        <a href="{{ $member->facebook }}" target="_blank" title="Facebook" class="text-blue-500 hover:text-blue-400 transition-colors">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                        </a>
                                    @endif
                                    @if($member->instagram)
                                        <a href="{{ $member->instagram }}" target="_blank" title="Instagram" class="text-pink-500 hover:text-pink-400 transition-colors">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                        </a>
                                    @endif
                                    @if($member->twitter)
                                        <a href="{{ $member->twitter }}" target="_blank" title="Twitter / X" class="text-sky-400 hover:text-sky-300 transition-colors">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                        </a>
                                    @endif
                                    @if($member->linkedin)
                                        <a href="{{ $member->linkedin }}" target="_blank" title="LinkedIn" class="text-blue-400 hover:text-blue-300 transition-colors">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                                        </a>
                                    @endif
                                    @if($member->whatsapp)
                                        @php
                                            $waUrl = \Illuminate\Support\Str::startsWith($member->whatsapp, ['http://', 'https://']) ? $member->whatsapp : 'https://wa.me/' . preg_replace('/[^0-9]/', '', $member->whatsapp);
                                        @endphp
                                        <a href="{{ $waUrl }}" target="_blank" title="WhatsApp" class="text-green-500 hover:text-green-400 transition-colors">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                        </a>
                                    @endif
                                    @if(!$member->facebook && !$member->instagram && !$member->twitter && !$member->linkedin && !$member->whatsapp)
                                        <span class="text-xs text-gray-600 font-mono">None</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <button wire:click="toggleActive({{ $member->id }})" class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full {{ $member->is_active ? 'bg-green-500/20 text-green-400 border border-green-500/30' : 'bg-red-500/20 text-red-400 border border-red-500/30' }} transition-colors hover:opacity-80">
                                    {{ $member->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button wire:click="edit({{ $member->id }})" class="text-blue-500 hover:text-blue-400 font-bold transition-colors mr-3">Edit</button>
                                <button wire:click="delete({{ $member->id }})" wire:confirm="Are you sure you want to delete this member?" class="text-red-500 hover:text-red-400 font-bold transition-colors">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">No members found. Add one above.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
