# NPL – IPL-Style Cricket Player Auction Platform

A full-featured web application for running **IPL-style live cricket player auctions** for a local league (NPL). It combines a public league website (teams, players, gallery, blogs, sponsors), online **player registration with payment**, an **admin panel**, and a **real-time auction room** where team owners bid on players live while spectators watch.

Built with **Laravel 12**, **Livewire 3 / Volt**, **Tailwind CSS**, and **Pusher / Laravel Echo** for real-time broadcasting.

---

## Table of Contents

1. [Features](#features)
2. [Tech Stack](#tech-stack)
3. [User Roles & Permissions](#user-roles--permissions)
4. [How the Live Auction Works](#how-the-live-auction-works)
5. [Project Structure](#project-structure)
6. [Database Schema](#database-schema)
7. [Routes Overview](#routes-overview)
8. [Installation](#installation)
9. [Environment Configuration](#environment-configuration)
10. [Running the Project](#running-the-project)
11. [Application Settings](#application-settings)
12. [Admin Theme (UI)](#admin-theme-ui)
13. [Testing](#testing)
14. [Known Issues / TODO](#known-issues--todo)

---

## Features

### Public Website
- **Home page** with banners/slider, approved teams, random players, gallery, core committee, guests and sponsors
- **Teams**: listing, team detail page (squad) and **team registration**
- **Players**: browsable player list and **player registration** form (photo upload, role, batting/bowling style, stats)
- **Live auction viewer** (`/auction/live/{auction}`): real-time player on the block, current bid, team purses and squads
- **Match schedule** (`/schedule`): fixtures and results grouped by date, filter by team, and a **points table**. Each team page also lists that team's matches
- **Blogs**, **Gallery**, **Contact form**, and admin-managed **CMS pages** (`/pages/{slug}`)
- Visitor tracking (`TrackVisitor` middleware) for analytics

### Player Registration & Payments
- Player photos are resized and converted to **WebP** (Intervention Image)
- Configurable registration fee, paid through a pluggable payment gateway:
  - **Razorpay** (live payments)
  - **Mock** gateway (for testing)
  - **None** (free registration, admin approves manually)
- Successful payment automatically approves the player

### Admin Panel (`/admin`)
- Dashboard and **Analytics**
- Manage **Teams, Players, Users, Auctions**
- **Auction Control room** – start the auction, move to the next player, sell / mark unsold, delete bids, sell to a specific bid, recall unsold players, revert a player, end the auction
- CMS: **Banners, Pages, Blogs, Gallery** (incl. bulk photo upload), **Core Committee, Guests, Sponsors, Contact messages**
- **Match Schedule** for each auction (`/admin/auctions/{auction}/schedule`): auto-generate a single or double round-robin league (start date, daily time slots, gap between match days, venue; a team never plays twice in one day), add playoff matches by hand, and enter scores, winner and result
- Global **Settings** (branding, social links, fees, payment gateway, base price, AdSense, etc.)
- **PDF exports** – full player list and team squad sheets (DomPDF)
- Auction **reports**

### Team Owner Area
- **My Team** – view own squad and budget
- **Bidding screen** (`/team/auction/{auction}`) – place bids in real time

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP 8.2+, Laravel 12 |
| UI | Livewire 3, Livewire Volt, Blade, Alpine.js, Tailwind CSS 3 |
| Real-time | Laravel Broadcasting → Pusher (Laravel Reverb also installed), Laravel Echo + pusher-js |
| Queue | Laravel Queue (database driver) – used for the auction timer |
| Auth | Laravel Breeze (Volt), Sanctum |
| Payments | Razorpay PHP SDK (`razorpay/razorpay`) |
| PDF | `barryvdh/laravel-dompdf` |
| Images | `intervention/image` v3 (GD driver) |
| Front-end libs | jQuery, Select2, Swiper, Quill editor, canvas-confetti |
| Build | Vite 7 |
| Database | MySQL (XAMPP) or SQLite |

---

## User Roles & Permissions

Roles are stored in `users.role`:

| Role | Access |
|---|---|
| `admin` | Full access to everything |
| `sub_admin` | Admin panel, limited to the permissions assigned to them |
| `team_owner` | My Team page and the live bidding screen |
| `viewer` | Default role – public site only |

Sub-admin permissions (stored as a JSON array in `users.permissions`, checked by `PermissionMiddleware` via `User::hasPermission()`):

```
manage_teams      manage_players    manage_users      view_analytics
manage_auctions   manage_settings   manage_banners    manage_pages
manage_gallery    manage_core_committees  manage_guests
manage_sponsors   manage_contacts
```

Middleware aliases: `role:admin,sub_admin` and `permission:<name>`.

---

## How the Live Auction Works

The core logic lives in [`app/Services/AuctionService.php`](app/Services/AuctionService.php).

1. **Setup** – the admin creates an auction, adds the participating teams (`auction_team`) and the players (`auction_players`, ordered by `order_no`). Each auction has a single `auction_states` row holding the live state.
2. **Start** – the first *pending, approved* player becomes `current`, the timer starts (`timer_seconds`, default 15) and the auction status becomes `live`.
3. **Bidding** – a team owner places a bid. The service validates (inside a DB transaction with a row lock):
   - the auction is live and the team participates in it
   - the first bid equals the player's **base price**; after that, bid ≥ current bid + increment
   - the team has enough **remaining budget**
   - the team is not already the highest bidder
   
   A valid bid is saved, the timer is reset, and a `BidPlaced` event is broadcast.
4. **Bid increments** – `calculateNextBidIncrement()` uses, in order: a manual increment set by the admin → configured slab rules (`bid_increment_rule` JSON: `[{upto, increment}]`) → built-in fallback slabs (₹5,000 up to ₹10,00,000).
5. **Timer / auto-sell** – each bid dispatches a delayed `ProcessAuctionTimer` job. When it runs, if no newer bid has reset the timer and `auto_sold` is on, the player is automatically sold to the highest bidder.
6. **Sold / Unsold** – on sale, the player is assigned to the team and the price is deducted from `teams.remaining_budget`. Admins can also mark a player unsold, sell to a specific earlier bid, delete a bid, or **revert** a player (the team is refunded and the bids are cleared).
7. **Next player / End** – the admin moves to the next pending player; unsold players can be recalled into the pool. Ending sets the auction to `completed`.

### Broadcast events
All events implement `ShouldBroadcastNow` on the public channel **`auction.{auctionId}`**:

| Event | Fired when |
|---|---|
| `PlayerOnAuction` | A new player comes up (or all bids on them are deleted) |
| `BidPlaced` | A bid is placed (or the top bid changes after a deletion) |
| `PlayerSold` | A player is sold |
| `PlayerUnsold` | A player is marked unsold |
| `AuctionEnded` | The auction ends (also used as a generic "refresh" signal) |

---

## Project Structure

```
app/
├── Events/                 # Broadcast events (BidPlaced, PlayerSold, ...)
├── Helpers/helpers.php     # Global helpers, e.g. setting('key', default)
├── Http/
│   ├── Controllers/
│   │   ├── Admin/PdfController.php     # Player list / team squad PDFs
│   │   ├── Api/                        # Public JSON API
│   │   ├── AuctionController.php       # Bidding/auction actions endpoints
│   │   └── PaymentController.php       # Payment gateway callback
│   └── Middleware/         # RoleMiddleware, PermissionMiddleware, TrackVisitor
├── Interfaces/PaymentGatewayInterface.php
├── Jobs/ProcessAuctionTimer.php        # Auction countdown / auto-sell
├── Livewire/
│   ├── Admin/              # Admin panel components
│   ├── Public/             # Public website components (incl. Auction/Live)
│   ├── Team/Auction/Bidding.php        # Team owner bidding screen
│   ├── TeamOwner/MyTeam.php
│   └── Reports/AuctionReport.php
├── Models/                 # Eloquent models
└── Services/
    ├── AuctionService.php
    └── Payments/           # PaymentService + Razorpay / Mock gateways
resources/views/
├── layouts/                # app, guest, ipl, public, auction-live
├── livewire/               # Blade views for Livewire components
├── pdf/                    # PDF templates
└── public/players/         # Razorpay checkout + payment result pages
routes/
├── web.php                 # Public, admin, team owner routes
├── api.php                 # Public JSON API
├── auth.php                # Breeze/Volt auth routes
├── channels.php
└── console.php             # Scheduler (queue:work every minute)
```

---

## Database Schema

Main tables:

| Table | Purpose / key columns |
|---|---|
| `users` | `role`, `permissions` (JSON), `image` |
| `teams` | `name`, `short_name`, `logo`, `primary_color`, `budget`, `remaining_budget`, `owner_id`, `is_approved` |
| `players` | `name`, `photo`, `role` (batsman/bowler/all-rounder/wicketkeeper), `country`, `city`, `contact_no`, `base_price`, `category` (marquee/set-a/b/c), `stats` (JSON), `status` (available/sold/unsold/unavailable), `current_team_id`, `is_approved`, `payment_status`, `is_captain`, `is_favorite` |
| `auctions` | `guid`, `title`, `auction_date`, `status` (upcoming/live/paused/completed) |
| `auction_team` | Teams taking part in an auction |
| `auction_players` | Players in an auction: `order_no`, `status` (pending/current/sold/unsold), `final_price`, `sold_to_team_id` |
| `auction_states` | Live state: current player, highest bid/team, `bid_increment_rule`, `timer_seconds`, `timer_end_at`, `auto_sold`, `manual_bid_increment` |
| `bids` | `auction_player_id`, `team_id`, `bid_amount` |
| `fixtures` | Match schedule: `auction_id`, `match_no`, `stage`, `team_a_id`, `team_b_id`, `match_date`, `venue`, `status` (scheduled/live/completed/abandoned), scores, `winner_team_id`, `result` |
| `payments` | Player registration payments (`gateway`, `status`, `transaction_id`) |
| `settings` | Key/value site settings |
| CMS | `banners`, `pages`, `blogs`, `galleries`, `core_committees`, `guests`, `sponsors`, `contact_messages`, `visitors` |

---

## Routes Overview

### Public
| URL | Description |
|---|---|
| `/home` | Home page |
| `/teams`, `/teams/{team}`, `/teams/register` | Teams |
| `/players`, `/players/register` | Players |
| `/auction/live/{auction}` | Live auction viewer |
| `/schedule?auction={guid}&team={id}` | Match schedule & points table |
| `/blogs`, `/blogs/{slug}`, `/gallery`, `/contact`, `/pages/{slug}` | Content |
| `/payment/checkout/{payment}`, `/payment/callback` | Payment flow |

### Admin (`/admin/*`, roles `admin` / `sub_admin`)
`dashboard`, `teams`, `players`, `players/pdf`, `users`, `analytics`, `auctions`, `auctions/create`, `auctions/{id}/edit`, `auctions/{id}/schedule`, `auction/control/{auction}`, `settings`, `banners`, `pages`, `blogs`, `gallery`, `core-committees`, `guests`, `sponsors`, `contacts`

### Team owner
`/team-owner/my-team`, `/team/auction/{auction}`

### Authenticated
`/reports/auction`, `/teams/{team}/pdf`, plus the auction action endpoints:
```
POST /api/auction/{auction}/start
POST /api/auction/{auction}/next-player
POST /api/auction/bid
POST /api/auction/player/{auctionPlayer}/sold
POST /api/auction/player/{auctionPlayer}/unsold
POST /api/auction/{auction}/end
```

### Public JSON API (`routes/api.php`, prefix `/api`)
```
GET /api/settings
GET /api/auction/current
GET /api/auction/live/{auction}
GET /api/players          GET /api/players/{player}
GET /api/teams            GET /api/teams/{team}
```

---

## Installation

**Requirements:** PHP 8.2+ (with the GD extension), Composer, Node.js 18+, MySQL (e.g. via XAMPP) or SQLite.

```bash
# 1. Install dependencies
composer install
npm install

# 2. Environment
cp .env.example .env
php artisan key:generate

# 3. Configure the database in .env, then migrate
php artisan migrate
php artisan db:seed --class=SettingSeeder

# 4. Public storage link (for player photos, logos, gallery)
php artisan storage:link

# 5. Build front-end assets
npm run build
```

Creating the first admin (with tinker):
```bash
php artisan tinker
>>> \App\Models\User::create(['name'=>'Admin','email'=>'admin@example.com','password'=>'secret123','role'=>'admin']);
```

---

## Environment Configuration

Besides the standard Laravel values, set the following in `.env`:

```env
APP_NAME=NPL
APP_URL=http://localhost/...

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=npl_auction
DB_USERNAME=root
DB_PASSWORD=

QUEUE_CONNECTION=database        # required for the auction timer
BROADCAST_CONNECTION=pusher      # required for real-time updates

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

# Razorpay (only if the razorpay gateway is used)
RAZORPAY_KEY=
RAZORPAY_SECRET=
```

> The front end (`resources/js/echo.js`) is currently configured for **Pusher**. A Reverb configuration is included but commented out. To self-host WebSockets, set the `REVERB_*` variables, set `BROADCAST_CONNECTION=reverb`, switch `echo.js` to the Reverb block and run `php artisan reverb:start`.

---

## Running the Project

Development (server + queue worker + logs + Vite together):
```bash
composer run dev
```

Or run them separately:
```bash
php artisan serve
php artisan queue:listen --tries=1 --timeout=0   # needed for the auction timer / auto-sell
npm run dev
```

On a production server, add the Laravel scheduler to cron. `routes/console.php` runs `queue:work --stop-when-empty` every minute:
```
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```
> Note: with only the per-minute scheduler, auto-sell can run up to about a minute late. For accurate timers, keep a dedicated `queue:work` process running (Supervisor).

---

## Application Settings

Managed from **Admin → Settings** and read with the `setting('key', $default)` helper:

| Key | Purpose |
|---|---|
| `app_name`, `logo`, `favicon`, `season` | Branding |
| `contact_email`, `contact_phone` | Contact info |
| `facebook`, `instagram`, `twitter`, `youtube` | Social links |
| `our_mission`, `our_vision` | About content |
| `player_base_price` | Default base price for newly registered players |
| `registration_fee` | Player registration fee |
| `active_payment_gateway` | `razorpay`, `mock` or `none` |
| `adsense_code` | Google AdSense snippet |
| `developer_name`, `developer_url` | Footer credit |

---

## Admin Theme (UI)

The whole application uses one light SaaS theme (deep navy `#172554` on light gray): the public website (`layouts/ipl`), the live auction and bidding screens (`layouts/auction-live`), the admin / team owner area (`layouts/app`) and the auth pages (`layouts/guest`).

- **Colours:** CSS variables in `resources/css/app.css` (`:root`), exposed to Tailwind as `brand`, `canvas`, `surface`, `line`, `ink`, `muted`, `faint`, `danger`, `success`, `warning` (see `tailwind.config.js`). Change a colour in one place and it applies everywhere.
- **Components:** `.card`, `.btn-primary`, `.btn-secondary`, `.btn-danger`, `.btn-ghost`, `.badge-*`, `.alert-*`, `.form-label`, `.nav-item`, `.menu-panel` (same file).
- **Base styles:** inputs, selects, checkboxes, tables, pagination, Select2 and Quill are styled centrally, inside `.theme-app` (set on `<body>` in every layout).
- **Sidebar menu:** defined once in `resources/views/layouts/partials/sidebar-nav.blade.php` (used for desktop and mobile). It can be collapsed, and the choice is remembered in the browser.
- **Public header & footer:** `resources/views/layouts/ipl.blade.php` (menu items are built once and reused for desktop and mobile).
- **Icons:** `<x-icon name="..." />` (`resources/views/components/icon.blade.php`).

After changing styles, run `npm run build` (the built files in `public/build` are committed).

---

## Testing

```bash
composer test        # or: php artisan test
```
The test suite currently covers the Breeze authentication and profile features only. `test_livewire_bulk.php` is an ad-hoc script that checks the gallery bulk upload.

---

## Known Issues / TODO

- `/test-broadcast` in `routes/web.php` is a public debug route. Remove it or protect it before going to production.
- The admin **blogs** routes have no `permission:` middleware, so any sub-admin can manage blogs.
- `.env.example` is missing the Pusher and Razorpay keys.
- `check.php`, `clean.php`, `swap.php` and `test_livewire_bulk.php` are one-off helper scripts, some with hard-coded paths. They can be deleted.
- `DatabaseSeeder` only creates a test user. There is no admin/demo-data seeder.
- Tests for the auction logic (`AuctionService`) are missing.

---

## License

MIT
