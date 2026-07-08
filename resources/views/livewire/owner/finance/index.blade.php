<div class="min-h-screen bg-[#f8fafc]">
    {{-- Page Header --}}
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h1 class="text-[22px] font-bold text-gray-900 tracking-tight">Laporan Keuangan</h1>
                    <p class="text-body text-gray-500 mt-0.5">Analisis pendapatan, outstanding, dan transaksi</p>
                </div>
                <div class="flex items-center gap-2">
                    <button
                        wire:click="confirmExport('csv')"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 text-body font-medium text-gray-700 bg-white border border-gray-200 rounded-[8px] hover:bg-gray-50 hover:border-gray-300 transition-all"
                    >
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        CSV
                    </button>
                    <button
                        wire:click="confirmExport('excel')"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 text-body font-medium text-white bg-primary rounded-[8px] hover:bg-primary-light transition-all"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Excel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="bg-white rounded-[12px] border border-gray-100 p-5">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-[10px] bg-emerald-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="text-[24px] font-bold text-gray-900 leading-none tracking-tight">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
                <div class="text-caption text-gray-500 mt-1">Total Revenue</div>
            </div>
            <div class="bg-white rounded-[12px] border border-gray-100 p-5">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-[10px] bg-amber-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="text-[24px] font-bold text-gray-900 leading-none tracking-tight">Rp {{ number_format($totalOutstanding, 0, ',', '.') }}</div>
                <div class="text-caption text-gray-500 mt-1">Outstanding</div>
            </div>
            <div class="bg-white rounded-[12px] border border-gray-100 p-5">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-[10px] bg-blue-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                </div>
                <div class="text-[24px] font-bold text-gray-900 leading-none tracking-tight">Rp {{ number_format($totalProfit, 0, ',', '.') }}</div>
                <div class="text-caption text-gray-500 mt-1">Profit</div>
            </div>
            <div class="bg-white rounded-[12px] border border-gray-100 p-5">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-[10px] bg-violet-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                </div>
                <div class="text-[24px] font-bold text-gray-900 leading-none tracking-tight">{{ $totalInvoiceCount }}</div>
                <div class="text-caption text-gray-500 mt-1">Total Invoice</div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white rounded-[12px] border border-gray-100 p-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-7 gap-3">
                {{-- Search --}}
                <div class="sm:col-span-2 relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari invoice atau customer..." class="w-full pl-10 pr-4 py-2.5 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors placeholder:text-gray-400">
                </div>

                {{-- Date From --}}
                <div>
                    <input type="date" wire:model.live="filterDateFrom" class="w-full py-2.5 px-3 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors text-gray-600">
                </div>

                {{-- Date To --}}
                <div>
                    <input type="date" wire:model.live="filterDateTo" class="w-full py-2.5 px-3 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors text-gray-600">
                </div>

                {{-- Month --}}
                <div>
                    <select wire:model.live="filterMonth" class="w-full py-2.5 px-3 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors text-gray-600">
                        <option value="">Semua Bulan</option>
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}">{{ DateTime::createFromFormat('!m', $m)->format('F') }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Year --}}
                <div>
                    <select wire:model.live="filterYear" class="w-full py-2.5 px-3 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors text-gray-600">
                        <option value="">Semua Tahun</option>
                        @foreach($years as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Customer --}}
                <div>
                    <select wire:model.live="filterCustomer" class="w-full py-2.5 px-3 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors text-gray-600">
                        <option value="">Semua Customer</option>
                        @foreach($customers as $cust)
                            <option value="{{ $cust->id }}">{{ $cust->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Status Filter + Reset --}}
            <div class="flex flex-wrap items-center gap-3 mt-3">
                <div class="flex flex-wrap gap-1.5">
                    @php
                        $statuses = [
                            '' => 'Semua',
                            'waiting_payment' => 'Menunggu Bayar',
                            'waiting_verification' => 'Menunggu Verifikasi',
                            'verified' => 'Terverifikasi',
                        ];
                    @endphp
                    @foreach($statuses as $val => $label)
                        <button
                            wire:click="$set('filterStatus', '{{ $val }}')"
                            class="px-3 py-1.5 rounded-full text-caption font-medium transition-all {{ $filterStatus === $val ? 'bg-primary text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                        >
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
                @if($search || $filterDateFrom || $filterDateTo || $filterMonth || $filterYear || $filterCustomer || $filterStatus)
                    <button wire:click="resetFilters" class="inline-flex items-center gap-1 px-3 py-2 text-caption text-gray-500 hover:text-gray-700 transition-colors min-h-[44px]">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Reset
                    </button>
                @endif
            </div>
        </div>

        {{-- Invoice Table --}}
        @if($invoices->isEmpty())
            <div class="bg-white rounded-[12px] border border-gray-100">
                <x-empty-state
                    icon="invoice"
                    title="Tidak ada data ditemukan"
                    text="Coba ubah filter atau kata kunci pencarian untuk menemukan data yang Anda cari."
                />
            </div>
        @else
            {{-- Desktop Table --}}
            <div class="hidden md:block bg-white rounded-[12px] border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Invoice</th>
                                <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Customer</th>
                                <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Box</th>
                                <th class="text-right px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Fee TAX</th>
                                <th class="text-right px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Fee WH</th>
                                <th class="text-right px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Fee Pack</th>
                                <th class="text-right px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Grand Total</th>
                                <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                                <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($invoices as $inv)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-5 py-3.5">
                                        <span class="text-body font-semibold text-gray-900">{{ $inv->invoice_number }}</span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                                                <span class="text-caption font-bold text-primary">{{ strtoupper(substr($inv->customer->name ?? '?', 0, 1)) }}</span>
                                            </div>
                                            <span class="text-body text-gray-700">{{ $inv->customer->name ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span class="text-caption text-gray-600">{{ $inv->box->tracking_number ?? $inv->box->batch_name ?? '-' }}</span>
                                    </td>
                                    <td class="px-5 py-3.5 text-right">
                                        <span class="text-body text-gray-700">Rp {{ number_format($inv->fee_tax, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="px-5 py-3.5 text-right">
                                        <span class="text-body text-gray-700">Rp {{ number_format($inv->fee_wh, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="px-5 py-3.5 text-right">
                                        <span class="text-body text-gray-700">Rp {{ number_format($inv->fee_packing, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="px-5 py-3.5 text-right">
                                        <span class="text-body font-semibold text-gray-900">Rp {{ number_format($inv->grand_total, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <x-status-badge :status="$inv->status" />
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span class="text-caption text-gray-500">{{ $inv->created_at->format('d M Y') }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($invoices->hasPages())
                    <div class="px-5 py-3 border-t border-gray-100">
                        {{ $invoices->links() }}
                    </div>
                @endif
            </div>

            {{-- Mobile Cards --}}
            <div class="md:hidden space-y-3">
                @foreach($invoices as $inv)
                    <div class="bg-white rounded-[12px] border border-gray-100 p-4">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <p class="text-body font-semibold text-gray-900">{{ $inv->invoice_number }}</p>
                                <p class="text-caption text-gray-500 mt-0.5">{{ $inv->customer->name ?? '-' }}</p>
                            </div>
                            <x-status-badge :status="$inv->status" />
                        </div>
                        <div class="space-y-2 pt-3 border-t border-gray-100">
                            <div class="flex justify-between text-caption">
                                <span class="text-gray-500">Fee TAX</span>
                                <span class="text-gray-700">Rp {{ number_format($inv->fee_tax, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-caption">
                                <span class="text-gray-500">Fee WH</span>
                                <span class="text-gray-700">Rp {{ number_format($inv->fee_wh, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-caption">
                                <span class="text-gray-500">Fee Packing</span>
                                <span class="text-gray-700">Rp {{ number_format($inv->fee_packing, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-body pt-2 border-t border-gray-100">
                                <span class="font-semibold text-gray-900">Grand Total</span>
                                <span class="font-bold text-gray-900">Rp {{ number_format($inv->grand_total, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between mt-3 pt-2 border-t border-gray-100">
                            <span class="text-caption text-gray-400">{{ $inv->box->tracking_number ?? $inv->box->batch_name ?? '-' }}</span>
                            <span class="text-caption text-gray-400">{{ $inv->created_at->format('d M Y') }}</span>
                        </div>
                    </div>
                @endforeach

                @if($invoices->hasPages())
                    <div class="px-1">
                        {{ $invoices->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>

    {{-- Export Confirmation Modal --}}
    @if($showExportConfirm)
        <div class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0">
            <div class="fixed inset-0 bg-gray-500/75" wire:click="cancelExport"></div>
            <div class="relative bg-white rounded-[16px] shadow-modal max-w-md mx-auto mt-[10vh] p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-[16px] font-semibold text-gray-900">Export Laporan</h3>
                        <p class="text-body text-gray-500">Format: {{ strtoupper($exportType) }}</p>
                    </div>
                </div>
                <p class="text-body text-gray-600 mb-6">
                    Export akan mencakup <strong>{{ $totalInvoiceCount }} invoice</strong> sesuai filter yang sedang aktif. File akan diunduh secara otomatis.
                </p>
                <div class="flex items-center gap-3 justify-end">
                    <button wire:click="cancelExport" class="px-4 py-2.5 text-body font-medium text-gray-700 bg-white border border-gray-200 rounded-[8px] hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <a href="{{ $this->export_url }}" class="px-4 py-2.5 text-body font-medium text-white bg-primary rounded-[8px] hover:bg-primary-light transition-colors">
                        Export Sekarang
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
