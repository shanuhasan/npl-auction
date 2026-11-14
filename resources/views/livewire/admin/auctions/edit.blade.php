<div>
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-ink">Edit Auction Setup</h1>
        <a href="{{ route('admin.auctions') }}" class="text-muted hover:text-ink transition" wire:navigate>&larr; Back to Auctions</a>
    </div>

    <form wire:submit.prevent="save" class="space-y-8">
        
        <!-- Section 1: Basic Details -->
        <div class="bg-surface rounded-lg shadow-sm p-6 border border-line">
            <h2 class="text-xl font-semibold text-brand mb-4 border-b border-line pb-2">1. Basic Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-ink-soft text-sm font-bold mb-2">Auction Title</label>
                    <input type="text" wire:model="title" class="w-full bg-canvas border border-line rounded py-2 px-3 text-ink focus:outline-none focus:border-brand" required placeholder="e.g. NPL Mega Auction 2026">
                    @error('title') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-ink-soft text-sm font-bold mb-2">Auction Date & Time</label>
                    <input type="datetime-local" wire:model="auction_date" class="w-full bg-canvas border border-line rounded py-2 px-3 text-ink focus:outline-none focus:border-brand" required>
                    @error('auction_date') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-ink-soft text-sm font-bold mb-2">Timer Duration (Seconds per Bid)</label>
                    <input type="number" wire:model="timer_seconds" class="w-full md:w-1/2 bg-canvas border border-line rounded py-2 px-3 text-ink focus:outline-none focus:border-brand" required min="5">
                    @error('timer_seconds') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Section 2: Bid Rules -->
        <div class="bg-surface rounded-lg shadow-sm p-6 border border-line">
            <div class="flex justify-between items-center mb-4 border-b border-line pb-2">
                <h2 class="text-xl font-semibold text-brand">2. Bid Increment Rules (₹)</h2>
                <button type="button" wire:click="addRule" class="text-sm bg-surface-subtle hover:bg-slate-200 text-ink px-3 py-1 rounded">Add Rule</button>
            </div>
            <div class="space-y-3">
                @foreach($bid_rules as $index => $rule)
                    <div class="flex items-center space-x-4">
                        <div class="flex-1">
                            <label class="block text-muted text-xs mb-1">Up to Amount (₹)</label>
                            <input type="number" wire:model="bid_rules.{{ $index }}.upto" class="w-full bg-canvas border border-line rounded py-2 px-3 text-ink focus:outline-none focus:border-brand" required>
                            @error('bid_rules.'.$index.'.upto') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex-1">
                            <label class="block text-muted text-xs mb-1">Increment By (₹)</label>
                            <input type="number" wire:model="bid_rules.{{ $index }}.increment" class="w-full bg-canvas border border-line rounded py-2 px-3 text-ink focus:outline-none focus:border-brand" required>
                            @error('bid_rules.'.$index.'.increment') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div class="pt-5">
                            <button type="button" wire:click="removeRule({{ $index }})" class="text-danger hover:text-red-700 font-bold p-2">&times;</button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Section 3: Participating Teams -->
        <div class="bg-surface rounded-lg shadow-sm p-6 border border-line">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-2 gap-4">
                <h2 class="text-xl font-semibold text-brand">3. Select Participating Teams</h2>
            </div>
            <p class="text-muted text-sm mb-4 border-b border-line pb-2">Only the selected teams will be able to enter the bidding room for this auction.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @forelse($teams as $team)
                    <label class="flex items-center space-x-3 bg-canvas p-3 rounded border border-line cursor-pointer hover:border-line-strong transition">
                        <input type="checkbox" wire:model="selectedTeams" value="{{ $team->id }}" class="form-checkbox h-5 w-5 text-brand bg-surface-muted border-line rounded focus:ring-brand">
                        <div class="flex-1 flex items-center space-x-2">
                            @if($team->logo)
                                <img src="{{ Storage::url($team->logo) }}" alt="{{ $team->name }}" class="w-6 h-6 object-cover rounded-full">
                            @else
                                <div class="w-6 h-6 rounded-full bg-surface-subtle flex items-center justify-center text-[10px] text-ink">
                                    {{ substr($team->name, 0, 2) }}
                                </div>
                            @endif
                            <p class="text-ink font-medium text-sm">{{ $team->name }}</p>
                        </div>
                    </label>
                @empty
                    <p class="text-muted">No teams found. Please add teams first.</p>
                @endforelse
            </div>
        </div>

        <!-- Section 4: Players -->
        <div class="bg-surface rounded-lg shadow-sm p-6 border border-line">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-2 gap-4">
                <h2 class="text-xl font-semibold text-brand">4. Select Players</h2>
                <div class="flex gap-2">
                    <button type="button" wire:click="selectAllPlayers" class="text-xs font-medium bg-green-600 hover:bg-green-500 text-white px-4 py-2 rounded-lg shadow-sm inline-flex items-center justify-center gap-2">Select All</button>
                    <button type="button" wire:click="deselectAllPlayers" class="text-xs font-bold bg-surface-subtle hover:bg-slate-200 text-ink px-4 py-2 rounded shadow-sm uppercase tracking-wider">Deselect All</button>
                </div>
            </div>
            <p class="text-muted text-sm mb-4 border-b border-line pb-2">Selected players will be automatically ordered for the auction (Marquee first, then Set A, Set B, etc., sorted by Base Price). Already selected players can be deselected (if they haven't been sold yet).</p>
            
            @forelse($playersByCategory as $category => $players)
                <div class="mb-6">
                    <!-- <h3 class="text-lg font-bold text-ink mb-3 bg-surface-muted px-4 py-2 rounded">{{ str_replace('-', ' ', $category) }}</h3> -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($players as $player)
                            <label class="flex items-center space-x-3 bg-canvas p-3 rounded border border-line cursor-pointer hover:border-line-strong transition">
                                <input type="checkbox" wire:model="selectedPlayers" value="{{ $player->id }}" class="form-checkbox h-5 w-5 text-brand bg-surface-muted border-line rounded focus:ring-brand">
                                <div class="flex-1">
                                    <p class="text-ink font-medium">{{ $player->name }}</p>
                                    <p class="text-xs text-muted">{{ ucfirst($player->role) }} &bull; ₹{{ $player->base_price }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            @empty
                <p class="text-muted">No available players found.</p>
            @endforelse
        </div>

        <div class="flex justify-end pt-4 pb-10">
            <button type="submit" class="bg-brand text-white px-8 py-3 rounded-lg font-medium text-lg hover:bg-brand-hover transition shadow-sm w-full md:w-auto">Update Auction</button>
        </div>
    </form>
</div>
