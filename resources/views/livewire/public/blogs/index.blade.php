@section('seo')
    <title>Latest News & Blogs | {{ setting('app_name', 'NPLT20') }}</title>
    <meta name="description" content="Stay updated with the latest news, updates, and blogs from the Naugawan Premier League.">
@endsection

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    
    <!-- Page Header -->
    <div class="mb-10 flex flex-col md:flex-row justify-between items-center">
        <div class="text-center md:text-left mb-6 md:mb-0">
            <h1 class="text-4xl md:text-5xl font-black text-white uppercase tracking-widest mb-4">News & Blogs</h1>
            <p class="text-gray-400 text-lg">Stay updated with the latest news, updates, and stories.</p>
        </div>
    </div>

    <!-- Blogs Grid -->
    <div class="mb-10">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($blogs as $blog)
                <a href="{{ route('public.blogs.show', $blog->slug) }}" class="group bg-[#1A2235] border border-gray-800 rounded-2xl overflow-hidden hover:border-[#FFC800]/50 transition duration-300 transform hover:-translate-y-1 hover:shadow-2xl hover:shadow-[#FFC800]/10 flex flex-col h-full">
                    
                    <!-- Image -->
                    <div class="relative h-48 md:h-56 overflow-hidden bg-gray-900">
                        @if($blog->image)
                            <img src="{{ asset('storage/' . $blog->image) }}" alt="{{ $blog->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-700">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-[#1A2235] via-transparent to-transparent opacity-80"></div>
                    </div>
                    
                    <!-- Content -->
                    <div class="p-6 flex flex-col flex-grow">
                        <div class="text-xs font-bold text-[#FFC800] uppercase tracking-wider mb-3 flex items-center gap-2">
                            <span>{{ $blog->published_at ? $blog->published_at->format('M d, Y') : $blog->created_at->format('M d, Y') }}</span>
                        </div>
                        
                        <h2 class="text-xl font-bold text-white mb-3 leading-tight group-hover:text-[#FFC800] transition line-clamp-2">
                            {{ $blog->title }}
                        </h2>
                        
                        @if($blog->excerpt)
                            <p class="text-gray-400 text-sm mb-4 line-clamp-3 flex-grow">
                                {{ $blog->excerpt }}
                            </p>
                        @else
                            <p class="text-gray-400 text-sm mb-4 line-clamp-3 flex-grow">
                                {{ Str::limit(strip_tags($blog->content), 120) }}
                            </p>
                        @endif
                        
                        <div class="mt-auto pt-4 border-t border-gray-800 flex items-center text-sm font-semibold text-white group-hover:text-[#FFC800] transition">
                            Read Article 
                            <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full text-center py-20">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-800 mb-4 text-gray-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">No News Yet</h3>
                    <p class="text-gray-400">We haven't published any news or blogs yet. Check back later!</p>
                </div>
            @endforelse
        </div>
        
        <div class="mt-12">
            {{ $blogs->links() }}
        </div>
    </div>
</div>
