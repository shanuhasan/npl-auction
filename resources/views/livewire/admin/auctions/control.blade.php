<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-brand leading-tight">
                Live Control: {{ $auction->title }}
            </h2>
            <span class="text-muted font-bold uppercase tracking-widest">{{ $auction->status }}</span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex flex-col lg:flex-row gap-6">
            
            <!-- LEFT COLUMN: Controls -->
            <div class="flex-1 space-y-6">
                
                <!-- Error / Success Messages -->
                @if (session()->has('error'))
                    <div class="alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
                @if (session()->has('success'))
                    <div class="alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Auction Status / Actions Panel -->
                <div class="bg-surface shadow-sm border border-line sm:rounded-lg p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-ink-soft mb-1">Auction Status</h3>
                        <p class="text-sm text-muted">Control the flow of your auction.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-4">
                        @if($auction->status === 'live')
                            <span class="flex h-3 w-3 relative">
                              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                              <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                            </span>
                            <span class="text-red-600 font-bold uppercase tracking-widest text-lg">LIVE</span>
                            <button wire:click="pauseAuction" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium text-sm shadow-sm inline-flex items-center justify-center gap-2">Pause</button>
                            <button wire:click="completeAuction" wire:confirm="Are you sure you want to permanently complete this auction?" class="px-4 py-2 bg-slate-200 hover:bg-gray-500 text-ink rounded font-bold text-sm uppercase shadow-sm">Complete</button>
                        @elseif($auction->status === 'paused')
                            <span class="text-amber-600 font-bold uppercase tracking-widest text-lg">PAUSED</span>
                            <button wire:click="resumeAuction" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium text-sm shadow-sm inline-flex items-center justify-center gap-2">Resume</button>
                            <button wire:click="completeAuction" wire:confirm="Are you sure you want to permanently complete this auction?" class="px-4 py-2 bg-slate-200 hover:bg-gray-500 text-ink rounded font-bold text-sm uppercase shadow-sm">Complete</button>
                        @elseif($auction->status === 'upcoming')
                            <span class="text-muted font-bold uppercase tracking-widest text-lg">UPCOMING</span>
                            <button wire:click="startAuction" type="button" class="px-6 py-3 bg-brand hover:bg-brand-hover text-white rounded-lg font-medium text-sm shadow-sm cursor-pointer relative z-50 inline-flex items-center justify-center gap-2">Start Auction</button>
                        @else
                            <span class="text-muted font-bold uppercase tracking-widest text-lg">{{ $auction->status }}</span>
                        @endif
                    </div>
                </div>

                <!-- Current Player Panel -->
                <div class="bg-surface shadow-sm border border-line sm:rounded-lg p-6">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-4">
                        <h3 class="text-lg font-bold text-ink-soft">Current Player</h3>
                        <div class="flex flex-col sm:flex-row items-center gap-4">
                            <!-- Manual Bid Increment -->
                            <div class="bg-surface px-4 py-2 rounded-lg border border-line flex items-center gap-2">
                                <span class="text-ink-soft font-semibold text-sm">Bid +</span>
                                <input type="number" wire:model.defer="manualBidIncrement" class="w-24 bg-surface-subtle border-line-strong text-ink text-sm rounded-md py-1" placeholder="Auto">
                                <button wire:click="updateManualIncrement" class="px-2 py-1 bg-brand text-white hover:bg-brand-hover rounded-lg text-xs font-medium transition inline-flex items-center justify-center gap-2">SET</button>
                            </div>
                            
                            <!-- Auto Sold -->
                            <div class="bg-surface px-4 py-2 rounded-lg border border-line flex items-center gap-3">
                                <span class="text-ink-soft font-semibold text-sm">Auto-Sold:</span>
                                <button wire:click="toggleAutoSold" type="button" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors {{ $auto_sold ? 'bg-green-500' : 'bg-slate-200' }}">
                                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $auto_sold ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                </button>
                                <span class="text-xs {{ $auto_sold ? 'text-green-600' : 'text-muted' }} font-bold w-8">{{ $auto_sold ? 'ON' : 'OFF' }}</span>
                            </div>
                        </div>
                    </div>
                    
                    @if($currentPlayer)
                        <div class="flex items-center gap-6 mb-6">
                            <img src="{{ $currentPlayer['player']['photo'] ? Storage::url($currentPlayer['player']['photo']) : 'https://ui-avatars.com/api/?name='.urlencode($currentPlayer['player']['name']).'&background=random' }}" 
                                 class="w-32 h-32 rounded-lg object-cover border-2 border-line">
                            <div>
                                <h2 class="text-xl font-semibold text-ink">{{ $currentPlayer['player']['name'] }}</h2>
                                <p class="text-muted text-lg">{{ $currentPlayer['player']['role'] }} | {{ $currentPlayer['player']['country'] }}</p>
                                <p class="text-brand font-bold mt-2 text-xl">Base: ₹{{ number_format($currentPlayer['player']['base_price']) }}</p>
                            </div>
                        </div>

                        <!-- Huge Control Buttons -->
                        @if($currentPlayer['status'] === 'current')
                            <div class="grid grid-cols-2 gap-4">
                                <button wire:click="markSold" wire:confirm="Are you sure you want to mark this player as SOLD to the highest bidder?" class="py-6 bg-success hover:bg-green-600 text-white text-2xl font-medium rounded-lg shadow-sm transition transform hover:scale-105 disabled:opacity-50 inline-flex items-center justify-center gap-2" @if(!($state && $state->current_highest_bid > 0)) disabled @endif>
                                    SOLD
                                </button>
                                <button wire:click="markUnsold" wire:confirm="Are you sure you want to mark this player as UNSOLD?" class="py-6 bg-slate-200 hover:bg-slate-200 text-ink text-2xl font-semibold uppercase rounded-xl shadow-sm transition transform hover:scale-105">
                                    UNSOLD
                                </button>
                            </div>
                        @else
                            <div class="p-6 text-center rounded-xl font-semibold text-2xl uppercase tracking-widest text-ink {{ $currentPlayer['status'] === 'sold' ? 'bg-success' : 'bg-surface-subtle' }}">
                                PLAYER {{ $currentPlayer['status'] }}
                            </div>
                        @endif

                    @else
                        <div class="text-center py-10 text-muted">
                            No player currently on auction.
                        </div>
                    @endif

                    <div class="mt-6 border-t border-line pt-6">
                        <button wire:click="nextPlayer" wire:loading.attr="disabled" class="w-full py-4 bg-brand hover:bg-brand-hover text-white text-xl font-medium rounded-lg transition">
                            <span wire:loading.remove>Call Next Player</span>
                            <span wire:loading>Processing...</span>
                        </button>
                    </div>
                </div>

                <!-- Manual Override Panel -->
                @if($currentPlayer && $currentPlayer['status'] === 'current')
                <div class="bg-surface shadow-sm border border-line sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold text-red-600 mb-4">Manual Price Override</h3>
                    <p class="text-sm text-muted mb-4">Use this only if a bid was missed or requires manual correction.</p>
                    <div class="flex gap-4 items-end">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-ink-soft mb-1">Select Team</label>
                            <x-select2 id="overrideTeamId" wire:model="overrideTeamId" placeholder="-- Choose Team --">
                                @foreach($teams as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }} (Budget: ₹{{ $t->remaining_budget }})</option>
                                @endforeach
                            </x-select2>
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-ink-soft mb-1">Amount (₹)</label>
                            <input type="number" wire:model="overrideAmount" class="w-full bg-canvas border border-line rounded-md text-ink px-3 py-2" placeholder="e.g. 500">
                        </div>
                        <div>
                            <button wire:click="overrideBid" wire:confirm="Are you sure you want to manually override the highest bid?" class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg text-sm inline-flex items-center justify-center gap-2">
                                Override
                            </button>
                        </div>
                    </div>
                    @error('overrideTeamId') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    @error('overrideAmount') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                </div>
                @endif
            </div>

            <!-- RIGHT COLUMN: Stats & History -->
            <div class="w-full lg:w-96 space-y-6">
                <!-- Live Stats -->
                <div class="bg-surface shadow-sm border border-line sm:rounded-lg p-6">
                    <div class="grid grid-cols-3 gap-4 text-center mb-4">
                        <div>
                            <p class="text-xs text-muted uppercase">Pending</p>
                            <p class="text-2xl font-semibold text-ink">{{ $pendingCount }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-muted uppercase">Sold</p>
                            <p class="text-2xl font-semibold text-green-600">{{ $soldCount }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-muted uppercase">Unsold</p>
                            <p class="text-2xl font-semibold text-muted">{{ $unsoldCount }}</p>
                        </div>
                    </div>
                    
                    @if($unsoldCount > 0)
                    <div class="border-t border-line pt-4 mt-2">
                        <button wire:click="recallUnsold" wire:confirm="Are you sure you want to bring {{ $unsoldCount }} unsold players back into the auction?" class="w-full py-2 bg-red-50 hover:bg-red-600 text-red-600 hover:text-white border border-red-600 rounded font-bold text-sm uppercase shadow-sm transition">
                            Recall Unsold Players
                        </button>
                    </div>
                    @endif
                </div>

                <!-- Live Bid History -->
                <div class="bg-surface shadow-sm border border-line sm:rounded-lg flex flex-col h-[500px]">
                    <div class="p-4 border-b border-line bg-surface rounded-t-lg">
                        <h3 class="font-bold text-brand">Live Bid History</h3>
                    </div>
                    <div class="flex-1 overflow-y-auto p-4 space-y-3">
                        @forelse($recentBids as $bid)
                            <div class="bg-canvas border border-line p-3 rounded-lg flex justify-between items-center animate-in slide-in-from-right-4 duration-300">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $bid['team']['logo'] ? Storage::url($bid['team']['logo']) : 'https://ui-avatars.com/api/?name='.urlencode($bid['team']['name']).'&background=random' }}" class="w-8 h-8 rounded-full">
                                    <div>
                                        <p class="font-bold text-sm text-ink">{{ $bid['team']['short_name'] }}</p>
                                        <p class="text-xs text-muted">{{ \Carbon\Carbon::parse($bid['created_at'])->format('H:i:s') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="font-semibold text-brand text-lg">
                                        ₹{{ number_format($bid['bid_amount']) }}
                                    </div>
                                    @if($currentPlayer && $currentPlayer['status'] === 'current')
                                        <button wire:click="sellToBid({{ $bid['id'] }})" wire:confirm="Sell player to {{ $bid['team']['short_name'] }} for ₹{{ number_format($bid['bid_amount']) }}?" class="bg-success hover:bg-green-600 text-white text-xs font-medium px-2 py-1 rounded-lg shadow-sm inline-flex items-center justify-center gap-2">
                                            Sold
                                        </button>
                                        <button wire:click="deleteBid({{ $bid['id'] }})" wire:confirm="Are you sure you want to delete this bid?" class="bg-red-600 hover:bg-red-700 text-white text-xs font-medium px-2 py-1 rounded-lg shadow-sm ml-2 inline-flex items-center justify-center gap-2" title="Delete Bid">
                                            <svg class="w-3 h-3 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-10 text-sm">
                                No bids yet.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Players List -->
                <div class="bg-surface shadow-sm border border-line sm:rounded-lg flex flex-col h-[400px]">
                    <div class="p-4 border-b border-line bg-surface rounded-t-lg flex justify-between items-center">
                        <!-- <h3 class="font-bold text-brand">Players List</h3> -->
                        <div class="flex gap-2">
                            <button wire:click="openAddPlayerModal" class="text-xs font-medium px-3 py-1.5 rounded-lg flex items-center gap-1 transition bg-surface text-ink border border-line-strong hover:bg-surface-subtle shadow-sm" title="Add Missed Player to Auction">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                + Add Player
                            </button>
                            <button wire:click="shufflePendingPlayers" class="text-xs font-medium px-3 py-1.5 rounded-lg flex items-center gap-2 transition bg-surface text-ink border border-line-strong hover:bg-surface-subtle shadow-sm" title="Shuffle Pending Players">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                Shuffle
                            </button>
                            <button wire:click="exportResults" class="text-xs font-medium text-white bg-green-600 hover:bg-green-700 px-3 py-1.5 rounded-lg flex items-center gap-2 transition" title="Download Non-Sold Players">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                Export
                            </button>
                        </div>
                    </div>
                    <div class="flex-1 overflow-y-auto p-4 space-y-2">
                        @forelse($playersList as $ap)
                            <div class="flex justify-between items-center bg-canvas border border-line p-2 rounded">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-surface-subtle rounded-full flex items-center justify-center text-xs font-bold text-muted">
                                        {{ $ap->order_no }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-sm text-ink">{{ $ap->player->name }}</p>
                                        <p class="text-xs text-muted">₹{{ number_format($ap->player->base_price) }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    @if($ap->status === 'pending')
                                        <span class="text-xs font-bold text-muted bg-surface-subtle px-2 py-1 rounded uppercase tracking-wider">Pending</span>
                                    @elseif($ap->status === 'current')
                                        <span class="text-xs font-bold text-white bg-blue-600 px-2 py-1 rounded animate-pulse uppercase tracking-wider">Current</span>
                                    @elseif($ap->status === 'sold')
                                        <div class="flex items-center justify-end gap-3">
                                            <div class="text-right">
                                                <span class="text-xs font-bold text-white bg-green-600 px-2 py-1 rounded uppercase tracking-wider">Sold</span>
                                                <p class="text-[10px] text-brand font-bold mt-1">{{ $ap->soldToTeam->short_name ?? '' }} (₹{{ number_format($ap->final_price) }})</p>
                                            </div>
                                            <button wire:click="revertPlayer({{ $ap->id }})" wire:confirm="Are you sure you want to revert this player to pending? This will refund the team and delete all previous bids for this player." class="p-1 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded transition" title="Revert to Pending">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                            </button>
                                        </div>
                                    @elseif($ap->status === 'unsold')
                                        <div class="flex items-center justify-end gap-3">
                                            <span class="text-xs font-bold text-ink bg-slate-200 px-2 py-1 rounded uppercase tracking-wider">Unsold</span>
                                            <button wire:click="revertPlayer({{ $ap->id }})" wire:confirm="Are you sure you want to revert this player to pending?" class="p-1 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded transition" title="Revert to Pending">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-10 text-sm">
                                No players assigned.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Team PDFs -->
                <div class="bg-surface shadow-sm border border-line sm:rounded-lg flex flex-col">
                    <div class="p-4 border-b border-line bg-surface rounded-t-lg">
                        <h3 class="font-bold text-brand">Download Team Squads (PDF)</h3>
                    </div>
                    <div class="p-4 space-y-2 max-h-48 overflow-y-auto custom-scrollbar">
                        @foreach($auction->teams as $team)
                            <div class="flex justify-between items-center bg-canvas p-2 rounded border border-line">
                                <span class="text-ink font-bold text-sm">{{ $team->name }}</span>
                                <a href="{{ route('teams.pdf', ['team' => $team->guid, 'auction_id' => $auction->id]) }}" target="_blank" class="text-xs bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded transition flex items-center gap-1" title="Download {{ $team->short_name }} PDF">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    PDF
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- Add Missed Player Modal -->
    @if($isAddPlayerModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm overflow-y-auto p-4 animate-in fade-in duration-200">
        <div class="bg-surface rounded-xl w-full max-w-2xl border border-line shadow-pop overflow-hidden my-8">
            <!-- Modal Header -->
            <div class="bg-surface px-6 py-4 border-b border-line flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    <div>
                        <h3 class="text-lg font-bold text-brand">Add Missed Player</h3>
                        <p class="text-xs text-muted">Include a missed player directly into this auction</p>
                    </div>
                </div>
                <button wire:click="closeAddPlayerModal" class="text-muted hover:text-ink text-2xl font-bold transition">&times;</button>
            </div>

            <!-- Modal Tabs -->
            <div class="flex border-b border-line bg-canvas">
                <button type="button" wire:click="$set('addPlayerTab', 'existing')" class="flex-1 py-3 px-4 text-center text-xs sm:text-sm font-bold uppercase tracking-wider border-b-2 transition {{ $addPlayerTab === 'existing' ? 'border-brand text-brand bg-surface-muted' : 'border-transparent text-muted hover:text-ink' }}">
                    Select System Player
                </button>
                <button type="button" wire:click="$set('addPlayerTab', 'new')" class="flex-1 py-3 px-4 text-center text-xs sm:text-sm font-bold uppercase tracking-wider border-b-2 transition {{ $addPlayerTab === 'new' ? 'border-brand text-brand bg-surface-muted' : 'border-transparent text-muted hover:text-ink' }}">
                    + Quick Create New Player
                </button>
            </div>

            <div class="p-6">
                <!-- Auction Priority Selection -->
                <div class="mb-5 bg-surface p-3 rounded-lg border border-line flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <span class="text-xs font-bold text-ink-soft uppercase tracking-wider">Auction Priority:</span>
                    <div class="flex gap-4">
                        <label class="inline-flex items-center text-xs font-bold text-ink-soft cursor-pointer">
                            <input type="radio" wire:model="addPosition" value="next" class="text-brand focus:ring-brand">
                            <span class="ml-2 text-green-600 uppercase tracking-wider">Call Next (Top of Queue)</span>
                        </label>
                        <label class="inline-flex items-center text-xs font-bold text-ink-soft cursor-pointer">
                            <input type="radio" wire:model="addPosition" value="end" class="text-brand focus:ring-brand">
                            <span class="ml-2 uppercase tracking-wider">Add to End of Queue</span>
                        </label>
                    </div>
                </div>

                @if($addPlayerTab === 'existing')
                    <!-- TAB 1: Select Existing Missed Player -->
                    <form wire:submit.prevent="addExistingPlayer" class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-ink-soft uppercase mb-1">Search Available Player</label>
                            <input type="text" wire:model.live.debounce.250ms="searchMissedPlayer" placeholder="Type player name, role, category..." class="w-full bg-canvas border border-line rounded-lg px-3 py-2 text-ink text-sm focus:border-brand">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-ink-soft uppercase mb-1">Select Player from Database</label>
                            <select wire:model="selectedMissedPlayerId" class="w-full bg-canvas border border-line rounded-lg px-3 py-2 text-ink text-sm focus:border-brand custom-scrollbar" size="6">
                                @forelse($missedPlayers as $mp)
                                    <option value="{{ $mp->id }}" class="p-2 border-b border-line hover:bg-surface-muted">
                                        {{ $mp->name }} — {{ ucfirst($mp->role) }} | {{ strtoupper($mp->category) }} | ₹{{ number_format($mp->base_price) }}
                                    </option>
                                @empty
                                    <option value="" disabled class="p-4 text-center text-muted">
                                        No available missed players found in system.
                                    </option>
                                @endforelse
                            </select>
                            @error('selectedMissedPlayerId') <span class="text-red-600 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t border-line">
                            <button type="button" wire:click="closeAddPlayerModal" class="px-4 py-2 bg-surface-subtle hover:bg-slate-200 text-ink font-bold rounded-lg text-xs uppercase tracking-wider">Cancel</button>
                            <button type="submit" class="px-6 py-2 bg-brand hover:bg-brand-hover text-white font-medium rounded-lg text-xs shadow-sm inline-flex items-center justify-center gap-2">Add Selected Player</button>
                        </div>
                    </form>
                @else
                    <!-- TAB 2: Quick Create New Player -->
                    <form wire:submit.prevent="quickCreatePlayer" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-ink-soft uppercase mb-1">Player Name *</label>
                                <input type="text" wire:model="new_name" required placeholder="e.g. Rahul Sharma" class="w-full bg-canvas border border-line rounded-lg px-3 py-2 text-ink text-sm">
                                @error('new_name') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-ink-soft uppercase mb-1">Role *</label>
                                <select wire:model="new_role" required class="w-full bg-canvas border border-line rounded-lg px-3 py-2 text-ink text-sm">
                                    <option value="batsman">Batsman</option>
                                    <option value="bowler">Bowler</option>
                                    <option value="all-rounder">All-Rounder</option>
                                    <option value="wicketkeeper">Wicketkeeper</option>
                                </select>
                                @error('new_role') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-ink-soft uppercase mb-1">Base Price (₹) *</label>
                                <input type="number" wire:model="new_base_price" required min="0" class="w-full bg-canvas border border-line rounded-lg px-3 py-2 text-ink text-sm">
                                @error('new_base_price') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-ink-soft uppercase mb-1">Category *</label>
                                <select wire:model="new_category" required class="w-full bg-canvas border border-line rounded-lg px-3 py-2 text-ink text-sm">
                                    <option value="marquee">Marquee</option>
                                    <option value="set-a">Set A</option>
                                    <option value="set-b">Set B</option>
                                    <option value="set-c">Set C</option>
                                </select>
                                @error('new_category') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-ink-soft uppercase mb-1">Contact No (Optional)</label>
                                <input type="text" wire:model="new_contact_no" placeholder="10-digit number" class="w-full bg-canvas border border-line rounded-lg px-3 py-2 text-ink text-sm">
                                @error('new_contact_no') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-ink-soft uppercase mb-1">City / Village (Optional)</label>
                                <input type="text" wire:model="new_city" placeholder="e.g. Mumbai" class="w-full bg-canvas border border-line rounded-lg px-3 py-2 text-ink text-sm">
                                @error('new_city') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-ink-soft uppercase mb-1">Player Photo (Optional)</label>
                                <input type="file" wire:model="new_photo" class="w-full bg-canvas border border-line rounded-lg px-3 py-2 text-ink text-sm">
                                @error('new_photo') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                                @if($new_photo)
                                    <img src="{{ $new_photo->temporaryUrl() }}" class="mt-2 w-14 h-14 object-cover rounded-full border border-line">
                                @endif
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t border-line">
                            <button type="button" wire:click="closeAddPlayerModal" class="px-4 py-2 bg-surface-subtle hover:bg-slate-200 text-ink font-bold rounded-lg text-xs uppercase tracking-wider">Cancel</button>
                            <button type="submit" class="px-6 py-2 bg-brand hover:bg-brand-hover text-white font-medium rounded-lg text-xs shadow-sm inline-flex items-center justify-center gap-2">Create & Add Player</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>
