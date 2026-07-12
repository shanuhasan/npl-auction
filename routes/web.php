<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/teams', \App\Livewire\Public\Teams\Index::class)->name('public.teams');
Route::get('/teams/{team}', \App\Livewire\Public\Teams\Show::class)->name('public.teams.show');
Route::get('/players', \App\Livewire\Public\Players\Index::class)->name('public.players');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', \App\Livewire\Admin\Dashboard::class)->name('dashboard');
    Route::get('/teams', \App\Livewire\Admin\Teams\Index::class)->name('teams');
    Route::get('/players', \App\Livewire\Admin\Players\Index::class)->name('players');
    Route::get('/users', \App\Livewire\Admin\Users\Index::class)->name('users');
    Route::get('/analytics', \App\Livewire\Admin\Analytics::class)->name('analytics');
    Route::get('/auctions', \App\Livewire\Admin\Auctions\Index::class)->name('auctions');
    Route::get('/auctions/create', \App\Livewire\Admin\Auctions\Create::class)->name('auctions.create');
    Route::get('/auctions/{auction}/edit', \App\Livewire\Admin\Auctions\Edit::class)->name('auctions.edit');
    Route::get('/auction/control/{auction}', \App\Livewire\Admin\Auctions\Control::class)->name('auction.control');
    Route::get('/settings', \App\Livewire\Admin\Settings::class)->name('settings');
});

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
    Route::get('/team/auction/{auction}', \App\Livewire\Team\Auction\Bidding::class)->name('team.auction.bidding');
});

require __DIR__.'/auth.php';
