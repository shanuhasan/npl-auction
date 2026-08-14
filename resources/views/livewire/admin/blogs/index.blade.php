<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Manage Blogs') }}
            </h2>
            <a href="{{ route('admin.blogs.create') }}" class="px-4 py-2 bg-accent-gold text-black font-bold rounded-lg hover:bg-yellow-400 transition">
                Create Blog
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-card-bg shadow border border-gray-800 rounded-lg overflow-hidden">
                <div class="p-6">
                    @if (session()->has('success'))
                        <div class="bg-green-500/20 border border-green-500 text-green-400 p-4 rounded-lg font-bold mb-6">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="mb-4">
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search blogs..." class="w-full md:w-1/3 bg-gray-900 border border-gray-700 rounded-lg p-2 text-white focus:ring-accent-gold focus:border-accent-gold">
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-800 text-gray-400 text-sm uppercase tracking-wider border-b border-gray-700">
                                    <th class="p-4">Image</th>
                                    <th class="p-4">Title</th>
                                    <th class="p-4">Published</th>
                                    <th class="p-4">Date</th>
                                    <th class="p-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-800">
                                @forelse($blogs as $blog)
                                    <tr class="hover:bg-gray-800/50 transition">
                                        <td class="p-4">
                                            @if($blog->image)
                                                <img src="{{ asset('storage/' . $blog->image) }}" class="h-12 w-16 object-cover rounded">
                                            @else
                                                <div class="h-12 w-16 bg-gray-700 rounded flex items-center justify-center text-xs text-gray-500">No Img</div>
                                            @endif
                                        </td>
                                        <td class="p-4 text-white font-medium">{{ $blog->title }}</td>
                                        <td class="p-4">
                                            <button wire:click="togglePublish({{ $blog->id }})" class="px-3 py-1 rounded text-xs font-bold {{ $blog->is_published ? 'bg-green-500/20 text-green-500 border border-green-500' : 'bg-red-500/20 text-red-500 border border-red-500' }}">
                                                {{ $blog->is_published ? 'Published' : 'Draft' }}
                                            </button>
                                        </td>
                                        <td class="p-4 text-gray-400 text-sm">
                                            {{ $blog->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="p-4 text-right space-x-2">
                                            <a href="{{ route('admin.blogs.edit', $blog) }}" class="text-blue-400 hover:text-blue-300">Edit</a>
                                            <button wire:click="deleteBlog({{ $blog->id }})" wire:confirm="Are you sure you want to delete this blog?" class="text-red-400 hover:text-red-300">Delete</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="p-4 text-center text-gray-500">No blogs found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $blogs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
