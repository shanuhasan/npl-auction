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
                    <div class="absolute inset-0 z-30 flex flex-col justify-end p-6 md:p-16 pointer-events-none">
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
                                <div class="mt-4 md:mt-8 flex space-x-4">
                                    <a href="{{ $banner->link }}" target="_blank" class="bg-black/50 backdrop-blur-sm border-2 border-[#FFC800] text-[#FFC800] px-6 py-2 md:px-8 md:py-3 font-bold uppercase hover:bg-[#FFC800] hover:text-black transition-colors rounded-sm">Learn More</a>
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

    <!-- Teams Slider -->
    @if(isset($teams) && $teams->count() > 0)
        <div class="bg-[#141B2D] border-y border-white/10 py-6">
            <div class="max-w-[1400px] mx-auto px-4 md:px-8">
                <div class="flex justify-between items-end mb-4">
                    <h2 class="text-xl md:text-2xl heading-font uppercase text-[#FFC800] flex items-center">
                        <span class="w-1.5 h-5 bg-[#FFC800] mr-2 block"></span> All Teams
                    </h2>
                    <a href="{{ route('public.teams') }}" class="text-xs md:text-sm font-bold text-gray-400 hover:text-[#FFC800] uppercase transition-colors">View All</a>
                </div>
                
                <!-- Swiper is loaded in the header if banners exist, but if not we should ensure Swiper is loaded. 
                     Since we load Swiper conditionally for banners, let's include it unconditionally if teams or banners exist. -->
                @if(!isset($banners) || $banners->count() == 0)
                    <link rel="stylesheet" href="{{ asset('css/swiper-bundle.min.css') }}" />
                    <script src="{{ asset('js/swiper-bundle.min.js') }}"></script>
                @endif

                <div class="swiper teamsSwiper">
                    <div class="swiper-wrapper">
                        @foreach($teams as $team)
                            <div class="swiper-slide">
                                <a href="{{ route('public.teams.show', $team->id) }}" class="block bg-[#0B0F19] rounded-lg p-4 border border-white/5 hover:border-[#FFC800]/50 transition-colors group text-center flex flex-col items-center">
                                    <div class="w-20 h-20 md:w-24 md:h-24 bg-[#141B2D] rounded-full flex items-center justify-center p-2 mb-3 shadow-lg border border-white/10 group-hover:border-[#FFC800]/50 transition-colors">
                                        @if($team->logo)
                                            <img src="{{ asset('storage/' . $team->logo) }}" alt="{{ $team->name }}" class="w-full h-full object-contain rounded-full">
                                        @else
                                            <span class="text-[#FFC800] font-bold text-xl uppercase">{{ substr($team->name, 0, 2) }}</span>
                                        @endif
                                    </div>
                                    <h3 class="text-white font-bold text-sm md:text-base group-hover:text-[#FFC800] transition-colors truncate w-full">{{ $team->name }}</h3>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var teamSwiper = new Swiper(".teamsSwiper", {
                    slidesPerView: 2,
                    spaceBetween: 10,
                    autoplay: {
                        delay: 3000,
                        disableOnInteraction: false,
                    },
                    loop: true,
                    breakpoints: {
                        640: { slidesPerView: 3, spaceBetween: 20 },
                        768: { slidesPerView: 4, spaceBetween: 20 },
                        1024: { slidesPerView: 6, spaceBetween: 30 },
                        1280: { slidesPerView: 8, spaceBetween: 30 },
                    },
                });
            });
        </script>
    @endif

    <!-- Content Sections -->
    <div class="max-w-[1400px] mx-auto px-4 md:px-8 py-12">
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left Column: Latest Videos & News (2/3 width) -->
            <div class="lg:col-span-2 space-y-12">
                
                <!-- Latest Videos -->
                <section>
                    <div class="flex justify-between items-end mb-6 border-b-2 border-gray-200 pb-2">
                        <h2 class="text-3xl heading-font uppercase text-[#0B0F19] flex items-center">
                            <span class="w-1.5 h-6 bg-[#FFC800] mr-3 block"></span> Latest Videos
                        </h2>
                        <a href="#" class="text-sm font-bold text-[#D4A000] hover:text-[#FFC800] uppercase">View All</a>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Video Card 1 -->
                        <div class="group cursor-pointer">
                            <div class="relative w-full aspect-video bg-gray-800 rounded-lg overflow-hidden mb-3">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent z-10"></div>
                                <div class="absolute inset-0 flex items-center justify-center z-20">
                                    <div class="w-12 h-12 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center group-hover:bg-[#FFC800] transition-colors border border-white/30">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white group-hover:text-black transition-colors ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                    </div>
                                </div>
                                <span class="absolute bottom-2 right-2 text-white text-xs font-bold bg-black/60 px-2 py-1 rounded z-20">05:24</span>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 group-hover:text-[#D4A000] transition-colors line-clamp-2 leading-tight">Highlights: Epic final over finish in Match 1</h3>
                            <p class="text-gray-500 text-sm mt-1">12 May 2026</p>
                        </div>
                        
                        <!-- Video Card 2 -->
                        <div class="group cursor-pointer">
                            <div class="relative w-full aspect-video bg-gray-700 rounded-lg overflow-hidden mb-3">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent z-10"></div>
                                <div class="absolute inset-0 flex items-center justify-center z-20">
                                    <div class="w-12 h-12 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center group-hover:bg-[#FFC800] transition-colors border border-white/30">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white group-hover:text-black transition-colors ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                    </div>
                                </div>
                                <span class="absolute bottom-2 right-2 text-white text-xs font-bold bg-black/60 px-2 py-1 rounded z-20">02:15</span>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 group-hover:text-[#D4A000] transition-colors line-clamp-2 leading-tight">Post Match Interview: Captain reflects on the win</h3>
                            <p class="text-gray-500 text-sm mt-1">12 May 2026</p>
                        </div>
                    </div>
                </section>

                <!-- Latest News -->
                <section>
                    <div class="flex justify-between items-end mb-6 border-b-2 border-gray-200 pb-2">
                        <h2 class="text-3xl heading-font uppercase text-[#0B0F19] flex items-center">
                            <span class="w-1.5 h-6 bg-[#FFC800] mr-3 block"></span> Latest News
                        </h2>
                        <a href="#" class="text-sm font-bold text-[#D4A000] hover:text-[#FFC800] uppercase">View All</a>
                    </div>
                    
                    <div class="flex flex-col space-y-6">
                        <!-- News Item 1 -->
                        <div class="flex flex-col sm:flex-row gap-6 group cursor-pointer border-b border-gray-100 pb-6">
                            <div class="w-full sm:w-1/3 aspect-video bg-gray-300 rounded overflow-hidden"></div>
                            <div class="w-full sm:w-2/3 flex flex-col justify-center">
                                <span class="text-[#D4A000] text-xs font-bold uppercase mb-2">Announcements</span>
                                <h3 class="text-xl font-bold text-gray-900 group-hover:text-[#D4A000] transition-colors leading-tight mb-3">Revised schedule for upcoming playoffs announced</h3>
                                <p class="text-gray-600 text-sm line-clamp-2">The organizing committee has finalized the dates for the much-anticipated playoff matches following recent weather disruptions.</p>
                            </div>
                        </div>
                        
                        <!-- News Item 2 -->
                        <div class="flex flex-col sm:flex-row gap-6 group cursor-pointer border-b border-gray-100 pb-6">
                            <div class="w-full sm:w-1/3 aspect-video bg-gray-200 rounded overflow-hidden"></div>
                            <div class="w-full sm:w-2/3 flex flex-col justify-center">
                                <span class="text-[#D4A000] text-xs font-bold uppercase mb-2">Team Updates</span>
                                <h3 class="text-xl font-bold text-gray-900 group-hover:text-[#D4A000] transition-colors leading-tight mb-3">Star player ruled out due to injury</h3>
                                <p class="text-gray-600 text-sm line-clamp-2">A major blow to the franchise as their lead fast bowler has been ruled out of the remainder of the tournament.</p>
                            </div>
                        </div>
                    </div>
                </section>
                
            </div>

            <!-- Right Column: Points Table & Magic Moments (1/3 width) -->
            <div class="space-y-8">
                
                <!-- Points Table Widget -->
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                    <div class="bg-[#141B2D] text-white p-4 flex justify-between items-center">
                        <h2 class="text-2xl heading-font uppercase text-[#FFC800]">Points Table</h2>
                        <a href="#" class="text-xs font-bold text-gray-300 hover:text-[#FFC800] uppercase">Full Table &gt;</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50 text-gray-500 font-bold uppercase text-[10px]">
                                <tr>
                                    <th class="px-4 py-3">Team</th>
                                    <th class="px-2 py-3 text-center">P</th>
                                    <th class="px-2 py-3 text-center">W</th>
                                    <th class="px-2 py-3 text-center">L</th>
                                    <th class="px-4 py-3 text-right">Pts</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <!-- Mock Rows -->
                                <tr class="bg-white hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 font-bold flex items-center"><span class="w-2 h-2 rounded-full bg-yellow-400 mr-2"></span> CSK</td>
                                    <td class="px-2 py-3 text-center">14</td>
                                    <td class="px-2 py-3 text-center">10</td>
                                    <td class="px-2 py-3 text-center">4</td>
                                    <td class="px-4 py-3 text-right font-black text-gray-900">20</td>
                                </tr>
                                <tr class="bg-gray-50 hover:bg-gray-100 transition-colors">
                                    <td class="px-4 py-3 font-bold flex items-center"><span class="w-2 h-2 rounded-full bg-blue-600 mr-2"></span> MI</td>
                                    <td class="px-2 py-3 text-center">14</td>
                                    <td class="px-2 py-3 text-center">9</td>
                                    <td class="px-2 py-3 text-center">5</td>
                                    <td class="px-4 py-3 text-right font-black text-gray-900">18</td>
                                </tr>
                                <tr class="bg-white hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 font-bold flex items-center"><span class="w-2 h-2 rounded-full bg-red-500 mr-2"></span> RCB</td>
                                    <td class="px-2 py-3 text-center">14</td>
                                    <td class="px-2 py-3 text-center">8</td>
                                    <td class="px-2 py-3 text-center">6</td>
                                    <td class="px-4 py-3 text-right font-black text-gray-900">16</td>
                                </tr>
                                <tr class="bg-gray-50 hover:bg-gray-100 transition-colors">
                                    <td class="px-4 py-3 font-bold flex items-center"><span class="w-2 h-2 rounded-full bg-purple-600 mr-2"></span> KKR</td>
                                    <td class="px-2 py-3 text-center">14</td>
                                    <td class="px-2 py-3 text-center">7</td>
                                    <td class="px-2 py-3 text-center">7</td>
                                    <td class="px-4 py-3 text-right font-black text-gray-900">14</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Magic Moments or Promo Banner -->
                <div class="relative w-full aspect-square bg-gradient-to-br from-[#0B0F19] to-[#141B2D] rounded-lg overflow-hidden flex flex-col justify-center items-center text-center p-6 border border-[#FFC800]/20 shadow-lg">
                    <h3 class="text-3xl heading-font text-[#FFC800] uppercase mb-2">Magic Moments</h3>
                    <p class="text-gray-300 text-sm mb-6">Relive the best catches, huge sixes, and unforgettable wickets.</p>
                    <a href="#" class="bg-[#FFC800] text-black px-6 py-2 font-bold uppercase rounded-sm hover:bg-[#D4A000] transition-colors shadow-md shadow-[#FFC800]/20">Watch Now</a>
                </div>

                <!-- Live Auction Widget (if applicable) -->
                @php
                    $auction = \App\Models\Auction::whereIn('status', ['live', 'paused'])->first();
                @endphp
                @if($auction)
                <div class="bg-white border-2 border-red-500 rounded-lg shadow-md p-6 text-center relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-16 h-16 bg-red-500 rounded-bl-full z-0 flex justify-end p-2 items-start">
                        <span class="w-3 h-3 bg-white rounded-full animate-pulse mr-1 mt-1"></span>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 uppercase relative z-10">Auction is Live!</h3>
                    <p class="text-gray-600 text-sm mt-2 mb-4 relative z-10">Don't miss the bidding war.</p>
                    <a href="{{ route('auction.live', $auction->guid) }}" class="inline-block bg-red-600 text-white px-6 py-2 font-bold uppercase rounded hover:bg-red-700 transition-colors relative z-10 w-full">Join Room</a>
                </div>
                @endif
                
            </div>
            
        </div>
    </div>
</x-ipl-layout>
