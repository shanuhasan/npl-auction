<div>

    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
        <h1 class="text-2xl font-semibold text-ink">{{ $isEditMode ? 'Edit Page' : 'Create New Page' }}</h1>
        <a href="{{ route('admin.pages.index') }}" class="text-muted hover:text-ink transition-colors flex items-center gap-2" wire:navigate>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Back to Pages
        </a>
    </div>

    <div class="bg-surface border border-line shadow-sm rounded-xl p-6 md:p-8">
        <form wire:submit.prevent="save">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Title -->
                <div>
                    <label for="title" class="block text-muted text-sm font-bold mb-2">Page Title</label>
                    <input type="text" id="title" wire:model.live.debounce.500ms="title" 
                           class="w-full bg-canvas border border-line rounded-lg px-4 py-3 text-ink focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand transition-colors" 
                           placeholder="e.g., About Us">
                    @error('title') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Slug -->
                <div>
                    <label for="slug" class="block text-muted text-sm font-bold mb-2">URL Slug</label>
                    <input type="text" id="slug" wire:model="slug" 
                           class="w-full bg-canvas border border-line rounded-lg px-4 py-3 text-ink focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand transition-colors" 
                           placeholder="e.g., about-us">
                    <p class="text-xs text-muted mt-1">This will be the URL: yourwebsite.com/pages/<strong>{{ $slug ?: '...' }}</strong></p>
                    @error('slug') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Content using QuillJS & AlpineJS -->
            <div class="mb-6" wire:ignore>
                <label class="block text-muted text-sm font-bold mb-2">Page Content</label>
                <div x-data="{
                         content: @entangle('content'),
                         init() {
                             let quill = new Quill($refs.quillEditor, {
                                 theme: 'snow',
                                 modules: {
                                     toolbar: [
                                         [{ 'header': [1, 2, 3, 4, false] }],
                                         ['bold', 'italic', 'underline', 'strike'],
                                         [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                         ['link', 'clean']
                                     ]
                                 }
                             });
                             quill.root.innerHTML = this.content || '';
                             quill.on('text-change', () => {
                                 this.content = quill.root.innerHTML;
                             });
                         }
                     }">
                    <div x-ref="quillEditor" class="bg-white text-black min-h-[300px] rounded-lg border-0 text-base"></div>
                </div>
            </div>
            @error('content') <span class="text-red-600 text-xs mt-1 block mb-6">{{ $message }}</span> @enderror

            <!-- Status -->
            <div class="mb-8">
                <label class="flex items-center cursor-pointer">
                    <div class="relative">
                        <input type="checkbox" wire:model="is_active" class="sr-only">
                        <div class="block {{ $is_active ? 'bg-green-500' : 'bg-slate-200' }} w-14 h-8 rounded-full transition-colors duration-300"></div>
                        <div class="dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition-transform duration-300 {{ $is_active ? 'transform translate-x-6' : '' }}"></div>
                    </div>
                    <div class="ml-3 text-ink-soft font-medium">
                        {{ $is_active ? 'Active (Published)' : 'Inactive (Draft)' }}
                    </div>
                </label>
                @error('is_active') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end gap-4 border-t border-line pt-6">
                <a href="{{ route('admin.pages.index') }}" class="px-6 py-3 border border-line text-ink rounded-lg hover:bg-surface-subtle transition-colors font-semibold" wire:navigate>
                    Cancel
                </a>
                <button type="submit" class="btn-primary px-8 py-3 rounded-lg font-bold">
                    {{ $isEditMode ? 'Update Page' : 'Create Page' }}
                </button>
            </div>
        </form>
    </div>

    <style>
        /* Quill colours/borders come from the central theme (resources/css/app.css) */
        .ql-editor {
            min-height: 300px;
            font-size: 15px;
        }
    </style>
</div>
