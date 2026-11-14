<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-ink">Manage Auctions</h1>
        <a href="{{ route('admin.auctions.create') }}" class="bg-brand text-white px-4 py-2 rounded-lg font-medium hover:bg-brand-hover transition text-sm inline-flex items-center justify-center gap-2" wire:navigate>Create Auction</a>
    </div>

    @if (session()->has('message'))
        <div class="alert-success mb-4">
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert-danger mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-surface rounded-lg shadow-sm overflow-x-auto border border-line">
        <table class="min-w-full divide-y divide-line">
            <thead class="bg-surface-muted">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-muted uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-surface divide-y divide-line">
                @forelse($auctions as $auction)
                <tr class="hover:bg-surface-muted transition">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-ink">{{ $auction->title }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-ink-soft">{{ $auction->auction_date->format('M d, Y h:i A') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($auction->status === 'upcoming')
                            <span class="badge-neutral">Upcoming</span>
                        @elseif($auction->status === 'live')
                            <span class="badge-danger animate-pulse">Live</span>
                        @elseif($auction->status === 'completed')
                            <span class="badge-success">Completed</span>
                        @else
                            <span class="badge-warning">{{ ucfirst($auction->status) }}</span>
                        @endif
                        <a href="{{ route('admin.auctions.schedule', $auction->guid) }}" class="text-success hover:text-green-700 font-bold" wire:navigate>Schedule</a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex justify-end gap-3">
                        @if($auction->status === 'upcoming' || $auction->status === 'live' || $auction->status === 'paused')
                            <a href="{{ route('admin.auctions.edit', $auction->guid) }}" class="text-brand hover:text-amber-700 font-bold" wire:navigate>Edit</a>
                            <a href="{{ route('admin.auction.control', $auction->guid) }}" class="text-indigo-600 hover:text-indigo-700 font-bold" wire:navigate>Control Panel</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-muted">No auctions found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
