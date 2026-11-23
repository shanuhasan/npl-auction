<div class="space-y-8">
    <!-- Page header -->
    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-ink">Dashboard</h1>
            <p class="mt-1.5 text-sm text-muted">{{ setting('app_name', 'NPL') }}{{ setting('season') ? ' · ' . setting('season') : '' }}</p>
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

    <!-- Stats: one scoreboard strip -->
    @php
        $user = auth()->user();
        $stats = [
            ['label' => 'Players', 'value' => $totalPlayers, 'route' => $user->hasPermission('manage_players') ? route('admin.players') : null],
            ['label' => 'Teams', 'value' => $totalTeams, 'route' => $user->hasPermission('manage_teams') ? route('admin.teams') : null],
            ['label' => 'Upcoming auctions', 'value' => $upcomingAuctions, 'route' => $user->hasPermission('manage_auctions') ? route('admin.auctions') : null],
            ['label' => 'Completed auctions', 'value' => $completedAuctions, 'route' => $user->hasPermission('manage_auctions') ? route('admin.auctions') : null],
            ['label' => 'Site visitors', 'value' => $totalVisitors, 'route' => $user->hasPermission('view_analytics') ? route('admin.analytics') : null],
        ];
    @endphp
    <div class="stat-strip">
        @foreach($stats as $stat)
            <{{ $stat['route'] ? 'a' : 'div' }} @if($stat['route']) href="{{ $stat['route'] }}" wire:navigate @endif>
                <p class="stat-label">{{ $stat['label'] }}</p>
                <p class="stat-value">{{ number_format($stat['value']) }}</p>
            </{{ $stat['route'] ? 'a' : 'div' }}>
        @endforeach
    </div>

    <!-- Shortcuts -->
    @php
        $actions = array_filter([
            $user->hasPermission('manage_auctions') ? ['label' => 'Create auction', 'desc' => 'New auction, its teams and players', 'route' => route('admin.auctions.create')] : null,
            $user->hasPermission('manage_players') ? ['label' => 'Players', 'desc' => 'Approve, edit and export to PDF', 'route' => route('admin.players')] : null,
            $user->hasPermission('manage_teams') ? ['label' => 'Teams', 'desc' => 'Owners, budgets and logos', 'route' => route('admin.teams')] : null,
            ['label' => 'Auction reports', 'desc' => 'Squads, spending and results', 'route' => route('reports.auction')],
            $user->hasPermission('manage_settings') ? ['label' => 'Settings', 'desc' => 'Branding, fees and payments', 'route' => route('admin.settings')] : null,
        ]);
    @endphp
    <div class="card overflow-hidden">
        <div class="card-header">
            <h2 class="card-title">Shortcuts</h2>
        </div>
        <div class="-mb-px -mr-px grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($actions as $action)
                <a href="{{ $action['route'] }}" wire:navigate class="group flex items-baseline justify-between gap-4 border-b border-line px-5 py-4 transition-colors hover:bg-accent-soft/60 sm:border-r">
                    <span>
                        <span class="block text-sm font-semibold text-ink">{{ $action['label'] }}</span>
                        <span class="mt-0.5 block text-xs text-muted">{{ $action['desc'] }}</span>
                    </span>
                    <span class="text-faint transition-transform group-hover:translate-x-0.5 group-hover:text-ink" aria-hidden="true">&rarr;</span>
                </a>
            @endforeach
        </div>
    </div>
</div>
