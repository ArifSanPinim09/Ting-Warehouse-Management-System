<div class="min-h-screen bg-[#f8fafc]">
    {{-- Page Header --}}
    <div class="bg-white border-b border-gray-100">
        <div class="mx-auto w-full max-w-[1500px] px-4 md:px-6 lg:px-8 xl:px-10 2xl:px-12 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-[22px] font-bold text-gray-900 tracking-tight">Recap</h1>
                    <p class="text-body text-gray-500 mt-0.5">Rekap data customer dan warehouse China</p>
                </div>
                <div class="flex items-center gap-2">
                    @if($activeTab === 'wh-china')
                        <button wire:click="runAutoMatch"
                            class="inline-flex items-center gap-2 px-3.5 py-2 text-[13px] font-medium text-gray-700 bg-white border border-gray-200 rounded-[8px] hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            Auto Match
                        </button>
                        <button wire:click="openWhModal"
                            class="inline-flex items-center gap-2 px-4 py-2 text-[13px] font-medium text-white bg-primary rounded-[8px] hover:bg-primary-light transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Input WH Data
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="mx-auto w-full max-w-[1500px] px-4 md:px-6 lg:px-8 xl:px-10 2xl:px-12 py-6 space-y-5">

        {{-- ─── Summary Stats ──────────────────────────────────────── --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2.5">
            <div class="bg-white rounded-[10px] border border-gray-100 px-3.5 py-3 flex items-center gap-3">
                <div class="w-8 h-8 rounded-[8px] bg-slate-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-[16px] font-bold text-gray-900 leading-tight tabular-nums">{{ number_format($totalBoxes) }}</p>
                    <p class="text-[11px] text-gray-400 leading-tight">Box</p>
                </div>
            </div>
            <div class="bg-white rounded-[10px] border border-gray-100 px-3.5 py-3 flex items-center gap-3">
                <div class="w-8 h-8 rounded-[8px] bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-[16px] font-bold text-gray-900 leading-tight tabular-nums">{{ number_format($totalItems) }}</p>
                    <p class="text-[11px] text-gray-400 leading-tight">Barang</p>
                </div>
            </div>
            <div class="bg-white rounded-[10px] border border-gray-100 px-3.5 py-3 flex items-center gap-3">
                <div class="w-8 h-8 rounded-[8px] bg-violet-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-[16px] font-bold text-gray-900 leading-tight tabular-nums">{{ number_format($totalWhChina) }}</p>
                    <p class="text-[11px] text-gray-400 leading-tight">WH China</p>
                </div>
            </div>
            <div class="bg-white rounded-[10px] border border-gray-100 px-3.5 py-3 flex items-center gap-3">
                <div class="w-8 h-8 rounded-[8px] bg-emerald-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-[16px] font-bold text-emerald-600 leading-tight tabular-nums">{{ number_format($totalMatched) }}</p>
                    <p class="text-[11px] text-gray-400 leading-tight">Matched</p>
                </div>
            </div>
            <div class="bg-white rounded-[10px] border border-gray-100 px-3.5 py-3 flex items-center gap-3">
                <div class="w-8 h-8 rounded-[8px] bg-amber-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-[16px] font-bold text-amber-600 leading-tight tabular-nums">{{ number_format($totalUnmatched) }}</p>
                    <p class="text-[11px] text-gray-400 leading-tight">Unmatched</p>
                </div>
            </div>
            <div class="bg-white rounded-[10px] border border-gray-100 px-3.5 py-3 flex items-center gap-3">
                <div class="w-8 h-8 rounded-[8px] bg-emerald-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-[16px] font-bold text-emerald-600 leading-tight tabular-nums">Rp {{ number_format($totalRevenue / 1000000, 1) }}jt</p>
                    <p class="text-[11px] text-gray-400 leading-tight">Revenue</p>
                </div>
            </div>
        </div>

        {{-- ─── Compact Filters + Tabs ─────────────────────────────── --}}
        <div class="bg-white rounded-[12px] border border-gray-100 px-4 py-3">
            <div class="flex flex-col lg:flex-row lg:items-center gap-3">
                {{-- Left: Search --}}
                <div class="relative flex-1 min-w-0">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari resi, nama, customer..."
                        class="w-full pl-9 pr-3 py-2 text-[13px] bg-gray-50 border border-gray-200 rounded-[8px] focus:bg-white focus:border-accent focus:ring-2 focus:ring-accent/40 transition-all placeholder:text-gray-400">
                </div>

                {{-- Center: Filters --}}
                <div class="flex items-center gap-2 flex-shrink-0">
                    <select wire:model.live="filterType" class="py-2 pl-3 pr-8 text-[13px] bg-gray-50 border border-gray-200 rounded-[8px] focus:bg-white focus:border-accent focus:ring-2 focus:ring-accent/40 transition-all text-gray-600">
                        <option value="">Semua Tipe</option>
                        <option value="sharing">Sharing</option>
                        <option value="direct">Direct</option>
                        <option value="handcarry">Handcarry</option>
                    </select>
                    <select wire:model.live="filterMethod" class="py-2 pl-3 pr-8 text-[13px] bg-gray-50 border border-gray-200 rounded-[8px] focus:bg-white focus:border-accent focus:ring-2 focus:ring-accent/40 transition-all text-gray-600">
                        <option value="">Metode</option>
                        <option value="air">Air</option>
                        <option value="sea">Sea</option>
                    </select>
                    <div class="hidden sm:flex items-center gap-1.5">
                        <input type="date" wire:model.live="filterDateFrom"
                            class="w-[130px] py-2 px-2.5 text-[13px] bg-gray-50 border border-gray-200 rounded-[8px] focus:bg-white focus:border-accent focus:ring-2 focus:ring-accent/40 transition-all text-gray-600">
                        <span class="text-gray-300 text-[12px]">—</span>
                        <input type="date" wire:model.live="filterDateTo"
                            class="w-[130px] py-2 px-2.5 text-[13px] bg-gray-50 border border-gray-200 rounded-[8px] focus:bg-white focus:border-accent focus:ring-2 focus:ring-accent/40 transition-all text-gray-600">
                    </div>
                    <button wire:click="$set('search', ''); $set('filterType', ''); $set('filterMethod', ''); $set('filterDateFrom', ''); $set('filterDateTo', ''); loadStats()"
                        class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-[8px] transition-colors" title="Reset filter">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </button>
                </div>

                {{-- Right: Tab pills --}}
                <div class="flex items-center gap-1 bg-gray-100 rounded-[8px] p-0.5 flex-shrink-0">
                    <button wire:click="switchTab('customer')"
                        class="px-3 py-1.5 rounded-[6px] text-[12px] font-semibold transition-all {{ $activeTab === 'customer' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        Customer <span class="ml-1 {{ $activeTab === 'customer' ? 'text-primary' : 'text-gray-400' }}">{{ number_format($totalItems) }}</span>
                    </button>
                    <button wire:click="switchTab('wh-china')"
                        class="px-3 py-1.5 rounded-[6px] text-[12px] font-semibold transition-all {{ $activeTab === 'wh-china' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        WH China <span class="ml-1 {{ $activeTab === 'wh-china' ? 'text-primary' : 'text-gray-400' }}">{{ number_format($totalWhChina) }}</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- PANEL 1: Data Customer                                    --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        @if($activeTab === 'customer')
            @if($customerItems->isEmpty())
                <div class="bg-white rounded-[12px] border border-gray-100">
                    <x-empty-state
                        icon="chart"
                        title="Tidak ada data customer"
                        text="Belum ada data setor resi dari customer yang sesuai filter."
                    />
                </div>
            @else
                {{-- Desktop Table --}}
                <div class="hidden md:block bg-white rounded-[12px] border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-100 bg-gray-50/60">
                                    <th class="text-left px-3 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Customer</th>
                                    <th class="text-left px-3 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">No Resi</th>
                                    <th class="text-left px-3 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Info Barang</th>
                                    <th class="text-center px-3 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Foto CO</th>
                                    <th class="text-center px-3 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Sensitif</th>
                                    <th class="text-left px-3 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Request</th>
                                    <th class="text-center px-3 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Foto CN</th>
                                    <th class="text-center px-3 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Foto INA</th>
                                    <th class="text-left px-3 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Berat/Vol</th>
                                    <th class="text-right px-3 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Tax</th>
                                    <th class="text-left px-3 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Box</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($customerItems as $item)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-3 py-2.5">
                                            <span class="text-[13px] text-gray-700 font-medium">{{ $item->customer->name ?? '-' }}</span>
                                        </td>
                                        <td class="px-3 py-2.5">
                                            <span class="text-[13px] font-mono font-semibold text-gray-900">{{ $item->resi_number }}</span>
                                            <p class="text-[11px] text-gray-400 mt-0.5">{{ $item->created_at->format('d M Y H:i') }}</p>
                                        </td>
                                        <td class="px-3 py-2.5">
                                            <span class="text-[13px] text-gray-700">{{ $item->name }}</span>
                                            <p class="text-[11px] text-gray-500">{{ $item->quantity }}x · ¥{{ number_format($item->price_yuan ?? 0, 2) }}</p>
                                        </td>
                                        <td class="px-3 py-2.5 text-center">
                                            @if($item->proof_co)
                                                <a href="{{ Storage::url($item->proof_co) }}" target="_blank" class="text-[11px] text-accent hover:underline font-medium">Foto</a>
                                            @else
                                                <span class="text-[11px] text-gray-300">-</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2.5 text-center">
                                            @if($item->is_sensitive)
                                                <span class="text-[10px] font-bold text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded-full">{{ $item->sensitive_type ?? 'Ya' }}</span>
                                            @else
                                                <span class="text-[11px] text-gray-300">-</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2.5">
                                            @if($item->request_type)
                                                @php $requests = json_decode($item->request_type, true) ?? []; @endphp
                                                @foreach($requests as $req)
                                                    <span class="text-[10px] font-medium text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded-full mr-0.5">{{ str_replace('_', ' ', ucfirst($req)) }}</span>
                                                @endforeach
                                            @else
                                                <span class="text-[11px] text-gray-300">-</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2.5 text-center">
                                            @if($item->whChinaData && $item->whChinaData->foto_arrived_china)
                                                <a href="{{ Storage::url($item->whChinaData->foto_arrived_china) }}" target="_blank" class="text-[11px] text-accent hover:underline font-medium">Foto</a>
                                            @else
                                                <span class="text-[11px] text-gray-300">-</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2.5 text-center">
                                            @if($item->whChinaData && $item->whChinaData->foto_arrived_ina)
                                                <a href="{{ Storage::url($item->whChinaData->foto_arrived_ina) }}" target="_blank" class="text-[11px] text-accent hover:underline font-medium">Foto</a>
                                            @else
                                                <span class="text-[11px] text-gray-300">-</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2.5">
                                            @if($item->whChinaData)
                                                @if($item->whChinaData->berat)
                                                    <span class="text-[12px] text-gray-700">{{ number_format($item->whChinaData->berat, 1) }} kg</span>
                                                @endif
                                                @if($item->whChinaData->panjang && $item->whChinaData->lebar && $item->whChinaData->tinggi)
                                                    <p class="text-[11px] text-gray-500">{{ $item->whChinaData->panjang }}×{{ $item->whChinaData->lebar }}×{{ $item->whChinaData->tinggi }} · {{ number_format($item->whChinaData->volume ?? 0, 2) }} m³</p>
                                                @endif
                                            @else
                                                <span class="text-[11px] text-gray-300">-</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2.5 text-right">
                                            @if($item->whChinaData && $item->whChinaData->biaya_tax)
                                                <span class="text-[12px] text-gray-700 tabular-nums">Rp {{ number_format($item->whChinaData->biaya_tax, 0, ',', '.') }}</span>
                                            @else
                                                <span class="text-[11px] text-gray-300">-</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2.5">
                                            <span class="text-[12px] text-gray-500">{{ $item->box->display_name ?? '-' }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($customerItems->hasPages())
                        <div class="px-5 py-3 border-t border-gray-100">
                            {{ $customerItems->links() }}
                        </div>
                    @endif
                </div>

                {{-- Mobile Cards --}}
                <div class="md:hidden space-y-2">
                    @foreach($customerItems as $item)
                        <div class="bg-white rounded-[10px] border border-gray-100 px-4 py-3 flex items-center gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-[13px] font-mono font-semibold text-gray-900">{{ $item->resi_number }}</span>
                                    @if($item->whChinaData)
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700">
                                            <span class="w-1 h-1 rounded-full bg-emerald-500"></span>
                                            OK
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700">
                                            <span class="w-1 h-1 rounded-full bg-amber-500"></span>
                                            ?
                                        </span>
                                    @endif
                                </div>
                                <p class="text-[12px] text-gray-500 truncate">{{ $item->name }} · {{ $item->customer->name ?? '-' }}</p>
                                <p class="text-[11px] text-gray-400 mt-0.5">{{ $item->created_at->format('d M Y H:i') }}</p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <p class="text-[13px] font-medium text-gray-700 tabular-nums">{{ $item->quantity }}x</p>
                                <p class="text-[11px] text-gray-400 tabular-nums">¥{{ number_format($item->price_yuan ?? 0, 0) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif

        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- PANEL 2: Data WH China                                    --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        @if($activeTab === 'wh-china')
            @if($whChinaData->isEmpty())
                <div class="bg-white rounded-[12px] border border-gray-100">
                    <x-empty-state
                        icon="chart"
                        title="Tidak ada data WH China"
                        text="Belum ada data warehouse China yang diinput. Klik 'Input Data WH' untuk menambah."
                    />
                </div>
            @else
                {{-- Desktop Table --}}
                <div class="hidden md:block bg-white rounded-[12px] border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-100 bg-gray-50/60">
                                    <th class="text-left px-5 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">No Resi</th>
                                    <th class="text-left px-5 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Huruf Box</th>
                                    <th class="text-right px-5 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Berat CN</th>
                                    <th class="text-right px-5 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Berat INA</th>
                                    <th class="text-right px-5 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Volume</th>
                                    <th class="text-left px-5 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">P×L×T</th>
                                    <th class="text-right px-5 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Biaya Jasa</th>
                                    <th class="text-right px-5 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Biaya Tax</th>
                                    <th class="text-center px-5 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Foto</th>
                                    <th class="text-center px-5 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Arrived China</th>
                                    <th class="text-center px-5 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Arrived INA</th>
                                    <th class="text-left px-5 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Match</th>
                                    <th class="text-center px-5 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="text-left px-5 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Tgl Setor</th>
                                    <th class="text-left px-5 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Tgl Input</th>
                                    <th class="text-right px-5 py-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-wider"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($whChinaData as $wh)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-5 py-2.5">
                                            <span class="text-[13px] font-mono font-semibold text-gray-900">{{ $wh->resi_number }}</span>
                                        </td>
                                        <td class="px-5 py-2.5">
                                            @if($wh->huruf_box)
                                                <span class="text-[13px] font-semibold text-gray-700 bg-gray-100 px-2 py-0.5 rounded">{{ $wh->huruf_box }}</span>
                                            @else
                                                <span class="text-[13px] text-gray-300">—</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-2.5 text-right">
                                            <span class="text-[13px] text-gray-600 tabular-nums">{{ number_format($wh->berat, 2) }} kg</span>
                                        </td>
                                        <td class="px-5 py-2.5 text-right">
                                            @if($wh->berat_ina)
                                                <span class="text-[13px] text-gray-700 font-semibold tabular-nums">{{ number_format($wh->berat_ina, 2) }} kg</span>
                                            @else
                                                <span class="text-[13px] text-gray-300">—</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-2.5 text-right">
                                            @if($wh->volume)
                                                <span class="text-[13px] text-gray-700 font-semibold tabular-nums">{{ number_format($wh->volume, 4) }} m³</span>
                                            @else
                                                <span class="text-[13px] text-gray-300">—</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-2.5">
                                            @if($wh->panjang && $wh->lebar && $wh->tinggi)
                                                <span class="text-[13px] text-gray-600 tabular-nums">{{ number_format($wh->panjang, 0) }}×{{ number_format($wh->lebar, 0) }}×{{ number_format($wh->tinggi, 0) }}</span>
                                            @else
                                                <span class="text-[13px] text-gray-600">{{ $wh->ukuran_box ?: '—' }}</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-2.5 text-right">
                                            @if($wh->biaya_jasa !== null)
                                                <span class="text-[13px] text-gray-700 font-medium tabular-nums">Rp {{ number_format($wh->biaya_jasa, 0, ',', '.') }}</span>
                                            @else
                                                <span class="text-[13px] text-gray-300">—</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-2.5 text-right">
                                            @if($wh->biaya_tax !== null)
                                                <span class="text-[13px] text-gray-700 font-medium tabular-nums">Rp {{ number_format($wh->biaya_tax, 0, ',', '.') }}</span>
                                            @else
                                                <span class="text-[13px] text-gray-300">—</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-2.5 text-center">
                                            @if($wh->foto_barang)
                                                <a href="{{ Storage::url($wh->foto_barang) }}" target="_blank" class="inline-flex items-center gap-1 text-[12px] text-accent hover:underline font-medium">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                    Foto
                                                </a>
                                            @else
                                                <span class="text-[13px] text-gray-300">—</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-2.5 text-center">
                                            @if($wh->foto_arrived_china)
                                                <a href="{{ Storage::url($wh->foto_arrived_china) }}" target="_blank" class="inline-flex items-center gap-1 text-[12px] text-blue-600 hover:underline font-medium">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                    Foto
                                                </a>
                                            @else
                                                <span class="text-[12px] text-gray-300">—</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-2.5 text-center">
                                            @if($wh->foto_arrived_ina)
                                                <a href="{{ Storage::url($wh->foto_arrived_ina) }}" target="_blank" class="inline-flex items-center gap-1 text-[12px] text-green-600 hover:underline font-medium">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                    Foto
                                                </a>
                                            @else
                                                <span class="text-[12px] text-gray-300">—</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-2.5">
                                            @if($wh->item)
                                                <div>
                                                    <p class="text-[13px] text-gray-700 font-medium leading-tight">{{ $wh->item->name }}</p>
                                                    <p class="text-[11px] text-gray-400 mt-0.5">{{ $wh->item->customer->name ?? '-' }}</p>
                                                </div>
                                            @else
                                                <span class="text-[12px] text-gray-400 italic">—</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-2.5 text-center">
                                            @if($wh->isMatched())
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                    Matched
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200/60">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                    Unmatched
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-2.5">
                                            @if($wh->tanggal_setor)
                                                <span class="text-[12px] text-gray-600">{{ $wh->tanggal_setor->format('d M Y') }}</span>
                                            @else
                                                <span class="text-[12px] text-gray-300">—</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-2.5">
                                            <span class="text-[12px] text-gray-400">{{ $wh->created_at->format('d M Y') }}</span>
                                        </td>
                                        <td class="px-5 py-2.5 text-right">
                                            <div class="inline-flex items-center gap-0.5">
                                                <button wire:click="editWhChinaData({{ $wh->id }})" class="p-1.5 text-gray-400 hover:text-accent hover:bg-gray-100 rounded-[6px] transition-colors" title="Edit">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                </button>
                                                <button wire:click="deleteWhChinaData({{ $wh->id }})" wire:confirm="Yakin hapus data WH China ini?" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-[6px] transition-colors" title="Hapus">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($whChinaData->hasPages())
                        <div class="px-5 py-3 border-t border-gray-100">
                            {{ $whChinaData->links() }}
                        </div>
                    @endif
                </div>

                {{-- Mobile Cards --}}
                <div class="md:hidden space-y-2">
                    @foreach($whChinaData as $wh)
                        <div class="bg-white rounded-[10px] border border-gray-100 px-4 py-3">
                            <div class="flex items-start justify-between gap-2 mb-2">
                                <div class="min-w-0">
                                    <span class="text-[13px] font-mono font-semibold text-gray-900">{{ $wh->resi_number }}</span>
                                    <p class="text-[11px] text-gray-400">{{ $wh->admin->name ?? '-' }} · {{ $wh->created_at->format('d M Y') }}</p>
                                </div>
                                @if($wh->isMatched())
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 flex-shrink-0">
                                        <span class="w-1 h-1 rounded-full bg-emerald-500"></span>
                                        Matched
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 flex-shrink-0">
                                        <span class="w-1 h-1 rounded-full bg-amber-500"></span>
                                        Unmatched
                                    </span>
                                @endif
                            </div>

                            <div class="flex items-center gap-3 text-[12px] text-gray-500">
                                <span>{{ number_format($wh->berat, 2) }} kg</span>
                                <span class="text-gray-200">·</span>
                                <span>{{ $wh->ukuran_box }}</span>
                                <span class="text-gray-200">·</span>
                                <span>Jasa: ••••</span>
                                @if($wh->item)
                                    <span class="text-gray-200">·</span>
                                    <span class="text-gray-700 font-medium truncate">{{ $wh->item->name }}</span>
                                @endif
                            </div>

                            <div class="flex items-center gap-2 mt-2 pt-2 border-t border-gray-100">
                                <button wire:click="editWhChinaData({{ $wh->id }})" class="text-[12px] font-medium text-accent">Edit</button>
                                <span class="text-gray-200">·</span>
                                <button wire:click="deleteWhChinaData({{ $wh->id }})" wire:confirm="Yakin hapus?" class="text-[12px] font-medium text-red-500">Hapus</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif
    </div>

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- MODAL: Input/Edit Data WH China                               --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    @if($showWhModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" wire:click.self="closeWhModal">
            <div class="fixed inset-0 bg-black/30 transition-opacity"></div>
            <div class="flex min-h-full items-end sm:items-center justify-center p-4">
                <div class="relative bg-white rounded-[16px] shadow-lg w-full max-w-lg transform transition-all" @click.stop>
                    {{-- Modal Header --}}
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-[10px] bg-violet-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <div>
                            <h3 class="text-[15px] font-semibold text-gray-900">{{ $editingWhId ? 'Edit WH Data' : 'Input WH China Data' }}</h3>
                            <p class="text-[12px] text-gray-400 mt-0.5">Data from China warehouse</p>
                        </div>
                    </div>
                        <button wire:click="closeWhModal" class="p-2 min-w-[40px] min-h-[40px] flex items-center justify-center rounded-[8px] text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    {{-- Modal Body --}}
                    <form wire:submit.prevent="submitWhChinaData" class="p-6 space-y-4">
                        {{-- Row 1: Resi + Service Fee --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[12px] font-medium text-gray-600 mb-1">Resi Number <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="resiNumber" maxlength="100" placeholder="Resi number"
                                    class="w-full px-3 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors">
                                @error('resiNumber') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-[12px] font-medium text-gray-600 mb-1">Service Fee (Rp) <span class="text-red-500">*</span></label>
                                <input type="number" wire:model="biayaJasa" step="0.01" min="0" placeholder="0"
                                    class="w-full px-3 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors tabular-nums">
                                @error('biayaJasa') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Row 2: Weight + Dimensions (optional) --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[12px] font-medium text-gray-600 mb-1">Weight (gram) <span class="text-gray-400 font-normal">(optional)</span></label>
                                <input type="number" wire:model="berat" step="0.01" min="0.01" placeholder="0.00"
                                    class="w-full px-3 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors tabular-nums">
                                @error('berat') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-[12px] font-medium text-gray-600 mb-1">Dimensions <span class="text-gray-400 font-normal">(optional)</span></label>
                                <input type="text" wire:model="ukuranBox" maxlength="100" placeholder="60x40x50"
                                    class="w-full px-3 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors">
                                @error('ukuranBox') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <p class="text-[11px] text-gray-400">Weight & dimensions can be filled later when goods arrive in Indonesia.</p>

                        {{-- Indonesia Measurements (when goods arrive) --}}
                        <div class="border-t border-gray-100 pt-4">
                            <p class="text-[12px] font-semibold text-gray-700 mb-3">📦 Indonesia Measurements (saat barang tiba)</p>

                            <div class="grid grid-cols-2 gap-3 mb-3">
                                <div>
                                    <label class="block text-[12px] font-medium text-gray-600 mb-1">Berat INA (gram) <span class="text-gray-400 font-normal">(optional)</span></label>
                                    <input type="number" wire:model="beratIna" step="0.01" min="0.01" placeholder="0.00"
                                        class="w-full px-3 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors tabular-nums">
                                    @error('beratIna') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-[12px] font-medium text-gray-600 mb-1">Volume (m³) <span class="text-gray-400 font-normal">(auto)</span></label>
                                    <input type="text" wire:model="volume" readonly
                                        class="w-full px-3 py-2 text-[13px] bg-gray-50 border border-gray-200 rounded-[8px] text-gray-600 tabular-nums">
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-[12px] font-medium text-gray-600 mb-1">Panjang (cm)</label>
                                    <input type="number" wire:model="panjang" step="0.01" min="0.01" placeholder="0"
                                        class="w-full px-3 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors tabular-nums">
                                    @error('panjang') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-[12px] font-medium text-gray-600 mb-1">Lebar (cm)</label>
                                    <input type="number" wire:model="lebar" step="0.01" min="0.01" placeholder="0"
                                        class="w-full px-3 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors tabular-nums">
                                    @error('lebar') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-[12px] font-medium text-gray-600 mb-1">Tinggi (cm)</label>
                                    <input type="number" wire:model="tinggi" step="0.01" min="0.01" placeholder="0"
                                        class="w-full px-3 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors tabular-nums">
                                    @error('tinggi') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            @if($volume)
                                <p class="text-[11px] text-emerald-600 mt-2 font-medium">
                                    Volume: {{ $volume }} — Formula: ({{ $panjang }}×{{ $lebar }}×{{ $tinggi }}) / 6000
                                </p>
                            @endif
                        </div>

                        {{-- Biaya Tax (auto-calculated or manual) --}}
                        <div>
                            <label class="block text-[12px] font-medium text-gray-600 mb-1">Biaya Tax <span class="text-gray-400 font-normal">(Rp)</span></label>
                            <input type="number" wire:model="biayaTax" step="1" min="0" placeholder="Auto atau manual"
                                class="w-full px-3 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors tabular-nums">
                            <p class="text-[11px] text-gray-400 mt-1">Diisi otomatis saat generate invoice, atau input manual.</p>
                        </div>

                        {{-- Huruf Box (optional) --}}
                        <div>
                            <label class="block text-[12px] font-medium text-gray-600 mb-1">Huruf Box <span class="text-gray-400 font-normal">(optional)</span></label>
                            <input type="text" wire:model="hurufBox" maxlength="10" placeholder="e.g. H, A, B"
                                class="w-full px-3 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors">
                            <p class="text-[11px] text-gray-400 mt-1">Kode huruf box dari China</p>
                            @error('hurufBox') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Sprint 5B: China Batch Name (untuk Matched Data) --}}
                        <div>
                            <label class="block text-[12px] font-medium text-gray-600 mb-1">China Batch Name <span class="text-gray-400 font-normal">(optional)</span></label>
                            <input type="text" wire:model="chinaBatchName" maxlength="50" placeholder="e.g. 20072607-26"
                                class="w-full px-3 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors">
                            <p class="text-[11px] text-gray-400 mt-1">Batch Admin China untuk Matched Data di Manage Box</p>
                        </div>

                        {{-- Photo (required) --}}
                        <div>
                            <label class="block text-[12px] font-medium text-gray-600 mb-1">Photo <span class="text-red-500">*</span></label>
                            <input type="file" wire:model="fotoBarang" accept="image/*"
                                class="w-full text-[13px] text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-[6px] file:border-0 file:text-[12px] file:font-medium file:bg-gray-100 file:text-gray-600 hover:file:bg-gray-200 transition-colors file:cursor-pointer">
                            @error('fotoBarang') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                            @if($editingWhId && $fotoBarang === null)
                                <p class="text-[11px] text-gray-400 mt-1">Leave empty to keep current photo.</p>
                            @endif
                        </div>

                        {{-- Foto Arrived China (optional) --}}
                        <div>
                            <label class="block text-[12px] font-medium text-gray-600 mb-1">Foto Arrived China <span class="text-gray-400 font-normal">(optional)</span></label>
                            <input type="file" wire:model="fotoArrivedChina" accept="image/*"
                                class="w-full text-[13px] text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-[6px] file:border-0 file:text-[12px] file:font-medium file:bg-blue-50 file:text-blue-600 hover:file:bg-blue-100 transition-colors file:cursor-pointer">
                            @error('fotoArrivedChina') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                            @if($editingWhId && $fotoArrivedChina === null)
                                <p class="text-[11px] text-gray-400 mt-1">Leave empty to keep current photo.</p>
                            @endif
                        </div>

                        {{-- Foto Arrived INA (optional) --}}
                        <div>
                            <label class="block text-[12px] font-medium text-gray-600 mb-1">Foto Arrived INA <span class="text-gray-400 font-normal">(optional)</span></label>
                            <input type="file" wire:model="fotoArrivedIna" accept="image/*"
                                class="w-full text-[13px] text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-[6px] file:border-0 file:text-[12px] file:font-medium file:bg-green-50 file:text-green-600 hover:file:bg-green-100 transition-colors file:cursor-pointer">
                            @error('fotoArrivedIna') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                            @if($editingWhId && $fotoArrivedIna === null)
                                <p class="text-[11px] text-gray-400 mt-1">Leave empty to keep current photo.</p>
                            @endif
                        </div>

                        {{-- Tanggal Setor (optional) --}}
                        <div>
                            <label class="block text-[12px] font-medium text-gray-600 mb-1">Tanggal Customer Setor <span class="text-gray-400 font-normal">(optional)</span></label>
                            <input type="date" wire:model="tanggalSetor"
                                class="w-full px-3 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors">
                            @error('tanggalSetor') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center justify-end gap-3 pt-2">
                            <button type="button" wire:click="closeWhModal"
                                class="px-4 py-2 text-[13px] font-medium text-gray-600 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 rounded-[8px] transition-colors">
                                Cancel
                            </button>
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2 text-[13px] font-medium text-white bg-primary rounded-[8px] hover:bg-primary-light transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                {{ $editingWhId ? 'Update' : 'Save' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
