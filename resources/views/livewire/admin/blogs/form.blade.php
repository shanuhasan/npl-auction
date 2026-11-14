<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-ink leading-tight">
                {{ $blog && $blog->exists ? __('Edit Blog') : __('Create Blog') }}
            </h2>
            <a href="{{ route('admin.blogs.index') }}" class="px-4 py-2 bg-surface-subtle text-ink font-bold rounded-lg hover:bg-slate-200 transition">
                Back to Blogs
            </a>
        </div>
    </x-slot>

    <div>
        <div class="space-y-6">
            <div class="bg-surface shadow-sm border border-line rounded-lg overflow-hidden p-6">
                
                <form wire:submit.prevent="save" class="space-y-6">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-muted mb-1">Title</label>
                            <input type="text" wire:model.live.debounce.500ms="title" class="w-full bg-surface-muted border border-line rounded-lg p-3 text-ink focus:ring-brand focus:border-brand transition" placeholder="Enter blog title">
                            @error('title') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-muted mb-1">Slug</label>
                            <input type="text" wire:model="slug" class="w-full bg-surface-muted border border-line rounded-lg p-3 text-ink focus:ring-brand focus:border-brand transition" placeholder="enter-blog-slug">
                            @error('slug') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-muted mb-1">Excerpt (Short description)</label>
                            <textarea wire:model="excerpt" rows="3" class="w-full bg-surface-muted border border-line rounded-lg p-3 text-ink focus:ring-brand focus:border-brand transition" placeholder="Enter short excerpt"></textarea>
                            @error('excerpt') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
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
                            <label class="block text-sm font-medium text-muted mb-1">Content</label>
                            <div x-ref="quillEditor" class="bg-surface-muted text-ink min-h-[300px] border border-line rounded-b-lg"></div>
                        </div>
                        <div class="md:col-span-2">
                             @error('content') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-muted mb-1">Featured Image</label>
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
                            <input type="file" wire:model="new_image" class="w-full bg-surface-muted border border-line rounded-lg p-2 text-ink file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-brand file:text-white hover:file:bg-brand-hover">
                            @error('new_image') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2 border-t border-line pt-6 mt-2">
                            <h3 class="text-lg font-bold text-brand mb-4">SEO Settings</h3>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-muted mb-1">Meta Title</label>
                            <input type="text" wire:model="meta_title" class="w-full bg-surface-muted border border-line rounded-lg p-3 text-ink focus:ring-brand focus:border-brand transition" placeholder="SEO Title">
                            @error('meta_title') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-muted mb-1">Meta Description</label>
                            <textarea wire:model="meta_description" rows="3" class="w-full bg-surface-muted border border-line rounded-lg p-3 text-ink focus:ring-brand focus:border-brand transition" placeholder="SEO Description"></textarea>
                            @error('meta_description') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-muted mb-1">Meta Keywords</label>
                            <input type="text" wire:model="meta_keywords" class="w-full bg-surface-muted border border-line rounded-lg p-3 text-ink focus:ring-brand focus:border-brand transition" placeholder="e.g. cricket, auction, npl">
                            @error('meta_keywords') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="flex items-center space-x-3 mt-4">
                                <input type="checkbox" wire:model="is_published" class="w-5 h-5 bg-surface-muted border-line rounded text-brand focus:ring-brand">
                                <span class="text-ink font-medium">Publish this blog immediately</span>
                            </label>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-line flex justify-end">
                        <button type="submit" class="px-8 py-3 bg-brand hover:bg-brand-hover text-white rounded-lg shadow-sm text-sm transition flex items-center gap-2 font-medium">
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
