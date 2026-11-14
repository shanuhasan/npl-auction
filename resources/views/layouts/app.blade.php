<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ setting('app_name', config('app.name', 'Naugawan Premier League (NPLT20)')) }} | Naugawan Premier League</title>
        <link rel="icon" href="{{ setting('favicon') ? asset('storage/' . setting('favicon')) : asset('favicon.ico') }}">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Quill Editor -->
        <link href="{{ asset('css/quill.snow.css') }}" rel="stylesheet">
        <script src="{{ asset('js/quill.min.js') }}"></script>

        <!-- jQuery & Select2 (themed in resources/css/app.css) -->
        <script src="{{ asset('js/jquery.min.js') }}"></script>
        <link href="{{ asset('css/select2.min.css') }}" rel="stylesheet" />
        <script src="{{ asset('js/select2.min.js') }}"></script>
    </head>
    <body class="theme-app">
        <div x-data="{
                collapsed: (() => { try { return localStorage.getItem('sidebar-collapsed') === '1' } catch (e) { return false } })(),
                mobileOpen: false,
                toggleCollapse() {
                    this.collapsed = !this.collapsed;
                    try { localStorage.setItem('sidebar-collapsed', this.collapsed ? '1' : '0') } catch (e) {}
                }
             }"
             x-on:toggle-sidebar.window="mobileOpen = !mobileOpen"
             x-on:toggle-sidebar-collapse.window="toggleCollapse()"
             x-on:keydown.escape.window="mobileOpen = false"
             class="flex h-screen overflow-hidden">

            <!-- Mobile drawer backdrop -->
            <div x-show="mobileOpen" x-cloak x-transition.opacity @click="mobileOpen = false"
                 class="fixed inset-0 z-40 bg-slate-900/40 lg:hidden"></div>

            <!-- Sidebar -->
            <aside class="fixed inset-y-0 left-0 z-50 flex w-[var(--sidebar-width)] flex-col border-r border-line bg-surface transition-[width,transform] duration-200 ease-out
                          -translate-x-full lg:static lg:translate-x-0"
                   :class="[mobileOpen && '!translate-x-0 shadow-pop', collapsed && 'lg:w-[var(--sidebar-width-collapsed)]']">
                <!-- Brand -->
                <div class="flex h-16 shrink-0 items-center gap-3 border-b border-line px-4" :class="collapsed && 'lg:justify-center lg:px-0'">
                    <a href="{{ route('dashboard') }}" wire:navigate class="flex min-w-0 items-center gap-3">
                        @if(setting('logo'))
                            <img src="{{ asset('storage/' . setting('logo')) }}" alt="Logo" class="h-9 w-9 shrink-0 rounded-lg object-contain">
                        @else
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-brand text-sm font-bold text-white">
                                {{ \Illuminate\Support\Str::of(setting('app_name', 'NPL'))->substr(0, 2)->upper() }}
                            </span>
                        @endif
                        <span class="truncate text-[15px] font-semibold text-ink" :class="collapsed && 'lg:hidden'">{{ setting('app_name', 'NPLT20') }}</span>
                    </a>
                    <button type="button" class="btn-ghost ml-auto p-1.5 lg:hidden" @click="mobileOpen = false" aria-label="Close menu">
                        <x-icon name="x" />
                    </button>
                </div>

                @auth
                    @include('layouts.partials.sidebar-nav')
                @endauth

                <!-- Collapse toggle (desktop) -->
                <div class="hidden shrink-0 border-t border-line p-2 lg:block">
                    <button type="button" @click="toggleCollapse()"
                            class="nav-item w-[calc(100%-1rem)]" :class="collapsed && 'lg:justify-center lg:px-0'"
                            :title="collapsed ? 'Expand sidebar' : 'Collapse sidebar'">
                        <x-icon name="chevrons-left" class="w-5 h-5 transition-transform" ::class="collapsed && 'rotate-180'" />
                        <span :class="collapsed && 'lg:hidden'">Collapse</span>
                    </button>
                </div>
            </aside>

            <!-- Main -->
            <div class="flex min-w-0 flex-1 flex-col overflow-hidden">
                <livewire:layout.navigation />

                <main class="flex flex-1 flex-col overflow-y-auto">
                    <div class="mx-auto w-full max-w-[1400px] flex-1 px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
                        @if (isset($header))
                            <div class="page-header mb-6">{{ $header }}</div>
                        @endif
                        {{ $slot }}
                    </div>

                    <footer class="border-t border-line px-4 py-4 text-center text-xs text-muted sm:px-6 lg:px-8">
                        &copy; {{ date('Y') }} {{ setting('app_name', 'NPLT20') }}. All rights reserved.
                        @if(setting('developer_name'))
                            <span class="mx-1 text-faint">·</span> Developed by <a href="{{ setting('developer_url', '#') }}" target="_blank" class="font-medium text-ink-soft hover:text-brand">{{ setting('developer_name') }}</a>
                        @endif
                    </footer>
                </main>
            </div>
        </div>

        @livewireScripts

        <!-- Test Echo Listener -->
        <script type="module">
            document.addEventListener('DOMContentLoaded', () => {
                if (window.Echo) {
                    window.Echo.channel('auction.1')
                        .listen('PlayerOnAuction', (e) => {
                            console.log('✅ [Reverb Test] PlayerOnAuction received:', e);
                        });
                }
            });
        </script>
        @stack('scripts')
    </body>
</html>
