<div>
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h1 class="text-3xl font-poppins font-bold text-white">Manage Gallery</h1>
        <div class="flex space-x-3">
            <button wire:click="openBulkModal" class="bg-success-green text-white px-4 py-2 rounded font-semibold hover:bg-green-500 transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Bulk Upload Photos
            </button>
            <button wire:click="create" class="bg-accent-gold text-primary-bg px-4 py-2 rounded font-semibold hover:bg-yellow-400 transition">Add New Media</button>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="bg-success-green text-white px-4 py-3 rounded mb-4 shadow">
            {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($galleries as $gallery)
            <div class="bg-card-bg rounded-lg shadow overflow-hidden flex flex-col h-full border border-gray-800 hover:border-gray-700 transition">
                <!-- Media box with locked fixed height -->
                <div class="relative w-full bg-gray-900 overflow-hidden flex-shrink-0" style="height: 200px; min-height: 200px; max-height: 200px;">
                    @if($gallery->type == 'photo' && $gallery->file_path)
                        <img src="{{ asset('storage/' . $gallery->file_path) }}" alt="{{ $gallery->title }}" class="w-full h-full object-cover object-center block">
                    @elseif($gallery->type == 'video')
                        @if($gallery->video_url)
                            @php
                                $videoId = '';
                                if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $gallery->video_url, $matches)) {
                                    $videoId = $matches[1];
                                }
                            @endphp
                            @if($videoId)
                                <img src="https://img.youtube.com/vi/{{ $videoId }}/hqdefault.jpg" alt="{{ $gallery->title }}" class="w-full h-full object-cover object-center opacity-70">
                            @endif
                            <div class="absolute inset-0 bg-black/40 flex flex-col items-center justify-center text-red-500">
                                <svg class="w-12 h-12 mb-1 drop-shadow" fill="currentColor" viewBox="0 0 24 24"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/></svg>
                                <span class="text-xs font-bold text-white drop-shadow">YouTube Video</span>
                            </div>
                        @elseif($gallery->file_path)
                            <video class="w-full h-full object-cover block" controls preload="metadata">
                                <source src="{{ asset('storage/' . $gallery->file_path) }}" type="video/mp4">
                            </video>
                        @endif
                    @endif
                    <div class="absolute top-2 right-2 bg-black/70 backdrop-blur-sm px-2.5 py-1 rounded text-xs font-bold text-white uppercase tracking-wider z-10 border border-white/10">
                        {{ $gallery->type }}
                    </div>
                </div>
                <div class="p-4 flex-1 flex flex-col justify-between">
                    <h2 class="text-base font-poppins font-bold text-white line-clamp-2 mb-3" title="{{ $gallery->title }}">{{ $gallery->title ?: 'Untitled' }}</h2>
                    <div class="mt-auto flex justify-between items-center pt-3 border-t border-gray-700/80">
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

    <!-- Single Item Modal -->
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

    <!-- Bulk Upload Modal -->
    @if($isBulkModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 overflow-y-auto">
            <div class="bg-card-bg rounded-lg w-full max-w-2xl p-6 my-8 mx-4">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-poppins font-bold text-white flex items-center gap-3">
                        <svg class="w-7 h-7 text-accent-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Bulk Upload Photos
                    </h2>
                    <button wire:click="closeBulkModal" class="text-gray-400 hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form wire:submit.prevent="saveBulkPhotos">
                    <div class="space-y-4">
                        <!-- Drop Zone / File Input -->
                        <div class="border-2 border-dashed border-gray-600 rounded-xl p-8 text-center hover:border-accent-gold transition-colors duration-300 bg-primary-bg/50">
                            <div class="flex flex-col items-center">
                                <svg class="w-16 h-16 text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <p class="text-white font-bold text-lg mb-2">Select Multiple Photos</p>
                                <p class="text-gray-400 text-sm mb-4">Supports JPEG, PNG, JPG, WebP — Max 10MB per photo</p>
                                <input type="file" wire:model="bulk_photos" multiple accept="image/jpeg,image/png,image/jpg,image/webp"
                                       class="w-full max-w-md bg-primary-bg border border-gray-700 rounded py-2 px-3 text-white focus:outline-none focus:border-accent-gold file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-accent-gold file:text-primary-bg hover:file:bg-yellow-400">
                            </div>
                        </div>

                        @error('bulk_photos') <span class="text-accent-red text-sm">{{ $message }}</span> @enderror
                        @error('bulk_photos.*') <span class="text-accent-red text-sm">{{ $message }}</span> @enderror

                        <!-- Upload Progress -->
                        <div wire:loading wire:target="bulk_photos" class="bg-primary-bg rounded-lg p-4">
                            <div class="flex items-center gap-3">
                                <svg class="animate-spin h-5 w-5 text-accent-gold" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span class="text-accent-gold font-semibold">Uploading photos... Please wait, do not close this window.</span>
                            </div>
                        </div>

                        <!-- Preview Selected Files -->
                        @if($bulk_photos && count($bulk_photos) > 0)
                            <div class="bg-primary-bg rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-white font-bold text-sm">{{ count($bulk_photos) }} photo(s) selected</span>
                                    <span class="text-gray-400 text-xs">All photos will be converted to WebP format</span>
                                </div>
                                <div class="grid grid-cols-4 sm:grid-cols-6 gap-2 max-h-48 overflow-y-auto">
                                    @foreach($bulk_photos as $index => $photo)
                                        <div class="relative aspect-square rounded-lg overflow-hidden bg-gray-800 border border-gray-700">
                                            <img src="{{ $photo->temporaryUrl() }}" class="w-full h-full object-cover" alt="Preview {{ $index + 1 }}">
                                            <div class="absolute bottom-0 left-0 right-0 bg-black/70 text-center py-0.5">
                                                <span class="text-white text-[10px] font-bold">{{ $index + 1 }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Info Note -->
                        <div class="bg-blue-900/30 border border-blue-700/50 rounded-lg p-3 flex items-start gap-3">
                            <svg class="w-5 h-5 text-blue-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div class="text-blue-300 text-sm">
                                <p class="font-semibold mb-1">How it works:</p>
                                <ul class="text-blue-300/80 text-xs space-y-1 list-disc list-inside">
                                    <li>All photos will be automatically resized (max 800x800px) and optimized to WebP.</li>
                                    <li>Photos will be uploaded without titles — you can edit titles later individually.</li>
                                    <li>All photos will be set as active by default.</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" wire:click="closeBulkModal" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded transition">Cancel</button>
                        <button type="submit" class="bg-success-green hover:bg-green-500 text-white px-6 py-2 rounded font-semibold transition flex items-center gap-2" 
                                wire:loading.attr="disabled" wire:target="saveBulkPhotos">
                            <span wire:loading.remove wire:target="saveBulkPhotos">
                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                Upload All Photos
                            </span>
                            <span wire:loading wire:target="saveBulkPhotos" class="flex items-center gap-2">
                                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Processing...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
