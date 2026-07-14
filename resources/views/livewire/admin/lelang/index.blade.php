<div class="min-h-screen bg-[#f8fafc]">
    {{-- Page Header --}}
    <div class="bg-white border-b border-gray-100">
        <div class="mx-auto w-full max-w-[1500px] px-4 md:px-6 lg:px-8 xl:px-10 2xl:px-12 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-[22px] font-bold text-gray-900 tracking-tight">Barang Lelang</h1>
                    <p class="text-body text-gray-500 mt-0.5">Kelola barang klaim WH dan barang hold untuk dijual/dilelang</p>
                </div>
                <button
                    wire:click="exportExcel"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-white text-gray-700 text-body font-medium rounded-[8px] border border-gray-200 hover:bg-gray-50 transition-colors shadow-sm"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span class="hidden sm:inline">Export Excel</span>
                </button>
            </div>
        </div>
    </div>

    <div class="mx-auto w-full max-w-[1500px] px-4 md:px-6 lg:px-8 xl:px-10 2xl:px-12 py-6 space-y-4">

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-[12px] border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-caption font-medium text-gray-500 uppercase tracking-wide">Total Barang</p>
                        <p class="text-[28px] font-bold text-gray-900 mt-1">{{ $summary['total_barang'] }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-[12px] bg-blue-50 flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-[12px] border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-caption font-medium text-gray-500 uppercase tracking-wide">Total Nilai</p>
                        <p class="text-[28px] font-bold text-gray-900 mt-1">¥ {{ number_format($summary['total_nilai'], 2) }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-[12px] bg-green-50 flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-[12px] border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-caption font-medium text-gray-500 uppercase tracking-wide">Belum Terjual</p>
                        <p class="text-[28px] font-bold text-gray-900 mt-1">{{ $summary['belum_terjual'] }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-[12px] bg-orange-50 flex items-center justify-center">
                        <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white rounded-[12px] border border-gray-100 p-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                {{-- Search --}}
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari nama barang, resi, atau customer..."
                        class="w-full pl-10 pr-4 py-2.5 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors placeholder:text-gray-400"
                    >
                </div>

                {{-- Status Filter --}}
                <select wire:model.live="filterStatus" class="py-2.5 px-3 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors text-gray-600">
                    <option value="">Semua Status</option>
                    <option value="klaim_wh">Klaim WH</option>
                    <option value="hold">Hold</option>
                    <option value="dijual">Dijual</option>
                    <option value="lelang">Lelang</option>
                </select>

                {{-- Customer Filter --}}
                <select wire:model.live="filterCustomer" class="py-2.5 px-3 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors text-gray-600">
                    <option value="">Semua Customer</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                    @endforeach
                </select>

                {{-- Date Filter --}}
                <input
                    type="date"
                    wire:model.live="filterDate"
                    class="py-2.5 px-3 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors text-gray-600"
                >
            </div>
        </div>

        {{-- Content --}}
        <div class="flex gap-6" x-data="{ detailOpen: @js($showDetail) }" x-effect="detailOpen = $wire.showDetail">
            {{-- Table --}}
            <div class="flex-1 min-w-0" :class="detailOpen && 'hidden lg:block'">
                @if($items->isEmpty())
                    <div class="bg-white rounded-[12px] border border-gray-100">
                        <x-empty-state
                            icon="box"
                            title="Belum ada barang lelang"
                            text="Belum ada barang yang ditandai untuk dijual atau dilelang."
                        />
                    </div>
                @else
                    {{-- Desktop Table --}}
                    <div class="hidden md:block bg-white rounded-[12px] border border-gray-100 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-gray-100">
                                        <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Barang</th>
                                        <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">No Resi</th>
                                        <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Customer</th>
                                        <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Berat</th>
                                        <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Harga</th>
                                        <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                                        <th class="text-right px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($items as $item)
                                        <tr class="hover:bg-gray-50/50 transition-colors cursor-pointer" wire:click="selectItem({{ $item->id }})">
                                            <td class="px-5 py-3.5">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-9 h-9 rounded-[8px] bg-gray-100 flex items-center justify-center flex-shrink-0">
                                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                                    </div>
                                                    <div>
                                                        <p class="text-body font-semibold text-gray-900">{{ $item->name }}</p>
                                                        <p class="text-caption text-gray-400">{{ $item->created_at->format('d M Y') }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-5 py-3.5">
                                                <span class="text-body text-gray-700">{{ $item->resi_number ?? '-' }}</span>
                                            </td>
                                            <td class="px-5 py-3.5">
                                                <span class="text-body text-gray-700">{{ $item->customer->name ?? '-' }}</span>
                                            </td>
                                            <td class="px-5 py-3.5">
                                                <span class="text-body text-gray-600">{{ $item->whChinaData->berat ?? '-' }} kg</span>
                                            </td>
                                            <td class="px-5 py-3.5">
                                                <span class="text-body text-gray-600">¥ {{ number_format($item->price_yuan ?? 0, 2) }}</span>
                                            </td>
                                            <td class="px-5 py-3.5">
                                                @if($item->status === 'klaim_wh')
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-caption font-medium bg-purple-100 text-purple-700">Klaim WH</span>
                                                @elseif($item->status === 'hold')
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-caption font-medium bg-red-100 text-red-700">Hold</span>
                                                @elseif($item->status === 'dijual')
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-caption font-medium bg-green-100 text-green-700">Dijual</span>
                                                @elseif($item->status === 'lelang')
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-caption font-medium bg-blue-100 text-blue-700">Lelang</span>
                                                @endif
                                            </td>
                                            <td class="px-5 py-3.5 text-right">
                                                <button wire:click.stop="selectItem({{ $item->id }})" class="text-caption font-medium text-accent hover:text-primary transition-colors">
                                                    Detail
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($items->hasPages())
                            <div class="px-5 py-3 border-t border-gray-100">
                                {{ $items->links() }}
                            </div>
                        @endif
                    </div>

                    {{-- Mobile Cards --}}
                    <div class="md:hidden space-y-3">
                        @foreach($items as $item)
                            <div wire:click="selectItem({{ $item->id }})" class="bg-white rounded-[12px] border border-gray-100 p-4 hover:shadow-card-hover transition-shadow cursor-pointer">
                                <div class="flex items-start justify-between mb-3">
                                    <div>
                                        <p class="text-body font-semibold text-gray-900">{{ $item->name }}</p>
                                        <p class="text-caption text-gray-400">{{ $item->resi_number ?? '-' }}</p>
                                    </div>
                                    @if($item->status === 'klaim_wh')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-caption font-medium bg-purple-100 text-purple-700">Klaim WH</span>
                                    @elseif($item->status === 'hold')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-caption font-medium bg-red-100 text-red-700">Hold</span>
                                    @elseif($item->status === 'dijual')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-caption font-medium bg-green-100 text-green-700">Dijual</span>
                                    @elseif($item->status === 'lelang')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-caption font-medium bg-blue-100 text-blue-700">Lelang</span>
                                    @endif
                                </div>
                                <div class="grid grid-cols-2 gap-2 text-caption">
                                    <div>
                                        <span class="text-gray-400">Customer:</span>
                                        <span class="text-gray-700 ml-1">{{ $item->customer->name ?? '-' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-400">Berat:</span>
                                        <span class="text-gray-700 ml-1">{{ $item->whChinaData->berat ?? '-' }} kg</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-400">Harga:</span>
                                        <span class="text-gray-700 ml-1">¥ {{ number_format($item->price_yuan ?? 0, 2) }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-400">Tanggal:</span>
                                        <span class="text-gray-700 ml-1">{{ $item->created_at->format('d M Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Detail Panel --}}
            @if($showDetail && $selectedItem)
                <div class="w-full lg:w-[380px] flex-shrink-0" x-show="detailOpen" x-transition>
                    <div class="bg-white rounded-[12px] border border-gray-100 overflow-hidden sticky top-24">
                        {{-- Detail Header --}}
                        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="text-body font-semibold text-gray-900">Detail Barang</h3>
                            <button wire:click="closeDetail" class="p-1.5 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        {{-- Detail Content --}}
                        <div class="px-5 py-4 space-y-4">
                            <div>
                                <p class="text-caption text-gray-400 uppercase tracking-wide mb-1">Nama Barang</p>
                                <p class="text-body font-medium text-gray-900">{{ $selectedItem->name }}</p>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-caption text-gray-400 uppercase tracking-wide mb-1">No Resi</p>
                                    <p class="text-body text-gray-700">{{ $selectedItem->resi_number ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-caption text-gray-400 uppercase tracking-wide mb-1">Quantity</p>
                                    <p class="text-body text-gray-700">{{ $selectedItem->quantity ?? 1 }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-caption text-gray-400 uppercase tracking-wide mb-1">Berat</p>
                                    <p class="text-body text-gray-700">{{ $selectedItem->whChinaData->berat ?? '-' }} kg</p>
                                </div>
                                <div>
                                    <p class="text-caption text-gray-400 uppercase tracking-wide mb-1">Harga Asli</p>
                                    <p class="text-body text-gray-700">¥ {{ number_format($selectedItem->price_yuan ?? 0, 2) }}</p>
                                </div>
                            </div>

                            <div>
                                <p class="text-caption text-gray-400 uppercase tracking-wide mb-1">Customer</p>
                                <p class="text-body text-gray-700">{{ $selectedItem->customer->name ?? '-' }}</p>
                            </div>

                            <div>
                                <p class="text-caption text-gray-400 uppercase tracking-wide mb-1">Box</p>
                                <p class="text-body text-gray-700">{{ $selectedItem->box->display_name ?? '-' }}</p>
                            </div>

                            <div>
                                <p class="text-caption text-gray-400 uppercase tracking-wide mb-1">Status</p>
                                @if($selectedItem->status === 'klaim_wh')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-caption font-medium bg-purple-100 text-purple-700">Klaim WH</span>
                                @elseif($selectedItem->status === 'hold')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-caption font-medium bg-red-100 text-red-700">Hold</span>
                                @elseif($selectedItem->status === 'dijual')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-caption font-medium bg-green-100 text-green-700">Dijual</span>
                                @elseif($selectedItem->status === 'lelang')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-caption font-medium bg-blue-100 text-blue-700">Lelang</span>
                                @endif
                            </div>

                            <div>
                                <p class="text-caption text-gray-400 uppercase tracking-wide mb-1">Tanggal Input</p>
                                <p class="text-body text-gray-700">{{ $selectedItem->created_at->format('d M Y H:i') }}</p>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        @if(in_array($selectedItem->status, ['klaim_wh', 'hold', 'dijual', 'lelang']))
                            <div class="px-5 py-4 border-t border-gray-100 space-y-2">
                                @if($selectedItem->status !== 'dijual')
                                    <button
                                        wire:click="confirmMark('dijual')"
                                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-green-500 text-white text-body font-medium rounded-[8px] hover:bg-green-600 transition-colors"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Tandai Dijual
                                    </button>
                                @endif
                                @if($selectedItem->status !== 'lelang')
                                    <button
                                        wire:click="confirmMark('lelang')"
                                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-500 text-white text-body font-medium rounded-[8px] hover:bg-blue-600 transition-colors"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                                        Tandai Lelang
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Mark Confirmation Modal --}}
    @if($showMarkConfirm)
        <div class="fixed inset-0 z-50 overflow-y-auto" wire:click.self="cancelMark">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black/30" wire:click="cancelMark"></div>
                <div class="relative bg-white rounded-[12px] shadow-xl max-w-md w-full p-6" wire:click.stop>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Konfirmasi</h3>
                    <p class="text-body text-gray-600 mb-6">
                        Apakah Anda yakin ingin menandai barang <strong>{{ $selectedItem->name }}</strong> sebagai
                        <strong>{{ $pendingMarkStatus === 'dijual' ? 'Dijual' : 'Lelang' }}</strong>?
                    </p>
                    <div class="flex items-center justify-end gap-3">
                        <button
                            wire:click="cancelMark"
                            class="px-4 py-2.5 text-body font-medium text-gray-700 bg-white border border-gray-200 rounded-[8px] hover:bg-gray-50 transition-colors"
                        >
                            Batal
                        </button>
                        <button
                            wire:click="markItem"
                            class="px-4 py-2.5 text-body font-medium text-white {{ $pendingMarkStatus === 'dijual' ? 'bg-green-500 hover:bg-green-600' : 'bg-blue-500 hover:bg-blue-600' }} rounded-[8px] transition-colors"
                        >
                            Ya, Tandai
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
