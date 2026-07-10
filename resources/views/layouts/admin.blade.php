<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Ting Warehouse') }} — Admin</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-[#f8fafc]">
        <div x-data="{ sidebarOpen: false }" class="h-screen flex overflow-hidden">

            {{-- Mobile Overlay --}}
            <div
                x-show="sidebarOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="sidebarOpen = false"
                class="fixed inset-0 bg-black/30 z-40 lg:hidden"
                style="display: none;"
            ></div>

            {{-- Sidebar --}}
            <aside
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
                class="fixed inset-y-0 left-0 z-50 w-[260px] bg-white border-r border-gray-100 flex flex-col transition-transform duration-200 lg:static lg:transition-none"
            >
                {{-- Logo --}}
                <div class="h-16 flex items-center gap-3 px-5 border-b border-gray-100 flex-shrink-0">
                    <div class="w-9 h-9 bg-primary rounded-[10px] flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-[15px] font-bold text-primary tracking-tight">Ting Warehouse</span>
                        <span class="block text-caption font-medium text-gray-400 uppercase tracking-widest">Admin Panel</span>
                    </div>
                </div>

                {{-- Navigation --}}
                <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-0.5">
                    @php
                        $user = auth()->user();
                        $isOwner = $user->isOwner();

                        $navSections = [
                            [
                                'label' => 'Overview',
                                'items' => [
                                    ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1"/>'],
                                ],
                            ],
                            [
                                'label' => 'Operasional',
                                'items' => [
                                    ['label' => 'Manage Box', 'route' => 'admin.boxes', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>'],
                                    ['label' => 'Generate Invoice', 'route' => 'admin.invoices', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>'],
                                    ['label' => 'Verifikasi', 'route' => 'admin.verification', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>'],
                                    ['label' => 'Recap', 'route' => 'admin.recap', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>'],
                                    ['label' => 'Checkout', 'route' => 'admin.checkouts', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>'],
                                    ['label' => 'Komplain', 'route' => 'admin.complains', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
                                    ['label' => 'Input No Tuan', 'route' => 'admin.no-tuan.create', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>'],
                                    ['label' => 'Barang Lelang', 'route' => 'admin.lelang', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>'],
                                ],
                            ],
                            [
                                'label' => 'Pelanggan',
                                'items' => [
                                    ['label' => 'Info Customer', 'route' => 'admin.customers', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/>'],
                                ],
                            ],
                            [
                                'label' => 'Pengaturan',
                                'items' => [
                                    ['label' => 'Est Update', 'route' => 'admin.est-update', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
                                    ['label' => 'Pengaturan Rate', 'route' => 'admin.settings', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>'],
                                    ['label' => 'History Kurs', 'route' => 'admin.kurs-history', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
                                ],
                            ],
                        ];

                        if ($isOwner) {
                            $navSections[] = [
                                'label' => 'Owner',
                                'items' => [
                                    ['label' => 'Owner Dashboard', 'route' => 'owner.dashboard', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1"/>'],
                                    ['label' => 'Laporan Keuangan', 'route' => 'owner.finance', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>'],
                                    ['label' => 'Manage Admin', 'route' => 'owner.manage-admin', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/>'],
                                    ['label' => 'Manage Users', 'route' => 'owner.users', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>'],
                                    ['label' => 'All Data', 'route' => 'owner.all-data', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>'],
                                    ['label' => 'Audit Log', 'route' => 'owner.audit-log', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>'],
                                ],
                            ];
                        }
                    @endphp

                    @foreach($navSections as $section)
                        <div class="mb-4">
                            <p class="px-3 mb-1.5 text-caption font-semibold uppercase tracking-[0.08em] text-gray-400">{{ $section['label'] }}</p>
                            @foreach($section['items'] as $item)
                                @php
                                    $isActive = request()->routeIs($item['route']);
                                @endphp
                                <a
                                    href="{{ route($item['route']) }}"
                                    wire:navigate
                                    class="flex items-center gap-3 px-3 py-2 rounded-[8px] text-body font-medium transition-all duration-150 {{ $isActive ? 'bg-primary text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                                >
                                    <svg class="w-[18px] h-[18px] flex-shrink-0 {{ $isActive ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $item['icon'] !!}</svg>
                                    {{ $item['label'] }}
                                    @if(isset($item['badge']))
                                        <span class="ml-auto text-caption font-bold {{ $isActive ? 'bg-white/20 text-white' : 'bg-red-100 text-red-600' }} px-1.5 py-0.5 rounded-full leading-none">{{ $item['badge'] }}</span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    @endforeach
                </nav>

                {{-- User Info --}}
                <div class="border-t border-gray-100 p-3 flex-shrink-0">
                    <div class="flex items-center gap-3 px-2 py-2">
                        <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                            <span class="text-caption font-bold text-primary">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-body font-semibold text-gray-800 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-caption text-gray-400 truncate">{{ ucfirst(auth()->user()->role) }}</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="flex-shrink-0">
                            @csrf
                            <button type="submit" class="p-2 min-w-[44px] min-h-[44px] flex items-center justify-center rounded-md text-gray-400 hover:text-red-500 hover:bg-red-50 transition-colors" title="Keluar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            {{-- Main Content --}}
            <div class="flex-1 flex flex-col min-w-0 lg:ml-0 overflow-y-auto">

                {{-- Top Bar --}}
                <header class="h-16 bg-white border-b border-gray-100 flex items-center justify-between px-4 sm:px-6 flex-shrink-0 sticky top-0 z-30">
                    <div class="flex items-center gap-3">
                        <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-3 -ml-1 rounded-button text-gray-500 hover:bg-gray-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                        <div>
                            @if (isset($header))
                                {{ $header }}
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <livewire:notifications.notification-bell />
                    </div>
                </header>

                {{-- Page Content --}}
                <main class="flex-1">
                    {{ $slot }}
                </main>
            </div>
        </div>

        {{-- Toast Notifications --}}
        <x-toast />
    </body>
</html>
