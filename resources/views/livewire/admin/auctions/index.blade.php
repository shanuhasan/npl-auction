<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-poppins font-bold text-white">Manage Auctions</h1>
        <a href="{{ route('admin.auctions.create') }}" class="bg-accent-gold text-primary-bg px-4 py-2 rounded font-semibold hover:bg-yellow-400 transition" wire:navigate>Create Auction</a>
    </div>

    @if (session()->has('message'))
        <div class="bg-success-green text-white px-4 py-3 rounded mb-4 shadow">
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-accent-red text-white px-4 py-3 rounded mb-4 shadow">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-card-bg rounded-lg shadow overflow-hidden border border-gray-800">
        <table class="min-w-full divide-y divide-gray-800">
            <thead class="bg-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-card-bg divide-y divide-gray-800">
                @forelse($auctions as $auction)
                <tr class="hover:bg-gray-800 transition">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white">{{ $auction->title }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ $auction->auction_date->format('M d, Y h:i A') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($auction->status === 'upcoming')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-600 text-white">Upcoming</span>
                        @elseif($auction->status === 'live')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-accent-red text-white animate-pulse">Live</span>
                        @elseif($auction->status === 'completed')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-success-green text-white">Completed</span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-600 text-white">{{ ucfirst($auction->status) }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        @if($auction->status === 'upcoming' || $auction->status === 'live')
                            <a href="{{ route('admin.auction.control', $auction->guid) }}" class="text-indigo-400 hover:text-indigo-300 font-bold">Control Panel</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-400">No auctions found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
