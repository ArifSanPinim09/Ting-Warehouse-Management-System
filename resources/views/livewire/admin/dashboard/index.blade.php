<div class="min-h-screen bg-[#f8fafc]">
    {{-- Page Header --}}
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-[22px] font-bold text-gray-900 tracking-tight">Dashboard</h1>
                    <p class="text-[13px] text-gray-500 mt-0.5">Ringkasan operasional hari ini</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="hidden sm:flex items-center gap-1.5 text-[12px] text-gray-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        Sistem aktif
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

        {{-- Primary Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Pending Verifications --}}
            <a href="{{ route('admin.verification') }}" wire:navigate class="group bg-white rounded-[12px] border border-gray-100 p-5 hover:shadow-card-hover hover:border-gray-200 transition-all duration-200">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-10 h-10 rounded-[10px] bg-blue-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    </div>
                    @if($pendingVerifications > 0)
                        <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">Perlu Aksi</span>
                    @endif
                </div>
                <div class="text-[28px] font-bold text-gray-900 leading-none">{{ $pendingVerifications }}</div>
                <div class="text-[12px] text-gray-500 mt-1">Verifikasi Pembayaran</div>
            </a>

            {{-- Pending Checkouts --}}
            <a href="{{ route('admin.boxes') }}" wire:navigate class="group bg-white rounded-[12px] border border-gray-100 p-5 hover:shadow-card-hover hover:border-gray-200 transition-all duration-200">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-10 h-10 rounded-[10px] bg-amber-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                    </div>
                    @if($pendingCheckouts > 0)
                        <span class="text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">Perlu Proses</span>
                    @endif
                </div>
                <div class="text-[28px] font-bold text-gray-900 leading-none">{{ $pendingCheckouts }}</div>
                <div class="text-[12px] text-gray-500 mt-1">Checkout Menunggu</div>
            </a>

            {{-- Active Customers --}}
            <a href="{{ route('admin.customers') }}" wire:navigate class="group bg-white rounded-[12px] border border-gray-100 p-5 hover:shadow-card-hover hover:border-gray-200 transition-all duration-200">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-10 h-10 rounded-[10px] bg-emerald-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    @if($customerPending > 0)
                        <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">{{ $customerPending }} baru</span>
                    @endif
                </div>
                <div class="text-[28px] font-bold text-gray-900 leading-none">{{ $customerActive }}</div>
                <div class="text-[12px] text-gray-500 mt-1">Customer Aktif <span class="text-gray-400">/ {{ $totalCustomers }} total</span></div>
            </a>

            {{-- Pending Complaints --}}
            <a href="{{ route('admin.customers') }}" wire:navigate class="group bg-white rounded-[12px] border border-gray-100 p-5 hover:shadow-card-hover hover:border-gray-200 transition-all duration-200">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-10 h-10 rounded-[10px] bg-red-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    @if($pendingComplaints > 0)
                        <span class="text-[10px] font-bold text-red-600 bg-red-50 px-2 py-0.5 rounded-full">Perlu Tindak</span>
                    @endif
                </div>
                <div class="text-[28px] font-bold text-gray-900 leading-none">{{ $pendingComplaints }}</div>
                <div class="text-[12px] text-gray-500 mt-1">Komplain Terbuka</div>
            </a>
        </div>

        {{-- Box Overview + Quick Actions + Notifications --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Box Status Overview --}}
            <div class="lg:col-span-2 bg-white rounded-[12px] border border-gray-100">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="text-[14px] font-semibold text-gray-900">Status Box</h3>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        {{-- Sharing --}}
                        <div class="rounded-[10px] border border-gray-100 p-4">
                            <div class="flex items-center gap-2 mb-3">
                                <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                                <span class="text-[12px] font-semibold text-gray-700 uppercase tracking-wide">Sharing</span>
                            </div>
                            <div class="flex items-end justify-between">
                                <div>
                                    <span class="text-[22px] font-bold text-gray-900">{{ $sharingOpen }}</span>
                                    <span class="text-[12px] text-gray-400 ml-1">aktif</span>
                                </div>
                                <span class="text-[11px] text-gray-400">{{ $sharingClosed }} selesai</span>
                            </div>
                            <div class="mt-3 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                @php $total = $sharingOpen + $sharingClosed; @endphp
                                <div class="h-full bg-blue-500 rounded-full transition-all" style="width: {{ $total > 0 ? ($sharingOpen / $total) * 100 : 0 }}%"></div>
                            </div>
                        </div>

                        {{-- Direct --}}
                        <div class="rounded-[10px] border border-gray-100 p-4">
                            <div class="flex items-center gap-2 mb-3">
                                <div class="w-2 h-2 rounded-full bg-amber-500"></div>
                                <span class="text-[12px] font-semibold text-gray-700 uppercase tracking-wide">Direct</span>
                            </div>
                            <div class="flex items-end justify-between">
                                <div>
                                    <span class="text-[22px] font-bold text-gray-900">{{ $directOpen }}</span>
                                    <span class="text-[12px] text-gray-400 ml-1">aktif</span>
                                </div>
                                <span class="text-[11px] text-gray-400">{{ $directClosed }} selesai</span>
                            </div>
                            <div class="mt-3 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                @php $total = $directOpen + $directClosed; @endphp
                                <div class="h-full bg-amber-500 rounded-full transition-all" style="width: {{ $total > 0 ? ($directOpen / $total) * 100 : 0 }}%"></div>
                            </div>
                        </div>

                        {{-- Handcarry --}}
                        <div class="rounded-[10px] border border-gray-100 p-4">
                            <div class="flex items-center gap-2 mb-3">
                                <div class="w-2 h-2 rounded-full bg-violet-500"></div>
                                <span class="text-[12px] font-semibold text-gray-700 uppercase tracking-wide">Handcarry</span>
                            </div>
                            <div class="flex items-end justify-between">
                                <div>
                                    <span class="text-[22px] font-bold text-gray-900">{{ $handcarryOpen }}</span>
                                    <span class="text-[12px] text-gray-400 ml-1">aktif</span>
                                </div>
                                <span class="text-[11px] text-gray-400">{{ $handcarryClosed }} selesai</span>
                            </div>
                            <div class="mt-3 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                @php $total = $handcarryOpen + $handcarryClosed; @endphp
                                <div class="h-full bg-violet-500 rounded-full transition-all" style="width: {{ $total > 0 ? ($handcarryOpen / $total) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="bg-white rounded-[12px] border border-gray-100">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="text-[14px] font-semibold text-gray-900">Menu Cepat</h3>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route('admin.verification') }}" wire:navigate class="flex flex-col items-center gap-2 p-3 rounded-[10px] hover:bg-gray-50 transition-colors group">
                            <div class="w-10 h-10 rounded-[10px] bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-100 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            </div>
                            <span class="text-[11px] font-medium text-gray-600">Verifikasi</span>
                        </a>
                        <a href="{{ route('admin.invoices') }}" wire:navigate class="flex flex-col items-center gap-2 p-3 rounded-[10px] hover:bg-gray-50 transition-colors group">
                            <div class="w-10 h-10 rounded-[10px] bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-100 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <span class="text-[11px] font-medium text-gray-600">Invoice</span>
                        </a>
                        <a href="{{ route('admin.est-update') }}" wire:navigate class="flex flex-col items-center gap-2 p-3 rounded-[10px] hover:bg-gray-50 transition-colors group">
                            <div class="w-10 h-10 rounded-[10px] bg-amber-50 text-amber-600 flex items-center justify-center group-hover:bg-amber-100 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <span class="text-[11px] font-medium text-gray-600">Est Update</span>
                        </a>
                        <a href="{{ route('admin.recap') }}" wire:navigate class="flex flex-col items-center gap-2 p-3 rounded-[10px] hover:bg-gray-50 transition-colors group">
                            <div class="w-10 h-10 rounded-[10px] bg-violet-50 text-violet-600 flex items-center justify-center group-hover:bg-violet-100 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            </div>
                            <span class="text-[11px] font-medium text-gray-600">Recap</span>
                        </a>
                        <a href="{{ route('admin.customers') }}" wire:navigate class="flex flex-col items-center gap-2 p-3 rounded-[10px] hover:bg-gray-50 transition-colors group">
                            <div class="w-10 h-10 rounded-[10px] bg-rose-50 text-rose-500 flex items-center justify-center group-hover:bg-rose-100 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            </div>
                            <span class="text-[11px] font-medium text-gray-600">Customer</span>
                        </a>
                        <a href="{{ route('admin.settings') }}" wire:navigate class="flex flex-col items-center gap-2 p-3 rounded-[10px] hover:bg-gray-50 transition-colors group">
                            <div class="w-10 h-10 rounded-[10px] bg-gray-100 text-gray-600 flex items-center justify-center group-hover:bg-gray-200 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <span class="text-[11px] font-medium text-gray-600">Pengaturan</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Notifications + Activity Log --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Notifications --}}
            <div class="bg-white rounded-[12px] border border-gray-100">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-[14px] font-semibold text-gray-900">Notifikasi</h3>
                    @if($notifications->where('read_at', null)->count() > 0)
                        <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">{{ $notifications->where('read_at', null)->count() }} baru</span>
                    @endif
                </div>
                <div class="max-h-[340px] overflow-y-auto">
                    @if($notifications->isEmpty())
                        <div class="flex flex-col items-center py-12 text-center px-4">
                            <div class="w-12 h-12 rounded-full bg-gray-50 flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            </div>
                            <p class="text-[13px] text-gray-400">Tidak ada notifikasi</p>
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
                                        <p class="text-[13px] font-medium text-gray-800 truncate">{{ $notif->data['title'] ?? 'Notifikasi' }}</p>
                                        <p class="text-[12px] text-gray-500 mt-0.5 line-clamp-2">{{ $notif->data['message'] ?? '' }}</p>
                                        <p class="text-[11px] text-gray-400 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Recent Activity --}}
            <div class="bg-white rounded-[12px] border border-gray-100">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="text-[14px] font-semibold text-gray-900">Aktivitas Terbaru</h3>
                </div>
                <div class="max-h-[340px] overflow-y-auto">
                    @if($recentActivities->isEmpty())
                        <div class="flex flex-col items-center py-12 text-center px-4">
                            <div class="w-12 h-12 rounded-full bg-gray-50 flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <p class="text-[13px] text-gray-400">Belum ada aktivitas</p>
                        </div>
                    @else
                        <div class="divide-y divide-gray-50">
                            @foreach($recentActivities as $log)
                                <div class="flex items-start gap-3 px-5 py-3.5 hover:bg-gray-50/50 transition-colors">
                                    <div class="mt-0.5 w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0">
                                        <span class="text-[10px] font-bold text-gray-500">{{ $log->user ? strtoupper(substr($log->user->name, 0, 1)) : '?' }}</span>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-[13px] text-gray-700">
                                            <span class="font-medium">{{ $log->user->name ?? 'System' }}</span>
                                            <span class="text-gray-400 mx-1">{{ $log->event }}</span>
                                            <span class="font-medium">{{ class_basename($log->subject_type) }}</span>
                                            @if($log->subject_id)
                                                <span class="text-gray-400">#{{ $log->subject_id }}</span>
                                            @endif
                                        </p>
                                        <p class="text-[11px] text-gray-400 mt-0.5">{{ $log->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Deadline Tracking --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Payment Deadlines --}}
            <div class="bg-white rounded-[12px] border border-gray-100">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-[14px] font-semibold text-gray-900">Menunggu Pembayaran</h3>
                    <a href="{{ route('admin.invoices') }}" wire:navigate class="text-[12px] text-accent hover:text-primary font-medium transition-colors">Lihat Semua</a>
                </div>
                @if($deadlinePayments->isEmpty())
                    <div class="p-5 text-center">
                        <p class="text-[13px] text-gray-400">Tidak ada invoice menunggu pembayaran</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-50">
                        @foreach($deadlinePayments as $invoice)
                            <div class="flex items-center justify-between px-5 py-3.5 hover:bg-gray-50/50 transition-colors">
                                <div class="min-w-0">
                                    <p class="text-[13px] font-medium text-gray-800 truncate">{{ $invoice->invoice_number }}</p>
                                    <p class="text-[12px] text-gray-500">{{ $invoice->customer->name ?? '-' }}</p>
                                </div>
                                <div class="text-right flex-shrink-0 ml-4">
                                    <p class="text-[13px] font-semibold text-gray-900">Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</p>
                                    <p class="text-[11px] text-gray-400">{{ $invoice->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Storage Deadlines --}}
            <div class="bg-white rounded-[12px] border border-gray-100">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-[14px] font-semibold text-gray-900">Box Aktif</h3>
                    <a href="{{ route('admin.boxes') }}" wire:navigate class="text-[12px] text-accent hover:text-primary font-medium transition-colors">Lihat Semua</a>
                </div>
                @if($deadlineStorages->isEmpty())
                    <div class="p-5 text-center">
                        <p class="text-[13px] text-gray-400">Tidak ada box aktif</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-50">
                        @foreach($deadlineStorages as $box)
                            <div class="flex items-center justify-between px-5 py-3.5 hover:bg-gray-50/50 transition-colors">
                                <div class="min-w-0 flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-[8px] bg-gray-100 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-[13px] font-medium text-gray-800 truncate">{{ $box->tracking_number ?? $box->batch_name ?? 'Box #' . $box->id }}</p>
                                        <p class="text-[12px] text-gray-500">{{ ucfirst($box->type) }} · {{ $box->customer->name ?? '-' }}</p>
                                    </div>
                                </div>
                                <x-status-badge :status="$box->status" />
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
