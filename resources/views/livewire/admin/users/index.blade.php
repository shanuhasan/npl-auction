<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-ink">Manage Users</h1>
        <button wire:click="create" class="bg-brand text-white px-4 py-2 rounded-lg font-medium hover:bg-brand-hover transition text-sm inline-flex items-center justify-center gap-2">Add New User</button>
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

    <div class="bg-surface p-4 rounded-lg shadow-sm mb-6 border border-line flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4">
        <input type="text" wire:model.live="search" placeholder="Search by name or email..." class="w-full md:flex-1 bg-canvas border border-line rounded py-2 px-3 text-ink focus:outline-none focus:border-brand">
        @if(auth()->user()->role !== 'sub_admin')
        <x-select2 id="filterRole" wire:model.live="filterRole" placeholder="All Roles">
            <option value="admin">Admin</option>
            <option value="sub_admin">Sub Admin</option>
            <option value="team_owner">Team Owner</option>
            <option value="viewer">Viewer</option>
        </x-select2>
        @endif
    </div>

    <div class="bg-surface rounded-lg shadow-sm overflow-x-auto border border-line">
        <table class="min-w-full divide-y divide-line">
            <thead class="bg-surface-muted">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-muted uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-surface divide-y divide-line">
                @foreach($users as $user)
                <tr class="hover:bg-surface-muted transition">
                    <td class="px-6 py-4 whitespace-nowrap flex items-center">
                        @if($user->image)
                            <img src="{{ asset('storage/' . $user->image) }}" alt="{{ $user->name }}" class="h-10 w-10 rounded-full object-cover border border-line-strong mr-3">
                        @else
                            <div class="h-10 w-10 rounded-full bg-surface-subtle flex items-center justify-center border border-line-strong text-muted font-bold mr-3">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                        <div class="text-sm font-medium text-ink">{{ $user->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-ink-soft">
                        {{ $user->email }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="{{ $user->role === 'admin' ? 'badge-danger' : ($user->role === 'sub_admin' ? 'badge-brand' : ($user->role === 'team_owner' ? 'badge-success' : 'badge-neutral')) }}">
                            {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button wire:click="edit({{ $user->id }})" class="text-indigo-600 hover:text-indigo-700 mr-3">Edit</button>
                        @if($user->role !== 'admin')
                            <button wire:click="delete({{ $user->id }})" wire:confirm="Are you sure you want to delete this user?" class="text-danger hover:text-red-700">Delete</button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4 border-t border-line">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Modal -->
    @if($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 overflow-y-auto pt-10 pb-10">
            <div class="bg-surface rounded-lg w-full max-w-lg p-6 my-8 overflow-y-auto max-h-[90vh]">
                <h2 class="text-lg font-semibold text-ink mb-4">{{ $user_id ? 'Edit User' : 'Create User' }}</h2>
                <form wire:submit.prevent="store">
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-ink-soft text-sm font-bold mb-2">Name</label>
                            <input type="text" wire:model="name" class="w-full bg-canvas border border-line rounded py-2 px-3 text-ink focus:outline-none focus:border-brand" required>
                            @error('name') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-ink-soft text-sm font-bold mb-2">Email</label>
                            <input type="email" wire:model="email" class="w-full bg-canvas border border-line rounded py-2 px-3 text-ink focus:outline-none focus:border-brand" required>
                            @error('email') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-ink-soft text-sm font-bold mb-2">Profile Image</label>
                            <input type="file" wire:model="newImage" accept="image/*" class="w-full bg-canvas border border-line rounded py-2 px-3 text-ink focus:outline-none focus:border-brand">
                            @error('newImage') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                            
                            <div wire:loading wire:target="newImage" class="text-sm text-muted mt-1">Uploading...</div>
                            
                            @if ($newImage)
                                <div class="mt-2">
                                    <span class="text-sm text-muted block mb-1">Image Preview:</span>
                                    <img src="{{ $newImage->temporaryUrl() }}" class="h-20 w-20 object-cover rounded border border-line-strong">
                                </div>
                            @elseif ($image)
                                <div class="mt-2">
                                    <span class="text-sm text-muted block mb-1">Current Image:</span>
                                    <img src="{{ asset('storage/' . $image) }}" class="h-20 w-20 object-cover rounded border border-line-strong">
                                </div>
                            @endif
                        </div>
                        <div>
                            <label class="block text-ink-soft text-sm font-bold mb-2">Role</label>
                            @if(auth()->user()->role === 'sub_admin')
                                <input type="hidden" wire:model="role" value="team_owner">
                                <input type="text" disabled value="Team Owner" class="w-full bg-surface-subtle border border-line rounded py-2 px-3 text-muted cursor-not-allowed">
                            @else
                                <x-select2 id="userRole" wire:model.live="role" placeholder="Select Role" required>
                                    <option value="viewer">Viewer</option>
                                    <option value="team_owner">Team Owner</option>
                                    <option value="sub_admin">Sub Admin</option>
                                    <option value="admin">Admin</option>
                                </x-select2>
                            @endif
                            @error('role') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                        </div>

                        @if($role === 'sub_admin')
                        <div class="col-span-1 border border-line p-4 rounded-lg bg-surface-muted mt-2">
                            <label class="block text-brand text-sm font-bold mb-3 uppercase tracking-wider">Sub-Admin Permissions</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" wire:model="permissions" value="manage_users" class="form-checkbox h-5 w-5 text-brand rounded border-line-strong bg-surface-subtle focus:ring-brand focus:ring-offset-gray-900">
                                    <span class="ml-2 text-ink-soft">Manage Users</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" wire:model="permissions" value="manage_teams" class="form-checkbox h-5 w-5 text-brand rounded border-line-strong bg-surface-subtle focus:ring-brand focus:ring-offset-gray-900">
                                    <span class="ml-2 text-ink-soft">Manage Teams</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" wire:model="permissions" value="manage_players" class="form-checkbox h-5 w-5 text-brand rounded border-line-strong bg-surface-subtle focus:ring-brand focus:ring-offset-gray-900">
                                    <span class="ml-2 text-ink-soft">Manage Players</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" wire:model="permissions" value="manage_auctions" class="form-checkbox h-5 w-5 text-brand rounded border-line-strong bg-surface-subtle focus:ring-brand focus:ring-offset-gray-900">
                                    <span class="ml-2 text-ink-soft">Manage Auctions</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" wire:model="permissions" value="manage_banners" class="form-checkbox h-5 w-5 text-brand rounded border-line-strong bg-surface-subtle focus:ring-brand focus:ring-offset-gray-900">
                                    <span class="ml-2 text-ink-soft">Manage Banners</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" wire:model="permissions" value="manage_pages" class="form-checkbox h-5 w-5 text-brand rounded border-line-strong bg-surface-subtle focus:ring-brand focus:ring-offset-gray-900">
                                    <span class="ml-2 text-ink-soft">Manage Pages</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" wire:model="permissions" value="manage_gallery" class="form-checkbox h-5 w-5 text-brand rounded border-line-strong bg-surface-subtle focus:ring-brand focus:ring-offset-gray-900">
                                    <span class="ml-2 text-ink-soft">Manage Gallery</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" wire:model="permissions" value="manage_core_committees" class="form-checkbox h-5 w-5 text-brand rounded border-line-strong bg-surface-subtle focus:ring-brand focus:ring-offset-gray-900">
                                    <span class="ml-2 text-ink-soft">Manage Core Committees</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" wire:model="permissions" value="manage_guests" class="form-checkbox h-5 w-5 text-brand rounded border-line-strong bg-surface-subtle focus:ring-brand focus:ring-offset-gray-900">
                                    <span class="ml-2 text-ink-soft">Manage Guests</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" wire:model="permissions" value="manage_sponsors" class="form-checkbox h-5 w-5 text-brand rounded border-line-strong bg-surface-subtle focus:ring-brand focus:ring-offset-gray-900">
                                    <span class="ml-2 text-ink-soft">Manage Sponsors</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" wire:model="permissions" value="manage_contacts" class="form-checkbox h-5 w-5 text-brand rounded border-line-strong bg-surface-subtle focus:ring-brand focus:ring-offset-gray-900">
                                    <span class="ml-2 text-ink-soft">Manage Contacts</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" wire:model="permissions" value="manage_settings" class="form-checkbox h-5 w-5 text-brand rounded border-line-strong bg-surface-subtle focus:ring-brand focus:ring-offset-gray-900">
                                    <span class="ml-2 text-ink-soft">Manage Settings</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" wire:model="permissions" value="view_analytics" class="form-checkbox h-5 w-5 text-brand rounded border-line-strong bg-surface-subtle focus:ring-brand focus:ring-offset-gray-900">
                                    <span class="ml-2 text-ink-soft">View Analytics</span>
                                </label>
                            </div>
                        </div>
                        @endif
                        <div>
                            <label class="block text-ink-soft text-sm font-bold mb-2">Password {{ $user_id ? '(Leave blank to keep current)' : '' }}</label>
                            <input type="password" wire:model="password" class="w-full bg-canvas border border-line rounded py-2 px-3 text-ink focus:outline-none focus:border-brand" {{ $user_id ? '' : 'required' }}>
                            @error('password') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3 border-t border-line pt-4">
                        <button type="button" wire:click="closeModal" class="bg-surface-subtle hover:bg-slate-200 text-ink px-4 py-2 rounded transition">Cancel</button>
                        <button type="submit" class="bg-brand hover:bg-brand-hover text-white px-4 py-2 rounded-lg font-medium transition text-sm inline-flex items-center justify-center gap-2">Save User</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
