<x-ipl-layout>
    <!-- Hero Section / Featured (Dynamic Swiper Slider) -->
    @if(isset($banners) && $banners->count() > 0)
        <!-- Include Swiper's CSS -->
        <link rel="stylesheet" href="{{ asset('css/swiper-bundle.min.css') }}" />
        
        <div class="swiper myHeroSwiper relative w-full h-[500px] md:h-[600px] bg-[#0B0F19] overflow-hidden">
            <div class="swiper-wrapper">
                @foreach($banners as $banner)
                <div class="swiper-slide relative w-full h-full flex justify-center items-center">
                    <!-- Blurred Background (fills empty space with matching colors) -->
                    <div class="absolute inset-0 bg-cover bg-center blur-2xl opacity-40" style="background-image: url('{{ asset('storage/' . $banner->image_path) }}');"></div>
                    
                    <!-- Banner Image (dictates the height of the slide) -->
                    <img src="{{ asset('storage/' . $banner->image_path) }}" alt="Banner" class="relative z-10 w-full h-full object-contain">
                    
                    <!-- Overlay -->
                    <div class="absolute inset-0 z-20 bg-gradient-to-t from-[#0B0F19] via-[#0B0F19]/40 to-transparent pointer-events-none"></div>
                    
                    <!-- Content -->
                    <div class="absolute inset-0 z-30 flex flex-col justify-end p-6 pb-12 md:p-16 md:pb-16 pointer-events-none">
                        <div class="max-w-[1400px] mx-auto w-full pointer-events-auto">
                            @if($banner->title)
                                <h1 class="text-2xl md:text-5xl lg:text-6xl text-white heading-font uppercase leading-tight max-w-4xl drop-shadow-lg">
                                    {{ $banner->title }}
                                </h1>
                            @endif
                            @if($banner->description)
                                <p class="text-gray-300 mt-2 md:mt-4 max-w-2xl text-sm md:text-lg hidden md:block drop-shadow-md">
                                    {{ $banner->description }}
                                </p>
                            @endif
                            @if($banner->link)
                                <div class="mt-4 md:mt-8 flex space-x-4 relative" style="z-index: 999; pointer-events: auto;">
                                    <a href="{{ $banner->link }}" target="_blank" class="animate-pulse bg-black text-white border border-[#FFC800]/50 px-6 py-2 md:px-8 md:py-3 font-bold uppercase hover:border-[#FFC800] hover:bg-gray-900 hover:animate-none transition-colors rounded-sm inline-block cursor-pointer shadow-[0_4px_15px_rgba(0,0,0,0.5)]" style="pointer-events: auto;">{{ $banner->button_name ?: 'Learn More' }}</a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <!-- Add Pagination -->
            <div class="swiper-pagination"></div>
            <!-- Add Navigation -->
            <div class="swiper-button-next !text-[#FFC800]"></div>
            <div class="swiper-button-prev !text-[#FFC800]"></div>
        </div>

        <!-- Include Swiper's JS -->
        <script src="{{ asset('js/swiper-bundle.min.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var swiper = new Swiper(".myHeroSwiper", {
                    loop: {{ $banners->count() > 1 ? 'true' : 'false' }},
                    autoplay: {
                        delay: 5000,
                        disableOnInteraction: false,
                    },
                    pagination: {
                        el: ".swiper-pagination",
                        clickable: true,
                    },
                    navigation: {
                        nextEl: ".swiper-button-next",
                        prevEl: ".swiper-button-prev",
                    },
                });
            });
        </script>
    @else
        <!-- Hero Section / Featured (Static Fallback) -->
        <div class="relative w-full h-[500px] md:h-[600px] bg-gray-900 overflow-hidden">
            <!-- Hero Background Placeholder (Gradient/Pattern) -->
            <div class="absolute inset-0 bg-gradient-to-r from-[#0B0F19] to-[#141B2D]">
                <div class="absolute inset-0 opacity-20 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGcgc3Ryb2tlPSIjZmZmIiBzdHJva2Utd2lkdGg9IjEiIGZpbGw9Im5vbmUiIGZpbGwtcnVsZT0iZXZlbm9kZCI+PHBhdGggZD0iTTAgNDBoNDBWMEgweiIvPjwvZz48L3N2Zz4=')]"></div>
            </div>
            
            <!-- Hero Content (Mock Featured News) -->
            <div class="absolute bottom-0 left-0 w-full p-8 md:p-16 bg-gradient-to-t from-[#0B0F19] via-[#0B0F19]/80 to-transparent">
                <div class="max-w-[1400px] mx-auto">
                    <span class="inline-block bg-[#FFC800] text-black text-xs font-bold px-3 py-1 uppercase tracking-wider mb-4 rounded-sm">Featured</span>
                    <h1 class="text-4xl md:text-6xl text-white heading-font uppercase leading-tight max-w-4xl drop-shadow-lg">
                        The Grand Auction Approaches: Prepare for the Ultimate Showdown
                    </h1>
                    <p class="text-gray-300 mt-4 max-w-2xl text-lg hidden md:block">
                        Franchises are gearing up to rebuild their squads. Who will command the highest bid this season?
                    </p>
                    <div class="mt-8 flex space-x-4">
                        <a href="{{ route('public.players') }}" class="bg-transparent border-2 border-[#FFC800] text-[#FFC800] px-8 py-3 font-bold uppercase hover:bg-[#FFC800] hover:text-black transition-colors rounded-sm">View Players</a>
                    </div>
                </div>
            </div>
        </div>
    @endif


    <!-- Sponsors & Partners Section -->
    @if(isset($sponsors) && $sponsors->count() > 0)
        <div class="bg-[#0B0F19] py-12 border-b border-white/5">
            <div class="max-w-[1400px] mx-auto px-4 md:px-8 text-center">
                
                @php
                    $titleSponsors = $sponsors->where('type', 'title_sponsor');
                    $premierPartners = $sponsors->where('type', 'premier_partner');
                    $generalSponsors = $sponsors->where('type', 'sponsor');
                @endphp

                <!-- Title Sponsor -->
                @if($titleSponsors->count() > 0)
                    <div class="mb-12">
                        <h2 class="text-sm font-bold text-[#FFC800] uppercase tracking-widest mb-6">Title Sponsor</h2>
                        <div class="flex justify-center items-center flex-wrap gap-8">
                            @foreach($titleSponsors as $sponsor)
                                <a href="{{ $sponsor->url ?? '#' }}" target="{{ $sponsor->url ? '_blank' : '_self' }}" class="group block bg-white p-4 rounded-xl shadow-lg border border-gray-700 hover:border-[#FFC800] transition-all transform hover:-translate-y-1">
                                    @if($sponsor->logo_path)
                                        <img src="{{ asset('storage/' . $sponsor->logo_path) }}" alt="{{ $sponsor->name }}" class="h-24 md:h-32 object-contain w-auto max-w-[200px] md:max-w-[250px]">
                                    @else
                                        <span class="text-2xl font-bold text-gray-800">{{ $sponsor->name }}</span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Premier Partners -->
                @if($premierPartners->count() > 0)
                    <div class="mb-12">
                        <h2 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-6">Premier Partners</h2>
                        <div class="flex justify-center items-center flex-wrap gap-6">
                            @foreach($premierPartners as $sponsor)
                                <a href="{{ $sponsor->url ?? '#' }}" target="{{ $sponsor->url ? '_blank' : '_self' }}" class="group block bg-white p-3 rounded-lg shadow-md border border-gray-800 hover:border-blue-400 transition-all transform hover:-translate-y-1">
                                    @if($sponsor->logo_path)
                                        <img src="{{ asset('storage/' . $sponsor->logo_path) }}" alt="{{ $sponsor->name }}" class="h-16 md:h-20 object-contain w-auto max-w-[150px] md:max-w-[180px]">
                                    @else
                                        <span class="text-xl font-bold text-gray-800">{{ $sponsor->name }}</span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- General Sponsors -->
                @if($generalSponsors->count() > 0)
                    <div>
                        <h2 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-4">Sponsors & Partners</h2>
                        <div class="flex justify-center items-center flex-wrap gap-4">
                            @foreach($generalSponsors as $sponsor)
                                <a href="{{ $sponsor->url ?? '#' }}" target="{{ $sponsor->url ? '_blank' : '_self' }}" class="group block bg-gray-50 p-2 rounded shadow border border-gray-200 hover:border-gray-400 transition-all opacity-80 hover:opacity-100">
                                    @if($sponsor->logo_path)
                                        <img src="{{ asset('storage/' . $sponsor->logo_path) }}" alt="{{ $sponsor->name }}" class="h-10 md:h-12 object-contain w-auto max-w-[100px] md:max-w-[120px]">
                                    @else
                                        <span class="text-sm font-bold text-gray-600">{{ $sponsor->name }}</span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Core Committee Section -->
    @if(isset($coreCommittees) && $coreCommittees->count() > 0)
        <div class="bg-[#141B2D] py-12 border-b border-white/5">
            <div class="max-w-[1400px] mx-auto px-4 md:px-8">
                <div class="flex justify-between items-end mb-8 border-b border-gray-800 pb-4">
                    <h2 class="text-3xl md:text-4xl heading-font uppercase text-white flex items-center">
                        <span class="w-2 h-8 bg-[#FFC800] mr-3 block"></span> Core Committee
                    </h2>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6">
                    @foreach($coreCommittees as $member)
                        <div class="group relative block w-full rounded-lg overflow-hidden bg-gray-900 border border-white/5 hover:border-[#FFC800]/50 transition-colors shadow-lg">
                            <div class="relative bg-[#0B0F19] overflow-hidden" style="padding-bottom: 100%;">
                                @if($member->image_path)
                                    <img src="{{ asset('storage/' . $member->image_path) }}" alt="{{ $member->name }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-gray-800 to-gray-900">
                                        <span class="text-4xl font-bold text-gray-600 uppercase">{{ substr($member->name, 0, 1) }}</span>
                                    </div>
                                @endif
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            </div>
                            <div class="p-4 bg-[#0B0F19]/95 backdrop-blur-sm border-t border-white/10 text-center">
                                <h3 class="text-white font-bold text-base md:text-lg leading-tight line-clamp-1 drop-shadow-md group-hover:text-[#FFC800] transition-colors uppercase">{{ $member->name }}</h3>
                                @if($member->role)
                                    <span class="text-[10px] font-bold px-2 py-1 rounded mt-2 inline-block bg-[#FFC800] text-black uppercase tracking-wider shadow-md">
                                        {{ $member->role }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Our Mission & Vision Section -->
    @php
        $mission = setting('our_mission');
        $vision = setting('our_vision');
    @endphp
    @if($mission || $vision)
        <div class="bg-[#141B2D] py-16 border-b border-white/5 relative overflow-hidden">
            <!-- Background Elements -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-[#FFC800]/5 rounded-full blur-3xl transform translate-x-1/2 -translate-y-1/2 pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-blue-500/5 rounded-full blur-3xl transform -translate-x-1/2 translate-y-1/2 pointer-events-none"></div>
            
            <div class="max-w-[1400px] mx-auto px-4 md:px-8 relative z-10">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-12 md:gap-16">
                    <!-- Mission -->
                    @if($mission)
                        <div class="group bg-[#0B0F19] rounded-2xl p-8 md:p-10 border border-white/5 hover:border-[#FFC800]/30 transition-all duration-300 shadow-xl hover:shadow-[0_0_30px_rgba(255,200,0,0.1)] relative">
                            <div class="absolute -top-6 -left-6 w-20 h-20 bg-[#FFC800]/10 rounded-full blur-2xl group-hover:bg-[#FFC800]/20 transition-all"></div>
                            
                            <div class="flex items-center mb-6 relative">
                                <div class="w-14 h-14 rounded-full bg-gradient-to-br from-[#FFC800] to-[#D4A000] flex items-center justify-center mr-5 shadow-[0_0_15px_rgba(255,200,0,0.3)]">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                                <h2 class="text-3xl heading-font uppercase text-white tracking-wide">Our Mission</h2>
                            </div>
                            
                            <div class="text-gray-300 text-lg leading-relaxed relative z-10">
                                {!! nl2br(e($mission)) !!}
                            </div>
                        </div>
                    @endif
                    
                    <!-- Vision -->
                    @if($vision)
                        <div class="group bg-[#0B0F19] rounded-2xl p-8 md:p-10 border border-white/5 hover:border-blue-400/30 transition-all duration-300 shadow-xl hover:shadow-[0_0_30px_rgba(59,130,246,0.1)] relative mt-8 md:mt-12 lg:mt-0">
                            <div class="absolute -top-6 -right-6 w-20 h-20 bg-blue-500/10 rounded-full blur-2xl group-hover:bg-blue-500/20 transition-all"></div>
                            
                            <div class="flex items-center mb-6 relative">
                                <div class="w-14 h-14 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center mr-5 shadow-[0_0_15px_rgba(59,130,246,0.3)]">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </div>
                                <h2 class="text-3xl heading-font uppercase text-white tracking-wide">Our Vision</h2>
                            </div>
                            
                            <div class="text-gray-300 text-lg leading-relaxed relative z-10">
                                {!! nl2br(e($vision)) !!}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Teams Grid -->
    @if(isset($teams) && $teams->count() > 0)
        <div class="bg-[#0B0F19] py-12">
            <div class="max-w-[1400px] mx-auto px-4 md:px-8">
                <div class="flex justify-between items-end mb-8 border-b border-gray-800 pb-4">
                    <h2 class="text-3xl md:text-4xl heading-font uppercase text-white flex items-center">
                        <span class="w-2 h-8 bg-[#FFC800] mr-3 block"></span> All Teams
                    </h2>
                    <a href="{{ route('public.teams') }}" class="text-xs md:text-sm font-bold text-gray-400 hover:text-[#FFC800] uppercase transition-colors">View All</a>
                </div>
                
                <div class="swiper teamsSwiper overflow-hidden" style="padding-bottom: 10px;">
                    <div class="swiper-wrapper">
                        @foreach($teams as $team)
                            <div class="swiper-slide">
                                <a href="{{ route('public.teams.show', $team->id) }}" class="block w-full group cursor-pointer relative rounded-lg overflow-hidden bg-gray-900 border border-white/5 hover:border-[#FFC800]/50 transition-colors shadow-lg" style="padding-bottom: 75%;">
                                    <div class="absolute inset-0 bg-[#0B0F19] flex items-center justify-center p-0 group-hover:scale-105 transition-transform duration-500">
                                        @if($team->logo)
                                            <img src="{{ asset('storage/' . $team->logo) }}" alt="{{ $team->name }}" class="w-full h-full object-cover object-top">
                                        @else
                                            <span class="text-[#FFC800] font-bold text-6xl uppercase">{{ substr($team->name, 0, 2) }}</span>
                                        @endif
                                    </div>

                                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                    
                                    <div class="absolute bottom-0 left-0 w-full p-4 z-40 bg-[#0B0F19]/95 backdrop-blur-sm border-t border-white/10 transition-colors group-hover:bg-[#141B2D]/95">
                                        <span class="text-[10px] font-bold px-2 py-1 rounded mb-1 inline-block bg-[#FFC800] text-black uppercase tracking-wider shadow-md">
                                            Team
                                        </span>
                                        <h3 class="text-white font-bold text-base md:text-lg leading-tight line-clamp-1 drop-shadow-md group-hover:text-[#FFC800] transition-colors">{{ $team->name }}</h3>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var teamsSwiper = new Swiper(".teamsSwiper", {
                    slidesPerView: 1.5,
                    spaceBetween: 15,
                    autoplay: {
                        delay: 2500,
                        disableOnInteraction: false,
                    },
                    loop: true,
                    breakpoints: {
                        640: { slidesPerView: 2, spaceBetween: 20 },
                        768: { slidesPerView: 3, spaceBetween: 20 },
                        1024: { slidesPerView: 4, spaceBetween: 24 },
                    },
                });
            });
        </script>
    @endif

    <!-- Players Slider -->
    @if(isset($players) && $players->count() > 0)
        <div class="bg-[#141B2D] py-12 border-y border-white/5">
            <div class="max-w-[1400px] mx-auto px-4 md:px-8">
                <div class="flex justify-between items-end mb-8 border-b border-gray-800 pb-4">
                    <h2 class="text-3xl md:text-4xl heading-font uppercase text-white flex items-center">
                        <span class="w-2 h-8 bg-[#FFC800] mr-3 block"></span> Featured Players
                    </h2>
                    <a href="{{ route('public.players') }}" class="text-xs md:text-sm font-bold text-gray-400 hover:text-[#FFC800] uppercase transition-colors">View All</a>
                </div>
                
                @if(!isset($banners) || $banners->count() == 0)
                    <link rel="stylesheet" href="{{ asset('css/swiper-bundle.min.css') }}" />
                    <script src="{{ asset('js/swiper-bundle.min.js') }}"></script>
                @endif

                <div class="swiper playersSwiper overflow-hidden" style="padding-bottom: 10px;">
                    <div class="swiper-wrapper">
                        @foreach($players as $player)
                            <div class="swiper-slide">
                                <div class="group relative block w-full rounded-lg overflow-hidden bg-gray-900 border border-white/5 hover:border-[#FFC800]/50 transition-colors shadow-lg cursor-pointer" style="padding-bottom: 75%;" onclick="@if($player->photo) openMediaModal('{{ asset('storage/' . $player->photo) }}', 'image') @endif">
                                    <div class="absolute inset-0 bg-[#0B0F19] flex items-center justify-center p-0 group-hover:scale-105 transition-transform duration-500">
                                        @if($player->photo)
                                            <img src="{{ asset('storage/' . $player->photo) }}" alt="{{ $player->name }}" class="w-full h-full object-contain">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-800 to-gray-900">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                    
                                    <div class="absolute bottom-0 left-0 w-full p-4 z-40 bg-[#0B0F19]/95 backdrop-blur-sm border-t border-white/10 transition-colors group-hover:bg-[#141B2D]/95">
                                        <span class="text-[10px] font-bold px-2 py-1 rounded mb-1 inline-block bg-[#FFC800] text-black uppercase tracking-wider shadow-md">
                                            {{ $player->role ?? 'Player' }}
                                        </span>
                                        <h3 class="text-white font-bold text-base md:text-lg leading-tight line-clamp-1 drop-shadow-md group-hover:text-[#FFC800] transition-colors uppercase">{{ $player->name }}</h3>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var playersSwiper = new Swiper(".playersSwiper", {
                    slidesPerView: 1.5,
                    spaceBetween: 15,
                    autoplay: {
                        delay: 3000,
                        disableOnInteraction: false,
                    },
                    loop: true,
                    breakpoints: {
                        640: { slidesPerView: 2, spaceBetween: 20 },
                        768: { slidesPerView: 3, spaceBetween: 20 },
                        1024: { slidesPerView: 4, spaceBetween: 24 },
                    },
                });
            });
        </script>
    @endif

    <!-- Photos & Videos Gallery -->
    @if(isset($galleries) && $galleries->count() > 0)
        <div class="bg-[#0B0F19] py-12">
            <div class="max-w-[1400px] mx-auto px-4 md:px-8">
                <div class="flex justify-between items-end mb-8 border-b border-gray-800 pb-4">
                    <h2 class="text-3xl md:text-4xl heading-font uppercase text-white flex items-center">
                        <span class="w-2 h-8 bg-[#FFC800] mr-3 block"></span> Photos & Videos
                    </h2>
                </div>
                
                <div class="swiper gallerySwiper overflow-hidden" style="padding-bottom: 10px;">
                    <div class="swiper-wrapper">
                        @foreach($galleries as $media)
                            <div class="swiper-slide">
                                <div class="group block w-full cursor-pointer relative rounded-lg overflow-hidden bg-gray-900 border border-white/5 hover:border-[#FFC800]/50 transition-colors shadow-lg" style="padding-bottom: 75%;">
                                    @if($media->type == 'photo' && $media->file_path)
                                        <img src="{{ asset('storage/' . $media->file_path) }}" alt="{{ $media->title }}" class="absolute inset-0 w-full h-full object-contain group-hover:scale-105 transition-transform duration-500">
                                        <a href="javascript:void(0)" onclick="openMediaModal('{{ asset('storage/' . $media->file_path) }}', 'image')" class="absolute inset-0 z-30"></a>
                                    @elseif($media->type == 'video')
                                        @if($media->video_url)
                                            @php
                                                $videoId = '';
                                                if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $media->video_url, $matches)) {
                                                    $videoId = $matches[1];
                                                }
                                            @endphp
                                            @if($videoId)
                                                <img src="https://img.youtube.com/vi/{{ $videoId }}/maxresdefault.jpg" onerror="this.src='https://img.youtube.com/vi/{{ $videoId }}/hqdefault.jpg'" alt="{{ $media->title }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-80">
                                            @else
                                                <div class="absolute inset-0 w-full h-full bg-gray-800 flex items-center justify-center opacity-80">
                                                    <span class="text-gray-500 font-bold uppercase">Video</span>
                                                </div>
                                            @endif
                                            
                                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-20">
                                                <div class="w-14 h-14 rounded-full bg-red-600 flex items-center justify-center shadow-lg border-2 border-white/20 group-hover:bg-red-500 transition-colors group-hover:scale-110 duration-300">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                                </div>
                                            </div>
                                            <a href="javascript:void(0)" onclick="openMediaModal('{{ $videoId }}', 'youtube')" class="absolute inset-0 z-30"></a>
                                        @elseif($media->file_path)
                                            <video class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-80" muted onmouseover="this.play()" onmouseout="this.pause()">
                                                <source src="{{ asset('storage/' . $media->file_path) }}" type="video/mp4">
                                            </video>
                                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-20">
                                                <div class="w-14 h-14 rounded-full bg-black/60 backdrop-blur flex items-center justify-center shadow-lg border-2 border-white/20 group-hover:bg-[#FFC800] transition-colors group-hover:scale-110 duration-300">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white group-hover:text-black transition-colors ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                                </div>
                                            </div>
                                            <a href="javascript:void(0)" onclick="openMediaModal('{{ asset('storage/' . $media->file_path) }}', 'video')" class="absolute inset-0 z-30"></a>
                                        @endif
                                    @endif
                                    
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-20"></div>

                                    @if($media->title)
                                    <div class="absolute bottom-0 left-0 w-full p-4 z-40 bg-[#0B0F19]/95 backdrop-blur-sm border-t border-white/10 transition-colors group-hover:bg-[#141B2D]/95 pointer-events-none">
                                        <span class="text-[10px] font-bold px-2 py-1 rounded mb-1 inline-block {{ $media->type == 'video' ? 'bg-red-600 text-white' : 'bg-[#FFC800] text-black' }} uppercase tracking-wider shadow-md">
                                            {{ ucfirst($media->type) }}
                                        </span>
                                        <h3 class="text-white font-bold text-base md:text-lg leading-tight line-clamp-1 drop-shadow-md group-hover:text-[#FFC800] transition-colors">{{ $media->title }}</h3>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var gallerySwiper = new Swiper(".gallerySwiper", {
                    slidesPerView: 1.5,
                    spaceBetween: 15,
                    autoplay: {
                        delay: 3500,
                        disableOnInteraction: false,
                    },
                    loop: true,
                    breakpoints: {
                        640: { slidesPerView: 2, spaceBetween: 20 },
                        768: { slidesPerView: 3, spaceBetween: 20 },
                        1024: { slidesPerView: 4, spaceBetween: 24 },
                    },
                });
            });
        </script>
    @endif
   
    <!-- Universal Media Modal -->
    <div id="universalMediaModal" class="fixed inset-0 hidden items-center justify-center bg-black/95 p-4 md:p-8 backdrop-blur-md transition-opacity opacity-0" style="transition: opacity 0.3s ease; z-index: 9999;">
        <button onclick="closeMediaModal()" style="position: absolute; top: 20px; right: 20px; z-index: 10000; width: 50px; height: 50px; background: rgba(255,200,0,0.8); border-radius: 50%; color: black; border: 2px solid #FFC800; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 4px 6px rgba(0,0,0,0.3); transition: all 0.2s;" onmouseover="this.style.background='#FFC800'; this.style.transform='scale(1.1)';" onmouseout="this.style.background='rgba(255,200,0,0.8)'; this.style.transform='scale(1)';">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
        <div id="modalMediaContainer" class="w-full max-w-5xl h-full max-h-[80vh] flex items-center justify-center transform scale-95 transition-transform duration-300">
            <!-- Content gets injected here -->
        </div>
    </div>

    <script>
        function openMediaModal(url, type = 'image') {
            const modal = document.getElementById('universalMediaModal');
            const container = document.getElementById('modalMediaContainer');
            
            // Clear existing content
            container.innerHTML = '';
            
            if (type === 'image') {
                container.innerHTML = `
                <div class="w-full h-full bg-[#0B0F19]/90 backdrop-blur-md rounded-2xl shadow-[0_0_40px_rgba(255,200,0,0.2)] border border-[#FFC800]/20 flex items-center justify-center p-2 md:p-6 overflow-hidden">
                    <img src="${url}" class="max-w-full max-h-full object-contain rounded-lg drop-shadow-2xl">
                </div>`;
            } else if (type === 'youtube') {
                container.innerHTML = `
                <div class="w-full h-full bg-[#0B0F19]/90 backdrop-blur-md rounded-2xl shadow-[0_0_40px_rgba(255,200,0,0.2)] border border-[#FFC800]/20 flex items-center justify-center p-2 md:p-6 overflow-hidden">
                    <iframe src="https://www.youtube.com/embed/${url}?autoplay=1" class="w-full h-full rounded-xl" style="aspect-ratio: 16/9; max-width: 100%; max-height: 100%;" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>`;
            } else if (type === 'video') {
                container.innerHTML = `
                <div class="w-full h-full bg-[#0B0F19]/90 backdrop-blur-md rounded-2xl shadow-[0_0_40px_rgba(255,200,0,0.2)] border border-[#FFC800]/20 flex items-center justify-center p-2 md:p-6 overflow-hidden">
                    <video src="${url}" class="max-w-full max-h-full object-contain rounded-xl drop-shadow-2xl" controls autoplay></video>
                </div>`;
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            // Force reflow
            void modal.offsetWidth;
            
            modal.classList.remove('opacity-0');
            container.classList.remove('scale-95');
            container.classList.add('scale-100');
            
            // Prevent scrolling on body
            document.body.style.overflow = 'hidden';
        }
        
        function closeMediaModal() {
            const modal = document.getElementById('universalMediaModal');
            const container = document.getElementById('modalMediaContainer');
            
            modal.classList.add('opacity-0');
            container.classList.remove('scale-100');
            container.classList.add('scale-95');
            
            // Wait for transition
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                container.innerHTML = ''; // Stop video/iframe playback
                document.body.style.overflow = '';
            }, 300);
        }
        
        // Close modal on Escape key or outside click
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
</x-ipl-layout>
