<div class="min-h-screen bg-[#f8fafc]">
    {{-- Page Header --}}
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h1 class="text-[22px] font-bold text-gray-900 tracking-tight">Dashboard Owner</h1>
                    <p class="text-body text-gray-500 mt-0.5">Ringkasan bisnis dan operasional ting warehouse</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="hidden sm:flex items-center gap-1.5 text-caption text-gray-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        {{ now()->format('d M Y, H:i') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

        {{-- Primary Financial Summary --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            {{-- Revenue Bulan Ini --}}
            <div class="bg-white rounded-[12px] border border-gray-100 p-5 group hover:shadow-card-hover hover:border-gray-200 focus:outline-none focus:ring-2 focus:ring-accent/40 transition-all duration-200">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-10 h-10 rounded-[10px] bg-emerald-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    @if($revenueGrowth >= 0)
                        <span class="flex items-center gap-0.5 text-caption font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                            {{ $revenueGrowth }}%
                        </span>
                    @else
                        <span class="flex items-center gap-0.5 text-caption font-semibold text-red-500 bg-red-50 px-2 py-0.5 rounded-full">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                            {{ abs($revenueGrowth) }}%
                        </span>
                    @endif
                </div>
                <div class="text-[26px] font-bold text-gray-900 leading-none tracking-tight">Rp {{ number_format($revenueThisMonth, 0, ',', '.') }}</div>
                <div class="text-caption text-gray-500 mt-1">Revenue bulan ini</div>
            </div>

            {{-- Outstanding --}}
            <div class="bg-white rounded-[12px] border border-gray-100 p-5 group hover:shadow-card-hover hover:border-gray-200 focus:outline-none focus:ring-2 focus:ring-accent/40 transition-all duration-200">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-10 h-10 rounded-[10px] bg-amber-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    @if($pendingPayments > 0)
                        <span class="text-caption font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">{{ $pendingPayments }} invoice</span>
                    @endif
                </div>
                <div class="text-[26px] font-bold text-gray-900 leading-none tracking-tight">Rp {{ number_format($outstanding, 0, ',', '.') }}</div>
                <div class="text-caption text-gray-500 mt-1">Outstanding</div>
            </div>

            {{-- Total Customers --}}
            <a href="{{ route('admin.customers') }}" wire:navigate class="bg-white rounded-[12px] border border-gray-100 p-5 group hover:shadow-card-hover hover:border-gray-200 focus:outline-none focus:ring-2 focus:ring-accent/40 transition-all duration-200">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-10 h-10 rounded-[10px] bg-blue-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    @if($newCustomersThisMonth > 0)
                        <span class="text-caption font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">+{{ $newCustomersThisMonth }} baru</span>
                    @endif
                </div>
                <div class="text-[26px] font-bold text-gray-900 leading-none tracking-tight">{{ $totalCustomers }}</div>
                <div class="text-caption text-gray-500 mt-1">Customer <span class="text-gray-400">{{ $activeCustomers }} aktif</span></div>
            </a>

            {{-- Active Shipments --}}
            <a href="{{ route('admin.boxes') }}" wire:navigate class="bg-white rounded-[12px] border border-gray-100 p-5 group hover:shadow-card-hover hover:border-gray-200 focus:outline-none focus:ring-2 focus:ring-accent/40 transition-all duration-200">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-10 h-10 rounded-[10px] bg-violet-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    @if($pendingCheckouts > 0)
                        <span class="text-caption font-bold text-violet-600 bg-violet-50 px-2 py-0.5 rounded-full">{{ $pendingCheckouts }} checkout</span>
                    @endif
                </div>
                <div class="text-[26px] font-bold text-gray-900 leading-none tracking-tight">{{ $activeBoxes }}</div>
                <div class="text-caption text-gray-500 mt-1">Pengiriman aktif <span class="text-gray-400">/ {{ $totalBoxes }} total</span></div>
            </a>
        </div>

        {{-- Revenue Chart + Top Customers --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Revenue Chart --}}
            <div class="lg:col-span-2 bg-white rounded-[12px] border border-gray-100">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-body font-semibold text-gray-900">Revenue 6 Bulan Terakhir</h3>
                        <p class="text-caption text-gray-400 mt-0.5">Trend revenue dari invoice terverifikasi</p>
                    </div>
                    <a href="{{ route('owner.finance') }}" wire:navigate class="text-caption text-accent hover:text-primary font-medium transition-colors">Detail</a>
                </div>
                <div class="p-5">
                    @if($revenueByMonth->sum() > 0)
                        {{-- Simple Bar Chart --}}
                        @php
                            $max = $revenueByMonth->max() ?: 1;
                            $months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];
                        @endphp
                        <div class="flex items-end gap-2 h-[200px]">
                            @foreach($revenueByMonth as $key => $value)
                                @php
                                    $monthIndex = (int) substr($key, 5, 2) - 1;
                                    $height = $max > 0 ? ($value / $max) * 100 : 0;
                                    $isCurrentMonth = $key === now()->format('Y-m');
                                @endphp
                                <div class="flex-1 flex flex-col items-center gap-2">
                                    @if($value > 0)
                                        <span class="text-caption font-medium text-gray-500">Rp {{ number_format($value / 1000000, 1) }}jt</span>
                                    @endif
                                    <div class="w-full flex items-end" style="height: 160px;">
                                        <div
                                            class="w-full rounded-t-[6px] transition-all duration-500 {{ $isCurrentMonth ? 'bg-primary' : 'bg-primary/20 hover:bg-primary/30' }}"
                                            style="height: {{ max($height, 2) }}%"
                                        ></div>
                                    </div>
                                    <span class="text-caption {{ $isCurrentMonth ? 'font-bold text-primary' : 'text-gray-400' }}">{{ $months[$monthIndex] }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center h-[200px] text-center">
                            <svg class="w-10 h-10 text-gray-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            <p class="text-body text-gray-400">Belum ada data revenue</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Top Customers --}}
            <div class="bg-white rounded-[12px] border border-gray-100">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="text-body font-semibold text-gray-900">Top Customer</h3>
                </div>
                <div class="p-5">
                    @if($topCustomers->isEmpty())
                        <div class="flex flex-col items-center justify-center py-10 text-center">
                            <svg class="w-10 h-10 text-gray-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <p class="text-body text-gray-400">Belum ada transaksi</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($topCustomers as $i => $tc)
                                <div class="flex items-center gap-3 p-2.5 rounded-[8px] hover:bg-gray-50 transition-colors">
                                    <div class="w-8 h-8 rounded-full {{ $i === 0 ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-500' }} flex items-center justify-center flex-shrink-0">
                                        <span class="text-caption font-bold">{{ $i + 1 }}</span>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-body font-medium text-gray-800 truncate">{{ $tc->customer->name ?? '-' }}</p>
                                        <p class="text-caption text-gray-400">{{ $tc->invoice_count }} invoice</p>
                                    </div>
                                    <span class="text-body font-semibold text-gray-900 flex-shrink-0">Rp {{ number_format($tc->total_spent, 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Quick Stats Row --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-white rounded-[12px] border border-gray-100 px-4 py-3.5 flex items-center gap-3">
                <div class="w-9 h-9 rounded-[8px] bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <p class="text-[18px] font-bold text-gray-900 leading-none">{{ $totalInvoices }}</p>
                    <p class="text-caption text-gray-500 mt-0.5">Total Invoice</p>
                </div>
            </div>
            <div class="bg-white rounded-[12px] border border-gray-100 px-4 py-3.5 flex items-center gap-3">
                <div class="w-9 h-9 rounded-[8px] bg-emerald-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </div>
                <div>
                    <p class="text-[18px] font-bold text-gray-900 leading-none">{{ $verifiedInvoices }}</p>
                    <p class="text-caption text-gray-500 mt-0.5">Terverifikasi</p>
                </div>
            </div>
            <div class="bg-white rounded-[12px] border border-gray-100 px-4 py-3.5 flex items-center gap-3">
                <div class="w-9 h-9 rounded-[8px] bg-violet-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <div>
                    <p class="text-[18px] font-bold text-gray-900 leading-none">{{ $completedBoxes }}</p>
                    <p class="text-caption text-gray-500 mt-0.5">Pengiriman Selesai</p>
                </div>
            </div>
            <div class="bg-white rounded-[12px] border border-gray-100 px-4 py-3.5 flex items-center gap-3">
                <div class="w-9 h-9 rounded-[8px] {{ $openComplaints > 0 ? 'bg-red-50' : 'bg-gray-100' }} flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 {{ $openComplaints > 0 ? 'text-red-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-[18px] font-bold {{ $openComplaints > 0 ? 'text-red-600' : 'text-gray-900' }} leading-none">{{ $openComplaints }}</p>
                    <p class="text-caption text-gray-500 mt-0.5">Komplain Terbuka</p>
                </div>
            </div>
        </div>

        {{-- Notifications + Recent Activity --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Notifications --}}
            <div class="bg-white rounded-[12px] border border-gray-100">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-body font-semibold text-gray-900">Notifikasi</h3>
                    @php $unread = $notifications->where('read_at', null)->count(); @endphp
                    @if($unread > 0)
                        <span class="text-caption font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">{{ $unread }} baru</span>
                    @endif
                </div>
                <div class="max-h-[340px] overflow-y-auto">
                    @if($notifications->isEmpty())
                        <div class="flex flex-col items-center py-12 text-center px-4">
                            <div class="w-12 h-12 rounded-full bg-gray-50 flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            </div>
                            <p class="text-body text-gray-400">Tidak ada notifikasi</p>
                        </div>
                    @else
                        <div class="divide-y divide-gray-50">
                            @foreach($notifications as $notif)
                                <div class="flex items-start gap-3 px-5 py-3.5 transition-colors {{ $notif->read_at ? 'hover:bg-gray-50/50' : 'bg-blue-50/30 hover:bg-blue-50/50' }}">
                                    @if(!$notif->read_at)
                                        <div class="mt-1.5 w-2 h-2 rounded-full bg-blue-500 flex-shrink-0"></div>
                                    @else
                                        <div class="mt-1.5 w-2 h-2 rounded-full bg-transparent flex-shrink-0"></div>
                                    @endif
                                    <div class="min-w-0 flex-1">
                                        <p class="text-body font-medium text-gray-800 truncate">{{ $notif->data['title'] ?? 'Notifikasi' }}</p>
                                        <p class="text-caption text-gray-500 mt-0.5 line-clamp-2">{{ $notif->data['message'] ?? '' }}</p>
                                        <p class="text-caption text-gray-400 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Recent Activity --}}
            <div class="bg-white rounded-[12px] border border-gray-100">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-body font-semibold text-gray-900">Aktivitas Terbaru</h3>
                    <a href="{{ route('owner.audit-log') }}" wire:navigate class="text-caption text-accent hover:text-primary font-medium transition-colors">Lihat Semua</a>
                </div>
                <div class="max-h-[340px] overflow-y-auto">
                    @if($recentActivities->isEmpty())
                        <div class="flex flex-col items-center py-12 text-center px-4">
                            <div class="w-12 h-12 rounded-full bg-gray-50 flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <p class="text-body text-gray-400">Belum ada aktivitas</p>
                        </div>
                    @else
                        <div class="divide-y divide-gray-50">
                            @foreach($recentActivities as $log)
                                <div class="flex items-start gap-3 px-5 py-3.5 hover:bg-gray-50/50 transition-colors">
                                    <div class="mt-0.5 w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0">
                                        <span class="text-caption font-bold text-gray-500">{{ $log->user ? strtoupper(substr($log->user->name, 0, 1)) : '?' }}</span>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-body text-gray-700">
                                            <span class="font-medium">{{ $log->user->name ?? 'System' }}</span>
                                            <span class="text-gray-400 mx-1">{{ $log->event }}</span>
                                            <span class="font-medium">{{ class_basename($log->subject_type) }}</span>
                                            @if($log->subject_id)
                                                <span class="text-gray-400">#{{ $log->subject_id }}</span>
                                            @endif
                                        </p>
                                        <p class="text-caption text-gray-400 mt-0.5">{{ $log->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Recent Invoices + Quick Actions --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Recent Invoices --}}
            <div class="lg:col-span-2 bg-white rounded-[12px] border border-gray-100">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-body font-semibold text-gray-900">Invoice Terbaru</h3>
                    <a href="{{ route('owner.finance') }}" wire:navigate class="text-caption text-accent hover:text-primary font-medium transition-colors">Lihat Semua</a>
                </div>
                @if($recentInvoices->isEmpty())
                    <div class="p-5 text-center">
                        <p class="text-body text-gray-400">Belum ada invoice</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-50">
                        @foreach($recentInvoices as $inv)
                            <div class="flex items-center justify-between px-5 py-3.5 hover:bg-gray-50/50 transition-colors">
                                <div class="min-w-0 flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-[8px] bg-gray-100 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-body font-medium text-gray-800">{{ $inv->invoice_number }}</p>
                                        <p class="text-caption text-gray-500">{{ $inv->customer->name ?? '-' }}</p>
                                    </div>
                                </div>
                                <div class="text-right flex-shrink-0 ml-4">
                                    <p class="text-body font-semibold text-gray-900">Rp {{ number_format($inv->grand_total, 0, ',', '.') }}</p>
                                    <x-status-badge :status="$inv->status" />
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Quick Actions --}}
            <div class="bg-white rounded-[12px] border border-gray-100">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="text-body font-semibold text-gray-900">Menu Cepat</h3>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route('owner.finance') }}" wire:navigate class="flex flex-col items-center gap-2 p-3 rounded-[10px] hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-accent/40 transition-colors group">
                            <div class="w-10 h-10 rounded-[10px] bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-100 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            </div>
                            <span class="text-caption font-medium text-gray-600">Keuangan</span>
                        </a>
                        <a href="{{ route('owner.audit-log') }}" wire:navigate class="flex flex-col items-center gap-2 p-3 rounded-[10px] hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-accent/40 transition-colors group">
                            <div class="w-10 h-10 rounded-[10px] bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-100 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                            </div>
                            <span class="text-caption font-medium text-gray-600">Audit Log</span>
                        </a>
                        <a href="{{ route('owner.manage-admin') }}" wire:navigate class="flex flex-col items-center gap-2 p-3 rounded-[10px] hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-accent/40 transition-colors group">
                            <div class="w-10 h-10 rounded-[10px] bg-violet-50 text-violet-600 flex items-center justify-center group-hover:bg-violet-100 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            </div>
                            <span class="text-caption font-medium text-gray-600">Manage Admin</span>
                        </a>
                        <a href="{{ route('admin.settings') }}" wire:navigate class="flex flex-col items-center gap-2 p-3 rounded-[10px] hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-accent/40 transition-colors group">
                            <div class="w-10 h-10 rounded-[10px] bg-amber-50 text-amber-600 flex items-center justify-center group-hover:bg-amber-100 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <span class="text-caption font-medium text-gray-600">Pengaturan</span>
                        </a>
                        <a href="{{ route('admin.boxes') }}" wire:navigate class="flex flex-col items-center gap-2 p-3 rounded-[10px] hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-accent/40 transition-colors group">
                            <div class="w-10 h-10 rounded-[10px] bg-gray-100 text-gray-600 flex items-center justify-center group-hover:bg-gray-200 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            </div>
                            <span class="text-caption font-medium text-gray-600">Manage Box</span>
                        </a>
                        <a href="{{ route('admin.invoices') }}" wire:navigate class="flex flex-col items-center gap-2 p-3 rounded-[10px] hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-accent/40 transition-colors group">
                            <div class="w-10 h-10 rounded-[10px] bg-gray-100 text-gray-600 flex items-center justify-center group-hover:bg-gray-200 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <span class="text-caption font-medium text-gray-600">Invoice</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
