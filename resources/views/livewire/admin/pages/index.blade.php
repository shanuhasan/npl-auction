<div>
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
        <h1 class="text-3xl font-poppins font-bold text-white">Manage Pages</h1>
        <a href="{{ route('admin.pages.create') }}" class="btn-primary flex items-center justify-center gap-2 whitespace-nowrap" wire:navigate>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Create New Page
        </a>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-900/50 border-l-4 border-green-500 text-green-200 p-4 mb-6 rounded-r shadow-lg" role="alert">
            {{ session('message') }}
        </div>
    @endif

    <div class="bg-[#141B2D] border border-white/10 shadow-xl rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-white/10">
                <thead class="bg-white/5">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Page Title</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">URL Slug</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10 bg-[#0B0F19]/50">
                    @forelse($pages as $page)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-white">{{ $page->title }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-400 font-mono text-xs">/pages/{{ $page->slug }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button wire:click="toggleStatus({{ $page->id }})" 
                                        class="relative inline-flex items-center h-6 rounded-full w-11 focus:outline-none transition-colors ease-in-out duration-200 {{ $page->is_active ? 'bg-green-500' : 'bg-gray-600' }}">
                                    <span class="inline-block w-4 h-4 transform bg-white rounded-full transition ease-in-out duration-200 {{ $page->is_active ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                </button>
                                <span class="ml-2 text-xs {{ $page->is_active ? 'text-green-400' : 'text-gray-500' }}">
                                    {{ $page->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-3">
                                    <a href="{{ route('public.page', $page->slug) }}" target="_blank" class="text-blue-400 hover:text-blue-300 transition-colors" title="View Page">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.pages.edit', $page->id) }}" class="text-[#FFC800] hover:text-[#D4A000] transition-colors" wire:navigate title="Edit Page">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                        </svg>
                                    </a>
                                    <button wire:click="deletePage({{ $page->id }})" wire:confirm="Are you sure you want to delete this page?" class="text-red-500 hover:text-red-400 transition-colors" title="Delete Page">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-600 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="text-lg">No pages found.</p>
                                    <p class="text-sm mt-1">Create a new page to get started.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
