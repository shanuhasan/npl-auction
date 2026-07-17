<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');
Route::get('/home', function () {
    $banners = \App\Models\Banner::where('is_active', true)->orderBy('order', 'asc')->get();
    $teams = \App\Models\Team::where('is_approved', true)->get();
    $galleries = \App\Models\Gallery::where('is_active', true)->orderBy('created_at', 'desc')->take(8)->get();
    $players = \App\Models\Player::where('is_approved', true)->inRandomOrder()->take(8)->get();
    $coreCommittees = \App\Models\CoreCommittee::where('is_active', true)->orderBy('order', 'asc')->get();
    $sponsors = \App\Models\Sponsor::where('is_active', true)->orderBy('order', 'asc')->get();
    $guests = \App\Models\Guest::where('is_active', true)->orderBy('order', 'asc')->get();
    return view('home', compact('banners', 'teams', 'galleries', 'players', 'coreCommittees', 'sponsors', 'guests'));
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/teams', \App\Livewire\Public\Teams\Index::class)->name('public.teams');
Route::get('/teams/register', \App\Livewire\Public\Teams\Register::class)->name('public.teams.register');
Route::get('/teams/{team}', \App\Livewire\Public\Teams\Show::class)->name('public.teams.show');
Route::get('/players/register', \App\Livewire\Public\Players\Register::class)->name('public.players.register');
Route::get('/players', \App\Livewire\Public\Players\Index::class)->name('public.players');
Route::get('/contact', \App\Livewire\Public\Contact::class)->name('public.contact');

Route::middleware(['auth', 'role:admin,sub_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', \App\Livewire\Admin\Dashboard::class)->name('dashboard'); // Basic dashboard accessible to both
    Route::get('/teams', \App\Livewire\Admin\Teams\Index::class)->middleware('permission:manage_teams')->name('teams');
    Route::get('/players', \App\Livewire\Admin\Players\Index::class)->middleware('permission:manage_players')->name('players');
    Route::get('/players/pdf', [\App\Http\Controllers\Admin\PdfController::class, 'playersList'])->middleware('permission:manage_players')->name('players.pdf');
    Route::get('/users', \App\Livewire\Admin\Users\Index::class)->middleware('permission:manage_users')->name('users');
    Route::get('/analytics', \App\Livewire\Admin\Analytics::class)->middleware('permission:view_analytics')->name('analytics');
    Route::get('/auctions', \App\Livewire\Admin\Auctions\Index::class)->middleware('permission:manage_auctions')->name('auctions');
    Route::get('/auctions/create', \App\Livewire\Admin\Auctions\Create::class)->middleware('permission:manage_auctions')->name('auctions.create');
    Route::get('/auctions/{auction}/edit', \App\Livewire\Admin\Auctions\Edit::class)->middleware('permission:manage_auctions')->name('auctions.edit');
    Route::get('/auction/control/{auction}', \App\Livewire\Admin\Auctions\Control::class)->middleware('permission:manage_auctions')->name('auction.control');
    Route::get('/settings', \App\Livewire\Admin\Settings::class)->middleware('permission:manage_settings')->name('settings');
    Route::get('/banners', \App\Livewire\Admin\Banners\Index::class)->middleware('permission:manage_banners')->name('banners');
    Route::get('/pages', \App\Livewire\Admin\Pages\Index::class)->middleware('permission:manage_pages')->name('pages.index');
    Route::get('/pages/create', \App\Livewire\Admin\Pages\Form::class)->middleware('permission:manage_pages')->name('pages.create');
    Route::get('/pages/{pageId}/edit', \App\Livewire\Admin\Pages\Form::class)->middleware('permission:manage_pages')->name('pages.edit');
    Route::get('/gallery', \App\Livewire\Admin\Gallery\Index::class)->middleware('permission:manage_gallery')->name('gallery');
    Route::get('/core-committees', \App\Livewire\Admin\CoreCommittees\Index::class)->middleware('permission:manage_core_committees')->name('core-committees');
    Route::get('/guests', \App\Livewire\Admin\Guests\Index::class)->middleware('permission:manage_guests')->name('guests');
    Route::get('/sponsors', \App\Livewire\Admin\Sponsors\Index::class)->middleware('permission:manage_sponsors')->name('sponsors');
});

// Public Pages Route
Route::get('/pages/{slug}', \App\Livewire\Public\PageView::class)->name('public.page');

Route::get('/test-broadcast', function () {
    event(new \App\Events\PlayerOnAuction(1, ['name' => 'Virat Kohli', 'role' => 'Batsman'], 200));
    return 'Event dispatched! Check your browser console on the dashboard or any page.';
});

// Public Live Auction Route
Route::get('/auction/live/{auction}', \App\Livewire\Public\Auction\Live::class)->name('auction.live');

Route::middleware(['auth'])->group(function () {
    // Bidding API Routes
    Route::post('/api/auction/{auction}/start', [\App\Http\Controllers\AuctionController::class, 'startAuction']);
    Route::post('/api/auction/{auction}/next-player', [\App\Http\Controllers\AuctionController::class, 'nextPlayer']);
    Route::post('/api/auction/bid', [\App\Http\Controllers\AuctionController::class, 'placeBid']);
    Route::post('/api/auction/player/{auctionPlayer}/sold', [\App\Http\Controllers\AuctionController::class, 'markSold']);
    Route::post('/api/auction/player/{auctionPlayer}/unsold', [\App\Http\Controllers\AuctionController::class, 'markUnsold']);
    Route::post('/api/auction/{auction}/end', [\App\Http\Controllers\AuctionController::class, 'endAuction']);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/reports/auction', \App\Livewire\Reports\AuctionReport::class)->name('reports.auction');
    Route::get('/teams/{team}/pdf', [\App\Http\Controllers\Admin\PdfController::class, 'teamSquad'])->name('teams.pdf');
});

Route::middleware(['auth', 'role:team_owner'])->group(function () {
    Route::get('/team-owner/my-team', \App\Livewire\TeamOwner\MyTeam::class)->name('team_owner.my_team');
    Route::get('/team/auction/{auction}', \App\Livewire\Team\Auction\Bidding::class)->name('team.auction.bidding');
});

require __DIR__.'/auth.php';
