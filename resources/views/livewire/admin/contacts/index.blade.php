<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-ink">Contact Messages</h1>
    </div>

    @if (session()->has('message'))
        <div class="alert-success mb-4">
            {{ session('message') }}
        </div>
    @endif
    
    <div class="bg-surface p-4 rounded-lg shadow-sm mb-6 border border-line flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4">
        <input type="text" wire:model.live="search" placeholder="Search by name or email..." class="w-full bg-canvas border border-line rounded py-2 px-3 text-ink focus:outline-none focus:border-brand">
    </div>

    <div class="bg-surface rounded-lg shadow-sm overflow-x-auto border border-line">
        <table class="min-w-full divide-y divide-line">
            <thead class="bg-surface-muted">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Phone</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-muted uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-surface divide-y divide-line">
                @forelse($messages as $message)
                <tr class="hover:bg-surface-muted transition {{ !$message->is_read ? 'font-bold' : '' }}">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-ink">{{ $message->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-ink-soft">
                        {{ $message->email }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-ink-soft">
                        {{ $message->phone }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($message->is_read)
                            <span class="badge-success">Read</span>
                        @else
                            <span class="badge-danger">Unread</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-ink-soft">
                        {{ $message->created_at->format('M d, Y h:i A') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button wire:click="viewMessage({{ $message->id }})" class="text-indigo-600 hover:text-indigo-700 mr-3">View</button>
                        <button wire:click="deleteMessage({{ $message->id }})" wire:confirm="Are you sure you want to delete this message?" class="text-danger hover:text-red-700">Delete</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-muted">No messages found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t border-line">
            {{ $messages->links() }}
        </div>
    </div>

    <!-- View Message Modal -->
    @if($messageModal && $selectedMessage)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 overflow-y-auto pt-10 pb-10">
            <div class="bg-surface rounded-lg w-full max-w-2xl p-6 my-8 overflow-y-auto max-h-[90vh]">
                <div class="flex justify-between items-start mb-4">
                    <h2 class="text-lg font-semibold text-ink">Message Details</h2>
                    <button wire:click="closeModal" class="text-muted hover:text-ink">&times;</button>
                </div>
                
                <div class="grid grid-cols-2 gap-4 mb-6 text-sm">
                    <div>
                        <p class="text-muted">Name</p>
                        <p class="text-ink font-medium">{{ $selectedMessage->name }}</p>
                    </div>
                    <div>
                        <p class="text-muted">Email</p>
                        <p class="text-ink font-medium">{{ $selectedMessage->email }}</p>
                    </div>
                    <div>
                        <p class="text-muted">Phone</p>
                        <p class="text-ink font-medium">{{ $selectedMessage->phone }}</p>
                    </div>
                    <div>
                        <p class="text-muted">Date Received</p>
                        <p class="text-ink font-medium">{{ $selectedMessage->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>

                <div class="mb-6">
                    <p class="text-muted mb-2">Message</p>
                    <div class="bg-canvas p-4 rounded text-ink whitespace-pre-wrap">{{ $selectedMessage->message }}</div>
                </div>
                
                <div class="flex justify-end border-t border-line pt-4">
                    <button type="button" wire:click="closeModal" class="bg-surface-subtle hover:bg-slate-200 text-ink px-4 py-2 rounded transition">Close</button>
                </div>
            </div>
        </div>
    @endif
</div>
