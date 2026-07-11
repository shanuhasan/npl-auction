<div>
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-poppins font-bold text-white">Create Auction Setup</h1>
        <a href="{{ route('admin.auctions') }}" class="text-gray-400 hover:text-white transition" wire:navigate>&larr; Back to Auctions</a>
    </div>

    <form wire:submit.prevent="save" class="space-y-8">
        
        <!-- Section 1: Basic Details -->
        <div class="bg-card-bg rounded-lg shadow p-6 border border-gray-800">
            <h2 class="text-xl font-poppins font-bold text-accent-gold mb-4 border-b border-gray-700 pb-2">1. Basic Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-300 text-sm font-bold mb-2">Auction Title</label>
                    <input type="text" wire:model="title" class="w-full bg-primary-bg border border-gray-700 rounded py-2 px-3 text-white focus:outline-none focus:border-accent-gold" required placeholder="e.g. NPL Mega Auction 2026">
                    @error('title') <span class="text-accent-red text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-gray-300 text-sm font-bold mb-2">Auction Date & Time</label>
                    <input type="datetime-local" wire:model="auction_date" class="w-full bg-primary-bg border border-gray-700 rounded py-2 px-3 text-white focus:outline-none focus:border-accent-gold" required>
                    @error('auction_date') <span class="text-accent-red text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-gray-300 text-sm font-bold mb-2">Timer Duration (Seconds per Bid)</label>
                    <input type="number" wire:model="timer_seconds" class="w-full md:w-1/2 bg-primary-bg border border-gray-700 rounded py-2 px-3 text-white focus:outline-none focus:border-accent-gold" required min="5">
                    @error('timer_seconds') <span class="text-accent-red text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Section 2: Bid Rules -->
        <div class="bg-card-bg rounded-lg shadow p-6 border border-gray-800">
            <div class="flex justify-between items-center mb-4 border-b border-gray-700 pb-2">
                <h2 class="text-xl font-poppins font-bold text-accent-gold">2. Bid Increment Rules (₹)</h2>
                <button type="button" wire:click="addRule" class="text-sm bg-gray-700 hover:bg-gray-600 text-white px-3 py-1 rounded">Add Rule</button>
            </div>
            <div class="space-y-3">
                @foreach($bid_rules as $index => $rule)
                    <div class="flex items-center space-x-4">
                        <div class="flex-1">
                            <label class="block text-gray-400 text-xs mb-1">Up to Amount (₹)</label>
                            <input type="number" wire:model="bid_rules.{{ $index }}.upto" class="w-full bg-primary-bg border border-gray-700 rounded py-2 px-3 text-white focus:outline-none focus:border-accent-gold" required>
                            @error('bid_rules.'.$index.'.upto') <span class="text-accent-red text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex-1">
                            <label class="block text-gray-400 text-xs mb-1">Increment By (₹)</label>
                            <input type="number" wire:model="bid_rules.{{ $index }}.increment" class="w-full bg-primary-bg border border-gray-700 rounded py-2 px-3 text-white focus:outline-none focus:border-accent-gold" required>
                            @error('bid_rules.'.$index.'.increment') <span class="text-accent-red text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div class="pt-5">
                            <button type="button" wire:click="removeRule({{ $index }})" class="text-accent-red hover:text-red-400 font-bold p-2">&times;</button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Section 3: Players -->
        <div class="bg-card-bg rounded-lg shadow p-6 border border-gray-800">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-2 gap-4">
                <h2 class="text-xl font-poppins font-bold text-accent-gold">3. Select Players</h2>
                <div class="flex gap-2">
                    <button type="button" wire:click="selectAllPlayers" class="text-xs font-bold bg-green-600 hover:bg-green-500 text-white px-4 py-2 rounded shadow uppercase tracking-wider">Select All</button>
                    <button type="button" wire:click="deselectAllPlayers" class="text-xs font-bold bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded shadow uppercase tracking-wider">Deselect All</button>
                </div>
            </div>
            <p class="text-gray-400 text-sm mb-4 border-b border-gray-700 pb-2">Selected players will be automatically ordered for the auction (Marquee first, then Set A, Set B, etc., sorted by Base Price).</p>
            
            @forelse($playersByCategory as $category => $players)
                <div class="mb-6">
                    <h3 class="text-lg font-bold text-white uppercase tracking-wide mb-3 bg-gray-900 px-4 py-2 rounded">{{ str_replace('-', ' ', $category) }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($players as $player)
                            <label class="flex items-center space-x-3 bg-primary-bg p-3 rounded border border-gray-800 cursor-pointer hover:border-gray-600 transition">
                                <input type="checkbox" wire:model="selectedPlayers" value="{{ $player->id }}" class="form-checkbox h-5 w-5 text-accent-gold bg-gray-900 border-gray-700 rounded focus:ring-accent-gold">
                                <div class="flex-1">
                                    <p class="text-white font-medium">{{ $player->name }}</p>
                                    <p class="text-xs text-gray-400">{{ ucfirst($player->role) }} &bull; ₹{{ $player->base_price }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            @empty
                <p class="text-gray-400">No available players found. Please add players first.</p>
            @endforelse
        </div>

        <div class="flex justify-end pt-4 pb-10">
            <button type="submit" class="bg-accent-gold text-primary-bg px-8 py-3 rounded-lg font-bold text-lg hover:bg-yellow-400 transition shadow-lg w-full md:w-auto">Save & Create Auction</button>
        </div>
    </form>
</div>
