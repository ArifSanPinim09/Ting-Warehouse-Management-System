<div class="min-h-screen bg-[#f8fafc]">
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-display text-primary">Buat Invoice</h1>
                    <p class="text-body text-gray-500 mt-1">Pilih barang yang sudah tiba untuk dibuat invoice</p>
                </div>
                @if(count($selectedItems) > 0)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-caption font-semibold bg-accent/10 text-accent">
                        {{ count($selectedItems) }} barang dipilih
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-4">

        @if($availableItems->isEmpty())
            <x-empty-state
                icon="package"
                title="Tidak ada barang tersedia"
                text="Belum ada barang yang sudah tiba dan siap dibuat invoice. Barang harus sudah matched dengan data WH China dan status arrived."
            />
        @else
            {{-- Select All Bar --}}
            <div class="ds-card px-4 py-3 flex items-center justify-between">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox"
                        wire:click="toggleAll"
                        {{ count($selectedItems) === $availableItems->count() && $availableItems->count() > 0 ? 'checked' : '' }}
                        class="rounded border-gray-300 text-accent focus:ring-accent/20">
                    <span class="text-body font-medium text-gray-700">
                        Pilih Semua ({{ $availableItems->count() }} barang)
                    </span>
                </label>
                @if(count($selectedItems) > 0)
                    <span class="text-caption text-gray-500">
                        {{ count($selectedItems) }} dari {{ $availableItems->count() }} dipilih
                    </span>
                @endif
            </div>

            {{-- Item List --}}
            <div class="space-y-2">
                @foreach($availableItems as $item)
                    <label class="ds-card px-4 py-3 flex items-start gap-3 cursor-pointer hover:border-accent/30 transition-colors {{ in_array($item->id, $selectedItems) ? 'border-accent bg-accent/5' : '' }}">
                        <input type="checkbox"
                            wire:click="toggleItem({{ $item->id }})"
                            {{ in_array($item->id, $selectedItems) ? 'checked' : '' }}
                            class="mt-1 rounded border-gray-300 text-accent focus:ring-accent/20">

                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-body font-semibold text-gray-900">{{ $item->name }}</p>
                                    <p class="text-caption text-gray-500 mt-0.5">
                                        Resi: <span class="font-mono">{{ $item->resi_number ?? '-' }}</span>
                                        · Box: {{ $item->box->display_name ?? '-' }}
                                    </p>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="text-body font-medium text-gray-700">{{ $item->quantity }}x</p>
                                    @if($item->price_yuan)
                                        <p class="text-caption text-gray-500">¥ {{ number_format($item->price_yuan, 2) }}</p>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-3 mt-2 text-caption text-gray-500">
                                @if($item->whChinaData && $item->whChinaData->berat_ina)
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        {{ number_format($item->whChinaData->berat_ina, 2) }} kg (INA)
                                    </span>
                                @elseif($item->whChinaData)
                                    <span class="text-amber-500">Menunggu QC Indonesia</span>
                                @else
                                    <span class="text-amber-500">Belum ada data WH China</span>
                                @endif
                                @if($item->is_sensitive)
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-orange-50 text-orange-600">Sensitive</span>
                                @endif
                                <span class="ml-auto capitalize">{{ $item->box->type }} · {{ $item->box->method }}</span>
                            </div>
                        </div>
                    </label>
                @endforeach
            </div>

            {{-- Dimensions + Preview (sticky bottom) --}}
            @if(count($selectedItems) > 0)
                <div class="sticky bottom-0 bg-white border border-gray-200 rounded-[12px] shadow-lg p-5 space-y-4">
                    {{-- Dimensions Input --}}
                    <div>
                        <h3 class="text-body font-semibold text-gray-900 mb-3">Ukuran Box Pengiriman</h3>
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-caption font-medium text-gray-600 mb-1">Panjang (cm) <span class="text-red-500">*</span></label>
                                <input type="number" wire:model.live="length" min="1" max="999" placeholder="0"
                                    class="w-full px-3 py-2 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors tabular-nums">
                            </div>
                            <div>
                                <label class="block text-caption font-medium text-gray-600 mb-1">Lebar (cm) <span class="text-red-500">*</span></label>
                                <input type="number" wire:model.live="width" min="1" max="999" placeholder="0"
                                    class="w-full px-3 py-2 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors tabular-nums">
                            </div>
                            <div>
                                <label class="block text-caption font-medium text-gray-600 mb-1">Tinggi (cm) <span class="text-red-500">*</span></label>
                                <input type="number" wire:model.live="height" min="1" max="999" placeholder="0"
                                    class="w-full px-3 py-2 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors tabular-nums">
                            </div>
                        </div>
                    </div>

                    {{-- Fee Preview --}}
                    @if($preview)
                        <div class="bg-gray-50 rounded-[8px] p-4">
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-caption">
                                <div>
                                    <span class="text-gray-500 block">Total Berat</span>
                                    <span class="text-body font-semibold text-gray-900">{{ number_format($preview['total_weight'], 2) }} kg</span>
                                </div>
                                <div>
                                    <span class="text-gray-500 block">Volume</span>
                                    <span class="text-body font-semibold text-gray-900">{{ number_format($preview['volume'], 2) }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500 block">Basis</span>
                                    <span class="text-body font-semibold text-gray-900">{{ number_format($preview['basis'], 2) }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500 block">Rate</span>
                                    <span class="text-body font-semibold text-gray-900">{{ $preview['rate_key'] }}</span>
                                </div>
                            </div>
                            <div class="border-t border-gray-200 mt-3 pt-3 grid grid-cols-2 sm:grid-cols-5 gap-3 text-caption">
                                <div>
                                    <span class="text-gray-500 block">Fee TAX</span>
                                    <span class="text-body font-medium text-gray-700">Rp {{ number_format($preview['fee_tax'], 0, ',', '.') }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500 block">Fee WH</span>
                                    <span class="text-body font-medium text-gray-700">Rp {{ number_format($preview['fee_wh'], 0, ',', '.') }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500 block">Fee Packing</span>
                                    <span class="text-body font-medium text-gray-700">Rp {{ number_format($preview['fee_packing'], 0, ',', '.') }}</span>
                                </div>
                                @if($preview['denda_total'] > 0)
                                    <div>
                                        <span class="text-gray-500 block">Denda</span>
                                        <span class="text-body font-medium text-red-600">Rp {{ number_format($preview['denda_total'], 0, ',', '.') }}</span>
                                    </div>
                                @endif
                                <div>
                                    <span class="text-gray-500 block">Grand Total</span>
                                    <span class="text-[16px] font-bold text-accent">Rp {{ number_format($preview['grand_total'], 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-caption text-gray-400 text-center py-2">Masukkan ukuran box untuk melihat estimasi biaya</p>
                    @endif

                    {{-- Create Button --}}
                    <div class="flex items-center justify-end gap-3">
                        <span class="text-caption text-gray-500">
                            {{ count($selectedItems) }} barang
                        </span>
                        <button wire:click="createInvoice"
                            wire:loading.attr="disabled"
                            @if(!$preview) disabled @endif
                            class="inline-flex items-center gap-2 px-6 py-2.5 text-body font-medium text-white bg-primary rounded-[8px] hover:bg-primary-light transition-colors shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg wire:loading class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            <span wire:loading.remove>Buat Invoice</span>
                            <span wire:loading>Membuat...</span>
                        </button>
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>
