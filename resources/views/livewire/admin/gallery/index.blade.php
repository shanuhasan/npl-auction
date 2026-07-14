<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-poppins font-bold text-white">Manage Gallery</h1>
        <button wire:click="create" class="bg-accent-gold text-primary-bg px-4 py-2 rounded font-semibold hover:bg-yellow-400 transition">Add New Media</button>
    </div>

    @if (session()->has('message'))
        <div class="bg-success-green text-white px-4 py-3 rounded mb-4 shadow">
            {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($galleries as $gallery)
            <div class="bg-card-bg rounded-lg shadow overflow-hidden flex flex-col">
                <div class="relative w-full aspect-video bg-gray-800 flex items-center justify-center">
                    @if($gallery->type == 'photo' && $gallery->file_path)
                        <img src="{{ asset('storage/' . $gallery->file_path) }}" alt="{{ $gallery->title }}" class="w-full h-full object-cover">
                    @elseif($gallery->type == 'video')
                        @if($gallery->video_url)
                            <div class="absolute inset-0 bg-black flex flex-col items-center justify-center text-red-500">
                                <svg class="w-12 h-12 mb-2" fill="currentColor" viewBox="0 0 24 24"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/></svg>
                                <span class="text-xs font-bold text-white">YouTube/External</span>
                            </div>
                        @elseif($gallery->file_path)
                            <video class="w-full h-full object-cover" controls preload="metadata">
                                <source src="{{ asset('storage/' . $gallery->file_path) }}" type="video/mp4">
                            </video>
                        @endif
                    @endif
                    <div class="absolute top-2 right-2 bg-black/60 px-2 py-1 rounded text-xs font-bold text-white uppercase">
                        {{ $gallery->type }}
                    </div>
                </div>
                <div class="p-4 flex-1 flex flex-col">
                    <h2 class="text-lg font-poppins font-bold text-white line-clamp-2 mb-2">{{ $gallery->title ?: 'Untitled' }}</h2>
                    <div class="mt-auto flex justify-between items-center pt-4 border-t border-gray-700">
                        <button wire:click="toggleStatus({{ $gallery->id }})" 
                                class="relative inline-flex items-center h-6 rounded-full w-11 focus:outline-none transition-colors ease-in-out duration-200 {{ $gallery->is_active ? 'bg-green-500' : 'bg-gray-600' }}" title="{{ $gallery->is_active ? 'Active' : 'Inactive' }}">
                            <span class="inline-block w-4 h-4 transform bg-white rounded-full transition ease-in-out duration-200 {{ $gallery->is_active ? 'translate-x-6' : 'translate-x-1' }}"></span>
                        </button>
                        <div class="flex space-x-2">
                            <button wire:click="edit({{ $gallery->id }})" class="bg-gray-700 hover:bg-gray-600 text-white px-3 py-1 rounded text-sm transition">Edit</button>
                            <button wire:click="delete({{ $gallery->id }})" wire:confirm="Are you sure you want to delete this media?" class="bg-accent-red hover:bg-red-500 text-white px-3 py-1 rounded text-sm transition">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if($galleries->isEmpty())
        <div class="text-center py-12 text-gray-400">
            No media found in the gallery.
        </div>
    @endif

    <!-- Modal -->
    @if($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 overflow-y-auto">
            <div class="bg-card-bg rounded-lg w-full max-w-lg p-6 my-8">
                <h2 class="text-2xl font-poppins font-bold text-white mb-4">{{ $gallery_id ? 'Edit Media' : 'Add New Media' }}</h2>
                <form wire:submit.prevent="store">
                    <div class="space-y-4">
                        
                        <div>
                            <label class="block text-gray-300 text-sm font-bold mb-2">Media Type</label>
                            <div class="flex space-x-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" wire:model.live="type" value="photo" class="form-radio text-accent-gold">
                                    <span class="ml-2 text-white">Photo</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" wire:model.live="type" value="video" class="form-radio text-accent-gold">
                                    <span class="ml-2 text-white">Video</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-gray-300 text-sm font-bold mb-2">Title / Caption</label>
                            <input type="text" wire:model="title" class="w-full bg-primary-bg border border-gray-700 rounded py-2 px-3 text-white focus:outline-none focus:border-accent-gold" placeholder="Enter title (optional)">
                            @error('title') <span class="text-accent-red text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        @if($type == 'video')
                            <div>
                                <label class="block text-gray-300 text-sm font-bold mb-2">YouTube URL (Preferred for videos)</label>
                                <input type="url" wire:model="video_url" class="w-full bg-primary-bg border border-gray-700 rounded py-2 px-3 text-white focus:outline-none focus:border-accent-gold" placeholder="https://youtube.com/...">
                                @error('video_url') <span class="text-accent-red text-xs">{{ $message }}</span> @enderror
                                <p class="text-xs text-gray-400 mt-1">If you provide a YouTube URL, file upload will be ignored.</p>
                            </div>
                        @endif

                        <div>
                            <label class="block text-gray-300 text-sm font-bold mb-2">Upload File {{ $type == 'video' ? '(Max 40MB)' : '' }}</label>
                            <input type="file" wire:model="file_path" class="w-full bg-primary-bg border border-gray-700 rounded py-2 px-3 text-white focus:outline-none focus:border-accent-gold">
                            @error('file_path') <span class="text-accent-red text-xs">{{ $message }}</span> @enderror
                            
                            <div wire:loading wire:target="file_path" class="text-accent-gold text-sm mt-2">
                                Uploading... Please wait.
                            </div>

                            @if($existing_file && !$file_path && $type == 'photo')
                                <div class="mt-2 text-xs text-gray-400">Current File: {{ $existing_file }}</div>
                                <img src="{{ asset('storage/' . $existing_file) }}" class="mt-2 w-32 h-20 object-cover rounded">
                            @endif
                        </div>

                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" wire:click="closeModal" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded transition">Cancel</button>
                        <button type="submit" class="bg-accent-gold hover:bg-yellow-400 text-primary-bg px-4 py-2 rounded font-semibold transition" wire:loading.attr="disabled">Save Media</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
