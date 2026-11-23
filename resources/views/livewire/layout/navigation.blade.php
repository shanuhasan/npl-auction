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

@php
    $user = auth()->user();
    $roleLabels = ['admin' => 'Administrator', 'sub_admin' => 'Sub Admin', 'team_owner' => 'Team Owner', 'viewer' => 'Viewer'];
    $canSeeMessages = $user && $user->hasPermission('manage_contacts');
    $unreadMessages = $canSeeMessages ? \App\Models\ContactMessage::where('is_read', false)->latest()->take(5)->get() : collect();
    $unreadCount = $canSeeMessages ? \App\Models\ContactMessage::where('is_read', false)->count() : 0;
    $initials = collect(explode(' ', trim($user->name ?? '')))->filter()->take(2)->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('');
@endphp

<header class="sticky top-0 z-30 flex h-16 shrink-0 items-center gap-3 border-b border-line bg-canvas/75 px-4 backdrop-blur-md sm:px-6 lg:px-8">
    <!-- Mobile: open drawer -->
    <button type="button" class="btn-ghost -ml-2 p-2 lg:hidden" @click="$dispatch('toggle-sidebar')" aria-label="Open menu">
        <x-icon name="menu" />
    </button>

    <!-- Desktop: collapse sidebar -->
    <button type="button" class="btn-ghost -ml-3 hidden p-2 lg:inline-flex" @click="$dispatch('toggle-sidebar-collapse')" title="Toggle sidebar" aria-label="Toggle sidebar">
        <x-icon name="menu" />
    </button>

    <div class="hidden min-w-0 sm:block">
        <p class="truncate text-sm text-muted">
            <span class="font-medium text-ink">{{ now()->format('l, j F Y') }}</span>
            @if(setting('season'))<span class="mx-2 text-faint">/</span>{{ setting('season') }}@endif
        </p>
    </div>

    <div class="ml-auto flex items-center gap-1 sm:gap-2">
        <a href="{{ route('home') }}" target="_blank" rel="noopener" class="btn-ghost hidden px-3 sm:inline-flex" title="Open public website">
            <x-icon name="globe" class="w-[18px] h-[18px]" />
            <span>View site</span>
        </a>

        @if($canSeeMessages)
            <!-- Notifications -->
            <div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape="open = false">
                <button type="button" @click="open = !open" class="btn-ghost relative p-2" aria-label="Notifications" :aria-expanded="open">
                    <x-icon name="bell" />
                    @if($unreadCount > 0)
                        <span class="absolute right-1 top-1 flex h-4 min-w-[1rem] items-center justify-center rounded-full bg-danger px-1 text-[10px] font-semibold leading-none text-white ring-2 ring-surface">
                            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                        </span>
                    @endif
                </button>

                <div x-show="open" x-cloak x-transition.origin.top.right
                     class="menu-panel absolute right-0 z-50 mt-2 w-80 max-w-[calc(100vw-2rem)]">
                    <div class="flex items-center justify-between px-4 py-2.5">
                        <p class="text-sm font-semibold text-ink">Notifications</p>
                        @if($unreadCount > 0)
                            <span class="badge-brand">{{ $unreadCount }} new</span>
                        @endif
                    </div>
                    <div class="max-h-80 overflow-y-auto border-y border-line">
                        @forelse($unreadMessages as $message)
                            <a href="{{ route('admin.contacts') }}" wire:navigate class="flex gap-3 px-4 py-3 hover:bg-surface-muted">
                                <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-accent"></span>
                                <span class="min-w-0">
                                    <span class="block truncate text-sm font-medium text-ink">{{ $message->name }}</span>
                                    <span class="block truncate text-xs text-muted">{{ \Illuminate\Support\Str::limit($message->message, 60) }}</span>
                                    <span class="block text-[11px] text-faint mt-0.5">{{ $message->created_at?->diffForHumans() }}</span>
                                </span>
                            </a>
                        @empty
                            <div class="px-4 py-8 text-center">
                                <x-icon name="check-circle" class="mx-auto mb-2 w-6 h-6 text-faint" />
                                <p class="text-sm text-muted">You're all caught up.</p>
                            </div>
                        @endforelse
                    </div>
                    <a href="{{ route('admin.contacts') }}" wire:navigate class="block px-4 py-2.5 text-center text-sm font-medium text-brand hover:bg-surface-muted">View all messages</a>
                </div>
            </div>
        @endif

        <!-- Profile menu -->
        <div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape="open = false">
            <button type="button" @click="open = !open" :aria-expanded="open"
                    class="flex items-center gap-2.5 rounded-lg py-1.5 pl-1.5 pr-2 transition-colors hover:bg-surface-subtle">
                @if($user->image)
                    <img src="{{ asset('storage/' . $user->image) }}" alt="" class="h-8 w-8 rounded-full object-cover ring-1 ring-line">
                @else
                    <span class="flex h-8 w-8 items-center justify-center rounded-sm bg-brand text-xs font-semibold text-white">{{ $initials ?: 'U' }}</span>
                @endif
                <span class="hidden text-left md:block">
                    <span class="block max-w-[10rem] truncate text-sm font-medium leading-tight text-ink"
                          x-data="{{ json_encode(['name' => $user->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name">{{ $user->name }}</span>
                    <span class="block text-xs leading-tight text-muted">{{ $roleLabels[$user->role] ?? ucfirst($user->role) }}</span>
                </span>
                <x-icon name="chevron-down" class="hidden w-4 h-4 text-faint md:block" />
            </button>

            <div x-show="open" x-cloak x-transition.origin.top.right class="menu-panel absolute right-0 z-50 mt-2 w-60">
                <div class="border-b border-line px-3.5 pb-2.5 pt-1.5">
                    <p class="truncate text-sm font-medium text-ink">{{ $user->name }}</p>
                    <p class="truncate text-xs text-muted">{{ $user->email }}</p>
                </div>
                <div class="py-1">
                    <a href="{{ route('profile') }}" wire:navigate class="menu-item"><x-icon name="user" /> Profile</a>
                    <a href="{{ route('dashboard') }}" wire:navigate class="menu-item"><x-icon name="home" /> Dashboard</a>
                    <a href="{{ route('home') }}" target="_blank" class="menu-item sm:hidden"><x-icon name="globe" /> View site</a>
                </div>
                <div class="border-t border-line pt-1">
                    <button wire:click="logout" class="menu-item text-danger hover:text-danger">
                        <x-icon name="logout" class="!text-danger" /> Log out
                    </button>
                </div>
            </div>
        </div>
    </div>
</header>
