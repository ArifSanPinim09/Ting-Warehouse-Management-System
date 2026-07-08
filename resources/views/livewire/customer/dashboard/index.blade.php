<div class="min-h-screen bg-[#f8fafc]">
    {{-- Page Header --}}
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-display text-primary">Dashboard</h1>
                    <p class="text-body text-gray-500 mt-1">Selamat datang, {{ auth()->user()->name }}</p>
                </div>
                <a href="{{ route('customer.setor-resi') }}" wire:navigate class="ds-btn-primary hidden sm:inline-flex">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Setor Resi
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

        {{-- Stat Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Active Boxes --}}
            <div class="ds-stat group">
                <div class="flex items-center justify-between mb-3">
                    <div class="ds-stat-icon bg-blue-50 text-blue-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                </div>
                <div class="ds-stat-value">{{ $activeBoxes }}</div>
                <div class="ds-stat-label">Box Aktif</div>
            </div>

            {{-- Unpaid Invoices --}}
            <div class="ds-stat group">
                <div class="flex items-center justify-between mb-3">
                    <div class="ds-stat-icon bg-amber-50 text-amber-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                    </div>
                </div>
                <div class="ds-stat-value text-amber-600">
                    @if($unpaidInvoices > 0)
                        {{ $unpaidCount }}
                    @else
                        0
                    @endif
                </div>
                <div class="ds-stat-label">
                    @if($unpaidInvoices > 0)
                        Invoice · Rp {{ number_format($unpaidInvoices, 0, ',', '.') }}
                    @else
                        Invoice Belum Bayar
                    @endif
                </div>
            </div>

            {{-- Goods This Month --}}
            <div class="ds-stat group">
                <div class="flex items-center justify-between mb-3">
                    <div class="ds-stat-icon bg-emerald-50 text-emerald-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    </div>
                </div>
                <div class="ds-stat-value">{{ $goodsThisMonth }}</div>
                <div class="ds-stat-label">Barang Bulan Ini</div>
            </div>

            {{-- Receipts This Month --}}
            <div class="ds-stat group">
                <div class="flex items-center justify-between mb-3">
                    <div class="ds-stat-icon bg-violet-50 text-violet-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                </div>
                <div class="ds-stat-value">{{ $receiptsThisMonth }}</div>
                <div class="ds-stat-label">Resi Bulan Ini</div>
            </div>
        </div>

        {{-- Rate Display + Shortcuts --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Rate Display --}}
            <div class="ds-card p-5">
                <h3 class="ds-section-title mb-4">Rate Hari Ini</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between py-2 border-b border-gray-50">
                        <span class="text-body text-gray-500">Kurs Yuan</span>
                        <span class="text-body font-semibold text-primary">Rp {{ number_format($kursYuan, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-50">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-blue-400"></div>
                            <span class="text-body text-gray-500">Rate Air</span>
                        </div>
                        <span class="text-body font-semibold text-primary">Rp {{ number_format($rateAir, 0, ',', '.') }}/kg</span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-cyan-400"></div>
                            <span class="text-body text-gray-500">Rate Sea</span>
                        </div>
                        <span class="text-body font-semibold text-primary">Rp {{ number_format($rateSea, 0, ',', '.') }}/kg</span>
                    </div>
                </div>
                <a href="{{ route('customer.kalkulator') }}" wire:navigate class="ds-btn-ghost ds-btn-sm w-full mt-4 justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    Buka Kalkulator
                </a>
            </div>

            {{-- Shortcuts --}}
            <div class="ds-card p-5">
                <h3 class="ds-section-title mb-4">Menu Cepat</h3>
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('customer.setor-resi') }}" wire:navigate class="flex flex-col items-center gap-2 p-3 rounded-card hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-accent/40 transition-colors group">
                        <div class="w-10 h-10 rounded-button bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </div>
                        <span class="text-caption font-medium text-gray-600">Setor Resi</span>
                    </a>
                    <a href="{{ route('customer.box.sharing') }}" wire:navigate class="flex flex-col items-center gap-2 p-3 rounded-card hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-accent/40 transition-colors group">
                        <div class="w-10 h-10 rounded-button bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </div>
                        <span class="text-caption font-medium text-gray-600">My Box</span>
                    </a>
                    <a href="{{ route('customer.invoice') }}" wire:navigate class="flex flex-col items-center gap-2 p-3 rounded-card hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-accent/40 transition-colors group">
                        <div class="w-10 h-10 rounded-button bg-amber-50 text-amber-600 flex items-center justify-center group-hover:bg-amber-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <span class="text-caption font-medium text-gray-600">Invoice</span>
                    </a>
                    <a href="{{ route('customer.checkout') }}" wire:navigate class="flex flex-col items-center gap-2 p-3 rounded-card hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-accent/40 transition-colors group">
                        <div class="w-10 h-10 rounded-button bg-violet-50 text-violet-600 flex items-center justify-center group-hover:bg-violet-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                        </div>
                        <span class="text-caption font-medium text-gray-600">Checkout</span>
                    </a>
                    <a href="{{ route('customer.komplain') }}" wire:navigate class="flex flex-col items-center gap-2 p-3 rounded-card hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-accent/40 transition-colors group">
                        <div class="w-10 h-10 rounded-button bg-red-50 text-red-600 flex items-center justify-center group-hover:bg-red-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <span class="text-caption font-medium text-gray-600">Komplain</span>
                    </a>
                    <a href="{{ route('customer.kalkulator') }}" wire:navigate class="flex flex-col items-center gap-2 p-3 rounded-card hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-accent/40 transition-colors group">
                        <div class="w-10 h-10 rounded-button bg-gray-100 text-gray-600 flex items-center justify-center group-hover:bg-gray-200 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        </div>
                        <span class="text-caption font-medium text-gray-600">Kalkulator</span>
                    </a>
                </div>
            </div>

            {{-- Notifications --}}
            <div class="ds-card p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="ds-section-title">Notifikasi</h3>
                    @if($notifications->where('read_at', null)->count() > 0)
                        <span class="ds-badge-info text-micro">{{ $notifications->where('read_at', null)->count() }} baru</span>
                    @endif
                </div>
                @if($notifications->isEmpty())
                    <div class="flex flex-col items-center py-6 text-center">
                        <svg class="w-10 h-10 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        <p class="text-body text-gray-400">Tidak ada notifikasi</p>
                    </div>
                @else
                    <div class="space-y-2">
                        @foreach($notifications as $notif)
                            <div class="flex items-start gap-3 p-2.5 rounded-lg transition-colors {{ $notif->read_at ? 'hover:bg-gray-50' : 'bg-blue-50/50 hover:bg-blue-50' }}">
                                @if(!$notif->read_at)
                                    <div class="mt-1.5 w-2 h-2 rounded-full bg-blue-500 flex-shrink-0"></div>
                                @else
                                    <div class="mt-1.5 w-2 h-2 rounded-full bg-transparent flex-shrink-0"></div>
                                @endif
                                <div class="min-w-0 flex-1">
                                    <p class="text-body font-medium text-gray-800 truncate">{{ $notif->data['title'] ?? 'Notifikasi' }}</p>
                                    <p class="text-caption text-gray-500 mt-0.5 line-clamp-2">{{ $notif->data['message'] ?? '' }}</p>
                                    <p class="text-micro text-gray-400 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Status Box List --}}
        <div class="ds-card">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="ds-section-title">Status Box</h3>
            </div>
            @if($boxes->isEmpty())
                <x-empty-state
                    icon="box"
                    title="Belum ada aktivitas"
                    text="Anda belum memiliki box. Mulai dengan menyetor resi pertama Anda."
                    action="Setor Resi Sekarang"
                    :actionUrl="route('customer.setor-resi')"
                />
            @else
                <div class="divide-y divide-gray-50">
                    @foreach($boxes as $box)
                        <a href="{{ route('customer.box.sharing') }}" wire:navigate class="flex items-center justify-between px-5 py-4 hover:bg-gray-50/50 transition-colors group">
                            <div class="flex items-center gap-4 min-w-0">
                                <div class="w-10 h-10 rounded-button bg-gray-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-body font-semibold text-primary truncate">
                                        {{ $box->tracking_number ?? $box->batch_name ?? 'Box #' . $box->id }}
                                    </p>
                                    <p class="text-caption text-gray-500 mt-0.5">
                                        {{ ucfirst($box->type) }} · {{ strtoupper($box->method) }} · {{ $box->items_count }} barang
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 flex-shrink-0">
                                <x-status-badge :status="$box->status" />
                                <svg class="w-4 h-4 text-gray-300 group-hover:text-gray-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
