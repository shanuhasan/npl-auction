<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        
        <!-- Page Header -->
        <div class="mb-10 text-center md:text-left">
            <h1 class="text-4xl md:text-3xl font-semibold text-ink mb-4 tracking-tight">
                {{ $page->title }}
            </h1>
        </div>

        <!-- Rich Text Content -->
        <div class="bg-surface border border-line rounded-2xl p-6 md:p-12 shadow-sm page-content">
            {!! $page->content !!}
        </div>
        
    </div>

    <!-- Scoped styles for the rich text editor output -->
    <style>
        .page-content {
            font-size: 1.0625rem;
            line-height: 1.75;
            color: rgb(var(--color-ink-soft));
        }
        .page-content h1, .page-content h2, .page-content h3, .page-content h4 {
            color: rgb(var(--color-ink));
            font-weight: 600;
            letter-spacing: -0.01em;
            margin-top: 2rem;
            margin-bottom: 1rem;
        }
        .page-content h1 { font-size: 2rem; line-height: 1.2; }
        .page-content h2 { font-size: 1.5rem; line-height: 1.3; }
        .page-content h3 { font-size: 1.25rem; line-height: 1.4; }
        .page-content p {
            margin-bottom: 1.25rem;
        }
        .page-content a {
            color: rgb(var(--color-brand)); font-weight: 500;
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
            color: rgb(var(--color-ink));
            font-weight: 600;
        }
        .page-content u {
            text-decoration-color: rgb(var(--color-brand));
        }
    </style>
</div>
