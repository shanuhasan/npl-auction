<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LiveAuctionController;
use App\Http\Controllers\Api\PlayerController;
use App\Http\Controllers\Api\TeamController;

// App Settings & Version API
Route::get('/settings', function () {
    $baseUrl = url('/');
    $logo = setting('logo');
    return response()->json([
        'success' => true,
        'data' => [
            'app_name' => setting('app_name', config('app.name')),
            'season' => setting('season', date('Y')),
            'logo_url' => $logo ? $baseUrl . '/storage/' . $logo : null,
            'contact_email' => setting('contact_email'),
            'contact_phone' => setting('contact_phone'),
            'social_links' => [
                'facebook' => setting('facebook'),
                'instagram' => setting('instagram'),
                'twitter' => setting('twitter'),
                'youtube' => setting('youtube'),
            ],
            'version' => '1.0.0', // App version checking
            'force_update' => false,
            'maintenance_mode' => app()->isDownForMaintenance(),
            'base_url' => $baseUrl,
        ]
    ]);
})->name('api.settings');

// Public Live Auction API
Route::get('/auction/current', [LiveAuctionController::class, 'current'])->name('api.auction.current');
Route::get('/auction/live/{auction}', [LiveAuctionController::class, 'show'])->name('api.auction.live');

// Public Players API
Route::get('/players', [PlayerController::class, 'index'])->name('api.players.index');
Route::get('/players/{player}', [PlayerController::class, 'show'])->name('api.players.show');

// Public Teams API
Route::get('/teams', [TeamController::class, 'index'])->name('api.teams.index');
Route::get('/teams/{team}', [TeamController::class, 'show'])->name('api.teams.show');
