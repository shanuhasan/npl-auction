@section('seo')
    <title>Photos & Videos Gallery | {{ setting('app_name', 'NPLT20') }}</title>
    <meta name="description" content="Browse all photos and videos from the {{ setting('app_name', 'Naugawan Premier League') }}. Match highlights, event photos, and exclusive behind-the-scenes content.">
@endsection

<div>
    {{-- Gallery Data for Lightbox --}}
    @php $galleryIndex = 0; @endphp
    <script>
        window.allGalleryItems = [
            @foreach($allGalleries as $media)
                @php
                    $mediaUrl = '';
                    $mediaType = 'image';
                    if ($media->type == 'photo' && $media->file_path) {
                        $mediaUrl = asset('storage/' . $media->file_path);
                        $mediaType = 'image';
                    } elseif ($media->type == 'video') {
                        if ($media->video_url) {
                            if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $media->video_url, $matches)) {
                                $mediaUrl = $matches[1];
                            }
                            $mediaType = 'youtube';
                        } elseif ($media->file_path) {
                            $mediaUrl = asset('storage/' . $media->file_path);
                            $mediaType = 'video';
                        }
                    }
                @endphp
                @if($mediaUrl)
                {
                    url: '{{ $mediaUrl }}',
                    type: '{{ $mediaType }}',
                    title: '{!! addslashes($media->title) !!}',
                    subtitle: '{{ ucfirst($media->type) }}'
                },
                @endif
            @endforeach
        ];
    </script>

    <div class="min-h-screen bg-[#0B0F19]">
        {{-- Hero Banner --}}
        <div class="relative bg-gradient-to-b from-[#141B2D] to-[#0B0F19] py-16 md:py-20">
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg%20width%3D%2260%22%20height%3D%2260%22%20viewBox%3D%220%200%2060%2060%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cg%20fill%3D%22none%22%20fill-rule%3D%22evenodd%22%3E%3Cg%20fill%3D%22%23ffffff%22%20fill-opacity%3D%220.02%22%3E%3Ccircle%20cx%3D%2230%22%20cy%3D%2230%22%20r%3D%221%22%2F%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E')] opacity-50"></div>
            </div>
            <div class="max-w-[1400px] mx-auto px-4 md:px-8 relative z-10">
                <div class="text-center">
                    <h1 class="text-4xl md:text-6xl font-black text-white uppercase tracking-widest mb-4 heading-font">
                        Photos & Videos
                    </h1>
                    <div class="w-24 h-1 bg-[#FFC800] mx-auto mb-4"></div>
                    <p class="text-gray-400 text-lg max-w-2xl mx-auto">
                        Relive the best moments — match highlights, event photos, and exclusive behind-the-scenes content.
                    </p>
                </div>
            </div>
        </div>

        {{-- Filter Tabs --}}
        <div class="max-w-[1400px] mx-auto px-4 md:px-8">
            <div class="flex items-center justify-center gap-3 mb-10 -mt-4">
                <button wire:click="setFilter('all')" 
                    class="px-6 py-2.5 rounded-full text-sm font-bold uppercase tracking-wider transition-all duration-300 border-2 {{ $filter === 'all' ? 'bg-[#FFC800] text-black border-[#FFC800] shadow-lg shadow-[#FFC800]/20' : 'bg-transparent text-gray-400 border-gray-700 hover:border-[#FFC800]/50 hover:text-white' }}">
                    <svg class="w-4 h-4 inline mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    All
                </button>
                <button wire:click="setFilter('photo')" 
                    class="px-6 py-2.5 rounded-full text-sm font-bold uppercase tracking-wider transition-all duration-300 border-2 {{ $filter === 'photo' ? 'bg-[#FFC800] text-black border-[#FFC800] shadow-lg shadow-[#FFC800]/20' : 'bg-transparent text-gray-400 border-gray-700 hover:border-[#FFC800]/50 hover:text-white' }}">
                    <svg class="w-4 h-4 inline mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Photos
                </button>
                <button wire:click="setFilter('video')" 
                    class="px-6 py-2.5 rounded-full text-sm font-bold uppercase tracking-wider transition-all duration-300 border-2 {{ $filter === 'video' ? 'bg-[#FFC800] text-black border-[#FFC800] shadow-lg shadow-[#FFC800]/20' : 'bg-transparent text-gray-400 border-gray-700 hover:border-[#FFC800]/50 hover:text-white' }}">
                    <svg class="w-4 h-4 inline mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    Videos
                </button>
            </div>

            {{-- Gallery Grid --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6 pb-16" wire:loading.class="opacity-50">
                @forelse($galleries as $media)
                    @php
                        $mediaUrl = '';
                        $mediaType = 'image';
                        if ($media->type == 'photo' && $media->file_path) {
                            $mediaUrl = asset('storage/' . $media->file_path);
                            $mediaType = 'image';
                        } elseif ($media->type == 'video') {
                            if ($media->video_url) {
                                if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $media->video_url, $matches)) {
                                    $mediaUrl = $matches[1];
                                }
                                $mediaType = 'youtube';
                            } elseif ($media->file_path) {
                                $mediaUrl = asset('storage/' . $media->file_path);
                                $mediaType = 'video';
                            }
                        }

                        // Find index in allGalleries for lightbox navigation
                        $lightboxIndex = $allGalleries->search(function($item) use ($media) {
                            return $item->id === $media->id;
                        });
                    @endphp

                    <div class="group relative rounded-xl overflow-hidden bg-gray-900 border border-white/5 hover:border-[#FFC800]/50 transition-all duration-300 cursor-pointer shadow-lg hover:shadow-xl hover:shadow-[#FFC800]/10 hover:-translate-y-1" style="padding-bottom: 100%;">
                        @if($media->type == 'photo' && $media->file_path)
                            <img src="{{ asset('storage/' . $media->file_path) }}" alt="{{ $media->title }}" 
                                 class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            <a href="javascript:void(0)" 
                               onclick="openMediaModal('{{ $mediaUrl }}', '{{ $mediaType }}', '{!! addslashes($media->title) !!}', 'Photo', window.allGalleryItems, {{ $lightboxIndex !== false ? $lightboxIndex : 0 }})" 
                               class="absolute inset-0 z-30"></a>
                        @elseif($media->type == 'video')
                            @if($media->video_url && isset($mediaUrl) && $mediaType === 'youtube')
                                <img src="https://img.youtube.com/vi/{{ $mediaUrl }}/maxresdefault.jpg" 
                                     onerror="this.src='https://img.youtube.com/vi/{{ $mediaUrl }}/hqdefault.jpg'" 
                                     alt="{{ $media->title }}" 
                                     class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 opacity-80">
                                {{-- Play button --}}
                                <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-20">
                                    <div class="w-16 h-16 rounded-full bg-red-600 flex items-center justify-center shadow-xl border-2 border-white/20 group-hover:bg-red-500 transition-all group-hover:scale-110 duration-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                    </div>
                                </div>
                                <a href="javascript:void(0)" 
                                   onclick="openMediaModal('{{ $mediaUrl }}', 'youtube', '{!! addslashes($media->title) !!}', 'Video', window.allGalleryItems, {{ $lightboxIndex !== false ? $lightboxIndex : 0 }})" 
                                   class="absolute inset-0 z-30"></a>
                            @elseif($media->file_path)
                                <video class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 opacity-80" muted preload="metadata"
                                       onmouseover="this.play()" onmouseout="this.pause()">
                                    <source src="{{ asset('storage/' . $media->file_path) }}" type="video/mp4">
                                </video>
                                <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-20">
                                    <div class="w-16 h-16 rounded-full bg-black/60 backdrop-blur flex items-center justify-center shadow-xl border-2 border-white/20 group-hover:bg-[#FFC800] transition-all group-hover:scale-110 duration-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white group-hover:text-black transition-colors ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                    </div>
                                </div>
                                <a href="javascript:void(0)" 
                                   onclick="openMediaModal('{{ $mediaUrl }}', 'video', '{!! addslashes($media->title) !!}', 'Video', window.allGalleryItems, {{ $lightboxIndex !== false ? $lightboxIndex : 0 }})" 
                                   class="absolute inset-0 z-30"></a>
                            @endif
                        @endif

                        {{-- Gradient overlay --}}
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none z-10"></div>

                        {{-- Type badge --}}
                        <div class="absolute top-3 right-3 z-20">
                            <span class="text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider shadow-md {{ $media->type == 'video' ? 'bg-red-600 text-white' : 'bg-[#FFC800] text-black' }}">
                                {{ ucfirst($media->type) }}
                            </span>
                        </div>

                        {{-- Title overlay --}}
                        @if($media->title)
                        <div class="absolute bottom-0 left-0 w-full p-3 md:p-4 z-20 bg-gradient-to-t from-black/90 to-transparent pointer-events-none">
                            <h3 class="text-white font-bold text-sm md:text-base leading-tight line-clamp-2 drop-shadow-lg group-hover:text-[#FFC800] transition-colors">
                                {{ $media->title }}
                            </h3>
                        </div>
                        @endif
                    </div>
                @empty
                    <div class="col-span-full text-center py-20">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-800/50 mb-6 text-gray-500">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-2 heading-font uppercase">No Media Found</h3>
                        <p class="text-gray-400 text-lg">
                            @if($filter !== 'all')
                                No {{ $filter }}s available. Try viewing all media.
                            @else
                                Photos and videos will appear here soon. Stay tuned!
                            @endif
                        </p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($galleries->hasPages())
                <div class="pb-16">
                    {{ $galleries->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Universal Media Modal --}}
    <div id="universalMediaModal" class="fixed inset-0 hidden items-center justify-center bg-black/95 p-4 md:p-8 backdrop-blur-md transition-opacity opacity-0" style="transition: opacity 0.3s ease; z-index: 9999;">
        <button onclick="closeMediaModal()" style="position: absolute; top: 20px; right: 20px; z-index: 10000; width: 50px; height: 50px; background: rgba(255,200,0,0.8); border-radius: 50%; color: black; border: 2px solid #FFC800; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 4px 6px rgba(0,0,0,0.3); transition: all 0.2s;" onmouseover="this.style.background='#FFC800'; this.style.transform='scale(1.1)';" onmouseout="this.style.background='rgba(255,200,0,0.8)'; this.style.transform='scale(1)';">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
        
        <button id="universalMediaPrevBtn" onclick="prevMediaModal(event)" class="hidden absolute top-1/2 -translate-y-1/2 bg-black/60 border border-white/20 hover:border-[#FFC800] hover:bg-[#FFC800] text-white hover:text-black rounded-full items-center justify-center transition-all shadow-[0_4px_10px_rgba(0,0,0,0.5)]" style="z-index: 10000; left: 1rem; width: 3rem; height: 3rem;">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </button>
        <button id="universalMediaNextBtn" onclick="nextMediaModal(event)" class="hidden absolute top-1/2 -translate-y-1/2 bg-black/60 border border-white/20 hover:border-[#FFC800] hover:bg-[#FFC800] text-white hover:text-black rounded-full items-center justify-center transition-all shadow-[0_4px_10px_rgba(0,0,0,0.5)]" style="z-index: 10000; right: 1rem; width: 3rem; height: 3rem;">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>

        <div id="modalMediaContainer" class="w-full max-w-5xl h-full max-h-[80vh] flex items-center justify-center transform scale-95 transition-transform duration-300">
        </div>
    </div>

    @push('scripts')
    <script>
        window.currentGalleryItems = [];
        window.currentGalleryIndex = -1;

        function openMediaModal(url, type = 'image', title = '', subtitle = '', galleryItems = null, index = -1) {
            const modal = document.getElementById('universalMediaModal');
            const container = document.getElementById('modalMediaContainer');
            
            if (galleryItems !== null) {
                window.currentGalleryItems = galleryItems;
                window.currentGalleryIndex = index;
            }
            
            container.innerHTML = '';
            
            const prevBtn = document.getElementById('universalMediaPrevBtn');
            const nextBtn = document.getElementById('universalMediaNextBtn');
            
            if (window.currentGalleryItems && window.currentGalleryItems.length > 1) {
                prevBtn.classList.remove('hidden');
                prevBtn.classList.add('flex');
                nextBtn.classList.remove('hidden');
                nextBtn.classList.add('flex');
            } else {
                prevBtn.classList.add('hidden');
                prevBtn.classList.remove('flex');
                nextBtn.classList.add('hidden');
                nextBtn.classList.remove('flex');
            }
            
            if (type === 'image') {
                let textHtml = '';
                if (title || subtitle) {
                    textHtml = `
                        <div class="absolute bottom-0 left-0 w-full p-4 md:p-5 bg-[#0B0F19]/90 backdrop-blur-md border-t-2 border-[#FFC800] text-center z-50 rounded-b-2xl shadow-[0_-4px_20px_rgba(0,0,0,0.5)]">
                            ${title ? `<h3 class="text-2xl md:text-3xl text-white font-extrabold uppercase mb-1 drop-shadow-lg tracking-wide">${title}</h3>` : ''}
                            ${subtitle ? `<p class="text-[#FFC800] text-sm md:text-lg font-bold uppercase tracking-widest drop-shadow-md">${subtitle}</p>` : ''}
                        </div>
                    `;
                }
                
                container.innerHTML = `
                <div class="relative w-full h-full bg-[#0B0F19]/90 backdrop-blur-md rounded-2xl shadow-[0_0_40px_rgba(255,200,0,0.2)] border border-[#FFC800]/20 flex items-center justify-center p-2 md:p-6 overflow-hidden">
                    <img src="${url}" class="max-w-full max-h-full object-contain rounded-lg drop-shadow-2xl relative z-10">
                    ${textHtml}
                </div>`;
            } else if (type === 'youtube') {
                container.innerHTML = `
                <div class="w-full h-full bg-[#0B0F19]/90 backdrop-blur-md rounded-2xl shadow-[0_0_40px_rgba(255,200,0,0.2)] border border-[#FFC800]/20 flex items-center justify-center p-2 md:p-6 overflow-hidden relative">
                    <iframe src="https://www.youtube.com/embed/${url}?autoplay=1" class="w-full h-full rounded-xl relative z-10" style="aspect-ratio: 16/9; max-width: 100%; max-height: 100%;" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>`;
            } else if (type === 'video') {
                container.innerHTML = `
                <div class="w-full h-full bg-[#0B0F19]/90 backdrop-blur-md rounded-2xl shadow-[0_0_40px_rgba(255,200,0,0.2)] border border-[#FFC800]/20 flex items-center justify-center p-2 md:p-6 overflow-hidden relative">
                    <video src="${url}" class="max-w-full max-h-full object-contain rounded-xl drop-shadow-2xl relative z-10" controls autoplay></video>
                </div>`;
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            void modal.offsetWidth;
            modal.classList.remove('opacity-0');
            container.classList.remove('scale-95');
            container.classList.add('scale-100');
            document.body.style.overflow = 'hidden';
        }
        
        function closeMediaModal() {
            const modal = document.getElementById('universalMediaModal');
            const container = document.getElementById('modalMediaContainer');
            const prevBtn = document.getElementById('universalMediaPrevBtn');
            const nextBtn = document.getElementById('universalMediaNextBtn');
            
            modal.classList.add('opacity-0');
            container.classList.remove('scale-100');
            container.classList.add('scale-95');
            
            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
                container.innerHTML = '';
                document.body.style.overflow = 'auto';
                window.currentGalleryItems = [];
                window.currentGalleryIndex = -1;
                prevBtn.classList.add('hidden');
                prevBtn.classList.remove('flex');
                nextBtn.classList.add('hidden');
                nextBtn.classList.remove('flex');
            }, 300);
        }

        function nextMediaModal(e) {
            if(e) e.stopPropagation();
            if(window.currentGalleryItems && window.currentGalleryItems.length > 0 && window.currentGalleryIndex > -1) {
                window.currentGalleryIndex = (window.currentGalleryIndex + 1) % window.currentGalleryItems.length;
                let nextItem = window.currentGalleryItems[window.currentGalleryIndex];
                openMediaModal(nextItem.url, nextItem.type, nextItem.title, nextItem.subtitle);
            }
        }

        function prevMediaModal(e) {
            if(e) e.stopPropagation();
            if(window.currentGalleryItems && window.currentGalleryItems.length > 0 && window.currentGalleryIndex > -1) {
                window.currentGalleryIndex = (window.currentGalleryIndex - 1 + window.currentGalleryItems.length) % window.currentGalleryItems.length;
                let prevItem = window.currentGalleryItems[window.currentGalleryIndex];
                openMediaModal(prevItem.url, prevItem.type, prevItem.title, prevItem.subtitle);
            }
        }
        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !document.getElementById('universalMediaModal').classList.contains('hidden')) {
                closeMediaModal();
            }
        });
        
        document.getElementById('universalMediaModal').addEventListener('click', function(e) {
            if (e.target === this || e.target.id === 'modalMediaContainer') {
                closeMediaModal();
            }
        });
    </script>
    @endpush
</div>

