<div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <div class="bg-surface shadow-sm rounded-lg p-8 border border-line">
        <h1 class="text-3xl font-semibold text-brand mb-6 border-b border-line pb-4 tracking-tight">Team Registration</h1>
        
        @if($errorMessage)
            <div class="bg-red-50 text-red-600 border border-red-200 p-4 rounded-lg mb-6 text-sm">
                {{ $errorMessage }}
            </div>
        @endif

        @if($isSubmitted)
            <div class="bg-success text-white p-6 rounded-lg text-center shadow-sm">
                <svg class="w-16 h-16 mx-auto mb-4 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <h2 class="text-2xl font-semibold mb-2 tracking-tight">Registration Successful!</h2>
                <p class="text-lg">Your team has been submitted and is currently pending admin approval. Your owner account has been created successfully.</p>
                <a href="{{ route('home') }}" class="inline-block mt-6 px-6 py-2 bg-white text-success font-bold rounded hover:bg-gray-100 transition">Back to Home</a>
            </div>
        @else
            <form wire:submit.prevent="register">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Team Info -->
                    <div class="md:col-span-2 mt-2">
                        <h3 class="text-xl text-brand font-semibold mb-4 border-b border-line pb-2 tracking-tight">Team Details</h3>
                    </div>

                    <div>
                        <label class="block text-ink-soft text-sm font-bold mb-2">Team Name <span class="text-red-600">*</span></label>
                        <input type="text" wire:model="team_name" class="w-full bg-canvas border border-line rounded py-3 px-4 text-ink focus:outline-none focus:border-brand transition" placeholder="e.g. Chennai Super Kings">
                        @error('team_name') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-ink-soft text-sm font-bold mb-2">Short Name <span class="text-red-600">*</span></label>
                        <input type="text" wire:model="short_name" class="w-full bg-canvas border border-line rounded py-3 px-4 text-ink focus:outline-none focus:border-brand transition" placeholder="e.g. CSK">
                        @error('short_name') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-ink-soft text-sm font-bold mb-2">Primary Color</label>
                        <div class="flex items-center space-x-2">
                            <input type="color" wire:model="primary_color" class="h-12 w-16 bg-canvas border border-line rounded cursor-pointer">
                            <input type="text" wire:model="primary_color" class="w-full bg-canvas border border-line rounded py-3 px-4 text-ink focus:outline-none focus:border-brand transition" placeholder="#FFC800">
                        </div>
                        @error('primary_color') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Logo Upload -->
                    <div class="md:col-span-2">
                        <label class="block text-ink-soft text-sm font-bold mb-2">Team Logo <span class="text-red-600">*</span></label>
                        <div class="flex items-center space-x-6">
                            <div class="flex-shrink-0 h-24 w-24 rounded-full border-2 border-dashed border-line-strong flex items-center justify-center overflow-hidden bg-canvas">
                                @if ($logo)
                                    <img src="{{ $logo->temporaryUrl() }}" class="h-full w-full object-cover">
                                @else
                                    <svg class="h-8 w-8 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                @endif
                            </div>
                            <div class="flex-1">
                                <input type="file" wire:model="logo" class="w-full bg-canvas border border-line rounded py-2 px-3 text-ink focus:outline-none focus:border-brand transition file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-brand file:text-white hover:file:bg-brand-hover">
                                <p class="text-xs text-muted mt-2">JPG, PNG or GIF up to 2MB. 1:1 aspect ratio recommended.</p>
                                @error('logo') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Owner Info -->
                    <div class="md:col-span-2 mt-6">
                        <h3 class="text-xl text-brand font-semibold mb-4 border-b border-line pb-2 tracking-tight">Owner Details (Account Creation)</h3>
                        <p class="text-sm text-muted mb-4">An account will be created for you to manage your team once approved.</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-ink-soft text-sm font-bold mb-2">Owner Full Name <span class="text-red-600">*</span></label>
                        <input type="text" wire:model="owner_name" class="w-full bg-canvas border border-line rounded py-3 px-4 text-ink focus:outline-none focus:border-brand transition" placeholder="e.g. John Doe">
                        @error('owner_name') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-ink-soft text-sm font-bold mb-2">Owner Email <span class="text-red-600">*</span></label>
                        <input type="email" wire:model="owner_email" class="w-full bg-canvas border border-line rounded py-3 px-4 text-ink focus:outline-none focus:border-brand transition" placeholder="e.g. owner@example.com">
                        @error('owner_email') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-ink-soft text-sm font-bold mb-2">Password <span class="text-red-600">*</span></label>
                        <input type="password" wire:model="owner_password" class="w-full bg-canvas border border-line rounded py-3 px-4 text-ink focus:outline-none focus:border-brand transition" placeholder="Minimum 8 characters">
                        @error('owner_password') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>

                </div>

                <div class="mt-8 pt-6 border-t border-line flex justify-end">
                    <a href="{{ route('home') }}" class="bg-surface-subtle hover:bg-slate-200 text-ink px-6 py-3 rounded-lg mr-4 transition font-semibold">Cancel</a>
                    <button type="submit" class="bg-brand hover:bg-brand-hover text-white px-8 py-3 rounded-lg font-medium text-lg shadow-sm transition flex items-center">
                        <span wire:loading.remove wire:target="register">Register Team</span>
                        <span wire:loading wire:target="register">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
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
