<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ setting('app_name', 'Naugawan Premier League') }} | Naugawan Premier League {{ isset($title) ? ' | ' . $title : '' }}</title>
    <link rel="icon" href="{{ setting('favicon') ? asset('storage/' . setting('favicon')) : asset('favicon.ico') }}">

    @yield('seo')
    {!! setting('adsense_code') !!}

    <!-- Scripts & Styles (theme: resources/css/app.css) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <!-- jQuery & Select2 -->
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <link href="{{ asset('css/select2.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('js/select2.min.js') }}"></script>
</head>
<body class="theme-app overflow-x-hidden">
@php
    $facebook = setting('facebook');
    $instagram = setting('instagram');
    $twitter = setting('twitter');
    $youtube = setting('youtube');
    $socials = array_filter(['facebook' => $facebook, 'instagram' => $instagram, 'twitter' => $twitter, 'youtube' => $youtube]);
    $auction = \App\Models\Auction::whereIn('status', ['live', 'paused'])->first();
    $navLinks = [
        ['label' => 'Home', 'href' => route('home'), 'active' => request()->routeIs('home')],
        ['label' => 'Teams', 'href' => route('public.teams'), 'active' => request()->routeIs('public.teams*')],
        ['label' => 'Players', 'href' => route('public.players'), 'active' => request()->routeIs('public.players*')],
        ['label' => 'Schedule', 'href' => route('public.schedule'), 'active' => request()->routeIs('public.schedule')],
        ['label' => 'News', 'href' => route('public.blogs'), 'active' => request()->routeIs('public.blogs*')],
        ['label' => 'Gallery', 'href' => route('public.gallery'), 'active' => request()->routeIs('public.gallery')],
    ];
    if (isset($globalPages)) {
        foreach ($globalPages as $gPage) {
            $navLinks[] = ['label' => $gPage->title, 'href' => route('public.page', $gPage->slug), 'active' => request()->is('pages/' . $gPage->slug)];
        }
    }
    $navLinks[] = ['label' => 'Contact', 'href' => route('public.contact'), 'active' => request()->routeIs('public.contact')];
    $dashboardUrl = auth()->check()
        ? (in_array(auth()->user()->role, ['admin', 'sub_admin']) ? route('admin.dashboard') : (auth()->user()->role === 'team_owner' ? route('team_owner.my_team') : route('dashboard')))
        : null;
@endphp

    <!-- Main Navigation -->
    <nav class="sticky top-0 z-50 border-b border-white/10 bg-brand shadow-sm">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4 md:px-8">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-3">
                @if(setting('logo'))
                    <img src="{{ asset('storage/' . setting('logo')) }}" alt="{{ setting('app_name', 'NPL') }}" class="h-10 w-auto max-w-[110px] object-contain">
                @else
                    <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-accent text-sm font-bold text-brand-deep">NPL</span>
                @endif
                <span class="truncate text-base font-semibold text-white">{{ setting('app_name', 'Naugawan Premier League') }}</span>
            </a>

            <!-- Desktop Menu -->
            <div class="hidden h-full items-center gap-1 lg:flex">
                @foreach($navLinks as $link)
                    <a href="{{ $link['href'] }}"
                       class="relative flex h-full items-center px-3 text-sm font-medium transition-colors {{ $link['active'] ? 'text-white after:absolute after:inset-x-3 after:bottom-0 after:h-0.5 after:rounded-full after:bg-accent' : 'text-white/70 hover:text-white' }}">{{ $link['label'] }}</a>
                @endforeach
            </div>

            <!-- Right Actions -->
            <div class="flex shrink-0 items-center gap-2">
                @if($auction)
                    <a href="{{ route('auction.live', $auction->guid) }}" class="inline-flex items-center gap-2 rounded-lg border border-red-400/40 bg-red-500/15 px-3 py-1.5 text-xs font-semibold text-red-100 transition-colors hover:bg-red-500/25 md:text-sm">
                        <span class="h-2 w-2 rounded-full bg-red-400 animate-pulse"></span>
                        Live Auction
                    </a>
                @endif

                @auth
                    <a href="{{ $dashboardUrl }}" class="btn-accent hidden lg:inline-flex">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}" class="m-0 hidden p-0 lg:flex">
                        @csrf
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="btn text-white/70 hover:bg-white/10 hover:text-white">Log out</a>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn-accent hidden lg:inline-flex">Log in</a>
                @endauth
                <button id="mobile-menu-button" class="btn p-2 text-white/80 hover:bg-white/10 hover:text-white lg:hidden" aria-label="Open menu">
                    <x-icon name="menu" />
                </button>
            </div>
        </div>

        <!-- Mobile Menu (Hidden by default) -->
        <div id="mobile-menu" class="hidden border-t border-white/10 bg-brand pb-3 lg:hidden">
            <div class="space-y-0.5 px-2 pt-2">
                @foreach($navLinks as $link)
                    <a href="{{ $link['href'] }}" class="block rounded-lg px-3 py-2.5 text-sm font-medium {{ $link['active'] ? 'bg-white/10 text-white' : 'text-white/75 hover:bg-white/5 hover:text-white' }}">{{ $link['label'] }}</a>
                @endforeach
                @if($auction)
                    <a href="{{ route('auction.live', $auction->guid) }}" class="block rounded-lg px-3 py-2.5 text-sm font-semibold text-red-200 hover:bg-white/5">Live Auction</a>
                @endif
            </div>
            <div class="mt-2 flex gap-2 border-t border-white/10 px-4 pt-3">
                @auth
                    <a href="{{ $dashboardUrl }}" class="btn-accent flex-1">{{ auth()->user()->role === 'team_owner' ? 'My Team' : 'Dashboard' }}</a>
                    <form method="POST" action="{{ route('logout') }}" class="m-0 flex-1 p-0">
                        @csrf
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="btn w-full text-white/75 hover:bg-white/10 hover:text-white">Log out</a>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn-accent flex-1">Log in</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Mobile Menu Script -->
    <script>
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
            } else {
                menu.classList.add('hidden');
            }
        });
    </script>

    <main class="min-h-[60vh]">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="border-t-2 border-accent bg-brand-deep text-white/65">
        <div class="mx-auto max-w-7xl px-4 py-14 md:px-8">
            <div class="grid grid-cols-1 gap-10 md:grid-cols-2 lg:grid-cols-4">
                <div>
                    <div class="flex items-center gap-3">
                        @if(setting('logo'))
                            <img src="{{ asset('storage/' . setting('logo')) }}" alt="{{ setting('app_name', 'NPLT20') }}" class="h-12 w-auto max-w-[120px] object-contain">
                        @endif
                        <span class="text-base font-semibold text-white">{{ setting('app_name', 'Naugawan Premier League') }}</span>
                    </div>
                    <p class="mt-4 text-sm leading-relaxed text-white/60">The official home of the premier league. Get live scores, news, and exclusive videos directly from the field.</p>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-white">Quick Links</h4>
                    <ul class="mt-4 space-y-2.5 text-sm text-white/60">
                        <li><a href="{{ route('home') }}" class="hover:text-accent">Home</a></li>
                        <li><a href="{{ route('public.teams') }}" class="hover:text-accent">Teams</a></li>
                        <li><a href="{{ route('public.players') }}" class="hover:text-accent">Players</a></li>
                        <li><a href="{{ route('public.schedule') }}" class="hover:text-accent">Schedule</a></li>
                        <li><a href="{{ route('public.gallery') }}" class="hover:text-accent">Gallery</a></li>
                        <li><a href="{{ route('public.blogs') }}" class="hover:text-accent">News</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-white">Guidelines &amp; Pages</h4>
                    <ul class="mt-4 space-y-2.5 text-sm text-white/60">
                        @if(isset($globalPages) && $globalPages->count() > 0)
                            @foreach($globalPages as $gPage)
                                <li><a href="{{ route('public.page', $gPage->slug) }}" class="hover:text-accent">{{ $gPage->title }}</a></li>
                            @endforeach
                        @endif
                        <li><a href="{{ route('public.contact') }}" class="hover:text-accent">Contact Us</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-white">Connect</h4>
                    <p class="mt-4 text-sm text-white/60">Follow us on social media for the latest updates.</p>
                    @if(count($socials))
                        <div class="mt-4 flex gap-2">
                            @foreach($socials as $net => $url)
                                <a href="{{ $url }}" target="_blank" rel="noopener" aria-label="{{ ucfirst($net) }}"
                                   class="flex h-9 w-9 items-center justify-center rounded-lg border border-white/15 text-white/70 transition-colors hover:border-accent hover:bg-accent hover:text-brand-deep">
                                    @switch($net)
                                        @case('facebook')<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd"/></svg>@break
                                        @case('instagram')<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd"/></svg>@break
                                        @case('twitter')<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"/></svg>@break
                                        @case('youtube')<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M19.812 5.418c.861.23 1.538.907 1.768 1.768C21.998 8.746 22 12 22 12s0 3.255-.418 4.814a2.504 2.504 0 0 1-1.768 1.768c-1.56.419-7.814.419-7.814.419s-6.255 0-7.814-.419a2.505 2.505 0 0 1-1.768-1.768C2 15.255 2 12 2 12s0-3.255.417-4.814a2.507 2.507 0 0 1 1.768-1.768C5.744 5 11.998 5 11.998 5s6.255 0 7.814.418ZM15.194 12 10 15V9l5.194 3Z" clip-rule="evenodd"/></svg>@break
                                    @endswitch
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-12 flex flex-col items-center justify-between gap-2 border-t border-white/10 pt-6 text-xs text-white/50 md:flex-row">
                <p>&copy; {{ date('Y') }} {{ setting('app_name', 'Naugawan Premier League') }}. All rights reserved.</p>
                @if(setting('developer_name'))
                    <p>Developed by <a href="{{ setting('developer_url', '#') }}" target="_blank" class="font-medium text-white/80 hover:text-accent">{{ setting('developer_name') }}</a></p>
                @endif
            </div>
        </div>
    </footer>

    <!-- Livewire Scripts -->
    @livewireScripts
    @stack('scripts')
</body>
</html>
