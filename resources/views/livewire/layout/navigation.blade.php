<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }
}; ?>

@php
    $user = auth()->user();
    $isCustomer = $user->isCustomer();
    $navItems = $isCustomer ? [
        ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'home'],
        ['label' => 'Box Sharing', 'route' => 'customer.box.sharing', 'icon' => 'box'],
        ['label' => 'Box Direct', 'route' => 'customer.box.direct', 'icon' => 'box'],
        ['label' => 'Setor Resi', 'route' => 'customer.setor-resi', 'icon' => 'document'],
        ['label' => 'Invoice', 'route' => 'customer.invoice', 'icon' => 'document'],
        ['label' => 'Checkout', 'route' => 'customer.checkout', 'icon' => 'truck'],
        ['label' => 'Komplain', 'route' => 'customer.komplain', 'icon' => 'exclamation'],
        ['label' => 'Kalkulator', 'route' => 'customer.kalkulator', 'icon' => 'calculator'],
        ['label' => 'No Tuan', 'route' => 'customer.no-tuan', 'icon' => 'archive'],
        ['label' => 'Resi Belum Dikenali', 'route' => 'customer.unmatched-resi', 'icon' => 'search'],
    ] : [
        ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'home'],
    ];
@endphp

<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            {{-- Left: Logo + Nav Links --}}
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-primary rounded-button flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <span class="text-subtitle font-bold text-primary hidden sm:block">Ting Warehouse</span>
                    </a>
                </div>

                {{-- Desktop nav links --}}
                <div class="hidden lg:flex lg:items-center lg:ms-8 lg:gap-1">
                    @foreach($navItems as $item)
                        @php $isActive = request()->routeIs($item['route']); @endphp
                        <a
                            href="{{ route($item['route']) }}"
                            wire:navigate
                            class="px-3 py-2 rounded-button text-body font-medium transition-colors duration-150 {{ $isActive ? 'bg-primary/5 text-primary' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}"
                        >
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Right: Actions --}}
            <div class="flex items-center gap-2">
                {{-- Notification Bell (Desktop) --}}
                <div class="hidden sm:block">
                    <livewire:notifications.notification-bell />
                </div>

                {{-- User Dropdown (Desktop) --}}
                <div class="hidden sm:block relative" x-data="{ dropdownOpen: false }">
                    <button
                        @click="dropdownOpen = !dropdownOpen"
                        @click.outside="dropdownOpen = false"
                        class="flex items-center gap-2 px-3 py-2 rounded-button transition-colors hover:bg-gray-50 min-h-[44px]"
                    >
                        <div class="w-8 h-8 rounded-full bg-accent/10 flex items-center justify-center">
                            <span class="text-caption font-semibold text-accent">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        </div>
                        <span class="text-body font-medium text-gray-700 hidden md:block max-w-[120px] truncate">{{ $user->name }}</span>
                        <svg class="w-4 h-4 text-gray-400 transition-transform" :class="dropdownOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div
                        x-show="dropdownOpen"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-48 bg-white rounded-card shadow-dropdown border border-border py-1 z-50"
                        style="display: none;"
                    >
                        <a href="{{ route('profile') }}" wire:navigate class="flex items-center gap-2 px-4 py-2 text-body text-gray-700 hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Profil Saya
                        </a>
                        <hr class="my-1 border-gray-100">
                        <button wire:click="logout" class="w-full flex items-center gap-2 px-4 py-2 text-body text-red-600 hover:bg-red-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Keluar
                        </button>
                    </div>
                </div>

                {{-- Mobile: Notification + Hamburger --}}
                <div class="flex items-center gap-1 sm:hidden">
                    <livewire:notifications.notification-bell />
                    <button @click="open = !open" class="p-3 min-w-[44px] min-h-[44px] flex items-center justify-center rounded-button text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                        <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg x-show="open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="lg:hidden border-t border-gray-100 bg-white" style="display: none;">
        <div class="px-4 py-3 space-y-1">
            @foreach($navItems as $item)
                @php $isActive = request()->routeIs($item['route']); @endphp
                <a
                    href="{{ route($item['route']) }}"
                    wire:navigate
                    @click="open = false"
                    class="flex items-center gap-3 px-3 py-3 min-h-[44px] rounded-button text-body font-medium transition-colors {{ $isActive ? 'bg-primary/5 text-primary' : 'text-gray-600 hover:bg-gray-50' }}"
                >
                    {{ $item['label'] }}
                </a>
            @endforeach
            <hr class="my-2 border-gray-100">
            <a href="{{ route('profile') }}" wire:navigate @click="open = false" class="flex items-center gap-3 px-3 py-3 min-h-[44px] rounded-button text-body text-gray-600 hover:bg-gray-50">
                Profil Saya
            </a>
            <button wire:click="logout" class="w-full flex items-center gap-3 px-3 py-3 min-h-[44px] rounded-button text-body text-red-600 hover:bg-red-50">
                Keluar
            </button>
        </div>
    </div>
</nav>
