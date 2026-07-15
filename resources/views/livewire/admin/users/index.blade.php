<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-poppins font-bold text-white">Manage Users</h1>
        <button wire:click="create" class="bg-accent-gold text-primary-bg px-4 py-2 rounded font-semibold hover:bg-yellow-400 transition">Add New User</button>
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

    <div class="bg-card-bg p-4 rounded-lg shadow mb-6 border border-gray-800 flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4">
        <input type="text" wire:model.live="search" placeholder="Search by name or email..." class="w-full md:flex-1 bg-primary-bg border border-gray-700 rounded py-2 px-3 text-white focus:outline-none focus:border-accent-gold">
        <select wire:model.live="filterRole" class="w-full md:w-48 bg-primary-bg border border-gray-700 rounded py-2 px-3 text-white focus:outline-none focus:border-accent-gold">
            <option value="">All Roles</option>
            <option value="admin">Admin</option>
            <option value="sub_admin">Sub Admin</option>
            <option value="team_owner">Team Owner</option>
            <option value="viewer">Viewer</option>
        </select>
    </div>

    <div class="bg-card-bg rounded-lg shadow overflow-x-auto border border-gray-800">
        <table class="min-w-full divide-y divide-gray-800">
            <thead class="bg-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-card-bg divide-y divide-gray-800">
                @foreach($users as $user)
                <tr class="hover:bg-gray-800 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-white">{{ $user->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                        {{ $user->email }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $user->role === 'admin' ? 'bg-accent-red text-white' : ($user->role === 'sub_admin' ? 'bg-purple-600 text-white' : ($user->role === 'team_owner' ? 'bg-success-green text-white' : 'bg-gray-600 text-white')) }}">
                            {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button wire:click="edit({{ $user->id }})" class="text-indigo-400 hover:text-indigo-300 mr-3">Edit</button>
                        @if($user->role !== 'admin')
                            <button wire:click="delete({{ $user->id }})" wire:confirm="Are you sure you want to delete this user?" class="text-accent-red hover:text-red-400">Delete</button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4 border-t border-gray-800">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Modal -->
    @if($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 overflow-y-auto pt-10 pb-10">
            <div class="bg-card-bg rounded-lg w-full max-w-lg p-6 my-8 overflow-y-auto max-h-[90vh]">
                <h2 class="text-2xl font-poppins font-bold text-white mb-4">{{ $user_id ? 'Edit User' : 'Create User' }}</h2>
                <form wire:submit.prevent="store">
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-gray-300 text-sm font-bold mb-2">Name</label>
                            <input type="text" wire:model="name" class="w-full bg-primary-bg border border-gray-700 rounded py-2 px-3 text-white focus:outline-none focus:border-accent-gold" required>
                            @error('name') <span class="text-accent-red text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-gray-300 text-sm font-bold mb-2">Email</label>
                            <input type="email" wire:model="email" class="w-full bg-primary-bg border border-gray-700 rounded py-2 px-3 text-white focus:outline-none focus:border-accent-gold" required>
                            @error('email') <span class="text-accent-red text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-gray-300 text-sm font-bold mb-2">Role</label>
                            <select wire:model.live="role" class="w-full bg-primary-bg border border-gray-700 rounded py-2 px-3 text-white focus:outline-none focus:border-accent-gold" required>
                                <option value="viewer">Viewer</option>
                                <option value="team_owner">Team Owner</option>
                                <option value="sub_admin">Sub Admin</option>
                                <option value="admin">Admin</option>
                            </select>
                            @error('role') <span class="text-accent-red text-xs">{{ $message }}</span> @enderror
                        </div>

                        @if($role === 'sub_admin')
                        <div class="col-span-1 border border-gray-700 p-4 rounded-lg bg-gray-900 mt-2">
                            <label class="block text-[#FFC800] text-sm font-bold mb-3 uppercase tracking-wider">Sub-Admin Permissions</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" wire:model="permissions" value="manage_users" class="form-checkbox h-5 w-5 text-accent-gold rounded border-gray-600 bg-gray-800 focus:ring-accent-gold focus:ring-offset-gray-900">
                                    <span class="ml-2 text-gray-300">Manage Users</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" wire:model="permissions" value="manage_teams" class="form-checkbox h-5 w-5 text-accent-gold rounded border-gray-600 bg-gray-800 focus:ring-accent-gold focus:ring-offset-gray-900">
                                    <span class="ml-2 text-gray-300">Manage Teams</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" wire:model="permissions" value="manage_players" class="form-checkbox h-5 w-5 text-accent-gold rounded border-gray-600 bg-gray-800 focus:ring-accent-gold focus:ring-offset-gray-900">
                                    <span class="ml-2 text-gray-300">Manage Players</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" wire:model="permissions" value="manage_auctions" class="form-checkbox h-5 w-5 text-accent-gold rounded border-gray-600 bg-gray-800 focus:ring-accent-gold focus:ring-offset-gray-900">
                                    <span class="ml-2 text-gray-300">Manage Auctions</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" wire:model="permissions" value="manage_banners" class="form-checkbox h-5 w-5 text-accent-gold rounded border-gray-600 bg-gray-800 focus:ring-accent-gold focus:ring-offset-gray-900">
                                    <span class="ml-2 text-gray-300">Manage Banners</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" wire:model="permissions" value="manage_pages" class="form-checkbox h-5 w-5 text-accent-gold rounded border-gray-600 bg-gray-800 focus:ring-accent-gold focus:ring-offset-gray-900">
                                    <span class="ml-2 text-gray-300">Manage Pages</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" wire:model="permissions" value="manage_gallery" class="form-checkbox h-5 w-5 text-accent-gold rounded border-gray-600 bg-gray-800 focus:ring-accent-gold focus:ring-offset-gray-900">
                                    <span class="ml-2 text-gray-300">Manage Gallery</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" wire:model="permissions" value="manage_core_committees" class="form-checkbox h-5 w-5 text-accent-gold rounded border-gray-600 bg-gray-800 focus:ring-accent-gold focus:ring-offset-gray-900">
                                    <span class="ml-2 text-gray-300">Manage Core Committees</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" wire:model="permissions" value="manage_sponsors" class="form-checkbox h-5 w-5 text-accent-gold rounded border-gray-600 bg-gray-800 focus:ring-accent-gold focus:ring-offset-gray-900">
                                    <span class="ml-2 text-gray-300">Manage Sponsors</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" wire:model="permissions" value="manage_settings" class="form-checkbox h-5 w-5 text-accent-gold rounded border-gray-600 bg-gray-800 focus:ring-accent-gold focus:ring-offset-gray-900">
                                    <span class="ml-2 text-gray-300">Manage Settings</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" wire:model="permissions" value="view_analytics" class="form-checkbox h-5 w-5 text-accent-gold rounded border-gray-600 bg-gray-800 focus:ring-accent-gold focus:ring-offset-gray-900">
                                    <span class="ml-2 text-gray-300">View Analytics</span>
                                </label>
                            </div>
                        </div>
                        @endif
                        <div>
                            <label class="block text-gray-300 text-sm font-bold mb-2">Password {{ $user_id ? '(Leave blank to keep current)' : '' }}</label>
                            <input type="password" wire:model="password" class="w-full bg-primary-bg border border-gray-700 rounded py-2 px-3 text-white focus:outline-none focus:border-accent-gold" {{ $user_id ? '' : 'required' }}>
                            @error('password') <span class="text-accent-red text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3 border-t border-gray-700 pt-4">
                        <button type="button" wire:click="closeModal" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded transition">Cancel</button>
                        <button type="submit" class="bg-accent-gold hover:bg-yellow-400 text-primary-bg px-4 py-2 rounded font-semibold transition">Save User</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
