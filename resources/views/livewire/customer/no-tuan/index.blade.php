<div class="min-h-screen bg-[#f8fafc]">
    {{-- Page Header --}}
    <div class="bg-white border-b border-gray-100">
        <div class="mx-auto w-full max-w-[1500px] px-4 md:px-6 lg:px-8 xl:px-10 2xl:px-12 py-6">
            <div>
                <h1 class="text-[22px] font-bold text-gray-900 tracking-tight">Barang No Tuan</h1>
                <p class="text-body text-gray-500 mt-0.5">Barang yang tidak diklaim oleh customer mana pun</p>
            </div>
        </div>
    </div>

    <div class="mx-auto w-full max-w-[1500px] px-4 md:px-6 lg:px-8 xl:px-10 2xl:px-12 py-6 space-y-6">

        {{-- Info Box --}}
        <div class="bg-amber-50 border border-amber-200 rounded-[12px] p-4">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                <div>
                    <p class="text-body font-semibold text-amber-800">Informasi Klaim Barang</p>
                    <p class="text-body text-amber-700 mt-1">Klaim barang dikenakan denda Rp 5.000 per barang. Denda ditagih bersamaan dengan pembayaran invoice berikutnya.</p>
                </div>
            </div>
        </div>

        @if($noTuanItems->isEmpty())
            <div class="bg-white rounded-[12px] border border-gray-100 p-12 text-center">
                <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                    <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <h3 class="text-body font-semibold text-gray-700 mb-1">Tidak ada barang No Tuan</h3>
                <p class="text-body text-gray-500">Saat ini tidak ada barang yang tersedia untuk diklaim.</p>
            </div>
        @else
            {{-- Items Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($noTuanItems as $item)
                    <div class="bg-white rounded-[12px] border border-gray-100 p-5 hover:shadow-card-hover transition-shadow">
                        <div class="flex items-start justify-between mb-3">
                            <div class="min-w-0">
                                <p class="text-body font-semibold text-gray-900 truncate">{{ $item->name }}</p>
                                <p class="text-caption text-gray-500">Resi: {{ $item->resi_number ?? '-' }}</p>
                            </div>
                            <span class="text-caption font-bold text-orange-600 bg-orange-50 px-2 py-0.5 rounded-full flex-shrink-0">No Tuan</span>
                        </div>

                        <div class="space-y-2 mb-4">
                            <div class="flex items-center justify-between">
                                <span class="text-caption text-gray-500">Jumlah</span>
                                <span class="text-body text-gray-700">{{ $item->quantity }}x</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-caption text-gray-500">Harga</span>
                                <span class="text-body text-gray-700">¥ {{ number_format($item->price_yuan, 2) }}</span>
                            </div>
                            @if($item->box)
                                <div class="flex items-center justify-between">
                                    <span class="text-caption text-gray-500">Box</span>
                                    <span class="text-body text-gray-700">{{ $item->box->display_name ?? '-' }}</span>
                                </div>
                            @endif
                            @if($item->is_sensitive)
                                <div class="flex items-center justify-between">
                                    <span class="text-caption text-gray-500">Kategori</span>
                                    <span class="text-caption font-bold text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded-full">Sensitive</span>
                                </div>
                            @endif
                        </div>

                        <button
                            wire:click="selectItem({{ $item->id }})"
                            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-primary text-white text-body font-medium rounded-[8px] hover:bg-primary-light transition-colors"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Klaim Barang
                        </button>
                    </div>
                @endforeach
            </div>

            @if($noTuanItems->hasPages())
                <div class="py-2">
                    {{ $noTuanItems->links() }}
                </div>
            @endif
        @endif
    </div>

    {{-- Claim Form Modal (§7.1) --}}
    @if($showClaimForm && $selectedItemId)
        @php
            $selectedItem = $noTuanItems->firstWhere('id', $selectedItemId);
        @endphp
        @if($selectedItem)
            <div class="fixed inset-0 z-50 overflow-y-auto" wire:click.self="cancelClaim">
                <div class="fixed inset-0 bg-black/30 transition-opacity"></div>
                <div class="flex min-h-full items-end sm:items-center justify-center p-4">
                    <div class="relative bg-white rounded-[16px] shadow-modal w-full max-w-lg p-6 transform transition-all" @click.stop>
                        <div class="flex items-center justify-between mb-5">
                            <h3 class="text-[16px] font-semibold text-gray-900">Klaim Barang</h3>
                            <button wire:click="cancelClaim" class="p-2 min-w-[44px] min-h-[44px] flex items-center justify-center rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        {{-- Item Info --}}
                        <div class="p-3 bg-gray-50 rounded-[8px] mb-4">
                            <p class="text-body font-semibold text-gray-900">{{ $selectedItem->name }}</p>
                            <p class="text-caption text-gray-500">Resi: {{ $selectedItem->resi_number ?? '-' }} · {{ $selectedItem->quantity }}x</p>
                        </div>

                        {{-- Warning §8.3 --}}
                        <div class="p-3 bg-amber-50 border border-amber-200 rounded-[8px] mb-4">
                            <div class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                                <p class="text-body text-amber-800">Klaim akan dikenakan denda Rp 5.000. Lanjutkan?</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            {{-- bukti_pembelian (§7.1) --}}
                            <div>
                                <label class="block text-caption font-medium text-gray-700 mb-1.5">
                                    Bukti Pembelian <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="file"
                                    wire:model="proofPembelian"
                                    accept="image/jpeg,image/png"
                                    class="w-full px-3 py-2 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors file:mr-3 file:py-1 file:px-3 file:rounded-[6px] file:border-0 file:text-caption file:font-medium file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200"
                                >
                                @error('proofPembelian') <p class="text-caption text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- keterangan (§7.1, optional) --}}
                            <div>
                                <label class="block text-caption font-medium text-gray-700 mb-1.5">
                                    Keterangan <span class="text-gray-400">(opsional)</span>
                                </label>
                                <textarea
                                    wire:model="keterangan"
                                    rows="2"
                                    maxlength="500"
                                    placeholder="Catatan tambahan..."
                                    class="w-full px-3 py-2 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors resize-none"
                                ></textarea>
                                @error('keterangan') <p class="text-caption text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="flex items-center gap-3 justify-end mt-6">
                            <button wire:click="cancelClaim" class="px-4 py-2.5 text-body font-medium text-gray-700 bg-white border border-gray-200 rounded-[8px] hover:bg-gray-50 transition-colors">
                                Batal
                            </button>
                            <button
                                wire:click="submitClaim"
                                wire:loading.attr="disabled"
                                class="px-4 py-2.5 text-body font-medium text-white bg-primary rounded-[8px] hover:bg-primary-light transition-colors disabled:opacity-50"
                            >
                                <span wire:loading.remove>Klaim Barang</span>
                                <span wire:loading>Memproses...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
