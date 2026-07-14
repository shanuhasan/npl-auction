<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        
        <!-- Page Header -->
        <div class="mb-10 text-center md:text-left">
            <h1 class="text-4xl md:text-5xl font-black text-white uppercase tracking-widest mb-4">
                {{ $page->title }}
            </h1>
        </div>

        <!-- Rich Text Content -->
        <div class="bg-[#141B2D] border border-white/5 rounded-3xl p-6 md:p-12 shadow-lg page-content">
            {!! $page->content !!}
        </div>
        
    </div>

    <!-- Scoped styles for the rich text editor output -->
    <style>
        .page-content {
            font-size: 1.125rem;
            line-height: 1.75;
            color: #d1d5db; /* gray-300 */
        }
        .page-content h1, .page-content h2, .page-content h3, .page-content h4 {
            color: #FFC800;
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            margin-top: 2rem;
            margin-bottom: 1rem;
        }
        .page-content h1 { font-size: 2.25rem; line-height: 1.2; }
        .page-content h2 { font-size: 1.875rem; line-height: 1.3; }
        .page-content h3 { font-size: 1.5rem; line-height: 1.4; }
        .page-content p {
            margin-bottom: 1.25rem;
        }
        .page-content a {
            color: #60a5fa; /* blue-400 */
            text-decoration: none;
            transition: text-decoration 0.2s;
        }
        .page-content a:hover {
            text-decoration: underline;
        }
        .page-content ul {
            list-style-type: disc;
            padding-left: 1.5rem;
            margin-bottom: 1.25rem;
        }
        .page-content ol {
            list-style-type: decimal;
            padding-left: 1.5rem;
            margin-bottom: 1.25rem;
        }
        .page-content li {
            margin-bottom: 0.5rem;
        }
        .page-content strong, .page-content b {
            color: #ffffff;
            font-weight: 700;
        }
        .page-content u {
            text-decoration-color: #FFC800;
        }
    </style>
</div>
