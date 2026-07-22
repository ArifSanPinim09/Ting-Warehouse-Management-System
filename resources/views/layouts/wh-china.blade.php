<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Ting Warehouse — China</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
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
                        <span class="block text-caption font-medium text-gray-400 uppercase tracking-widest">China</span>
                    </div>
                </div>

                {{-- Navigation --}}
                <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-0.5">
                    @php
                        $navSections = [
                            [
                                'label' => 'Main',
                                'items' => [
                                    ['label' => 'Dashboard', 'route' => 'china.dashboard', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1"/>'],
                                    ['label' => 'New Batch 新一批', 'route' => 'china.new-batch', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/>'],
                                    ['label' => 'Requests 请求', 'route' => 'china.requests', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>'],
                                    ['label' => 'Request to Send', 'route' => 'china.request-to-send', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/>'],
                                ],
                            ],
                            [
                                'label' => 'Finance',
                                'items' => [
                                    ['label' => 'Shipping & Material', 'route' => 'china.shipping-material-fees', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>'],
                                    ['label' => 'Goods Weight Fee', 'route' => 'china.goods-weight-fees', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>'],
                                ],
                            ],
                            [
                                'label' => 'Archive',
                                'items' => [
                                    ['label' => 'History 历史', 'route' => 'china.history', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
                                ],
                            ],
                        ];
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
                            <p class="text-caption text-gray-400 truncate">China Admin</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="flex-shrink-0">
                            @csrf
                            <button type="submit" class="p-2 min-w-[44px] min-h-[44px] flex items-center justify-center rounded-md text-gray-400 hover:text-red-500 hover:bg-red-50 transition-colors" title="Logout">
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
                </header>

                {{-- Page Content --}}
                <main class="flex-1">
                    {{ $slot }}
                </main>
            </div>
        </div>

        @livewireScripts
        @stack('scripts')
    </body>
</html>
