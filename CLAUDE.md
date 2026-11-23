# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

NPL is an IPL-style cricket player auction platform: public league website, player registration with payments, an admin panel, and a real-time auction room. Laravel 12, Livewire 3 + Volt, Tailwind 3, Pusher/Laravel Echo. Runs locally under XAMPP (MySQL). `README.md` holds the full feature list, schema table, route list and settings keys — read it for detail not repeated here.

## Commands

```bash
composer run dev          # serve + queue:listen + pail logs + vite, all together
composer test             # config:clear then php artisan test
php artisan test --filter=ProfileTest          # single test class / method
php artisan test tests/Feature/Auth/AuthenticationTest.php
vendor/bin/pint           # code style (Laravel preset, no pint.json)
npm run build             # rebuild assets — public/build IS committed, rebuild after CSS/JS changes
php artisan migrate
php artisan db:seed --class=SettingSeeder
```

Tests use in-memory SQLite (`phpunit.xml`) and currently only cover Breeze auth/profile. There are no tests for `AuctionService`.

A queue worker must be running for the auction timer / auto-sell (`QUEUE_CONNECTION=database`). In production `routes/console.php` schedules `queue:work --stop-when-empty` every minute, so auto-sell can lag up to ~1 minute without a dedicated worker.

## Architecture

### Live auction (the core of the app)
- **All auction state changes go through `app/Services/AuctionService.php`** (start, nextPlayer, placeBid, deleteBid, markSold, markSoldToBid, markUnsold, recallUnsoldPlayers, revertPlayer, endAuction). Each method runs in a `DB::transaction` with row locks and broadcasts an event afterwards. Callers: `Livewire/Admin/Auctions/Control.php` (admin control room), `Livewire/Team/Auction/Bidding.php` (team owner bidding), `Http/Controllers/AuctionController.php` (`/api/auction/*` POST endpoints in `web.php`, session-auth), and `Jobs/ProcessAuctionTimer.php`. Don't put auction mutation logic in components.
- Data model: `auctions` → `auction_team` (participating teams) and `auction_players` (`order_no`, status pending/current/sold/unsold) → `bids`. Each auction has one `auction_states` row with the live state (current player, highest bid/team, `bid_increment_rule` JSON slabs, `manual_bid_increment`, `timer_seconds`, `timer_end_at`, `auto_sold`). Selling deducts from `teams.remaining_budget`; reverting refunds it.
- `AuctionService::calculateNextBidIncrement()` is static and also used by the bidding UI: manual increment → configured slabs → built-in fallback slabs.
- Timer: every bid dispatches a delayed `ProcessAuctionTimer` carrying the `timer_end_at` ISO string; the job aborts if the state's `timer_end_at` changed (newer bid) and auto-sells otherwise.
- Real-time: events in `app/Events` (`PlayerOnAuction`, `BidPlaced`, `PlayerSold`, `PlayerUnsold`, `AuctionEnded`) are `ShouldBroadcastNow` on public channel `auction.{auctionId}`. Livewire components subscribe via `getListeners()` with `echo:auction.{id},EventName`. `AuctionEnded` doubles as a generic "refresh" signal. Frontend Echo is configured for Pusher in `resources/js/echo.js` (Reverb block present but commented out).

### Auth, roles, permissions
- `users.role`: `admin`, `sub_admin`, `team_owner`, `viewer`. `users.permissions` is a JSON array checked by `User::hasPermission()`.
- Middleware aliases registered in `bootstrap/app.php`: `role:admin,sub_admin` and `permission:manage_x`. Admin routes in `routes/web.php` apply `permission:` per route — new admin routes need one (the blogs routes currently lack it). `TrackVisitor` is appended to the web group.
- Auth pages are Breeze Volt components in `resources/views/livewire/pages/auth` (`routes/auth.php`).

### UI layer
- Pages are full-page Livewire class components routed directly in `routes/web.php`, grouped by area: `Livewire/Public`, `Livewire/Admin`, `Livewire/Team`, `Livewire/TeamOwner`, `Livewire/Reports`. Views mirror this under `resources/views/livewire/`. The home page is a closure route in `web.php`.
- Layouts: `layouts/ipl` (public site), `layouts/auction-live` (live viewer + bidding), `layouts/app` (admin/team owner, sidebar in `layouts/partials/sidebar-nav.blade.php`), `layouts/guest` (auth).
- Single theme: CSS variables in `resources/css/app.css` exposed as Tailwind colors (`brand`, `accent`, `canvas`, `surface`, `ink`, …) in `tailwind.config.js`, plus shared classes (`.card`, `.btn-primary`, `.badge-*`, `.page-intro`, …). Use these tokens/classes rather than raw colors. Icons: `<x-icon name="..." />`.

### Other pieces
- **Settings**: key/value `settings` table read via the global `setting('key', $default)` helper (`app/Helpers/helpers.php`, autoloaded). `Setting::get` uses `Cache::rememberForever('setting_'.$key)`, so write through `Setting::set()` (which clears the cache) — direct DB edits won't show until the cache is cleared.
- **Payments**: `Services/Payments/PaymentService` + `PaymentGatewayInterface` with `RazorpayGateway` / `MockGateway`. The gateway is chosen from `setting('active_payment_gateway')` (`razorpay|mock|none`) in both `Livewire/Public/Players/Register.php` and `PaymentController::callback` — keep them in sync when adding a gateway. Successful payment auto-approves the player.
- **Fixtures**: `Services/FixtureScheduler.php` generates single/double round-robin schedules per auction (season); admin UI `Livewire/Admin/Fixtures/Index`, public `/schedule` with points table. Playoffs (`Semi Final 1`, `Semi Final 2`, `Final` — stage names are matched exactly) are created with null teams + `team_a_label`/`team_b_label` placeholders (shown as "TBC"); `FixtureScheduler::advancePlayoffs()` runs after every admin save and fills the semis from the top 4 once all league matches are done, then moves semi winners into the Final. Any code reading `$fixture->teamA/teamB` must handle null.
- **Images**: uploads are resized/converted to WebP with Intervention Image (GD); stored on the `public` disk (needs `php artisan storage:link`).
- **PDFs**: `Http/Controllers/Admin/PdfController` (DomPDF) with templates in `resources/views/pdf`.
- **Public JSON API** (`routes/api.php`): read-only settings, current/live auction, players, teams.
- Teams are route-bound by `guid` (`{team:guid}`).

## Gotchas
- Images missing everywhere → check the `public/storage` link targets this project's `storage/app/public` (it broke once after the project folder was moved). Fix: remove the link, run `php artisan storage:link`. Views mix `asset('storage/...')` (request-relative) and `Storage::url()` (uses `APP_URL`), so a wrong `APP_URL` breaks only some images.
- `/test-broadcast` in `web.php` is a public debug route.
- Root-level `check.php`, `clean.php`, `swap.php`, `test_livewire_bulk.php` are one-off scripts, not part of the app.
- `.env.example` lacks the Pusher and Razorpay keys; real-time needs `BROADCAST_CONNECTION=pusher` plus `PUSHER_*` / `VITE_PUSHER_*`.
