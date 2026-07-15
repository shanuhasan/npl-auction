<div>
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
        <h1 class="text-3xl font-poppins font-bold text-white">Manage Sponsors & Partners</h1>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-500 text-white p-4 rounded-lg font-bold mb-6" role="alert">
            <p>{{ session('message') }}</p>
        </div>
    @endif

    <!-- Upload Form -->
    <div class="bg-card-bg p-6 rounded-xl shadow-lg border border-gray-800 mb-8">
        <h2 class="text-xl font-bold mb-4 text-white font-poppins">{{ $editId ? 'Edit Sponsor' : 'Add New Sponsor' }}</h2>
        <form wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Sponsor Logo</label>
                    <input type="file" wire:model="logo" id="logo-{{ $editId }}" class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-gray-800 file:text-white hover:file:bg-gray-700 bg-primary-bg border border-gray-700 rounded-md">
                    @error('logo') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    <div wire:loading wire:target="logo" class="text-xs text-accent-gold mt-1 block">Uploading...</div>
                    @if ($logo)
                        <img src="{{ $logo->temporaryUrl() }}" class="mt-3 h-32 object-contain rounded shadow-sm border border-gray-700 bg-white">
                    @elseif($editId && $sponsors->firstWhere('id', $editId)?->logo_path)
                        <span class="text-xs text-gray-500 mt-1 block">Leave empty to keep current logo.</span>
                    @endif
                </div>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Name *</label>
                        <input type="text" wire:model="name" class="block w-full rounded-md border-gray-700 bg-primary-bg text-white shadow-sm focus:border-accent-gold focus:ring-accent-gold sm:text-sm" required>
                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Type</label>
                        <select wire:model="type" class="block w-full rounded-md border-gray-700 bg-primary-bg text-white shadow-sm focus:border-accent-gold focus:ring-accent-gold sm:text-sm">
                            <option value="title_sponsor">Title Sponsor</option>
                            <option value="premier_partner">Premier Partner</option>
                            <option value="sponsor">Sponsor</option>
                        </select>
                        @error('type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">URL (Website Link)</label>
                        <input type="url" wire:model="url" placeholder="https://" class="block w-full rounded-md border-gray-700 bg-primary-bg text-white shadow-sm focus:border-accent-gold focus:ring-accent-gold sm:text-sm">
                        @error('url') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Order</label>
                        <input type="number" wire:model="order" class="block w-full rounded-md border-gray-700 bg-primary-bg text-white shadow-sm focus:border-accent-gold focus:ring-accent-gold sm:text-sm">
                        @error('order') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
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
                    {{ $editId ? 'Update Sponsor' : 'Add Sponsor' }}
                </button>
            </div>
        </form>
    </div>

    <!-- Sponsors List -->
    <div class="bg-card-bg rounded-xl shadow-lg border border-gray-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-800">
                <thead class="bg-gray-800/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Order</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Logo</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Details</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    @forelse ($sponsors as $sponsor)
                        <tr class="hover:bg-gray-800/30 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="number" value="{{ $sponsor->order }}" wire:change="updateOrder({{ $sponsor->id }}, $event.target.value)" class="w-16 rounded border-gray-700 bg-primary-bg text-white text-sm focus:border-accent-gold focus:ring-accent-gold">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($sponsor->logo_path)
                                    <div class="bg-white p-1 rounded inline-block">
                                        <img src="{{ asset('storage/' . $sponsor->logo_path) }}" alt="{{ $sponsor->name }}" class="h-12 w-auto max-w-[100px] object-contain">
                                    </div>
                                @else
                                    <div class="h-12 w-12 rounded-full bg-gray-700 flex items-center justify-center text-gray-400 font-bold border border-gray-600">
                                        {{ substr($sponsor->name, 0, 1) }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-white">
                                    @if($sponsor->url)
                                        <a href="{{ $sponsor->url }}" target="_blank" class="text-accent-gold hover:underline">{{ $sponsor->name }}</a>
                                    @else
                                        {{ $sponsor->name }}
                                    @endif
                                </div>
                                <div class="text-xs mt-1 inline-flex items-center px-2 py-0.5 rounded text-gray-300 bg-gray-800 border border-gray-700">
                                    {{ str_replace('_', ' ', ucwords($sponsor->type, '_')) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <button wire:click="toggleActive({{ $sponsor->id }})" class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full {{ $sponsor->is_active ? 'bg-green-500/20 text-green-400 border border-green-500/30' : 'bg-red-500/20 text-red-400 border border-red-500/30' }} transition-colors hover:opacity-80">
                                    {{ $sponsor->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button wire:click="edit({{ $sponsor->id }})" class="text-blue-500 hover:text-blue-400 font-bold transition-colors mr-3">Edit</button>
                                <button wire:click="delete({{ $sponsor->id }})" wire:confirm="Are you sure you want to delete this sponsor?" class="text-red-500 hover:text-red-400 font-bold transition-colors">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">No sponsors found. Add one above.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
