<div class="min-h-screen bg-[#f8fafc]">
    {{-- Page Header --}}
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div>
                <h1 class="text-[22px] font-bold text-gray-900 tracking-tight">Recap</h1>
                <p class="text-[13px] text-gray-500 mt-0.5">Rekap data operasional pengiriman</p>
            </div>
        </div>
    </div>

    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

        {{-- Summary Stats --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            <div class="bg-white rounded-[10px] border border-gray-100 p-4 text-center">
                <p class="text-[22px] font-bold text-gray-900">{{ number_format($totalBoxes) }}</p>
                <p class="text-[11px] text-gray-500 mt-0.5">Total Box</p>
            </div>
            <div class="bg-white rounded-[10px] border border-gray-100 p-4 text-center">
                <p class="text-[22px] font-bold text-gray-900">{{ number_format($totalItems) }}</p>
                <p class="text-[11px] text-gray-500 mt-0.5">Total Barang</p>
            </div>
            <div class="bg-white rounded-[10px] border border-gray-100 p-4 text-center">
                <p class="text-[22px] font-bold text-gray-900">{{ number_format($totalInvoices) }}</p>
                <p class="text-[11px] text-gray-500 mt-0.5">Total Invoice</p>
            </div>
            <div class="bg-white rounded-[10px] border border-gray-100 p-4 text-center">
                <p class="text-[22px] font-bold text-emerald-600">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                <p class="text-[11px] text-gray-500 mt-0.5">Total Revenue</p>
            </div>
            <div class="bg-white rounded-[10px] border border-gray-100 p-4 text-center">
                <p class="text-[22px] font-bold text-gray-900">{{ number_format($totalCheckouts) }}</p>
                <p class="text-[11px] text-gray-500 mt-0.5">Total Checkout</p>
            </div>
            <div class="bg-white rounded-[10px] border border-gray-100 p-4 text-center">
                <p class="text-[22px] font-bold {{ $totalComplaints > 0 ? 'text-red-500' : 'text-gray-900' }}">{{ number_format($totalComplaints) }}</p>
                <p class="text-[11px] text-gray-500 mt-0.5">Total Komplain</p>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white rounded-[12px] border border-gray-100 p-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <div class="lg:col-span-2 relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari tracking, batch, atau customer..." class="w-full pl-10 pr-4 py-2.5 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-1 focus:ring-accent/20 transition-colors placeholder:text-gray-400">
                </div>
                <select wire:model.live="filterType" class="py-2.5 px-3 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-1 focus:ring-accent/20 transition-colors text-gray-600">
                    <option value="">Semua Tipe</option>
                    <option value="sharing">Sharing</option>
                    <option value="direct">Direct</option>
                    <option value="handcarry">Handcarry</option>
                </select>
                <select wire:model.live="filterMethod" class="py-2.5 px-3 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-1 focus:ring-accent/20 transition-colors text-gray-600">
                    <option value="">Semua Metode</option>
                    <option value="air">Air</option>
                    <option value="sea">Sea</option>
                </select>
                <div class="flex items-center gap-2">
                    <input type="date" wire:model.live="filterDateFrom" class="w-full px-3 py-2.5 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-1 focus:ring-accent/20 transition-colors text-gray-600" title="Dari Tanggal">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 mt-3">
                <div class="lg:col-span-2"></div>
                <div class="lg:col-span-2 flex items-center gap-2">
                    <span class="text-[12px] text-gray-500 whitespace-nowrap">Sampai:</span>
                    <input type="date" wire:model.live="filterDateTo" class="w-full px-3 py-2.5 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-1 focus:ring-accent/20 transition-colors text-gray-600">
                </div>
                <div class="flex items-center">
                    <button wire:click="$set('search', ''); $set('filterType', ''); $set('filterMethod', ''); $set('filterDateFrom', ''); $set('filterDateTo', ''); loadStats()" class="px-3 py-2.5 text-[12px] font-medium text-gray-600 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 rounded-[8px] transition-colors w-full text-center">
                        Reset Filter
                    </button>
                </div>
            </div>
        </div>

        {{-- Data Table --}}
        @if($boxes->isEmpty())
            <div class="bg-white rounded-[12px] border border-gray-100">
                <x-empty-state
                    icon="chart"
                    title="Tidak ada data"
                    text="Tidak ada data yang sesuai dengan filter yang dipilih."
                />
            </div>
        @else
            {{-- Desktop Table --}}
            <div class="hidden md:block bg-white rounded-[12px] border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Box</th>
                                <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Customer</th>
                                <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Tipe</th>
                                <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Metode</th>
                                <th class="text-center px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Barang</th>
                                <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                                <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">ETD</th>
                                <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">ETA</th>
                                <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Dibuat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($boxes as $box)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-5 py-3.5">
                                        <div>
                                            <p class="text-[13px] font-semibold text-gray-900">{{ $box->tracking_number ?? $box->batch_name ?? 'Box #' . $box->id }}</p>
                                            @if($box->notes)
                                                <p class="text-[11px] text-gray-400 truncate max-w-[200px]">{{ $box->notes }}</p>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span class="text-[13px] text-gray-700">{{ $box->customer->name ?? '-' }}</span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span class="text-[12px] font-medium text-gray-600 capitalize">{{ $box->type }}</span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span class="inline-flex items-center gap-1 text-[12px] font-medium uppercase {{ $box->method === 'air' ? 'text-blue-600' : 'text-cyan-600' }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $box->method === 'air' ? 'bg-blue-400' : 'bg-cyan-400' }}"></span>
                                            {{ $box->method }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        <span class="text-[13px] text-gray-700 font-medium">{{ $box->items_count }}</span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <x-status-badge :status="$box->status" />
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span class="text-[12px] {{ $box->etd ? 'text-gray-700' : 'text-gray-400' }}">{{ $box->etd ? $box->etd->format('d/m/y') : '-' }}</span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span class="text-[12px] {{ $box->eta ? 'text-gray-700' : 'text-gray-400' }}">{{ $box->eta ? $box->eta->format('d/m/y') : '-' }}</span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span class="text-[12px] text-gray-500">{{ $box->created_at->format('d M Y') }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($boxes->hasPages())
                    <div class="px-5 py-3 border-t border-gray-100">
                        {{ $boxes->links() }}
                    </div>
                @endif
            </div>

            {{-- Mobile Cards --}}
            <div class="md:hidden space-y-3">
                @foreach($boxes as $box)
                    <div class="bg-white rounded-[12px] border border-gray-100 p-4">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-[10px] bg-gray-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                </div>
                                <div>
                                    <p class="text-[14px] font-semibold text-gray-900">{{ $box->tracking_number ?? $box->batch_name ?? 'Box #' . $box->id }}</p>
                                    <p class="text-[12px] text-gray-500">{{ $box->customer->name ?? '-' }}</p>
                                </div>
                            </div>
                            <x-status-badge :status="$box->status" />
                        </div>
                        <div class="grid grid-cols-3 gap-2 text-[11px] py-3 border-t border-gray-100">
                            <div>
                                <span class="text-gray-400 block">Tipe</span>
                                <span class="font-medium text-gray-700 capitalize">{{ $box->type }}</span>
                            </div>
                            <div>
                                <span class="text-gray-400 block">Metode</span>
                                <span class="font-medium text-gray-700 uppercase">{{ $box->method }}</span>
                            </div>
                            <div>
                                <span class="text-gray-400 block">Barang</span>
                                <span class="font-medium text-gray-700">{{ $box->items_count }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 text-[11px] text-gray-500 pt-2 border-t border-gray-100">
                            <span>ETD: {{ $box->etd ? $box->etd->format('d/m/y') : '-' }}</span>
                            <span>ETA: {{ $box->eta ? $box->eta->format('d/m/y') : '-' }}</span>
                            <span class="ml-auto">{{ $box->created_at->format('d M Y') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
