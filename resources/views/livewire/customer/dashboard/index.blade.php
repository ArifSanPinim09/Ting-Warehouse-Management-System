<div class="min-h-screen bg-[#f8fafc]">

    {{-- R6: Livewire Loading State --}}
    <div wire:loading class="fixed inset-x-0 top-16 h-0.5 bg-primary/20 z-50">
        <div class="h-full bg-primary animate-pulse w-1/3 rounded-full"></div>
    </div>

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

        {{-- R2: Stat Cards — Clickable --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4" role="region" aria-label="Statistik ringkasan">
            {{-- Active Boxes --}}
            <a href="{{ route('customer.box.sharing') }}" wire:navigate class="ds-stat group cursor-pointer hover:shadow-card-hover transition-all" aria-label="Lihat box aktif Anda">
                <div class="flex items-center justify-between mb-3">
                    <div class="ds-stat-icon bg-blue-50 text-blue-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                </div>
                <div class="ds-stat-value">{{ $activeBoxes }}</div>
                <div class="ds-stat-label">Box Aktif</div>
            </a>

            {{-- Unpaid Invoices — Highlighted --}}
            <a href="{{ route('customer.invoice') }}" wire:navigate class="ds-stat group cursor-pointer hover:shadow-card-hover transition-all {{ $unpaidInvoices > 0 ? 'ring-1 ring-amber-200 bg-amber-50/30' : '' }}" aria-label="Lihat invoice belum bayar">
                <div class="flex items-center justify-between mb-3">
                    <div class="ds-stat-icon bg-amber-50 text-amber-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                    </div>
                    @if($unpaidInvoices > 0)
                        <span class="ds-badge-warning text-micro animate-pulse">Perlu Dibayar</span>
                    @endif
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
            </a>

            {{-- Goods This Month --}}
            <a href="{{ route('customer.setor-resi') }}" wire:navigate class="ds-stat group cursor-pointer hover:shadow-card-hover transition-all" aria-label="Lihat barang bulan ini">
                <div class="flex items-center justify-between mb-3">
                    <div class="ds-stat-icon bg-emerald-50 text-emerald-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    </div>
                </div>
                <div class="ds-stat-value">{{ $goodsThisMonth }}</div>
                <div class="ds-stat-label">Barang Bulan Ini</div>
            </a>

            {{-- Receipts This Month --}}
            <a href="{{ route('customer.setor-resi') }}" wire:navigate class="ds-stat group cursor-pointer hover:shadow-card-hover transition-all" aria-label="Lihat resi bulan ini">
                <div class="flex items-center justify-between mb-3">
                    <div class="ds-stat-icon bg-violet-50 text-violet-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                </div>
                <div class="ds-stat-value">{{ $receiptsThisMonth }}</div>
                <div class="ds-stat-label">Resi Bulan Ini</div>
            </a>
        </div>

        {{-- Unmatched WH China Alert Banner --}}
        @if($unmatchedWhCount > 0)
            <a href="{{ route('customer.unmatched-resi') }}" wire:navigate
                class="block bg-blue-50 border border-blue-200 rounded-[12px] p-4 hover:bg-blue-100/50 transition-colors group">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0 group-hover:bg-blue-200 transition-colors">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-body font-semibold text-blue-800">Ada {{ $unmatchedWhCount }} resi dari gudang China yang belum dikenali</p>
                        <p class="text-caption text-blue-600 mt-0.5">Klik di sini untuk melihat dan mengklaim resi yang mungkin milik Anda.</p>
                    </div>
                    <svg class="w-5 h-5 text-blue-400 flex-shrink-0 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </a>
        @endif

        {{-- R4: Status Box — Moved to top (most-viewed content) --}}
        <div class="ds-card overflow-hidden" role="region" aria-label="Daftar box Anda">
            <div class="px-6 py-5 border-b border-gray-100">
                <h3 class="ds-section-title">Status Box</h3>
                <p class="ds-section-subtitle">Daftar box yang Anda miliki</p>
            </div>

            @if($boxes->isEmpty())
                <div class="ds-empty">
                    <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                        <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <h3 class="ds-empty-title">Anda belum memiliki box</h3>
                    <p class="ds-empty-text">Mulai dengan menyetor resi pertama Anda.</p>
                    <a href="{{ route('customer.setor-resi') }}" wire:navigate class="ds-btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Setor Resi Sekarang
                    </a>
                </div>
            @else
                {{-- R1: Mobile card list (<768px) --}}
                <div class="block md:hidden divide-y divide-gray-100">
                    @foreach($boxes as $box)
                        <button
                            wire:click="openBoxDetail({{ $box->id }})"
                            class="w-full text-left px-4 py-3.5 hover:bg-gray-50/50 transition-colors focus:outline-none focus:ring-2 focus:ring-inset focus:ring-accent/40"
                        >
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-body font-semibold text-primary">{{ $box->display_name }}</span>
                                <x-status-badge :status="$box->status" />
                            </div>
                            <div class="flex items-center gap-3 text-caption text-gray-500">
                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded {{ $box->method === 'air' ? 'bg-blue-50 text-blue-700' : 'bg-cyan-50 text-cyan-700' }}">
                                    {{ strtoupper($box->method) }}
                                </span>
                                <span>{{ $box->items_count }} barang</span>
                                @if($box->etd)
                                    <span>ETD {{ $box->etd->format('d M') }}</span>
                                @elseif($box->eta)
                                    <span>ETA {{ $box->eta->format('d M') }}</span>
                                @endif
                            </div>
                        </button>
                    @endforeach
                </div>

                {{-- Desktop table (≥768px) --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left" aria-label="Daftar box pengiriman">
                        <caption class="sr-only">Tabel box pengiriman milik Anda</caption>
                        <thead>
                            <tr class="border-b border-gray-100 bg-gray-50/50">
                                <th class="px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wider">Nomor Box</th>
                                <th class="px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wider">Kode Box</th>
                                <th class="px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wider">Jenis Kirim</th>
                                <th class="px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wider">ETD</th>
                                <th class="px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wider">ETA</th>
                                <th class="px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wider">Stevedoring</th>
                                <th class="px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wider">Tgl Tagihan</th>
                                <th class="px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wider">Barang</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($boxes as $box)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-5 py-3.5">
                                        <button
                                            wire:click="openBoxDetail({{ $box->id }})"
                                            class="text-body font-semibold text-primary hover:text-primary-light hover:underline transition-colors"
                                        >
                                            {{ $box->display_name }}
                                        </button>
                                    </td>
                                    <td class="px-5 py-3.5 text-body text-gray-700">{{ $box->batch_name ?? '-' }}</td>
                                    <td class="px-5 py-3.5">
                                        <span class="text-caption font-medium px-2 py-1 rounded-full {{ $box->method === 'air' ? 'bg-blue-50 text-blue-700' : 'bg-cyan-50 text-cyan-700' }}">
                                            {{ strtoupper($box->method) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 text-body text-gray-700">
                                        {{ $box->etd ? $box->etd->format('d M Y') : '-' }}
                                    </td>
                                    <td class="px-5 py-3.5 text-body text-gray-700">
                                        {{ $box->eta ? $box->eta->format('d M Y') : '-' }}
                                    </td>
                                    <td class="px-5 py-3.5 text-body text-gray-700">
                                        {{ $box->stevedoring_date ? $box->stevedoring_date->format('d M Y') : '-' }}
                                    </td>
                                    <td class="px-5 py-3.5 text-body text-gray-700">
                                        {{ $box->tagihan_update_date ? $box->tagihan_update_date->format('d M Y') : '-' }}
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <x-status-badge :status="$box->status" />
                                    </td>
                                    <td class="px-5 py-3.5 text-body text-gray-500">
                                        {{ $box->items_count }} barang
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($boxes->hasPages())
                    <div class="px-5 py-4 border-t border-gray-100">
                        {{ $boxes->links() }}
                    </div>
                @endif
            @endif
        </div>

        {{-- R5: Menu Cepat — Horizontal strip on mobile, grid on desktop --}}
        <div class="ds-card p-5" role="navigation" aria-label="Menu cepat">
            <h3 class="ds-section-title mb-4">Menu Cepat</h3>

            {{-- Mobile: horizontal scrollable strip --}}
            <div class="flex md:hidden gap-3 overflow-x-auto scrollbar-hide pb-2 snap-x snap-mandatory -mx-1 px-1">
                <a href="{{ route('customer.setor-resi') }}" wire:navigate class="flex-shrink-0 flex flex-col items-center gap-1.5 w-[72px] p-2 rounded-card hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-accent/40 transition-colors group snap-start">
                    <div class="w-10 h-10 rounded-button bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <span class="text-caption font-medium text-gray-600 text-center leading-tight">Setor Resi</span>
                </a>
                <a href="{{ route('customer.box.sharing') }}" wire:navigate class="flex-shrink-0 flex flex-col items-center gap-1.5 w-[72px] p-2 rounded-card hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-accent/40 transition-colors group snap-start">
                    <div class="w-10 h-10 rounded-button bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <span class="text-caption font-medium text-gray-600 text-center leading-tight">My Box</span>
                </a>
                <a href="{{ route('customer.invoice') }}" wire:navigate class="flex-shrink-0 flex flex-col items-center gap-1.5 w-[72px] p-2 rounded-card hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-accent/40 transition-colors group snap-start">
                    <div class="w-10 h-10 rounded-button bg-amber-50 text-amber-600 flex items-center justify-center group-hover:bg-amber-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <span class="text-caption font-medium text-gray-600 text-center leading-tight">Invoice</span>
                </a>
                <a href="{{ route('customer.checkout') }}" wire:navigate class="flex-shrink-0 flex flex-col items-center gap-1.5 w-[72px] p-2 rounded-card hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-accent/40 transition-colors group snap-start">
                    <div class="w-10 h-10 rounded-button bg-violet-50 text-violet-600 flex items-center justify-center group-hover:bg-violet-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                    </div>
                    <span class="text-caption font-medium text-gray-600 text-center leading-tight">Checkout</span>
                </a>
                <a href="{{ route('customer.komplain') }}" wire:navigate class="flex-shrink-0 flex flex-col items-center gap-1.5 w-[72px] p-2 rounded-card hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-accent/40 transition-colors group snap-start">
                    <div class="w-10 h-10 rounded-button bg-red-50 text-red-600 flex items-center justify-center group-hover:bg-red-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="text-caption font-medium text-gray-600 text-center leading-tight">Komplain</span>
                </a>
                <a href="{{ route('customer.kalkulator') }}" wire:navigate class="flex-shrink-0 flex flex-col items-center gap-1.5 w-[72px] p-2 rounded-card hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-accent/40 transition-colors group snap-start">
                    <div class="w-10 h-10 rounded-button bg-gray-100 text-gray-600 flex items-center justify-center group-hover:bg-gray-200 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <span class="text-caption font-medium text-gray-600 text-center leading-tight">Kalkulator</span>
                </a>
                <a href="{{ route('customer.no-tuan') }}" wire:navigate class="flex-shrink-0 flex flex-col items-center gap-1.5 w-[72px] p-2 rounded-card hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-accent/40 transition-colors group snap-start">
                    <div class="w-10 h-10 rounded-button bg-orange-50 text-orange-600 flex items-center justify-center group-hover:bg-orange-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="text-caption font-medium text-gray-600 text-center leading-tight">No Tuan</span>
                </a>
            </div>

            {{-- Desktop: 2-col grid --}}
            <div class="hidden md:grid grid-cols-2 gap-3">
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
                <a href="{{ route('customer.no-tuan') }}" wire:navigate class="flex flex-col items-center gap-2 p-3 rounded-card hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-accent/40 transition-colors group">
                    <div class="w-10 h-10 rounded-button bg-orange-50 text-orange-600 flex items-center justify-center group-hover:bg-orange-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="text-caption font-medium text-gray-600">No Tuan</span>
                </a>
            </div>
        </div>

        {{-- Rate Display + Notifications --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

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
                        <span class="text-body font-semibold text-primary">Rp {{ number_format($rateAir, 0, ',', '.') }}/gram</span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-cyan-400"></div>
                            <span class="text-body text-gray-500">Rate Sea</span>
                        </div>
                        <span class="text-body font-semibold text-primary">Rp {{ number_format($rateSea, 0, ',', '.') }}/gram</span>
                    </div>
                </div>
                <a href="{{ route('customer.kalkulator') }}" wire:navigate class="ds-btn-ghost ds-btn-sm w-full mt-4 justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    Buka Kalkulator
                </a>
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
                            <div class="flex items-start gap-3 p-2.5 rounded-lg transition-colors {{ $notif->read_at ? 'hover:bg-gray-50' : 'bg-blue-50/50 hover:bg-blue-50' }}" role="listitem">
                                @if(!$notif->read_at)
                                    <div class="mt-1.5 w-2 h-2 rounded-full bg-blue-500 flex-shrink-0" aria-label="Belum dibaca"></div>
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

    </div>

    {{-- R3: Mobile FAB — Floating Action Button for Setor Resi --}}
    <a href="{{ route('customer.setor-resi') }}" wire:navigate
       class="fixed bottom-6 right-6 sm:hidden z-30 w-14 h-14 rounded-full bg-primary text-white shadow-lg flex items-center justify-center hover:bg-primary-light active:scale-95 transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary/40"
       aria-label="Setor Resi"
    >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
    </a>

    {{-- Box Detail Modal --}}
    @if($showDetail && $detailBox)
        <div class="fixed inset-0 z-50 overflow-y-auto" wire:click.self="closeBoxDetail" role="dialog" aria-modal="true" aria-label="Detail Box">
            <div class="fixed inset-0 bg-black/30 transition-opacity"></div>
            <div class="flex min-h-full items-end sm:items-center justify-center p-4">
                <div class="relative bg-white rounded-modal shadow-modal w-full max-w-3xl max-h-[90vh] overflow-hidden transform transition-all" @click.stop>
                    {{-- Modal Header --}}
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <div>
                            <h3 class="text-subtitle font-semibold text-gray-900">Detail Box</h3>
                            <p class="text-body text-gray-500 mt-0.5">{{ $detailBox->display_name }}</p>
                        </div>
                        <button wire:click="closeBoxDetail" class="p-2 min-w-[44px] min-h-[44px] flex items-center justify-center rounded-button text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors" aria-label="Tutup detail box">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="overflow-y-auto max-h-[calc(90vh-140px)] p-6 space-y-6">
                        {{-- Box Info --}}
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <div class="p-3 bg-gray-50 rounded-button">
                                <p class="text-caption text-gray-500 mb-1">Status</p>
                                <x-status-badge :status="$detailBox->status" />
                            </div>
                            <div class="p-3 bg-gray-50 rounded-button">
                                <p class="text-caption text-gray-500 mb-1">Tipe</p>
                                <p class="text-body font-medium text-gray-900 capitalize">{{ $detailBox->type }}</p>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-button">
                                <p class="text-caption text-gray-500 mb-1">Jenis Kirim</p>
                                <p class="text-body font-medium text-gray-900 uppercase">{{ $detailBox->method }}</p>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-button">
                                <p class="text-caption text-gray-500 mb-1">Kode Box</p>
                                <p class="text-body font-medium text-gray-900">{{ $detailBox->batch_name ?? '-' }}</p>
                            </div>
                        </div>

                        {{-- Items Table --}}
                        <div>
                            <h4 class="text-body font-semibold text-gray-900 mb-3">Daftar Barang ({{ $detailBox->items->count() }})</h4>
                            @if($detailBox->items->isEmpty())
                                <div class="p-6 text-center bg-gray-50 rounded-button">
                                    <p class="text-body text-gray-500">Belum ada barang di box ini</p>
                                </div>
                            @else
                                <div class="overflow-x-auto border border-gray-200 rounded-button">
                                    <table class="w-full text-left">
                                        <thead>
                                            <tr class="bg-gray-50">
                                                <th class="px-4 py-2.5 text-caption font-semibold text-gray-500 uppercase">No</th>
                                                <th class="px-4 py-2.5 text-caption font-semibold text-gray-500 uppercase">Nama Barang</th>
                                                <th class="px-4 py-2.5 text-caption font-semibold text-gray-500 uppercase">Qty</th>
                                                <th class="px-4 py-2.5 text-caption font-semibold text-gray-500 uppercase">Berat</th>
                                                <th class="px-4 py-2.5 text-caption font-semibold text-gray-500 uppercase">P×L×T</th>
                                                <th class="px-4 py-2.5 text-caption font-semibold text-gray-500 uppercase">Volume</th>
                                                <th class="px-4 py-2.5 text-caption font-semibold text-gray-500 uppercase">Biaya Tax</th>
                                                <th class="px-4 py-2.5 text-caption font-semibold text-gray-500 uppercase">Foto INA</th>
                                                <th class="px-4 py-2.5 text-caption font-semibold text-gray-500 uppercase">Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            @foreach($detailBox->items as $j => $item)
                                                <tr class="hover:bg-gray-50/50">
                                                    <td class="px-4 py-2.5 text-body text-gray-500">{{ $j + 1 }}</td>
                                                    <td class="px-4 py-2.5">
                                                        <div class="flex items-center gap-2">
                                                            <span class="text-body font-medium text-gray-900">{{ $item->name }}</span>
                                                            @if($item->is_sensitive)
                                                                <span class="text-caption font-bold text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded-full">Sensitive</span>
                                                            @endif
                                                        </div>
                                                        <p class="text-caption text-gray-500">{{ $item->resi_number }}</p>
                                                    </td>
                                                    <td class="px-4 py-2.5 text-body text-gray-700">{{ $item->quantity }}</td>
                                                    <td class="px-4 py-2.5 text-body text-gray-700">
                                                        {{ $item->whChinaData ? number_format($item->whChinaData->berat, 1) . ' kg' : '-' }}
                                                    </td>
                                                    <td class="px-4 py-2.5 text-body text-gray-700">
                                                        @if($item->whChinaData && $item->whChinaData->panjang && $item->whChinaData->lebar && $item->whChinaData->tinggi)
                                                            {{ $item->whChinaData->panjang }}×{{ $item->whChinaData->lebar }}×{{ $item->whChinaData->tinggi }} cm
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-2.5 text-body text-gray-700">
                                                        {{ $item->whChinaData && $item->whChinaData->volume ? number_format($item->whChinaData->volume, 4) : '-' }}
                                                    </td>
                                                    <td class="px-4 py-2.5 text-body text-gray-700">
                                                        {{ $item->whChinaData && $item->whChinaData->biaya_tax ? 'Rp ' . number_format($item->whChinaData->biaya_tax, 0, ',', '.') : '-' }}
                                                    </td>
                                                    <td class="px-4 py-2.5">
                                                        @if($item->whChinaData && $item->whChinaData->foto_arrived_ina)
                                                            <a href="{{ Storage::url($item->whChinaData->foto_arrived_ina) }}" target="_blank" class="inline-flex items-center gap-1 text-[12px] text-accent hover:underline font-medium">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                                Foto
                                                            </a>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-2.5">
                                                        @if($item->status !== 'active')
                                                            @php
                                                                $statusColors = [
                                                                    'no_tuan' => 'bg-orange-100 text-orange-700',
                                                                    'claimed' => 'bg-emerald-100 text-emerald-700',
                                                                    'klaim_wh' => 'bg-red-100 text-red-700',
                                                                    'shipped' => 'bg-blue-100 text-blue-700',
                                                                ];
                                                                $statusLabels = [
                                                                    'no_tuan' => 'No Tuan',
                                                                    'claimed' => 'Diklaim',
                                                                    'klaim_wh' => 'Klaim WH',
                                                                    'shipped' => 'Shipped',
                                                                ];
                                                            @endphp
                                                            <span class="text-caption font-bold {{ $statusColors[$item->status] ?? 'bg-gray-100 text-gray-700' }} px-1.5 py-0.5 rounded-full">
                                                                {{ $statusLabels[$item->status] ?? $item->status }}
                                                            </span>
                                                        @else
                                                            <span class="text-body text-gray-500">-</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="px-6 py-4 border-t border-gray-100 flex justify-end">
                        <button wire:click="closeBoxDetail" class="ds-btn-secondary">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
