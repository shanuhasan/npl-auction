<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-ink leading-tight">
                {{ __('Manage Blogs') }}
            </h2>
            <a href="{{ route('admin.blogs.create') }}" class="px-4 py-2 bg-brand text-white font-medium rounded-lg hover:bg-brand-hover transition text-sm inline-flex items-center justify-center gap-2">
                Create Blog
            </a>
        </div>
    </x-slot>

    <div>
        <div class="space-y-6">
            <div class="bg-surface shadow-sm border border-line rounded-lg overflow-hidden">
                <div class="p-6">
                    @if (session()->has('success'))
                        <div class="bg-green-50 border border-green-500 text-green-600 p-4 rounded-lg font-bold mb-6">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="mb-4">
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search blogs..." class="w-full md:w-1/3 bg-surface-muted border border-line rounded-lg p-2 text-ink focus:ring-brand focus:border-brand">
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-surface-subtle text-muted text-sm uppercase tracking-wider border-b border-line">
                                    <th class="p-4">Image</th>
                                    <th class="p-4">Title</th>
                                    <th class="p-4">Published</th>
                                    <th class="p-4">Date</th>
                                    <th class="p-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-line">
                                @forelse($blogs as $blog)
                                    <tr class="hover:bg-surface-muted transition">
                                        <td class="p-4">
                                            @if($blog->image)
                                                <img src="{{ asset('storage/' . $blog->image) }}" class="h-12 w-16 object-cover rounded">
                                            @else
                                                <div class="h-12 w-16 bg-surface-subtle rounded flex items-center justify-center text-xs text-muted">No Img</div>
                                            @endif
                                        </td>
                                        <td class="p-4 text-ink font-medium">{{ $blog->title }}</td>
                                        <td class="p-4">
                                            <button wire:click="togglePublish({{ $blog->id }})" class="px-3 py-1 rounded text-xs font-bold {{ $blog->is_published ? 'bg-green-50 text-green-600 border border-green-500' : 'bg-red-50 text-red-600 border border-red-500' }}">
                                                {{ $blog->is_published ? 'Published' : 'Draft' }}
                                            </button>
                                        </td>
                                        <td class="p-4 text-muted text-sm">
                                            {{ $blog->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="p-4 text-right space-x-2">
                                            <a href="{{ route('admin.blogs.edit', $blog) }}" class="text-blue-600 hover:text-blue-700">Edit</a>
                                            <button wire:click="deleteBlog({{ $blog->id }})" wire:confirm="Are you sure you want to delete this blog?" class="text-red-600 hover:text-red-700">Delete</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="p-4 text-center text-muted">No blogs found.</td>
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
