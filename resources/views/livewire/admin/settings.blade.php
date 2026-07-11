<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Application Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-card-bg shadow border border-gray-800 rounded-lg overflow-hidden">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <h3 class="text-2xl font-poppins font-bold text-accent-gold mb-6 border-b border-gray-800 pb-2">Global Settings</h3>
                    
                    @if (session()->has('success'))
                        <div class="bg-green-500/20 border border-green-500 text-green-400 p-4 rounded-lg font-bold mb-6">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form wire:submit.prevent="save" class="space-y-6">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- App Name -->
                            <div>
                                <label for="app_name" class="block text-sm font-medium text-gray-400 mb-1">Application Name</label>
                                <input type="text" id="app_name" wire:model="app_name" class="w-full bg-gray-900 border border-gray-700 rounded-lg p-3 text-white focus:ring-accent-gold focus:border-accent-gold transition" placeholder="e.g. Naugawan Premier League">
                                @error('app_name') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Season -->
                            <div>
                                <label for="season" class="block text-sm font-medium text-gray-400 mb-1">Season</label>
                                <input type="text" id="season" wire:model="season" class="w-full bg-gray-900 border border-gray-700 rounded-lg p-3 text-white focus:ring-accent-gold focus:border-accent-gold transition" placeholder="e.g. Season 2026">
                                @error('season') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Logo File -->
                            <div>
                                <label for="new_logo" class="block text-sm font-medium text-gray-400 mb-1">Upload Logo</label>
                                @if($logo)
                                    <div class="mb-3 p-2 bg-gray-800 rounded-lg inline-block">
                                        <img src="{{ asset('storage/' . $logo) }}" alt="Current Logo" class="h-12 object-contain">
                                    </div>
                                @endif
                                <input type="file" id="new_logo" wire:model="new_logo" class="w-full bg-gray-900 border border-gray-700 rounded-lg p-2 text-white focus:ring-accent-gold focus:border-accent-gold transition file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-accent-gold file:text-gray-900 hover:file:bg-yellow-400">
                                @error('new_logo') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Favicon File -->
                            <div>
                                <label for="new_favicon" class="block text-sm font-medium text-gray-400 mb-1">Upload Favicon (Icon)</label>
                                @if($favicon)
                                    <div class="mb-3 p-2 bg-gray-800 rounded-lg inline-block">
                                        <img src="{{ asset('storage/' . $favicon) }}" alt="Current Favicon" class="h-8 w-8 object-contain">
                                    </div>
                                @endif
                                <input type="file" id="new_favicon" wire:model="new_favicon" class="w-full bg-gray-900 border border-gray-700 rounded-lg p-2 text-white focus:ring-accent-gold focus:border-accent-gold transition file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-accent-gold file:text-gray-900 hover:file:bg-yellow-400">
                                <p class="text-xs text-gray-500 mt-1">Recommended format: .ico, .png (square)</p>
                                @error('new_favicon') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Contact Email -->
                            <div>
                                <label for="contact_email" class="block text-sm font-medium text-gray-400 mb-1">Contact Email</label>
                                <input type="email" id="contact_email" wire:model="contact_email" class="w-full bg-gray-900 border border-gray-700 rounded-lg p-3 text-white focus:ring-accent-gold focus:border-accent-gold transition" placeholder="info@example.com">
                                @error('contact_email') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Contact Phone -->
                            <div>
                                <label for="contact_phone" class="block text-sm font-medium text-gray-400 mb-1">Contact Phone</label>
                                <input type="text" id="contact_phone" wire:model="contact_phone" class="w-full bg-gray-900 border border-gray-700 rounded-lg p-3 text-white focus:ring-accent-gold focus:border-accent-gold transition" placeholder="+91 9876543210">
                                @error('contact_phone') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>
                            
                            <!-- Developer Name -->
                            <div>
                                <label for="developer_name" class="block text-sm font-medium text-gray-400 mb-1">Developer Name</label>
                                <input type="text" id="developer_name" wire:model="developer_name" class="w-full bg-gray-900 border border-gray-700 rounded-lg p-3 text-white focus:ring-accent-gold focus:border-accent-gold transition" placeholder="e.g. Insphire">
                                @error('developer_name') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Developer URL -->
                            <div>
                                <label for="developer_url" class="block text-sm font-medium text-gray-400 mb-1">Developer URL</label>
                                <input type="text" id="developer_url" wire:model="developer_url" class="w-full bg-gray-900 border border-gray-700 rounded-lg p-3 text-white focus:ring-accent-gold focus:border-accent-gold transition" placeholder="e.g. https://insphire.in">
                                @error('developer_url') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="pt-4 border-t border-gray-800 flex justify-end">
                            <button type="submit" class="px-8 py-3 bg-gradient-to-r from-[#FFC800] to-[#D4A000] hover:from-[#FFE040] hover:to-[#FFC800] text-[#0B0F19] font-bold rounded-lg shadow uppercase tracking-wider text-sm transition transform hover:-translate-y-1 hover:shadow-[0_0_15px_rgba(255,200,0,0.4)] flex items-center gap-2">
                                <span wire:loading.remove wire:target="save">Save Settings</span>
                                <span wire:loading wire:target="save">Saving...</span>
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
