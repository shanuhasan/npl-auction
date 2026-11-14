<div class="space-y-8">
    <!-- Page header -->
    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-ink">Dashboard</h1>
            <p class="mt-1 text-sm text-muted">Overview of your league, auctions and website activity.</p>
        </div>
        @if(!$hasLiveAuctions)
            <button wire:click="resetSeason" wire:confirm="WARNING: This will reset ALL Teams' remaining budgets back to full, and set ALL players to 'available'. ONLY DO THIS IF YOU ARE STARTING A BRAND NEW AUCTION / NEW SEASON. Are you sure?"
                    class="btn-secondary text-danger hover:text-danger">
                <x-icon name="refresh" class="w-4 h-4" />
                Reset Data for New Season
            </button>
        @else
            <span class="badge-danger self-start md:self-auto"><span class="h-1.5 w-1.5 rounded-full bg-danger animate-pulse"></span> Auction live</span>
        @endif
    </div>

    @if (session()->has('success'))
        <div class="alert-success">
            <x-icon name="check-circle" class="w-5 h-5 shrink-0" />
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Stats -->
    @php
        $stats = [
            ['label' => 'Players', 'value' => $totalPlayers, 'icon' => 'users', 'tone' => 'bg-blue-50 text-blue-600', 'route' => auth()->user()->hasPermission('manage_players') ? route('admin.players') : null],
            ['label' => 'Teams', 'value' => $totalTeams, 'icon' => 'shield', 'tone' => 'bg-violet-50 text-violet-600', 'route' => auth()->user()->hasPermission('manage_teams') ? route('admin.teams') : null],
            ['label' => 'Upcoming Auctions', 'value' => $upcomingAuctions, 'icon' => 'clock', 'tone' => 'bg-amber-50 text-amber-600', 'route' => auth()->user()->hasPermission('manage_auctions') ? route('admin.auctions') : null],
            ['label' => 'Completed Auctions', 'value' => $completedAuctions, 'icon' => 'check-circle', 'tone' => 'bg-green-50 text-green-600', 'route' => auth()->user()->hasPermission('manage_auctions') ? route('admin.auctions') : null],
            ['label' => 'Website Visitors', 'value' => $totalVisitors, 'icon' => 'eye', 'tone' => 'bg-slate-100 text-slate-600', 'route' => auth()->user()->hasPermission('view_analytics') ? route('admin.analytics') : null],
        ];
    @endphp
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
        @foreach($stats as $stat)
            <{{ $stat['route'] ? 'a' : 'div' }} @if($stat['route']) href="{{ $stat['route'] }}" wire:navigate @endif
                class="card group p-5 transition-colors {{ $stat['route'] ? 'hover:border-line-strong' : '' }}">
                <div class="flex items-start justify-between">
                    <p class="text-sm font-medium text-muted">{{ $stat['label'] }}</p>
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg {{ $stat['tone'] }}">
                        <x-icon :name="$stat['icon']" class="w-5 h-5" />
                    </span>
                </div>
                <p class="mt-3 text-3xl font-semibold tracking-tight text-ink">{{ number_format($stat['value']) }}</p>
            </{{ $stat['route'] ? 'a' : 'div' }}>
        @endforeach
    </div>

    <!-- Quick actions -->
    @php
        $actions = array_filter([
            auth()->user()->hasPermission('manage_auctions') ? ['label' => 'Create auction', 'desc' => 'Set up a new auction and its teams', 'icon' => 'trophy', 'route' => route('admin.auctions.create')] : null,
            auth()->user()->hasPermission('manage_players') ? ['label' => 'Manage players', 'desc' => 'Approve, edit and export players', 'icon' => 'users', 'route' => route('admin.players')] : null,
            auth()->user()->hasPermission('manage_teams') ? ['label' => 'Manage teams', 'desc' => 'Owners, budgets and logos', 'icon' => 'shield', 'route' => route('admin.teams')] : null,
            ['label' => 'Auction reports', 'desc' => 'Squads, spending and results', 'icon' => 'report', 'route' => route('reports.auction')],
            auth()->user()->hasPermission('manage_settings') ? ['label' => 'Settings', 'desc' => 'Branding, fees and payments', 'icon' => 'cog', 'route' => route('admin.settings')] : null,
        ]);
    @endphp
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Quick actions</h2>
        </div>
        <div class="grid grid-cols-1 divide-y divide-line sm:grid-cols-2 sm:divide-y-0 lg:grid-cols-3 xl:grid-cols-5">
            @foreach($actions as $action)
                <a href="{{ $action['route'] }}" wire:navigate class="group flex items-start gap-3 p-5 transition-colors hover:bg-surface-muted">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-brand-soft text-brand">
                        <x-icon :name="$action['icon']" class="w-5 h-5" />
                    </span>
                    <span>
                        <span class="block text-sm font-medium text-ink group-hover:text-brand">{{ $action['label'] }}</span>
                        <span class="mt-0.5 block text-xs text-muted">{{ $action['desc'] }}</span>
                    </span>
                </a>
            @endforeach
        </div>
    </div>
</div>
