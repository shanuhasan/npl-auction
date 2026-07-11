<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-2">
                        @if(setting('logo'))
                            <img src="{{ asset('storage/' . setting('logo')) }}" alt="Logo" class="block h-9 w-auto object-contain">
                        @else
                            <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                        @endif
                    </a>
                </div>

                <!-- Navigation Links -->
                <!-- <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </div> -->
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile')" wire:navigate>
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <button wire:click="logout" class="w-full text-start">
                            <x-dropdown-link>
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </button>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @auth
                @if(auth()->user()->role === 'admin')
                    <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')" wire:navigate class="text-accent-red font-bold">
                        Dashboard (Admin)
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.auctions')" :active="request()->routeIs('admin.auctions*')" wire:navigate>
                        Manage Auctions
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.teams')" :active="request()->routeIs('admin.teams*')" wire:navigate>
                        Manage Teams
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.players')" :active="request()->routeIs('admin.players*')" wire:navigate>
                        Manage Players
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.users')" :active="request()->routeIs('admin.users*')" wire:navigate>
                        Manage Users
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.analytics')" :active="request()->routeIs('admin.analytics')" wire:navigate>
                        Analytics
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.settings')" :active="request()->routeIs('admin.settings')" wire:navigate>
                        Settings
                    </x-responsive-nav-link>
                @elseif(auth()->user()->role === 'team_owner')
                    @php
                        $myTeam = \App\Models\Team::where('owner_id', auth()->id())->first();
                        $activeAuction = \App\Models\Auction::whereIn('status', ['live', 'upcoming'])->first();
                    @endphp
                    @if($myTeam)
                        <x-responsive-nav-link :href="route('public.teams.show', $myTeam->id)" :active="request()->routeIs('public.teams.show')" wire:navigate class="text-success-green font-bold">
                            My Team
                        </x-responsive-nav-link>
                    @endif
                    @if($activeAuction)
                        <x-responsive-nav-link :href="route('team.auction.bidding', $activeAuction->guid)" :active="request()->routeIs('team.auction.bidding')" wire:navigate>
                            Bidding Room
                        </x-responsive-nav-link>
                    @endif
                @endif
                <x-responsive-nav-link :href="route('public.players')" :active="request()->routeIs('public.players')" wire:navigate>
                    All Players
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('reports.auction')" :active="request()->routeIs('reports.auction')" wire:navigate class="text-[#00C853] font-bold">
                    Reports
                </x-responsive-nav-link>
            @endauth
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
                <div class="font-medium text-sm text-gray-500">{{ auth()->user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile')" wire:navigate>
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <button wire:click="logout" class="w-full text-start">
                    <x-responsive-nav-link>
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </button>
            </div>
        </div>
    </div>
</nav>
