<div class="min-h-screen bg-[#f8fafc]">
    {{-- Page Header --}}
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-display text-primary">My Box</h1>
                    <p class="text-body text-gray-500 mt-1">Kelola box sharing dan direct Anda</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex items-center bg-gray-100 rounded-button p-1">
                        <a href="{{ route('customer.box.sharing') }}" wire:navigate class="px-4 py-2 rounded-button text-body font-medium transition-colors text-gray-500 hover:text-gray-700">Sharing</a>
                        <a href="{{ route('customer.box.direct') }}" wire:navigate class="px-4 py-2 rounded-button text-body font-medium transition-colors bg-white text-primary shadow-sm">Direct</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-4">
        {{-- Filter Bar --}}
        <div class="ds-card p-4">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari tracking number atau batch..." class="ds-input" />
                </div>
                <select wire:model.live="filterStatus" class="ds-input sm:w-48">
                    <option value="">Semua Status</option>
                    @foreach(\App\Models\Box::getValidStatuses() as $status)
                        <option value="{{ $status }}">{{ $status }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Box List --}}
        @if($boxes->isEmpty())
            <x-empty-state
                icon="box"
                title="Belum ada box direct"
                text="Anda belum memiliki box direct. Hubungi admin untuk membuat box direct baru."
                action="Hubungi Admin"
                :actionUrl="'#'"
            />
        @else
            <div class="space-y-4">
                @foreach($boxes as $box)
                    <div class="ds-card-hover overflow-hidden" x-data="{ expanded: false }">
                        <div class="p-5">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-start gap-4 min-w-0">
                                    <div class="w-12 h-12 rounded-card bg-violet-50 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-subtitle font-semibold text-primary">
                                            {{ $box->batch_name ?? 'Batch #' . $box->id }}
                                        </p>
                                        <div class="flex flex-wrap items-center gap-2 mt-1.5">
                                            @if($box->tracking_number)
                                                <span class="ds-badge-neutral">{{ $box->tracking_number }}</span>
                                            @endif
                                            <span class="ds-badge-neutral">{{ strtoupper($box->method) }}</span>
                                            <x-status-badge :status="$box->status" />
                                            <span class="text-caption text-gray-400">{{ $box->items_count }} barang</span>
                                        </div>
                                    </div>
                                </div>
                                <button @click="expanded = !expanded" class="p-2 rounded-button text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors flex-shrink-0">
                                    <svg class="w-5 h-5 transition-transform" :class="expanded && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                            </div>
                        </div>

                        <div x-show="expanded" x-collapse class="border-t border-gray-100">
                            @if($box->items->isEmpty())
                                <div class="p-5 text-center text-body text-gray-400">Belum ada barang di box ini</div>
                            @else
                                <div class="divide-y divide-gray-50">
                                    @foreach($box->items as $item)
                                        <div class="px-5 py-4 flex items-center justify-between gap-4">
                                            <div class="min-w-0 flex-1">
                                                <p class="text-body font-medium text-gray-800 truncate">{{ $item->name }}</p>
                                                <div class="flex flex-wrap items-center gap-3 mt-1">
                                                    <span class="text-caption text-gray-500">Qty: {{ $item->quantity }}</span>
                                                    <span class="text-caption text-gray-500">¥{{ number_format($item->price_yuan, 2) }}</span>
                                                    @if($item->resi_number)
                                                        <span class="text-caption text-gray-400">Resi: {{ $item->resi_number }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2 flex-shrink-0">
                                                @if($item->arrived_china)
                                                    <span class="ds-badge-success text-micro">Sampai China</span>
                                                @endif
                                                @if($item->arrived_indonesia)
                                                    <span class="ds-badge-success text-micro">Sampai Indonesia</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">{{ $boxes->links() }}</div>
        @endif
    </div>
</div>
