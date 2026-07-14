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
        <h2 class="text-xl font-bold mb-4 text-white font-poppins">Add New Member</h2>
        <form wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Member Image</label>
                    <input type="file" wire:model="image" class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-gray-800 file:text-white hover:file:bg-gray-700 bg-primary-bg border border-gray-700 rounded-md">
                    @error('image') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    <div wire:loading wire:target="image" class="text-xs text-accent-gold mt-1 block">Uploading...</div>
                    @if ($image)
                        <img src="{{ $image->temporaryUrl() }}" class="mt-3 h-32 object-cover rounded shadow-sm border border-gray-700">
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

            <div class="flex justify-end mt-6">
                <button type="submit" class="bg-accent-gold hover:bg-yellow-500 text-black font-bold py-2 px-6 rounded shadow transition-colors">
                    Add Member
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
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <button wire:click="toggleActive({{ $member->id }})" class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full {{ $member->is_active ? 'bg-green-500/20 text-green-400 border border-green-500/30' : 'bg-red-500/20 text-red-400 border border-red-500/30' }} transition-colors hover:opacity-80">
                                    {{ $member->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button wire:click="delete({{ $member->id }})" wire:confirm="Are you sure you want to delete this member?" class="text-red-500 hover:text-red-400 font-bold transition-colors">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">No members found. Add one above.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
