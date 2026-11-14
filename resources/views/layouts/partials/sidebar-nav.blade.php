{{-- Single source of truth for the app sidebar (desktop + mobile drawer). --}}
@php
    $user = auth()->user();
    $sections = [];

    if (in_array($user->role, ['admin', 'sub_admin'])) {
        $sections = [
            'Overview' => [
                ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'active' => 'admin.dashboard', 'icon' => 'home'],
                ['label' => 'Analytics', 'route' => 'admin.analytics', 'active' => 'admin.analytics', 'icon' => 'chart', 'perm' => 'view_analytics'],
                ['label' => 'Reports', 'route' => 'reports.auction', 'active' => 'reports.auction', 'icon' => 'report'],
            ],
            'Auction' => [
                ['label' => 'Auctions', 'route' => 'admin.auctions', 'active' => ['admin.auctions', 'admin.auctions.create', 'admin.auctions.edit', 'admin.auction.*'], 'icon' => 'trophy', 'perm' => 'manage_auctions'],
                ['label' => 'Match Schedule', 'route' => 'admin.auctions', 'active' => 'admin.auctions.schedule', 'icon' => 'calendar', 'perm' => 'manage_auctions', 'hidden_unless_active' => true],
                ['label' => 'Teams', 'route' => 'admin.teams', 'active' => 'admin.teams*', 'icon' => 'shield', 'perm' => 'manage_teams'],
                ['label' => 'Players', 'route' => 'admin.players', 'active' => 'admin.players*', 'icon' => 'users', 'perm' => 'manage_players'],
            ],
            'Content' => [
                ['label' => 'Banners', 'route' => 'admin.banners', 'active' => 'admin.banners', 'icon' => 'stack', 'perm' => 'manage_banners'],
                ['label' => 'Pages', 'route' => 'admin.pages.index', 'active' => 'admin.pages*', 'icon' => 'document', 'perm' => 'manage_pages'],
                ['label' => 'Blogs', 'route' => 'admin.blogs.index', 'active' => 'admin.blogs*', 'icon' => 'newspaper'],
                ['label' => 'Gallery', 'route' => 'admin.gallery', 'active' => 'admin.gallery*', 'icon' => 'photo', 'perm' => 'manage_gallery'],
                ['label' => 'Committee', 'route' => 'admin.core-committees', 'active' => 'admin.core-committees', 'icon' => 'id-card', 'perm' => 'manage_core_committees'],
                ['label' => 'Guests', 'route' => 'admin.guests', 'active' => 'admin.guests', 'icon' => 'star', 'perm' => 'manage_guests'],
                ['label' => 'Sponsors', 'route' => 'admin.sponsors', 'active' => 'admin.sponsors', 'icon' => 'briefcase', 'perm' => 'manage_sponsors'],
                ['label' => 'Contact Messages', 'route' => 'admin.contacts', 'active' => 'admin.contacts', 'icon' => 'inbox', 'perm' => 'manage_contacts'],
            ],
            'System' => [
                ['label' => 'Users', 'route' => 'admin.users', 'active' => 'admin.users*', 'icon' => 'key', 'perm' => 'manage_users'],
                ['label' => 'Settings', 'route' => 'admin.settings', 'active' => 'admin.settings', 'icon' => 'cog', 'perm' => 'manage_settings'],
            ],
        ];
    } elseif ($user->role === 'team_owner') {
        $myTeam = \App\Models\Team::where('owner_id', $user->id)->first();
        $activeAuction = \App\Models\Auction::whereIn('status', ['live', 'upcoming'])->first();
        $sections['My Team'] = array_values(array_filter([
            $myTeam ? ['label' => 'My Team', 'route' => 'team_owner.my_team', 'active' => 'team_owner.my_team', 'icon' => 'shield'] : null,
            $activeAuction ? ['label' => 'Bidding Room', 'route' => 'team.auction.bidding', 'params' => $activeAuction->guid, 'active' => 'team.auction.bidding', 'icon' => 'bolt'] : null,
            ['label' => 'Reports', 'route' => 'reports.auction', 'active' => 'reports.auction', 'icon' => 'report'],
        ]));
    } else {
        $sections['Menu'] = [
            ['label' => 'Reports', 'route' => 'reports.auction', 'active' => 'reports.auction', 'icon' => 'report'],
        ];
    }

    $sections['Website'] = [
        ['label' => 'All Players', 'route' => 'public.players', 'active' => '__none', 'icon' => 'external', 'external' => true],
        ['label' => 'Visit Site', 'route' => 'home', 'active' => '__none', 'icon' => 'globe', 'external' => true],
    ];
@endphp

<nav class="flex-1 overflow-y-auto overflow-x-hidden pb-4" aria-label="Main navigation">
    @foreach($sections as $title => $items)
        @php
            $items = array_filter($items, function ($item) use ($user) {
                if (!empty($item['perm']) && !$user->hasPermission($item['perm'])) return false;
                if (!empty($item['hidden_unless_active']) && !request()->routeIs($item['active'])) return false;
                return true;
            });
        @endphp
        @if(count($items))
            <div class="nav-section" :class="collapsed && 'lg:invisible lg:h-3 lg:p-0'">{{ $title }}</div>
            @foreach($items as $item)
                @php $isActive = request()->routeIs(...(array) $item['active']); @endphp
                <a href="{{ route($item['route'], $item['params'] ?? []) }}"
                   @if(!empty($item['external'])) target="_blank" rel="noopener" @else wire:navigate @endif
                   class="nav-item {{ $isActive ? 'is-active' : '' }}"
                   :class="collapsed && 'lg:justify-center lg:px-0'"
                   title="{{ $item['label'] }}"
                   @if($isActive) aria-current="page" @endif>
                    <x-icon :name="$item['icon']" />
                    <span class="truncate" :class="collapsed && 'lg:hidden'">{{ $item['label'] }}</span>
                </a>
            @endforeach
        @endif
    @endforeach
</nav>
