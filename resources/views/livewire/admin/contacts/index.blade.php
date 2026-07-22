<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-poppins font-bold text-white">Contact Messages</h1>
    </div>

    @if (session()->has('message'))
        <div class="bg-success-green text-white px-4 py-3 rounded mb-4 shadow">
            {{ session('message') }}
        </div>
    @endif
    
    <div class="bg-card-bg p-4 rounded-lg shadow mb-6 border border-gray-800 flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4">
        <input type="text" wire:model.live="search" placeholder="Search by name or email..." class="w-full bg-primary-bg border border-gray-700 rounded py-2 px-3 text-white focus:outline-none focus:border-accent-gold">
    </div>

    <div class="bg-card-bg rounded-lg shadow overflow-x-auto border border-gray-800">
        <table class="min-w-full divide-y divide-gray-800">
            <thead class="bg-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Phone</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-card-bg divide-y divide-gray-800">
                @forelse($messages as $message)
                <tr class="hover:bg-gray-800 transition {{ !$message->is_read ? 'font-bold' : '' }}">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-white">{{ $message->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                        {{ $message->email }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                        {{ $message->phone }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($message->is_read)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-success-green text-white">Read</span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-accent-red text-white">Unread</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                        {{ $message->created_at->format('M d, Y h:i A') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button wire:click="viewMessage({{ $message->id }})" class="text-indigo-400 hover:text-indigo-300 mr-3">View</button>
                        <button wire:click="deleteMessage({{ $message->id }})" wire:confirm="Are you sure you want to delete this message?" class="text-accent-red hover:text-red-400">Delete</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-400">No messages found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t border-gray-800">
            {{ $messages->links() }}
        </div>
    </div>

    <!-- View Message Modal -->
    @if($messageModal && $selectedMessage)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 overflow-y-auto pt-10 pb-10">
            <div class="bg-card-bg rounded-lg w-full max-w-2xl p-6 my-8 overflow-y-auto max-h-[90vh]">
                <div class="flex justify-between items-start mb-4">
                    <h2 class="text-2xl font-poppins font-bold text-white">Message Details</h2>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-white">&times;</button>
                </div>
                
                <div class="grid grid-cols-2 gap-4 mb-6 text-sm">
                    <div>
                        <p class="text-gray-400">Name</p>
                        <p class="text-white font-medium">{{ $selectedMessage->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400">Email</p>
                        <p class="text-white font-medium">{{ $selectedMessage->email }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400">Phone</p>
                        <p class="text-white font-medium">{{ $selectedMessage->phone }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400">Date Received</p>
                        <p class="text-white font-medium">{{ $selectedMessage->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>

                <div class="mb-6">
                    <p class="text-gray-400 mb-2">Message</p>
                    <div class="bg-primary-bg p-4 rounded text-white whitespace-pre-wrap">{{ $selectedMessage->message }}</div>
                </div>
                
                <div class="flex justify-end border-t border-gray-700 pt-4">
                    <button type="button" wire:click="closeModal" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded transition">Close</button>
                </div>
            </div>
        </div>
    @endif
</div>
