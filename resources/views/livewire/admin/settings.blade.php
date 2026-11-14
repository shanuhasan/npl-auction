<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ink leading-tight">
            {{ __('Application Settings') }}
        </h2>
    </x-slot>

    <div>
        <div class="space-y-6">
            <div class="bg-surface shadow-sm border border-line rounded-lg overflow-hidden">
                <div class="p-6 text-ink">
                    
                    <h3 class="text-2xl font-bold text-brand mb-6 border-b border-line pb-2">Global Settings</h3>
                    
                    @if (session()->has('success'))
                        <div class="bg-green-50 border border-green-500 text-green-600 p-4 rounded-lg font-bold mb-6">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form wire:submit.prevent="save" class="space-y-6">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- App Name -->
                            <div>
                                <label for="app_name" class="block text-sm font-medium text-muted mb-1">Application Name</label>
                                <input type="text" id="app_name" wire:model="app_name" class="w-full bg-surface-muted border border-line rounded-lg p-3 text-ink focus:ring-brand focus:border-brand transition" placeholder="e.g. Naugawan Premier League">
                                @error('app_name') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Season -->
                            <div>
                                <label for="season" class="block text-sm font-medium text-muted mb-1">Season</label>
                                <input type="text" id="season" wire:model="season" class="w-full bg-surface-muted border border-line rounded-lg p-3 text-ink focus:ring-brand focus:border-brand transition" placeholder="e.g. Season 2026">
                                @error('season') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Logo File -->
                            <div>
                                <label for="new_logo" class="block text-sm font-medium text-muted mb-1">Upload Logo</label>
                                @if($logo)
                                    <div class="mb-3 p-2 bg-surface-subtle rounded-lg inline-block">
                                        <img src="{{ asset('storage/' . $logo) }}" alt="Current Logo" class="h-12 object-contain">
                                    </div>
                                @endif
                                <input type="file" id="new_logo" wire:model="new_logo" class="w-full bg-surface-muted border border-line rounded-lg p-2 text-ink focus:ring-brand focus:border-brand transition file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-brand file:text-white hover:file:bg-brand-hover">
                                @error('new_logo') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Favicon File -->
                            <div>
                                <label for="new_favicon" class="block text-sm font-medium text-muted mb-1">Upload Favicon (Icon)</label>
                                @if($favicon)
                                    <div class="mb-3 p-2 bg-surface-subtle rounded-lg inline-block">
                                        <img src="{{ asset('storage/' . $favicon) }}" alt="Current Favicon" class="h-8 w-8 object-contain">
                                    </div>
                                @endif
                                <input type="file" id="new_favicon" wire:model="new_favicon" class="w-full bg-surface-muted border border-line rounded-lg p-2 text-ink focus:ring-brand focus:border-brand transition file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-brand file:text-white hover:file:bg-brand-hover">
                                <p class="text-xs text-muted mt-1">Recommended format: .ico, .png (square)</p>
                                @error('new_favicon') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Contact Email -->
                            <div>
                                <label for="contact_email" class="block text-sm font-medium text-muted mb-1">Contact Email</label>
                                <input type="email" id="contact_email" wire:model="contact_email" class="w-full bg-surface-muted border border-line rounded-lg p-3 text-ink focus:ring-brand focus:border-brand transition" placeholder="info@example.com">
                                @error('contact_email') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Contact Phone -->
                            <div>
                                <label for="contact_phone" class="block text-sm font-medium text-muted mb-1">Contact Phone</label>
                                <input type="text" id="contact_phone" wire:model="contact_phone" class="w-full bg-surface-muted border border-line rounded-lg p-3 text-ink focus:ring-brand focus:border-brand transition" placeholder="+91 9876543210">
                                @error('contact_phone') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Registration Fee -->
                            <div>
                                <label for="registration_fee" class="block text-sm font-medium text-muted mb-1">Player Registration Fee (Rs.)</label>
                                <input type="number" id="registration_fee" wire:model="registration_fee" class="w-full bg-surface-muted border border-line rounded-lg p-3 text-ink focus:ring-brand focus:border-brand transition" placeholder="1500">
                                @error('registration_fee') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Player Base Price -->
                            <div>
                                <label for="player_base_price" class="block text-sm font-medium text-muted mb-1">Player Base Price (Rs.)</label>
                                <input type="number" id="player_base_price" wire:model="player_base_price" class="w-full bg-surface-muted border border-line rounded-lg p-3 text-ink focus:ring-brand focus:border-brand transition" placeholder="1000">
                                @error('player_base_price') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Active Payment Gateway -->
                            <div>
                                <label for="active_payment_gateway" class="block text-sm font-medium text-muted mb-1">Active Payment Gateway</label>
                                <x-select2 id="active_payment_gateway" wire:model="active_payment_gateway" placeholder="Select Gateway">
                                    <option value="none">None (Direct Registration)</option>
                                    <option value="mock">Mock Gateway (Testing)</option>
                                    <option value="razorpay">Razorpay (Live/Test)</option>
                                </x-select2>
                                @error('active_payment_gateway') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6">
                            <!-- Our Mission -->
                            <div>
                                <label for="our_mission" class="block text-sm font-medium text-muted mb-1">Our Mission</label>
                                <textarea id="our_mission" wire:model="our_mission" rows="4" class="w-full bg-surface-muted border border-line rounded-lg p-3 text-ink focus:ring-brand focus:border-brand transition" placeholder="Enter mission statement..."></textarea>
                                @error('our_mission') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Our Vision -->
                            <div>
                                <label for="our_vision" class="block text-sm font-medium text-muted mb-1">Our Vision</label>
                                <textarea id="our_vision" wire:model="our_vision" rows="4" class="w-full bg-surface-muted border border-line rounded-lg p-3 text-ink focus:ring-brand focus:border-brand transition" placeholder="Enter vision statement..."></textarea>
                                @error('our_vision') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Facebook -->
                            <div>
                                <label for="facebook" class="block text-sm font-medium text-muted mb-1">Facebook URL</label>
                                <input type="url" id="facebook" wire:model="facebook" class="w-full bg-surface-muted border border-line rounded-lg p-3 text-ink focus:ring-brand focus:border-brand transition" placeholder="https://facebook.com/npl">
                                @error('facebook') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Instagram -->
                            <div>
                                <label for="instagram" class="block text-sm font-medium text-muted mb-1">Instagram URL</label>
                                <input type="url" id="instagram" wire:model="instagram" class="w-full bg-surface-muted border border-line rounded-lg p-3 text-ink focus:ring-brand focus:border-brand transition" placeholder="https://instagram.com/npl">
                                @error('instagram') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Twitter -->
                            <div>
                                <label for="twitter" class="block text-sm font-medium text-muted mb-1">Twitter / X URL</label>
                                <input type="url" id="twitter" wire:model="twitter" class="w-full bg-surface-muted border border-line rounded-lg p-3 text-ink focus:ring-brand focus:border-brand transition" placeholder="https://twitter.com/npl">
                                @error('twitter') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- YouTube -->
                            <div>
                                <label for="youtube" class="block text-sm font-medium text-muted mb-1">YouTube Channel URL</label>
                                <input type="url" id="youtube" wire:model="youtube" class="w-full bg-surface-muted border border-line rounded-lg p-3 text-ink focus:ring-brand focus:border-brand transition" placeholder="https://youtube.com/c/npl">
                                @error('youtube') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <!-- Google AdSense Code -->
                            <div class="md:col-span-2">
                                <label for="adsense_code" class="block text-sm font-medium text-muted mb-1">Google AdSense Global Code</label>
                                <textarea id="adsense_code" wire:model="adsense_code" rows="4" class="w-full bg-surface-muted border border-line rounded-lg p-3 text-ink focus:ring-brand focus:border-brand transition font-mono text-sm" placeholder="<script async src='https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-XXXXXXXXXX' crossorigin='anonymous'></script>"></textarea>
                                <p class="text-xs text-muted mt-1">Paste your Google AdSense code here. It will be added to the &lt;head&gt; of all public pages.</p>
                                @error('adsense_code') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <!-- Developer Name -->
                            <div>
                                <label for="developer_name" class="block text-sm font-medium text-muted mb-1">Developer Name</label>
                                <input type="text" id="developer_name" wire:model="developer_name" class="w-full bg-surface-muted border border-line rounded-lg p-3 text-ink focus:ring-brand focus:border-brand transition" placeholder="e.g. Insphire">
                                @error('developer_name') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Developer URL -->
                            <div>
                                <label for="developer_url" class="block text-sm font-medium text-muted mb-1">Developer URL</label>
                                <input type="text" id="developer_url" wire:model="developer_url" class="w-full bg-surface-muted border border-line rounded-lg p-3 text-ink focus:ring-brand focus:border-brand transition" placeholder="e.g. https://insphire.in">
                                @error('developer_url') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="pt-4 border-t border-line flex justify-end">
                            <button type="submit" class="px-8 py-3 bg-brand hover:bg-brand-hover text-white rounded-lg shadow-sm text-sm transition flex items-center gap-2 font-medium">
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
