<div class="min-h-screen bg-[#f8fafc]">
    {{-- Page Header --}}
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-display text-primary">My Box Sharing</h1>
                    <p class="text-body text-gray-500 mt-1">Kelola box sharing Anda</p>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-4">
        {{-- Filter Bar --}}
        <div class="bg-white rounded-[12px] border border-gray-100 p-4">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari tracking number..."
                        aria-label="Cari box berdasarkan tracking number"
                        class="w-full pl-10 pr-4 py-2.5 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors placeholder:text-gray-400"
                        wire:loading.class="opacity-50"
                    />
                </div>
                <div class="relative sm:w-48">
                    <select wire:model.live="filterStatus" class="w-full py-2.5 px-3 pr-8 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors text-gray-600 appearance-none" wire:loading.class="opacity-50">
                        <option value="">Semua Status</option>
                        @foreach(\App\Models\Box::getValidStatuses() as $status)
                            <option value="{{ $status }}">{{ str_replace('_', ' ', Str::title(Str::lower($status))) }}</option>
                        @endforeach
                    </select>
                    <svg wire:loading class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                </div>
            </div>
        </div>

        {{-- Box Table --}}
        <div class="bg-white rounded-[12px] border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <h3 class="text-[15px] font-semibold text-gray-900">Daftar Box Sharing</h3>
                <p class="text-body text-gray-500 mt-0.5">Klik pada nomor box untuk melihat detail barang</p>
            </div>

            @if($boxes->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                        <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <h3 class="text-body font-semibold text-gray-700 mb-1">Belum ada barang di box sharing</h3>
                    <p class="text-body text-gray-500 mb-4">Anda belum memiliki box sharing. Mulai dengan menyetor resi pertama Anda.</p>
                    <a href="{{ route('customer.setor-resi') }}" wire:navigate class="inline-flex items-center gap-2 px-5 py-2.5 text-body font-medium text-white bg-primary rounded-[8px] hover:bg-primary-light transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Setor Resi
                    </a>
                </div>
            @else
                {{-- Mobile Cards --}}
                <div class="space-y-3 p-4 lg:hidden" wire:loading.class="opacity-50">
                    @foreach($boxes as $box)
                        <div wire:click="openBoxDetail({{ $box->id }})" class="bg-gray-50 rounded-[10px] p-4 space-y-2 cursor-pointer hover:bg-gray-100/70 transition-colors active:scale-[0.99]">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <p class="text-body font-semibold text-primary truncate">{{ $box->display_name }}</p>
                                    <p class="text-caption text-gray-500 mt-0.5">{{ $box->batch_name ?? '-' }} · {{ $box->items_count }} item</p>
                                </div>
                                <span class="shrink-0 text-caption font-medium px-2 py-0.5 rounded-full {{ $box->method === 'air' ? 'bg-blue-50 text-blue-700' : 'bg-cyan-50 text-cyan-700' }}">{{ strtoupper($box->method) }}</span>
                            </div>

                            @if($box->etd || $box->eta)
                                <div class="flex flex-wrap gap-x-4 gap-y-1 text-caption">
                                    @if($box->etd)
                                        <span><span class="text-gray-400">ETD:</span> <span class="text-gray-700">{{ $box->etd->format('d M Y') }}</span></span>
                                    @endif
                                    @if($box->eta)
                                        <span><span class="text-gray-400">ETA:</span> <span class="text-gray-700">{{ $box->eta->format('d M Y') }}</span></span>
                                    @endif
                                </div>
                            @endif

                            <div class="flex items-center justify-between pt-1">
                                <x-status-badge :status="$box->status" />
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Desktop Table --}}
                <div class="hidden lg:block overflow-x-auto" wire:loading.class="opacity-50">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-gray-100 bg-gray-50/50">
                                <th class="px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wider">Nomor Box</th>
                                <th class="px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wider">Kode Box</th>
                                <th class="px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wider">Jenis Kirim</th>
                                <th class="px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wider">Open Date</th>
                                <th class="px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wider">Close Date</th>
                                <th class="px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wider">ETD</th>
                                <th class="px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wider">ETA</th>
                                <th class="px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wider">Barang</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($boxes as $i => $box)
                                <tr class="hover:bg-gray-50/50 transition-colors cursor-pointer" wire:click="openBoxDetail({{ $box->id }})">
                                    <td class="px-5 py-3.5 text-body text-gray-500">{{ $boxes->firstItem() + $i }}</td>
                                    <td class="px-5 py-3.5">
                                        <span class="text-body font-semibold text-primary hover:text-primary-light hover:underline">
                                            {{ $box->display_name }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 text-body text-gray-700">{{ $box->batch_name ?? '-' }}</td>
                                    <td class="px-5 py-3.5">
                                        <span class="text-caption font-medium px-2 py-1 rounded-full {{ $box->method === 'air' ? 'bg-blue-50 text-blue-700' : 'bg-cyan-50 text-cyan-700' }}">
                                            {{ strtoupper($box->method) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 text-body text-gray-700">
                                        {{ $box->open_date ? $box->open_date->format('d M Y') : '-' }}
                                    </td>
                                    <td class="px-5 py-3.5 text-body text-gray-700">
                                        {{ $box->close_date ? $box->close_date->format('d M Y') : '-' }}
                                    </td>
                                    <td class="px-5 py-3.5 text-body text-gray-700">
                                        {{ $box->etd ? $box->etd->format('d M Y') : '-' }}
                                    </td>
                                    <td class="px-5 py-3.5 text-body text-gray-700">
                                        {{ $box->eta ? $box->eta->format('d M Y') : '-' }}
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <x-status-badge :status="$box->status" />
                                    </td>
                                    <td class="px-5 py-3.5 text-body text-gray-500">
                                        {{ $box->items_count }} item
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
    </div>

    {{-- Box Detail Modal --}}
    @if($showDetail && $detailBox)
        <div class="fixed inset-0 z-50 overflow-y-auto" wire:click.self="closeBoxDetail">
            <div class="fixed inset-0 bg-black/30 transition-opacity"></div>
            <div class="flex min-h-full items-end sm:items-center justify-center p-4">
                <div class="relative bg-white rounded-[16px] shadow-modal w-full max-w-4xl max-h-[90vh] overflow-hidden transform transition-all" @click.stop>
                    {{-- Modal Header --}}
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-[10px] bg-blue-50 flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            </div>
                            <div>
                                <h3 class="text-[16px] font-semibold text-gray-900">Detail Box</h3>
                                <p class="text-body text-gray-500 mt-0.5">{{ $detailBox->display_name }}</p>
                            </div>
                        </div>
                        <button wire:click="closeBoxDetail" class="p-2 min-w-[44px] min-h-[44px] flex items-center justify-center rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="overflow-y-auto max-h-[calc(90vh-140px)] p-6 space-y-6">
                        {{-- Box Info Grid --}}
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                            <div class="p-3 bg-gray-50 rounded-[8px]">
                                <p class="text-caption text-gray-500 mb-1">Status</p>
                                <x-status-badge :status="$detailBox->status" />
                            </div>
                            <div class="p-3 bg-gray-50 rounded-[8px]">
                                <p class="text-caption text-gray-500 mb-1">Tipe</p>
                                <p class="text-body font-medium text-gray-900 capitalize">{{ $detailBox->type }}</p>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-[8px]">
                                <p class="text-caption text-gray-500 mb-1">Jenis Kirim</p>
                                <p class="text-body font-medium text-gray-900 uppercase">{{ $detailBox->method }}</p>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-[8px]">
                                <p class="text-caption text-gray-500 mb-1">Open Date</p>
                                <p class="text-body font-medium text-gray-900">{{ $detailBox->open_date ? $detailBox->open_date->format('d M Y') : '-' }}</p>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-[8px]">
                                <p class="text-caption text-gray-500 mb-1">Close Date</p>
                                <p class="text-body font-medium text-gray-900">{{ $detailBox->close_date ? $detailBox->close_date->format('d M Y') : '-' }}</p>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-[8px]">
                                <p class="text-caption text-gray-500 mb-1">Kode Box</p>
                                <p class="text-body font-medium text-gray-900">{{ $detailBox->batch_name ?? '-' }}</p>
                            </div>
                        </div>

                        {{-- ETD/ETA --}}
                        @if($detailBox->etd || $detailBox->eta)
                            <div class="grid grid-cols-2 gap-3">
                                @if($detailBox->etd)
                                    <div class="p-3 bg-blue-50 rounded-[8px]">
                                        <p class="text-caption text-blue-600 mb-1">ETD (Estimasi Berangkat)</p>
                                        <p class="text-body font-semibold text-blue-900">{{ $detailBox->etd->translatedFormat('d F Y') }}</p>
                                    </div>
                                @endif
                                @if($detailBox->eta)
                                    <div class="p-3 bg-emerald-50 rounded-[8px]">
                                        <p class="text-caption text-emerald-600 mb-1">ETA (Estimasi Tiba)</p>
                                        <p class="text-body font-semibold text-emerald-900">{{ $detailBox->eta->translatedFormat('d F Y') }}</p>
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- Notes --}}
                        @if($detailBox->notes)
                            <div class="p-3 bg-amber-50 border border-amber-200 rounded-[8px]">
                                <p class="text-caption text-amber-600 mb-1">Catatan</p>
                                <p class="text-body text-amber-900">{{ $detailBox->notes }}</p>
                            </div>
                        @endif

                        {{-- Items --}}
                        <div>
                            <h4 class="text-body font-semibold text-gray-900 mb-3">Daftar Barang ({{ $detailBox->items->count() }})</h4>
                            @if($detailBox->items->isEmpty())
                                <div class="p-8 text-center bg-gray-50 rounded-[8px]">
                                    <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    <p class="text-body text-gray-500">Belum ada barang di box ini</p>
                                </div>
                            @else
                                {{-- Mobile Item Cards --}}
                                <div class="space-y-3 md:hidden">
                                    @foreach($detailBox->items as $item)
                                        <div class="bg-gray-50 rounded-[8px] p-3 space-y-2">
                                            <div class="flex items-start gap-3">
                                                @if($item->proof_co)
                                                    <div class="w-14 h-14 shrink-0 rounded-[8px] overflow-hidden bg-gray-100 border border-gray-200">
                                                        <img src="{{ Storage::url($item->proof_co) }}" alt="{{ $item->name }}" class="w-full h-full object-cover" loading="lazy">
                                                    </div>
                                                @else
                                                    <div class="w-14 h-14 shrink-0 rounded-[8px] bg-gray-100 flex items-center justify-center">
                                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                    </div>
                                                @endif
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-start justify-between gap-2">
                                                        <p class="text-body font-medium text-gray-900 truncate">{{ $item->name }}</p>
                                                        @if($item->is_sensitive)
                                                            <span class="shrink-0 text-caption font-medium text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded-full">Sensitive</span>
                                                        @endif
                                                    </div>
                                                    <p class="text-caption text-gray-600 mt-0.5">¥{{ number_format($item->price_yuan, 2) }} × {{ $item->quantity }}</p>
                                                </div>
                                            </div>
                                            <div class="flex flex-wrap gap-x-4 gap-y-1 text-caption">
                                                @if($item->whChinaData)
                                                    <span><span class="text-gray-400">Berat:</span> <span class="text-gray-700">{{ number_format($item->whChinaData->berat, 1) }} kg</span></span>
                                                    @if($item->whChinaData->ukuran_box)
                                                        <span><span class="text-gray-400">Ukuran:</span> <span class="text-gray-700">{{ $item->whChinaData->ukuran_box }}</span></span>
                                                    @endif
                                                @endif
                                                @if($item->resi_number)
                                                    <span><span class="text-gray-400">Resi:</span> <span class="text-gray-700 font-mono">{{ $item->resi_number }}</span></span>
                                                @endif
                                            </div>
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
                                                <span class="text-caption font-medium {{ $statusColors[$item->status] ?? 'bg-gray-100 text-gray-700' }} px-1.5 py-0.5 rounded-full">
                                                    {{ $statusLabels[$item->status] ?? $item->status }}
                                                </span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Desktop Item Table --}}
                                <div class="hidden md:block overflow-x-auto border border-gray-200 rounded-[8px]">
                                    <table class="w-full text-left">
                                        <thead>
                                            <tr class="bg-gray-50">
                                                <th class="px-4 py-2.5 text-caption font-semibold text-gray-500 uppercase">No</th>
                                                <th class="px-4 py-2.5 text-caption font-semibold text-gray-500 uppercase">Foto</th>
                                                <th class="px-4 py-2.5 text-caption font-semibold text-gray-500 uppercase">Nama Barang</th>
                                                <th class="px-4 py-2.5 text-caption font-semibold text-gray-500 uppercase">Qty</th>
                                                <th class="px-4 py-2.5 text-caption font-semibold text-gray-500 uppercase">Harga (¥)</th>
                                                <th class="px-4 py-2.5 text-caption font-semibold text-gray-500 uppercase">Berat</th>
                                                <th class="px-4 py-2.5 text-caption font-semibold text-gray-500 uppercase">Ukuran</th>
                                                <th class="px-4 py-2.5 text-caption font-semibold text-gray-500 uppercase">Resi</th>
                                                <th class="px-4 py-2.5 text-caption font-semibold text-gray-500 uppercase">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            @foreach($detailBox->items as $j => $item)
                                                <tr class="hover:bg-gray-50/50">
                                                    <td class="px-4 py-3 text-body text-gray-500">{{ $j + 1 }}</td>
                                                    <td class="px-4 py-3">
                                                        @if($item->proof_co)
                                                            <div class="w-12 h-12 rounded-[8px] overflow-hidden bg-gray-100 border border-gray-200">
                                                                <img src="{{ Storage::url($item->proof_co) }}" alt="{{ $item->name }}" class="w-full h-full object-cover" loading="lazy">
                                                            </div>
                                                        @else
                                                            <div class="w-12 h-12 rounded-[8px] bg-gray-100 flex items-center justify-center">
                                                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        <div class="flex items-center gap-2">
                                                            <span class="text-body font-medium text-gray-900">{{ $item->name }}</span>
                                                            @if($item->is_sensitive)
                                                                <span class="text-caption font-medium text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded-full">Sensitive</span>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td class="px-4 py-3 text-body text-gray-700">{{ $item->quantity }}</td>
                                                    <td class="px-4 py-3 text-body text-gray-700">¥{{ number_format($item->price_yuan, 2) }}</td>
                                                    <td class="px-4 py-3 text-body text-gray-700">
                                                        {{ $item->whChinaData ? number_format($item->whChinaData->berat, 1) . ' kg' : '-' }}
                                                    </td>
                                                    <td class="px-4 py-3 text-body text-gray-700">
                                                        {{ $item->whChinaData->ukuran_box ?? '-' }}
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        <span class="text-caption text-gray-500 font-mono">{{ $item->resi_number ?? '-' }}</span>
                                                    </td>
                                                    <td class="px-4 py-3">
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
                                                            <span class="text-caption font-medium {{ $statusColors[$item->status] ?? 'bg-gray-100 text-gray-700' }} px-1.5 py-0.5 rounded-full">
                                                                {{ $statusLabels[$item->status] ?? $item->status }}
                                                            </span>
                                                        @else
                                                            <span class="text-caption text-gray-400">-</span>
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
                    <div class="px-6 py-4 border-t border-gray-100 flex justify-end bg-gray-50">
                        <button wire:click="closeBoxDetail" class="px-5 py-2.5 text-body font-medium text-gray-700 bg-white border border-gray-200 rounded-[8px] hover:bg-gray-50 transition-colors">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
