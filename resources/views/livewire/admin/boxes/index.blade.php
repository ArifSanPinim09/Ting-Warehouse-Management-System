<div class="min-h-screen bg-[#f8fafc]">
    {{-- Page Header --}}
    <div class="bg-white border-b border-gray-100">
        <div class="mx-auto w-full max-w-[1500px] px-4 md:px-6 lg:px-8 xl:px-10 2xl:px-12 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-[22px] font-bold text-gray-900 tracking-tight">Manage Box</h1>
                    <p class="text-body text-gray-500 mt-0.5">Kelola box dan status pengiriman</p>
                </div>
                <button
                    wire:click="openCreateModal"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary text-white text-body font-medium rounded-[8px] hover:bg-primary-light transition-colors shadow-sm"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span class="hidden sm:inline">Buat Box</span>
                </button>
            </div>
        </div>
    </div>

    <div class="mx-auto w-full max-w-[1500px] px-4 md:px-6 lg:px-8 xl:px-10 2xl:px-12 py-6 space-y-4">

        {{-- Filters --}}
        <div class="bg-white rounded-[12px] border border-gray-100 p-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                {{-- Search --}}
                <div class="lg:col-span-2 relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari tracking number, batch, atau customer..."
                        class="w-full pl-10 pr-4 py-2.5 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors placeholder:text-gray-400"
                    >
                </div>

                {{-- Type Filter --}}
                <select wire:model.live="filterType" class="py-2.5 px-3 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors text-gray-600">
                    <option value="">Semua Tipe</option>
                    <option value="sharing">Sharing</option>
                    <option value="direct">Direct</option>
                    <option value="handcarry">Handcarry</option>
                </select>

                {{-- Status Filter --}}
                <select wire:model.live="filterStatus" class="py-2.5 px-3 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors text-gray-600">
                    <option value="">Semua Status</option>
                    @foreach(\App\Models\Box::getValidStatuses() as $status)
                        <option value="{{ $status }}">{{ str_replace('_', ' ', Str::title(Str::lower($status))) }}</option>
                    @endforeach
                </select>

                {{-- Customer Filter --}}
                <select wire:model.live="filterCustomer" class="py-2.5 px-3 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors text-gray-600">
                    <option value="">Semua Customer</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Content --}}
        <div class="flex gap-6" x-data="{ detailOpen: @js($showDetail) }" x-effect="detailOpen = $wire.showDetail">
            {{-- Table / List --}}
            <div class="flex-1 min-w-0" :class="detailOpen && 'hidden lg:block'">
                @if($boxes->isEmpty())
                    <div class="bg-white rounded-[12px] border border-gray-100">
                        <x-empty-state
                            icon="box"
                            title="Belum ada box"
                            text="Belum ada box yang terdaftar. Klik tombol 'Buat Box' di pojok kanan atas untuk memulai."
                        />
                    </div>
                @else
                    {{-- Desktop Table --}}
                    <div class="hidden md:block bg-white rounded-[12px] border border-gray-100 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-gray-100">
                                        <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Box</th>
                                        <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Customer</th>
                                        <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Tipe</th>
                                        <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Metode</th>
                                        <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Barang</th>
                                        <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                                        <th class="text-right px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($boxes as $box)
                                        <tr class="hover:bg-gray-50/50 transition-colors cursor-pointer" wire:click="selectBox({{ $box->id }})">
                                            <td class="px-5 py-3.5">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-9 h-9 rounded-[8px] bg-gray-100 flex items-center justify-center flex-shrink-0">
                                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                                    </div>
                                                    <div>
                                                        <p class="text-body font-semibold text-gray-900">{{ $box->display_name }}</p>
                                                        <p class="text-caption text-gray-400">{{ $box->created_at->format('d M Y') }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-5 py-3.5">
                                                <span class="text-body text-gray-700">{{ $box->customer->name ?? '-' }}</span>
                                            </td>
                                            <td class="px-5 py-3.5">
                                                <span class="text-caption font-medium text-gray-600 capitalize">{{ $box->type }}</span>
                                            </td>
                                            <td class="px-5 py-3.5">
                                                <span class="text-caption font-medium text-gray-600 uppercase">{{ $box->method }}</span>
                                            </td>
                                            <td class="px-5 py-3.5">
                                                <span class="text-body text-gray-600">{{ $box->items_count }}</span>
                                            </td>
                                            <td class="px-5 py-3.5">
                                                <x-status-badge :status="$box->status" />
                                            </td>
                                            <td class="px-5 py-3.5 text-right">
                                                <button wire:click.stop="selectBox({{ $box->id }})" class="text-caption font-medium text-accent hover:text-primary transition-colors">
                                                    Detail
                                                </button>
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
                            <div wire:click="selectBox({{ $box->id }})" class="bg-white rounded-[12px] border border-gray-100 p-4 hover:shadow-card-hover transition-shadow cursor-pointer">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-[10px] bg-gray-100 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                        </div>
                                        <div>
                                            <p class="text-body font-semibold text-gray-900">{{ $box->display_name }}</p>
                                            <p class="text-caption text-gray-500">{{ $box->customer->name ?? '-' }}</p>
                                        </div>
                                    </div>
                                    <x-status-badge :status="$box->status" />
                                </div>
                                <div class="flex items-center gap-4 text-caption text-gray-500">
                                    <span class="capitalize">{{ $box->type }}</span>
                                    <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                                    <span class="uppercase">{{ $box->method }}</span>
                                    <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                                    <span>{{ $box->items_count }} barang</span>
                                </div>
                            </div>
                        @endforeach
                        @if($boxes->hasPages())
                            <div class="py-2">
                                {{ $boxes->links() }}
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Detail Panel --}}
            @if($showDetail && $selectedBox)
                <div class="w-full lg:w-[400px] flex-shrink-0" x-show="detailOpen" x-transition>
                    <div class="bg-white rounded-[12px] border border-gray-100 sticky top-[88px]">
                        {{-- Detail Header --}}
                        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="text-body font-semibold text-gray-900">Detail Box</h3>
                            <button wire:click="closeDetail" class="p-2 min-w-[44px] min-h-[44px] flex items-center justify-center rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <div class="p-5 space-y-5">
                            {{-- Box Info --}}
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-caption text-gray-500">Tracking Number</span>
                                    <span class="text-body font-semibold text-gray-900">{{ $selectedBox->tracking_number ?? '-' }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-caption text-gray-500">Batch</span>
                                    <span class="text-body text-gray-700">{{ $selectedBox->batch_name ?? '-' }}</span>
                                </div>
                                @if($selectedBox->huruf_box)
                                <div class="flex items-center justify-between">
                                    <span class="text-caption text-gray-500">Huruf Box</span>
                                    <span class="text-body text-gray-700 font-semibold">{{ $selectedBox->huruf_box }}</span>
                                </div>
                                @endif
                                <div class="flex items-center justify-between">
                                    <span class="text-caption text-gray-500">Customer</span>
                                    <span class="text-body text-gray-700">{{ $selectedBox->customer->name ?? '-' }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-caption text-gray-500">Tipe</span>
                                    <span class="text-body text-gray-700 capitalize">{{ $selectedBox->type }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-caption text-gray-500">Metode</span>
                                    <span class="text-body text-gray-700 uppercase">{{ $selectedBox->method }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-caption text-gray-500">Status</span>
                                    <x-status-badge :status="$selectedBox->status" size="lg" />
                                </div>
                                @if($selectedBox->etd)
                                    <div class="flex items-center justify-between">
                                        <span class="text-caption text-gray-500">ETD</span>
                                        <span class="text-body text-gray-700">{{ $selectedBox->etd->format('d M Y') }}</span>
                                    </div>
                                @endif
                                @if($selectedBox->eta)
                                    <div class="flex items-center justify-between">
                                        <span class="text-caption text-gray-500">ETA</span>
                                        <span class="text-body text-gray-700">{{ $selectedBox->eta->format('d M Y') }}</span>
                                    </div>
                                @endif
                                @if($selectedBox->stevedoring_date)
                                    <div class="flex items-center justify-between">
                                        <span class="text-caption text-gray-500">Stevedoring</span>
                                        <span class="text-body text-gray-700">{{ $selectedBox->stevedoring_date->format('d M Y') }}</span>
                                    </div>
                                @endif
                                @if($selectedBox->tagihan_update_date)
                                    <div class="flex items-center justify-between">
                                        <span class="text-caption text-gray-500">Tgl Update Tagihan</span>
                                        <span class="text-body text-gray-700">{{ $selectedBox->tagihan_update_date->format('d M Y') }}</span>
                                    </div>
                                @endif
                                @if($selectedBox->open_date)
                                    <div class="flex items-center justify-between">
                                        <span class="text-caption text-gray-500">Dibuka</span>
                                        <span class="text-body text-gray-700">{{ $selectedBox->open_date->format('d M Y H:i') }}</span>
                                    </div>
                                @endif
                                @if($selectedBox->close_date)
                                    <div class="flex items-center justify-between">
                                        <span class="text-caption text-gray-500">Ditutup</span>
                                        <span class="text-body text-gray-700">{{ $selectedBox->close_date->format('d M Y H:i') }}</span>
                                    </div>
                                @endif
                                @if($selectedBox->last_setor_date)
                                    <div class="flex items-center justify-between">
                                        <span class="text-caption text-gray-500">Last Setor</span>
                                        <span class="text-body text-gray-700">{{ $selectedBox->last_setor_date->format('d M Y H:i') }}</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Edit Tracking + ETA --}}
                            <button wire:click="openEditModal"
                                class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 text-[12px] font-medium text-gray-600 bg-gray-50 border border-gray-200 rounded-[8px] hover:bg-gray-100 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Edit Tracking, Huruf Box & ETA
                            </button>

                            {{-- Notes --}}
                            @if($selectedBox->notes)
                                <div class="p-3 bg-gray-50 rounded-[8px]">
                                    <p class="text-caption text-gray-500 mb-1">Catatan</p>
                                    <p class="text-body text-gray-700">{{ $selectedBox->notes }}</p>
                                </div>
                            @endif

                            {{-- Status Timeline --}}
                            <div>
                                <p class="text-caption font-semibold text-gray-700 mb-3 uppercase tracking-wide">Timeline Status</p>
                                @if($selectedBox->status === 'CLOSED')
                                    {{-- Special timeline for CLOSED box --}}
                                    <div class="p-3 bg-red-50 border border-red-200 rounded-[8px] mb-3">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                            <span class="text-body font-semibold text-red-700">Box Ditutup</span>
                                        </div>
                                        <p class="text-caption text-red-600 mt-1">Customer tidak bisa setor resi lagi</p>
                                    </div>
                                @endif
                                <div class="space-y-0">
                                    @php
                                        $steps = [
                                            ['status' => 'OPEN', 'label' => 'Open', 'desc' => 'Box dibuat'],
                                            ['status' => 'SENT_TO_CARGO', 'label' => 'Sent to Cargo', 'desc' => 'Dikirim ke cargo'],
                                            ['status' => 'OTW_INA', 'label' => 'OTW Indonesia', 'desc' => 'Dalam perjalanan'],
                                            ['status' => 'UP_INVOICE', 'label' => 'Invoice Dibuat', 'desc' => 'Invoice digenerate'],
                                            ['status' => 'DONE', 'label' => 'Selesai', 'desc' => 'Proses selesai'],
                                        ];
                                        // For CLOSED boxes, treat as OPEN for timeline purposes
                                        $timelineStatus = $selectedBox->status === 'CLOSED' ? 'OPEN' : $selectedBox->status;
                                        $currentIndex = collect($steps)->search(fn($s) => $s['status'] === $timelineStatus);
                                    @endphp
                                    @foreach($steps as $i => $step)
                                        @php
                                            $isCompleted = $i < $currentIndex;
                                            $isCurrent = $i === $currentIndex;
                                            $isPending = $i > $currentIndex;
                                        @endphp
                                        <div class="flex gap-3">
                                            <div class="flex flex-col items-center">
                                                <div class="w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0 {{ $isCompleted ? 'bg-emerald-500' : ($isCurrent ? 'bg-blue-500 ring-4 ring-blue-100' : 'bg-gray-200') }}">
                                                    @if($isCompleted)
                                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                    @elseif($isCurrent)
                                                        <div class="w-2 h-2 rounded-full bg-white"></div>
                                                    @endif
                                                </div>
                                                @if($i < count($steps) - 1)
                                                    <div class="w-0.5 h-8 {{ $isCompleted ? 'bg-emerald-300' : 'bg-gray-200' }}"></div>
                                                @endif
                                            </div>
                                            <div class="pb-4">
                                                <p class="text-body font-medium {{ $isCurrent ? 'text-blue-700' : ($isCompleted ? 'text-gray-700' : 'text-gray-400') }}">{{ $step['label'] }}</p>
                                                <p class="text-caption text-gray-400">{{ $step['desc'] }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Status Action Buttons --}}
                            <div class="space-y-2">
                                <p class="text-caption font-semibold text-gray-700 uppercase tracking-wide">Ubah Status</p>
                                <select wire:change="confirmStatusChange($event.target.value)" 
                                    class="w-full px-3 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40">
                                    <option value="">-- Pilih Status --</option>
                                    <option value="OPEN" {{ $selectedBox->status === 'OPEN' ? 'selected' : '' }}>Open</option>
                                    <option value="CLOSED" {{ $selectedBox->status === 'CLOSED' ? 'selected' : '' }}>Closed</option>
                                    <option value="SENT_TO_CARGO" {{ $selectedBox->status === 'SENT_TO_CARGO' ? 'selected' : '' }}>Sent to Cargo</option>
                                    <option value="OTW_INA" {{ $selectedBox->status === 'OTW_INA' ? 'selected' : '' }}>OTW Indonesia</option>
                                    <option value="UP_INVOICE" {{ $selectedBox->status === 'UP_INVOICE' ? 'selected' : '' }}>Buat Invoice</option>
                                    <option value="DONE" {{ $selectedBox->status === 'DONE' ? 'selected' : '' }}>Selesai</option>
                                </select>
                            </div>

                            {{-- Items --}}
                            @if($selectedBox->items->count() > 0)
                                <div>
                                    <p class="text-caption font-semibold text-gray-700 mb-3 uppercase tracking-wide">Daftar Barang ({{ $selectedBox->items->count() }})</p>
                                    <div class="space-y-2 max-h-[300px] overflow-y-auto">
                                        @foreach($selectedBox->items as $item)
                                            <div class="flex items-center justify-between p-2.5 rounded-[8px] bg-gray-50">
                                                <div class="min-w-0 flex-1">
                                                    <div class="flex items-center gap-2">
                                                        <p class="text-body font-medium text-gray-800 truncate">{{ $item->name }}</p>
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
                                                            <span class="text-caption font-bold {{ $statusColors[$item->status] ?? 'bg-gray-100 text-gray-700' }} px-1.5 py-0.5 rounded-full flex-shrink-0">
                                                                {{ $statusLabels[$item->status] ?? $item->status }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <p class="text-caption text-gray-500">{{ $item->resi_number }} · {{ $item->quantity }}x</p>
                                                </div>
                                                <div class="flex items-center gap-1.5 flex-shrink-0 ml-2">
                                                    @if($item->is_sensitive)
                                                        <span class="text-caption font-bold text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded-full">Sensitive</span>
                                                    @endif

                                                    {{-- Mark as No Tuan (active → no_tuan) --}}
                                                    @if($item->status === 'active')
                                                        <button
                                                            wire:click="markItemNoTuan({{ $item->id }})"
                                                            wire:confirm="Tandai barang '{{ $item->name }}' sebagai No Tuan?"
                                                            class="text-caption font-medium px-2 py-1 rounded-[6px] bg-orange-100 text-orange-700 hover:bg-orange-200 transition-colors"
                                                            title="Tandai sebagai No Tuan"
                                                        >
                                                            No Tuan
                                                        </button>
                                                    @endif

                                                    {{-- Mark as Klaim WH (no_tuan → klaim_wh) --}}
                                                    @if($item->status === 'no_tuan')
                                                        <button
                                                            wire:click="markItemKlaimWh({{ $item->id }})"
                                                            wire:confirm="Barang '{{ $item->name }}' akan ditandai Klaim WH untuk dijual/dilelang dan tidak bisa diklaim customer lagi. Lanjutkan?"
                                                            class="text-caption font-medium px-2 py-1 rounded-[6px] bg-red-100 text-red-700 hover:bg-red-200 transition-colors"
                                                            title="Klaim WH untuk dijual/dilelang"
                                                        >
                                                            Klaim WH
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Invoices --}}
                            @if($selectedBox->invoices->count() > 0)
                                <div>
                                    <p class="text-caption font-semibold text-gray-700 mb-3 uppercase tracking-wide">Invoice</p>
                                    <div class="space-y-2">
                                        @foreach($selectedBox->invoices as $invoice)
                                            <div class="flex items-center justify-between p-2.5 rounded-[8px] bg-gray-50">
                                                <div class="min-w-0">
                                                    <p class="text-body font-medium text-gray-800">{{ $invoice->invoice_number }}</p>
                                                    <p class="text-caption text-gray-500">Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</p>
                                                </div>
                                                <x-status-badge :status="$invoice->status" />
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Status Change Confirmation Modal --}}
    @if($showStatusConfirm)
        <div class="fixed inset-0 z-50 overflow-y-auto" wire:click.self="cancelStatusChange">
            <div class="fixed inset-0 bg-black/30 transition-opacity"></div>
            <div class="flex min-h-full items-end sm:items-center justify-center p-4">
                <div class="relative bg-white rounded-[16px] shadow-modal w-full max-w-md p-6 transform transition-all" @click.stop>
                    <div class="flex items-start gap-4 mb-5">
                        <div class="w-10 h-10 rounded-full bg-amber-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-[16px] font-semibold text-gray-900">Konfirmasi Perubahan Status</h3>
                            <p class="text-body text-gray-500 mt-1">
                                Anda akan mengubah status box menjadi
                                <span class="font-semibold text-gray-700">{{ $pendingStatus }}</span>.
                                Pastikan box sudah siap untuk tahap ini.
                            </p>
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="block text-caption font-medium text-gray-700 mb-1.5">Catatan (opsional)</label>
                        <textarea
                            wire:model="statusNote"
                            rows="2"
                            placeholder="Tambahkan catatan untuk perubahan ini..."
                            class="w-full px-3 py-2 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors resize-none"
                        ></textarea>
                    </div>

                    <div class="flex items-center gap-3 justify-end">
                        <button wire:click="cancelStatusChange" class="px-4 py-2 text-body font-medium text-gray-700 bg-white border border-gray-200 rounded-[8px] hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button wire:click="updateStatus" class="px-4 py-2 text-body font-medium text-white bg-primary rounded-[8px] hover:bg-primary-light transition-colors">
                            Konfirmasi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Create Box Modal --}}
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" wire:click.self="closeCreateModal">
            <div class="fixed inset-0 bg-black/30 transition-opacity"></div>
            <div class="flex min-h-full items-end sm:items-center justify-center p-4">
                <div class="relative bg-white rounded-[16px] shadow-modal w-full max-w-lg p-6 transform transition-all" @click.stop>
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-[16px] font-semibold text-gray-900">Buat Box Baru</h3>
                        <button wire:click="closeCreateModal" class="p-2 min-w-[44px] min-h-[44px] flex items-center justify-center rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="space-y-4">
                        {{-- Type & Method --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-caption font-medium text-gray-700 mb-1.5">Tipe Box</label>
                                <select wire:model="newType" class="w-full py-2.5 px-3 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40">
                                    <option value="sharing">Sharing</option>
                                    <option value="direct">Direct</option>
                                    <option value="handcarry">Handcarry</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-caption font-medium text-gray-700 mb-1.5">Metode</label>
                                <select wire:model="newMethod" class="w-full py-2.5 px-3 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40">
                                    <option value="air">Air</option>
                                    <option value="sea">Sea</option>
                                </select>
                            </div>
                        </div>

                        {{-- Customer --}}
                        <div>
                            <label class="block text-caption font-medium text-gray-700 mb-1.5">Customer</label>
                            <select wire:model="newCustomerId" class="w-full py-2.5 px-3 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40">
                                <option value="">Tanpa Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Tracking & Batch --}}
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-caption font-medium text-gray-700 mb-1.5">Tracking Number</label>
                                <input type="text" wire:model="newTrackingNumber" placeholder="Opsional" class="w-full px-3 py-2.5 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40">
                            </div>
                            <div>
                                <label class="block text-caption font-medium text-gray-700 mb-1.5">Batch Name</label>
                                <input type="text" wire:model="newBatchName" placeholder="Opsional" class="w-full px-3 py-2.5 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40">
                            </div>
                            <div>
                                <label class="block text-caption font-medium text-gray-700 mb-1.5">Huruf Box</label>
                                <input type="text" wire:model="newHurufBox" maxlength="10" placeholder="e.g. H, A" class="w-full px-3 py-2.5 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40">
                            </div>
                        </div>

                        {{-- Notes --}}
                        <div>
                            <label class="block text-caption font-medium text-gray-700 mb-1.5">Catatan</label>
                            <textarea wire:model="newNotes" rows="2" placeholder="Catatan opsional..." class="w-full px-3 py-2.5 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 resize-none"></textarea>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 justify-end mt-6">
                        <button wire:click="closeCreateModal" class="px-4 py-2.5 text-body font-medium text-gray-700 bg-white border border-gray-200 rounded-[8px] hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button wire:click="createBox" class="px-4 py-2.5 text-body font-medium text-white bg-primary rounded-[8px] hover:bg-primary-light transition-colors">
                            Buat Box
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- MODAL: Edit Box (Tracking + Huruf Box + ETA)                 --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    @if($showEditModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" wire:click.self="closeEditModal">
            <div class="fixed inset-0 bg-black/30 transition-opacity"></div>
            <div class="flex min-h-full items-end sm:items-center justify-center p-4">
                <div class="relative bg-white rounded-[16px] shadow-lg w-full max-w-md transform transition-all" @click.stop>
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                        <h3 class="text-[15px] font-semibold text-gray-900">Edit Box</h3>
                        <button wire:click="closeEditModal" class="p-2 text-gray-400 hover:text-gray-600 rounded-[8px]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <form wire:submit.prevent="saveBoxEdit" class="p-6 space-y-4">
                        {{-- Type + Method --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[12px] font-medium text-gray-600 mb-1">Tipe <span class="text-red-500">*</span></label>
                                <select wire:model="editType" class="w-full px-3 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40">
                                    <option value="sharing">Sharing</option>
                                    <option value="direct">Direct</option>
                                    <option value="handcarry">Handcarry</option>
                                </select>
                                @error('editType') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-[12px] font-medium text-gray-600 mb-1">Metode <span class="text-red-500">*</span></label>
                                <select wire:model="editMethod" class="w-full px-3 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40">
                                    <option value="air">Air</option>
                                    <option value="sea">Sea</option>
                                </select>
                                @error('editMethod') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Tracking + Batch --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[12px] font-medium text-gray-600 mb-1">Tracking Number</label>
                                <input type="text" wire:model="editTrackingNumber" maxlength="100" placeholder="Enter tracking number"
                                    class="w-full px-3 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40">
                                @error('editTrackingNumber') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-[12px] font-medium text-gray-600 mb-1">Batch Name</label>
                                <input type="text" wire:model="editBatchName" maxlength="100" placeholder="e.g. Batch Juli 1"
                                    class="w-full px-3 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40">
                                @error('editBatchName') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Huruf Box + Customer --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[12px] font-medium text-gray-600 mb-1">Huruf Box</label>
                                <input type="text" wire:model="editHurufBox" maxlength="10" placeholder="e.g. H, A, B"
                                    class="w-full px-3 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40">
                                @error('editHurufBox') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-[12px] font-medium text-gray-600 mb-1">Customer</label>
                                <select wire:model="editCustomerId" class="w-full px-3 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40">
                                    <option value="">Sharing (Semua Customer)</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                                @error('editCustomerId') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- ETA --}}
                        <div>
                            <label class="block text-[12px] font-medium text-gray-600 mb-1">ETA</label>
                            <input type="date" wire:model="editEta"
                                class="w-full px-3 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40">
                            @error('editEta') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Stevedoring --}}
                        <div>
                            <label class="block text-[12px] font-medium text-gray-600 mb-1">Stevedoring</label>
                            <input type="date" wire:model="editStevedoringDate"
                                class="w-full px-3 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40">
                            @error('editStevedoringDate') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Tanggal Update Tagihan --}}
                        <div>
                            <label class="block text-[12px] font-medium text-gray-600 mb-1">Tanggal Update Tagihan</label>
                            <input type="date" wire:model="editTagihanUpdateDate"
                                class="w-full px-3 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40">
                            @error('editTagihanUpdateDate') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Notes --}}
                        <div>
                            <label class="block text-[12px] font-medium text-gray-600 mb-1">Catatan</label>
                            <textarea wire:model="editNotes" maxlength="1000" rows="2" placeholder="Catatan box (opsional)"
                                class="w-full px-3 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40"></textarea>
                            @error('editNotes') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-2">
                            <button type="button" wire:click="closeEditModal"
                                class="px-4 py-2 text-[13px] font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-[8px] transition-colors">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-4 py-2 text-[13px] font-medium text-white bg-primary rounded-[8px] hover:bg-primary-light transition-colors">
                                Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
