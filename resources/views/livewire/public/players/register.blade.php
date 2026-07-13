<div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <div class="bg-card-bg shadow-2xl rounded-lg p-8 border border-gray-800">
        <h1 class="text-3xl font-poppins font-bold text-accent-gold mb-6 border-b border-gray-700 pb-4">Player Registration</h1>
        
        @if($isSubmitted)
            <div class="bg-success-green text-white p-6 rounded-lg text-center shadow-lg">
                <svg class="w-16 h-16 mx-auto mb-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <h2 class="text-2xl font-bold mb-2">Registration Successful!</h2>
                <p class="text-lg">Your profile has been submitted and is currently pending admin approval. You will appear in the Players Directory once approved.</p>
                <a href="{{ route('public.players') }}" class="inline-block mt-6 px-6 py-2 bg-white text-success-green font-bold rounded hover:bg-gray-100 transition">Back to Players Directory</a>
            </div>
        @else
            <form wire:submit.prevent="register">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Basic Info -->
                    <div>
                        <label class="block text-gray-300 text-sm font-bold mb-2">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="name" class="w-full bg-primary-bg border border-gray-700 rounded py-3 px-4 text-white focus:outline-none focus:border-accent-gold transition" placeholder="e.g. Virat Kohli">
                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-gray-300 text-sm font-bold mb-2">Contact No. <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="contact_no" class="w-full bg-primary-bg border border-gray-700 rounded py-3 px-4 text-white focus:outline-none focus:border-accent-gold transition" placeholder="e.g. +91 9876543210">
                        @error('contact_no') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-gray-300 text-sm font-bold mb-2">Role <span class="text-red-500">*</span></label>
                        <select wire:model="role" class="w-full bg-primary-bg border border-gray-700 rounded py-3 px-4 text-white focus:outline-none focus:border-accent-gold transition">
                            <option value="batsman">Batsman</option>
                            <option value="bowler">Bowler</option>
                            <option value="all-rounder">All-Rounder</option>
                            <option value="wicketkeeper">Wicketkeeper</option>
                        </select>
                        @error('role') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-300 text-sm font-bold mb-2">Country <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="country" class="w-full bg-primary-bg border border-gray-700 rounded py-3 px-4 text-white focus:outline-none focus:border-accent-gold transition" placeholder="e.g. India">
                        @error('country') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-300 text-sm font-bold mb-2">City/Village/Town</label>
                        <input type="text" wire:model="city" class="w-full bg-primary-bg border border-gray-700 rounded py-3 px-4 text-white focus:outline-none focus:border-accent-gold transition" placeholder="e.g. Delhi">
                        @error('city') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-300 text-sm font-bold mb-2">Batting Style</label>
                        <select wire:model="batting_style" class="w-full bg-primary-bg border border-gray-700 rounded py-3 px-4 text-white focus:outline-none focus:border-accent-gold transition">
                            <option value="">Select Batting Style</option>
                            <option value="Right-hand bat">Right-hand bat</option>
                            <option value="Left-hand bat">Left-hand bat</option>
                        </select>
                        @error('batting_style') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-300 text-sm font-bold mb-2">Bowling Style</label>
                        <select wire:model="bowling_style" class="w-full bg-primary-bg border border-gray-700 rounded py-3 px-4 text-white focus:outline-none focus:border-accent-gold transition">
                            <option value="">Select Bowling Style (or None)</option>
                            <option value="None">None</option>
                            <option value="Right-arm fast">Right-arm fast</option>
                            <option value="Right-arm medium">Right-arm medium</option>
                            <option value="Right-arm offbreak">Right-arm offbreak</option>
                            <option value="Right-arm legbreak">Right-arm legbreak</option>
                            <option value="Left-arm fast">Left-arm fast</option>
                            <option value="Left-arm medium">Left-arm medium</option>
                            <option value="Left-arm orthodox">Left-arm orthodox</option>
                            <option value="Left-arm chinaman">Left-arm chinaman</option>
                        </select>
                        @error('bowling_style') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Photo Upload -->
                    <div class="md:col-span-2">
                        <label class="block text-gray-300 text-sm font-bold mb-2">Profile Photo <span class="text-red-500">*</span></label>
                        <div class="flex items-center space-x-6">
                            <div class="flex-shrink-0 h-24 w-24 rounded-full border-2 border-dashed border-gray-600 flex items-center justify-center overflow-hidden bg-primary-bg">
                                @if ($photo)
                                    <img src="{{ $photo->temporaryUrl() }}" class="h-full w-full object-cover">
                                @else
                                    <svg class="h-8 w-8 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                @endif
                            </div>
                            <div class="flex-1">
                                <input type="file" wire:model="photo" class="w-full bg-primary-bg border border-gray-700 rounded py-2 px-3 text-white focus:outline-none focus:border-accent-gold transition file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-accent-gold file:text-primary-bg hover:file:bg-yellow-400">
                                <p class="text-xs text-gray-400 mt-2">JPG, PNG or GIF up to 2MB. Clear face photo recommended.</p>
                                @error('photo') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Stats Section -->
                    <div class="md:col-span-2 mt-6">
                        <h3 class="text-xl text-accent-gold font-bold mb-4 border-b border-gray-700 pb-2">Career Statistics</h3>
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                            <div>
                                <label class="block text-gray-400 text-xs font-bold mb-1">Matches</label>
                                <input type="number" wire:model="stats.matches" class="w-full bg-primary-bg border border-gray-700 rounded py-2 px-3 text-white focus:outline-none focus:border-accent-gold">
                            </div>
                            <div>
                                <label class="block text-gray-400 text-xs font-bold mb-1">Runs</label>
                                <input type="number" wire:model="stats.runs" class="w-full bg-primary-bg border border-gray-700 rounded py-2 px-3 text-white focus:outline-none focus:border-accent-gold">
                            </div>
                            <div>
                                <label class="block text-gray-400 text-xs font-bold mb-1">Wickets</label>
                                <input type="number" wire:model="stats.wickets" class="w-full bg-primary-bg border border-gray-700 rounded py-2 px-3 text-white focus:outline-none focus:border-accent-gold">
                            </div>
                            <div>
                                <label class="block text-gray-400 text-xs font-bold mb-1">Average</label>
                                <input type="number" step="0.01" wire:model="stats.average" class="w-full bg-primary-bg border border-gray-700 rounded py-2 px-3 text-white focus:outline-none focus:border-accent-gold">
                            </div>
                            <div>
                                <label class="block text-gray-400 text-xs font-bold mb-1">Strike Rate</label>
                                <input type="number" step="0.01" wire:model="stats.strike_rate" class="w-full bg-primary-bg border border-gray-700 rounded py-2 px-3 text-white focus:outline-none focus:border-accent-gold">
                            </div>
                        </div>
                    </div>

                </div>

                <div class="mt-8 pt-6 border-t border-gray-700 flex justify-end">
                    <a href="{{ route('public.players') }}" class="bg-gray-700 hover:bg-gray-600 text-white px-6 py-3 rounded-lg mr-4 transition font-semibold">Cancel</a>
                    <button type="submit" class="bg-accent-gold hover:bg-yellow-400 text-primary-bg px-8 py-3 rounded-lg font-bold text-lg shadow-lg transition flex items-center">
                        <span wire:loading.remove wire:target="register">Submit Registration</span>
                        <span wire:loading wire:target="register">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-primary-bg inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing...
                        </span>
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>
