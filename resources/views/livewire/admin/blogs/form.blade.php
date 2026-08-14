<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $blog && $blog->exists ? __('Edit Blog') : __('Create Blog') }}
            </h2>
            <a href="{{ route('admin.blogs.index') }}" class="px-4 py-2 bg-gray-700 text-white font-bold rounded-lg hover:bg-gray-600 transition">
                Back to Blogs
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-card-bg shadow border border-gray-800 rounded-lg overflow-hidden p-6">
                
                <form wire:submit.prevent="save" class="space-y-6">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-400 mb-1">Title</label>
                            <input type="text" wire:model.live.debounce.500ms="title" class="w-full bg-gray-900 border border-gray-700 rounded-lg p-3 text-white focus:ring-accent-gold focus:border-accent-gold transition" placeholder="Enter blog title">
                            @error('title') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-400 mb-1">Slug</label>
                            <input type="text" wire:model="slug" class="w-full bg-gray-900 border border-gray-700 rounded-lg p-3 text-white focus:ring-accent-gold focus:border-accent-gold transition" placeholder="enter-blog-slug">
                            @error('slug') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-400 mb-1">Excerpt (Short description)</label>
                            <textarea wire:model="excerpt" rows="3" class="w-full bg-gray-900 border border-gray-700 rounded-lg p-3 text-white focus:ring-accent-gold focus:border-accent-gold transition" placeholder="Enter short excerpt"></textarea>
                            @error('excerpt') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2" wire:ignore 
                             x-data="{ content: @entangle('content') }"
                             x-init="
                                 quill = new Quill($refs.quillEditor, {
                                     theme: 'snow',
                                     placeholder: 'Write your blog content here...',
                                     modules: {
                                         toolbar: [
                                             [{ 'header': [1, 2, 3, false] }],
                                             ['bold', 'italic', 'underline', 'strike'],
                                             ['blockquote', 'code-block'],
                                             [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                             [{ 'script': 'sub'}, { 'script': 'super' }],
                                             [{ 'color': [] }, { 'background': [] }],
                                             ['link', 'image', 'video'],
                                             ['clean']
                                         ]
                                     }
                                 });
                                 quill.root.innerHTML = content;
                                 quill.on('text-change', function () {
                                     content = quill.root.innerHTML;
                                 });
                             ">
                            <label class="block text-sm font-medium text-gray-400 mb-1">Content</label>
                            <div x-ref="quillEditor" class="bg-gray-900 text-white min-h-[300px] border border-gray-700 rounded-b-lg"></div>
                        </div>
                        <div class="md:col-span-2">
                             @error('content') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-400 mb-1">Featured Image</label>
                            @if($image && !$new_image)
                                <div class="mb-3">
                                    <img src="{{ asset('storage/' . $image) }}" class="h-32 object-cover rounded">
                                </div>
                            @endif
                            @if($new_image)
                                <div class="mb-3">
                                    <img src="{{ $new_image->temporaryUrl() }}" class="h-32 object-cover rounded">
                                </div>
                            @endif
                            <input type="file" wire:model="new_image" class="w-full bg-gray-900 border border-gray-700 rounded-lg p-2 text-white file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-accent-gold file:text-gray-900 hover:file:bg-yellow-400">
                            @error('new_image') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2 border-t border-gray-800 pt-6 mt-2">
                            <h3 class="text-lg font-bold text-accent-gold mb-4">SEO Settings</h3>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-400 mb-1">Meta Title</label>
                            <input type="text" wire:model="meta_title" class="w-full bg-gray-900 border border-gray-700 rounded-lg p-3 text-white focus:ring-accent-gold focus:border-accent-gold transition" placeholder="SEO Title">
                            @error('meta_title') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-400 mb-1">Meta Description</label>
                            <textarea wire:model="meta_description" rows="3" class="w-full bg-gray-900 border border-gray-700 rounded-lg p-3 text-white focus:ring-accent-gold focus:border-accent-gold transition" placeholder="SEO Description"></textarea>
                            @error('meta_description') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-400 mb-1">Meta Keywords</label>
                            <input type="text" wire:model="meta_keywords" class="w-full bg-gray-900 border border-gray-700 rounded-lg p-3 text-white focus:ring-accent-gold focus:border-accent-gold transition" placeholder="e.g. cricket, auction, npl">
                            @error('meta_keywords') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="flex items-center space-x-3 mt-4">
                                <input type="checkbox" wire:model="is_published" class="w-5 h-5 bg-gray-900 border-gray-700 rounded text-accent-gold focus:ring-accent-gold">
                                <span class="text-white font-medium">Publish this blog immediately</span>
                            </label>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-800 flex justify-end">
                        <button type="submit" class="px-8 py-3 bg-gradient-to-r from-[#FFC800] to-[#D4A000] hover:from-[#FFE040] hover:to-[#FFC800] text-[#0B0F19] font-bold rounded-lg shadow uppercase tracking-wider text-sm transition flex items-center gap-2">
                            <span wire:loading.remove wire:target="save">Save Blog</span>
                            <span wire:loading wire:target="save">Saving...</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    @push('scripts')
    @endpush
</div>
