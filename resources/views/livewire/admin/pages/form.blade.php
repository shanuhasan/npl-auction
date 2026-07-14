<div>

    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
        <h1 class="text-3xl font-poppins font-bold text-white">{{ $isEditMode ? 'Edit Page' : 'Create New Page' }}</h1>
        <a href="{{ route('admin.pages.index') }}" class="text-gray-400 hover:text-white transition-colors flex items-center gap-2" wire:navigate>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Back to Pages
        </a>
    </div>

    <div class="bg-[#141B2D] border border-white/10 shadow-xl rounded-xl p-6 md:p-8">
        <form wire:submit.prevent="save">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Title -->
                <div>
                    <label for="title" class="block text-gray-400 text-sm font-bold mb-2">Page Title</label>
                    <input type="text" id="title" wire:model.live.debounce.500ms="title" 
                           class="w-full bg-[#0B0F19] border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-[#FFC800] focus:ring-1 focus:ring-[#FFC800] transition-colors" 
                           placeholder="e.g., About Us">
                    @error('title') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Slug -->
                <div>
                    <label for="slug" class="block text-gray-400 text-sm font-bold mb-2">URL Slug</label>
                    <input type="text" id="slug" wire:model="slug" 
                           class="w-full bg-[#0B0F19] border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-[#FFC800] focus:ring-1 focus:ring-[#FFC800] transition-colors" 
                           placeholder="e.g., about-us">
                    <p class="text-xs text-gray-500 mt-1">This will be the URL: yourwebsite.com/pages/<strong>{{ $slug ?: '...' }}</strong></p>
                    @error('slug') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Content using QuillJS & AlpineJS -->
            <div class="mb-6" wire:ignore>
                <label class="block text-gray-400 text-sm font-bold mb-2">Page Content</label>
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
            @error('content') <span class="text-red-500 text-xs mt-1 block mb-6">{{ $message }}</span> @enderror

            <!-- Status -->
            <div class="mb-8">
                <label class="flex items-center cursor-pointer">
                    <div class="relative">
                        <input type="checkbox" wire:model="is_active" class="sr-only">
                        <div class="block {{ $is_active ? 'bg-green-500' : 'bg-gray-600' }} w-14 h-8 rounded-full transition-colors duration-300"></div>
                        <div class="dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition-transform duration-300 {{ $is_active ? 'transform translate-x-6' : '' }}"></div>
                    </div>
                    <div class="ml-3 text-gray-300 font-medium">
                        {{ $is_active ? 'Active (Published)' : 'Inactive (Draft)' }}
                    </div>
                </label>
                @error('is_active') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end gap-4 border-t border-white/10 pt-6">
                <a href="{{ route('admin.pages.index') }}" class="px-6 py-3 border border-white/20 text-white rounded-lg hover:bg-white/5 transition-colors font-semibold" wire:navigate>
                    Cancel
                </a>
                <button type="submit" class="btn-primary px-8 py-3 rounded-lg font-bold">
                    {{ $isEditMode ? 'Update Page' : 'Create Page' }}
                </button>
            </div>
        </form>
    </div>

    <style>
        /* Custom Quill styling for dark theme integration */
        .ql-toolbar.ql-snow {
            background-color: #f3f4f6;
            border: none !important;
            border-top-left-radius: 0.5rem;
            border-top-right-radius: 0.5rem;
        }
        .ql-container.ql-snow {
            border: none !important;
            border-bottom-left-radius: 0.5rem;
            border-bottom-right-radius: 0.5rem;
            font-family: inherit;
        }
        .ql-editor {
            min-height: 300px;
            font-size: 16px;
        }
    </style>
</div>
