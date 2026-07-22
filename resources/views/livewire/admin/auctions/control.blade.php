<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-accent-gold leading-tight">
                Live Control: {{ $auction->title }}
            </h2>
            <span class="text-gray-500 font-bold uppercase tracking-widest">{{ $auction->status }}</span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex flex-col lg:flex-row gap-6">
            
            <!-- LEFT COLUMN: Controls -->
            <div class="flex-1 space-y-6">
                
                <!-- Error / Success Messages -->
                @if (session()->has('error'))
                    <div class="bg-red-500 text-white p-4 rounded-lg font-bold">
                        {{ session('error') }}
                    </div>
                @endif
                @if (session()->has('success'))
                    <div class="bg-green-500 text-white p-4 rounded-lg font-bold">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Auction Status / Actions Panel -->
                <div class="bg-card-bg shadow border border-gray-800 sm:rounded-lg p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-300 uppercase tracking-wider mb-1">Auction Status</h3>
                        <p class="text-sm text-gray-500">Control the flow of your auction.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-4">
                        @if($auction->status === 'live')
                            <span class="flex h-3 w-3 relative">
                              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                              <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                            </span>
                            <span class="text-red-500 font-bold uppercase tracking-widest text-lg">LIVE</span>
                            <button wire:click="pauseAuction" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded font-bold text-sm uppercase shadow">Pause</button>
                            <button wire:click="completeAuction" wire:confirm="Are you sure you want to permanently complete this auction?" class="px-4 py-2 bg-gray-600 hover:bg-gray-500 text-white rounded font-bold text-sm uppercase shadow">Complete</button>
                        @elseif($auction->status === 'paused')
                            <span class="text-yellow-500 font-bold uppercase tracking-widest text-lg">PAUSED</span>
                            <button wire:click="resumeAuction" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded font-bold text-sm uppercase shadow">Resume</button>
                            <button wire:click="completeAuction" wire:confirm="Are you sure you want to permanently complete this auction?" class="px-4 py-2 bg-gray-600 hover:bg-gray-500 text-white rounded font-bold text-sm uppercase shadow">Complete</button>
                        @elseif($auction->status === 'upcoming')
                            <span class="text-gray-500 font-bold uppercase tracking-widest text-lg">UPCOMING</span>
                            <button wire:click="startAuction" type="button" class="px-6 py-3 bg-accent-gold hover:bg-yellow-500 text-primary-bg rounded font-black text-sm uppercase shadow cursor-pointer relative z-50">Start Auction</button>
                        @else
                            <span class="text-gray-500 font-bold uppercase tracking-widest text-lg">{{ $auction->status }}</span>
                        @endif
                    </div>
                </div>

                <!-- Current Player Panel -->
                <div class="bg-card-bg shadow border border-gray-800 sm:rounded-lg p-6">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-4">
                        <h3 class="text-lg font-bold text-gray-300 uppercase tracking-wider">Current Player</h3>
                        <div class="flex flex-col sm:flex-row items-center gap-4">
                            <!-- Manual Bid Increment -->
                            <div class="bg-[#141B2D] px-4 py-2 rounded-lg border border-gray-700 flex items-center gap-2">
                                <span class="text-gray-300 font-semibold text-sm">Bid +</span>
                                <input type="number" wire:model.defer="manualBidIncrement" class="w-24 bg-gray-800 border-gray-600 text-white text-sm rounded-md py-1" placeholder="Auto">
                                <button wire:click="updateManualIncrement" class="px-2 py-1 bg-accent-gold text-primary-bg hover:bg-yellow-500 rounded text-xs font-bold transition">SET</button>
                            </div>
                            
                            <!-- Auto Sold -->
                            <div class="bg-[#141B2D] px-4 py-2 rounded-lg border border-gray-700 flex items-center gap-3">
                                <span class="text-gray-300 font-semibold text-sm">Auto-Sold:</span>
                                <button wire:click="toggleAutoSold" type="button" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors {{ $auto_sold ? 'bg-green-500' : 'bg-gray-600' }}">
                                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $auto_sold ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                </button>
                                <span class="text-xs {{ $auto_sold ? 'text-green-400' : 'text-gray-400' }} font-bold w-8">{{ $auto_sold ? 'ON' : 'OFF' }}</span>
                            </div>
                        </div>
                    </div>
                    
                    @if($currentPlayer)
                        <div class="flex items-center gap-6 mb-6">
                            <img src="{{ $currentPlayer['player']['photo'] ? Storage::url($currentPlayer['player']['photo']) : 'https://ui-avatars.com/api/?name='.urlencode($currentPlayer['player']['name']).'&background=random' }}" 
                                 class="w-32 h-32 rounded-lg object-cover border-2 border-gray-700">
                            <div>
                                <h2 class="text-3xl font-black text-white">{{ $currentPlayer['player']['name'] }}</h2>
                                <p class="text-gray-400 text-lg">{{ $currentPlayer['player']['role'] }} | {{ $currentPlayer['player']['country'] }}</p>
                                <p class="text-accent-gold font-bold mt-2 text-xl">Base: ₹{{ number_format($currentPlayer['player']['base_price']) }}</p>
                            </div>
                        </div>

                        <!-- Huge Control Buttons -->
                        @if($currentPlayer['status'] === 'current')
                            <div class="grid grid-cols-2 gap-4">
                                <button wire:click="markSold" wire:confirm="Are you sure you want to mark this player as SOLD to the highest bidder?" class="py-6 bg-[#00C853] hover:bg-green-600 text-white text-2xl font-black uppercase rounded-xl shadow-lg transition transform hover:scale-105 disabled:opacity-50" @if(!($state && $state->current_highest_bid > 0)) disabled @endif>
                                    SOLD
                                </button>
                                <button wire:click="markUnsold" wire:confirm="Are you sure you want to mark this player as UNSOLD?" class="py-6 bg-gray-600 hover:bg-gray-700 text-white text-2xl font-black uppercase rounded-xl shadow-lg transition transform hover:scale-105">
                                    UNSOLD
                                </button>
                            </div>
                        @else
                            <div class="p-6 text-center rounded-xl font-black text-2xl uppercase tracking-widest text-white {{ $currentPlayer['status'] === 'sold' ? 'bg-[#00C853]' : 'bg-gray-700' }}">
                                PLAYER {{ $currentPlayer['status'] }}
                            </div>
                        @endif

                    @else
                        <div class="text-center py-10 text-gray-500">
                            No player currently on auction.
                        </div>
                    @endif

                    <div class="mt-6 border-t border-gray-800 pt-6">
                        <button wire:click="nextPlayer" wire:loading.attr="disabled" class="w-full py-4 bg-accent-gold hover:bg-yellow-500 text-primary-bg text-xl font-black uppercase tracking-widest rounded-xl transition">
                            <span wire:loading.remove>Call Next Player</span>
                            <span wire:loading>Processing...</span>
                        </button>
                    </div>
                </div>

                <!-- Manual Override Panel -->
                @if($currentPlayer && $currentPlayer['status'] === 'current')
                <div class="bg-card-bg shadow border border-gray-800 sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold text-red-500 mb-4 uppercase tracking-wider">Manual Price Override</h3>
                    <p class="text-sm text-gray-400 mb-4">Use this only if a bid was missed or requires manual correction.</p>
                    <div class="flex gap-4 items-end">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-300 mb-1">Select Team</label>
                            <select wire:model="overrideTeamId" class="w-full bg-primary-bg border border-gray-700 rounded-md text-white px-3 py-2">
                                <option value="">-- Choose Team --</option>
                                @foreach($teams as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }} (Budget: ₹{{ $t->remaining_budget }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-300 mb-1">Amount (₹)</label>
                            <input type="number" wire:model="overrideAmount" class="w-full bg-primary-bg border border-gray-700 rounded-md text-white px-3 py-2" placeholder="e.g. 500">
                        </div>
                        <div>
                            <button wire:click="overrideBid" wire:confirm="Are you sure you want to manually override the highest bid?" class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white font-bold rounded-md uppercase">
                                Override
                            </button>
                        </div>
                    </div>
                    @error('overrideTeamId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    @error('overrideAmount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                @endif
            </div>

            <!-- RIGHT COLUMN: Stats & History -->
            <div class="w-full lg:w-96 space-y-6">
                <!-- Live Stats -->
                <div class="bg-card-bg shadow border border-gray-800 sm:rounded-lg p-6">
                    <div class="grid grid-cols-3 gap-4 text-center mb-4">
                        <div>
                            <p class="text-xs text-gray-400 uppercase">Pending</p>
                            <p class="text-2xl font-black text-white">{{ $pendingCount }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase">Sold</p>
                            <p class="text-2xl font-black text-green-500">{{ $soldCount }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase">Unsold</p>
                            <p class="text-2xl font-black text-gray-500">{{ $unsoldCount }}</p>
                        </div>
                    </div>
                    
                    @if($unsoldCount > 0)
                    <div class="border-t border-gray-800 pt-4 mt-2">
                        <button wire:click="recallUnsold" wire:confirm="Are you sure you want to bring {{ $unsoldCount }} unsold players back into the auction?" class="w-full py-2 bg-red-600/20 hover:bg-red-600 text-red-500 hover:text-white border border-red-600 rounded font-bold text-sm uppercase shadow transition">
                            Recall Unsold Players
                        </button>
                    </div>
                    @endif
                </div>

                <!-- Live Bid History -->
                <div class="bg-card-bg shadow border border-gray-800 sm:rounded-lg flex flex-col h-[500px]">
                    <div class="p-4 border-b border-gray-800 bg-[#141B2D] rounded-t-lg">
                        <h3 class="font-bold text-accent-gold uppercase tracking-wider">Live Bid History</h3>
                    </div>
                    <div class="flex-1 overflow-y-auto p-4 space-y-3">
                        @forelse($recentBids as $bid)
                            <div class="bg-primary-bg border border-gray-800 p-3 rounded-lg flex justify-between items-center animate-in slide-in-from-right-4 duration-300">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $bid['team']['logo'] ? Storage::url($bid['team']['logo']) : 'https://ui-avatars.com/api/?name='.urlencode($bid['team']['name']).'&background=random' }}" class="w-8 h-8 rounded-full">
                                    <div>
                                        <p class="font-bold text-sm text-white">{{ $bid['team']['short_name'] }}</p>
                                        <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($bid['created_at'])->format('H:i:s') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="font-black text-[#FFC800] text-lg">
                                        ₹{{ number_format($bid['bid_amount']) }}
                                    </div>
                                    @if($currentPlayer && $currentPlayer['status'] === 'current')
                                        <button wire:click="sellToBid({{ $bid['id'] }})" wire:confirm="Sell player to {{ $bid['team']['short_name'] }} for ₹{{ number_format($bid['bid_amount']) }}?" class="bg-[#00C853] hover:bg-green-600 text-white text-xs font-bold px-2 py-1 rounded shadow uppercase">
                                            Sold
                                        </button>
                                        <button wire:click="deleteBid({{ $bid['id'] }})" wire:confirm="Are you sure you want to delete this bid?" class="bg-red-600 hover:bg-red-700 text-white text-xs font-bold px-2 py-1 rounded shadow uppercase ml-2" title="Delete Bid">
                                            <svg class="w-3 h-3 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-gray-500 py-10 text-sm">
                                No bids yet.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Players List -->
                <div class="bg-card-bg shadow border border-gray-800 sm:rounded-lg flex flex-col h-[400px]">
                    <div class="p-4 border-b border-gray-800 bg-[#141B2D] rounded-t-lg flex justify-between items-center">
                        <h3 class="font-bold text-accent-gold uppercase tracking-wider">Players List</h3>
                        <div class="flex gap-2">
                            <button wire:click="shufflePendingPlayers" class="text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 px-3 py-1.5 rounded uppercase flex items-center gap-2 transition" title="Shuffle Pending Players">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                Shuffle
                            </button>
                            <button wire:click="exportResults" class="text-xs font-bold text-white bg-green-600 hover:bg-green-700 px-3 py-1.5 rounded uppercase flex items-center gap-2 transition" title="Download Auction Results">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                Export
                            </button>
                        </div>
                    </div>
                    <div class="flex-1 overflow-y-auto p-4 space-y-2">
                        @forelse($playersList as $ap)
                            <div class="flex justify-between items-center bg-primary-bg border border-gray-800 p-2 rounded">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-gray-800 rounded-full flex items-center justify-center text-xs font-bold text-gray-400">
                                        {{ $ap->order_no }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-sm text-white">{{ $ap->player->name }}</p>
                                        <p class="text-xs text-gray-500">₹{{ number_format($ap->player->base_price) }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    @if($ap->status === 'pending')
                                        <span class="text-xs font-bold text-gray-400 bg-gray-800 px-2 py-1 rounded uppercase tracking-wider">Pending</span>
                                    @elseif($ap->status === 'current')
                                        <span class="text-xs font-bold text-white bg-blue-600 px-2 py-1 rounded animate-pulse uppercase tracking-wider">Current</span>
                                    @elseif($ap->status === 'sold')
                                        <div class="flex items-center justify-end gap-3">
                                            <div class="text-right">
                                                <span class="text-xs font-bold text-white bg-green-600 px-2 py-1 rounded uppercase tracking-wider">Sold</span>
                                                <p class="text-[10px] text-accent-gold font-bold mt-1">{{ $ap->soldToTeam->short_name ?? '' }} (₹{{ number_format($ap->final_price) }})</p>
                                            </div>
                                            <button wire:click="revertPlayer({{ $ap->id }})" wire:confirm="Are you sure you want to revert this player to pending? This will refund the team and delete all previous bids for this player." class="p-1 bg-red-600/20 text-red-500 hover:bg-red-600 hover:text-white rounded transition" title="Revert to Pending">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                            </button>
                                        </div>
                                    @elseif($ap->status === 'unsold')
                                        <div class="flex items-center justify-end gap-3">
                                            <span class="text-xs font-bold text-white bg-gray-600 px-2 py-1 rounded uppercase tracking-wider">Unsold</span>
                                            <button wire:click="revertPlayer({{ $ap->id }})" wire:confirm="Are you sure you want to revert this player to pending?" class="p-1 bg-red-600/20 text-red-500 hover:bg-red-600 hover:text-white rounded transition" title="Revert to Pending">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-gray-500 py-10 text-sm">
                                No players assigned.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Team PDFs -->
                <div class="bg-card-bg shadow border border-gray-800 sm:rounded-lg flex flex-col">
                    <div class="p-4 border-b border-gray-800 bg-[#141B2D] rounded-t-lg">
                        <h3 class="font-bold text-accent-gold uppercase tracking-wider">Download Team Squads (PDF)</h3>
                    </div>
                    <div class="p-4 space-y-2 max-h-48 overflow-y-auto custom-scrollbar">
                        @foreach($auction->teams as $team)
                            <div class="flex justify-between items-center bg-primary-bg p-2 rounded border border-gray-800">
                                <span class="text-white font-bold text-sm">{{ $team->name }}</span>
                                <a href="{{ route('teams.pdf', ['team' => $team->id, 'auction_id' => $auction->id]) }}" target="_blank" class="text-xs bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded transition flex items-center gap-1" title="Download {{ $team->short_name }} PDF">
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
</div>
