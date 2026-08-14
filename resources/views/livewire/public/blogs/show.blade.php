@section('seo')
    <title>{{ $blog->meta_title ?: $blog->title }} | {{ setting('app_name', 'NPLT20') }}</title>
    @if($blog->meta_description || $blog->excerpt)
        <meta name="description" content="{{ $blog->meta_description ?: $blog->excerpt }}">
    @endif
    @if($blog->meta_keywords)
        <meta name="keywords" content="{{ $blog->meta_keywords }}">
    @endif
    
    <!-- Open Graph Tags for Social Media Sharing -->
    <meta property="og:title" content="{{ $blog->meta_title ?: $blog->title }}">
    @if($blog->meta_description || $blog->excerpt)
        <meta property="og:description" content="{{ $blog->meta_description ?: $blog->excerpt }}">
    @endif
    @if($blog->image)
        <meta property="og:image" content="{{ asset('storage/' . $blog->image) }}">
    @endif
    <meta property="og:url" content="{{ request()->url() }}">
    <meta property="og:type" content="article">
@endsection

<div>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
        
        <!-- Article Card -->
        <div class="bg-[#141B2D] border border-gray-700 rounded-3xl p-6 md:p-12 shadow-2xl">
            <!-- Blog Title & Meta -->
        <div class="text-center mb-10">
            <div class="flex items-center justify-center gap-4 text-sm font-semibold text-gray-400 uppercase tracking-wider mb-6">
                <span class="text-[#FFC800]">{{ $blog->published_at ? $blog->published_at->format('F d, Y') : $blog->created_at->format('F d, Y') }}</span>
                <span>•</span>
                <span>NPL Media</span>
            </div>
            <h1 class="text-3xl md:text-5xl font-black text-white mb-6 leading-tight font-poppins">
                {{ $blog->title }}
            </h1>
        </div>

        <!-- Blog Header Image -->
        @if($blog->image)
        <div class="mb-12 flex justify-center">
            <div class="p-3 bg-[#1A2235] border-2 border-gray-700 rounded-2xl shadow-2xl inline-block">
                <img src="{{ asset('storage/' . $blog->image) }}" alt="{{ $blog->title }}" class="max-w-full h-auto max-h-[300px] object-contain rounded-xl">
            </div>
        </div>
        @endif

        <!-- Ad Slot (Optional place for manual adsense insertion if auto ads are not used) -->
        <div class="my-8 flex justify-center">
            <!-- Example Manual Ad Slot -->
            <!-- <ins class="adsbygoogle"
                 style="display:block"
                 data-ad-client="ca-pub-XXXXXXXXXX"
                 data-ad-slot="XXXXXXXXXX"
                 data-ad-format="auto"
                 data-full-width-responsive="true"></ins>
            <script>
                 (adsbygoogle = window.adsbygoogle || []).push({});
            </script> -->
        </div>

        <!-- Blog Content -->
        <div class="prose prose-invert prose-lg max-w-none text-gray-300 prose-headings:text-white prose-headings:font-poppins prose-headings:font-bold prose-a:text-[#FFC800] hover:prose-a:text-yellow-400 prose-img:rounded-xl prose-img:mx-auto prose-img:max-h-[300px] prose-img:object-contain prose-img:shadow-2xl prose-img:p-2 prose-img:border-2 prose-img:border-gray-700 prose-img:bg-[#1A2235]">
            {!! $blog->content !!}
        </div>
        
            <!-- Bottom Ad Slot -->
            <div class="my-12 flex justify-center border-t border-gray-800 pt-8">
                <!-- Add bottom ad slot here if needed -->
            </div>
        </div> <!-- End Article Card -->

        <!-- Back Button -->
        <div class="text-center mt-8">
            <a href="{{ route('public.blogs') }}" class="inline-flex items-center px-6 py-3 bg-[#1A2235] hover:bg-gray-800 border border-gray-700 hover:border-[#FFC800] rounded-full text-white font-semibold transition group">
                <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"></path></svg>
                Back to News
            </a>
        </div>
    </div>
</div>
