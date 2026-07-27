<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LiveAuctionController;
use App\Http\Controllers\Api\PlayerController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\SettingsController;

// App Settings & Version API
Route::get('/settings', [SettingsController::class, 'index'])->name('api.settings');

// Public Live Auction API
Route::get('/auction/current', [LiveAuctionController::class, 'current'])->name('api.auction.current');
Route::get('/auction/live/{auction}', [LiveAuctionController::class, 'show'])->name('api.auction.live');

// Public Players API
Route::get('/players', [PlayerController::class, 'index'])->name('api.players.index');
Route::get('/players/{player}', [PlayerController::class, 'show'])->name('api.players.show');

// Public Teams API
Route::get('/teams', [TeamController::class, 'index'])->name('api.teams.index');
Route::get('/teams/{team}', [TeamController::class, 'show'])->name('api.teams.show');
