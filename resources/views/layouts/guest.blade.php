<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ setting('app_name', config('app.name', 'Laravel')) }}</title>
        <link rel="icon" href="{{ setting('favicon') ? asset('storage/' . setting('favicon')) : asset('favicon.ico') }}">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="theme-app">
        <div class="flex min-h-screen flex-col">
            <main class="flex flex-1 flex-col items-center justify-center px-4 py-10 sm:px-6">
                <a href="{{ url('/') }}" class="mb-8 flex items-center gap-3" title="Back to Home">
                    @if(setting('logo'))
                        <img src="{{ asset('storage/' . setting('logo')) }}" alt="Logo" class="h-11 w-11 rounded-xl object-contain">
                    @else
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-brand text-base font-bold text-white">
                            {{ \Illuminate\Support\Str::of(setting('app_name', 'NPL'))->substr(0, 2)->upper() }}
                        </span>
                    @endif
                    <span class="text-lg font-semibold text-ink">{{ setting('app_name', 'NPLT20') }}</span>
                </a>

                <div class="card w-full max-w-md px-6 py-8 sm:px-8">
                    {{ $slot }}
                </div>

                <a href="{{ url('/') }}" class="mt-6 inline-flex items-center gap-1.5 text-sm font-medium text-muted hover:text-brand">
                    <x-icon name="home" class="w-4 h-4" /> Back to website
                </a>
            </main>

            <footer class="py-5 text-center text-xs text-muted">
                &copy; {{ date('Y') }} {{ setting('app_name', 'NPLT20') }}. All rights reserved.
                @if(setting('developer_name'))
                    <span class="mx-1 text-faint">·</span>
                    Developed by <a href="{{ setting('developer_url', '#') }}" target="_blank" class="font-medium text-ink-soft hover:text-brand">{{ setting('developer_name') }}</a>
                @endif
            </footer>
        </div>
    </body>
</html>
