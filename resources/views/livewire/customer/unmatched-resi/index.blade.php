<div class="min-h-screen bg-[#f8fafc]">
    {{-- Page Header --}}
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div>
                <h1 class="text-display text-primary">Resi Belum Dikenali</h1>
                <p class="text-body text-gray-500 mt-1">Resi dari gudang China yang belum terhubung dengan data customer</p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-4">

        {{-- Info Box --}}
        <div class="bg-blue-50 border border-blue-200 rounded-[12px] p-4">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="text-body font-semibold text-blue-800">Cara Klaim Resi</p>
                    <p class="text-body text-blue-700 mt-1">Jika Anda melihat nomor resi yang merupakan milik Anda, klik "Klaim Resi" lalu isi data barang. Sistem akan otomatis menghubungkan data Anda dengan data gudang China.</p>
                </div>
            </div>
        </div>

        @if($unmatchedData->isEmpty())
            <div class="bg-white rounded-[12px] border border-gray-100 p-12 text-center">
                <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                    <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-body font-semibold text-gray-700 mb-1">Semua resi sudah dikenali</h3>
                <p class="text-body text-gray-500">Saat ini tidak ada resi dari gudang China yang menunggu klaim.</p>
            </div>
        @else
            {{-- Unmatched Resi Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($unmatchedData as $wh)
                    <div class="bg-white rounded-[12px] border border-gray-100 p-5 hover:shadow-card-hover transition-shadow">
                        <div class="flex items-start justify-between mb-3">
                            <div class="min-w-0">
                                <p class="text-body font-semibold text-gray-900 font-mono truncate">{{ $wh->resi_number }}</p>
                                <p class="text-caption text-gray-500 mt-0.5">Diinput {{ $wh->created_at->format('d M Y H:i') }}</p>
                            </div>
                            <span class="text-caption font-medium text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full shrink-0">Belum Dikenali</span>
                        </div>

                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between text-body">
                                <span class="text-gray-500">Berat</span>
                                <span class="font-medium text-gray-800">{{ number_format($wh->berat, 2) }} kg</span>
                            </div>
                            <div class="flex justify-between text-body">
                                <span class="text-gray-500">Ukuran Box</span>
                                <span class="font-medium text-gray-800">{{ $wh->ukuran_box }}</span>
                            </div>
                            @if($wh->foto_barang)
                                <div class="mt-2">
                                    <img src="{{ Storage::url($wh->foto_barang) }}" alt="Foto barang" class="w-full h-32 object-cover rounded-[8px] border border-gray-200" loading="lazy">
                                </div>
                            @endif
                        </div>

                        <button
                            wire:click="selectResi({{ $wh->id }})"
                            class="w-full ds-btn-primary ds-btn-sm"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Klaim Resi Ini
                        </button>
                    </div>
                @endforeach
            </div>

            @if($unmatchedData->hasPages())
                <div class="mt-4">
                    {{ $unmatchedData->links() }}
                </div>
            @endif
        @endif
    </div>

    {{-- Claim Form Modal --}}
    @if($showClaimForm && $selectedWhId)
        @php
            $selectedWh = \App\Models\WhChinaData::find($selectedWhId);
        @endphp
        <div class="fixed inset-0 z-50 overflow-y-auto" wire:click.self="cancelClaim">
            <div class="fixed inset-0 bg-black/30 transition-opacity"></div>
            <div class="flex min-h-full items-end sm:items-center justify-center p-4">
                <div class="relative bg-white rounded-t-modal sm:rounded-modal shadow-modal w-full max-w-lg max-h-[90vh] overflow-y-auto animate-slide-up" @click.stop>
                    {{-- Modal Header --}}
                    <div class="sticky top-0 bg-white px-6 py-4 border-b border-gray-100 flex items-center justify-between z-10">
                        <div>
                            <h3 class="text-title font-semibold text-primary">Klaim Resi</h3>
                            <p class="text-caption text-gray-500 mt-0.5 font-mono">{{ $selectedWh?->resi_number ?? '' }}</p>
                        </div>
                        <button wire:click="cancelClaim" type="button" class="p-2 min-w-[44px] min-h-[44px] flex items-center justify-center rounded-button text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form wire:submit="submitClaim" class="px-6 py-5 space-y-5">
                        {{-- WH China Data Summary --}}
                        @if($selectedWh)
                            <div class="bg-gray-50 rounded-[10px] p-4">
                                <p class="text-caption font-semibold text-gray-500 uppercase tracking-wide mb-2">Data dari Gudang China</p>
                                <div class="grid grid-cols-2 gap-3 text-body">
                                    <div>
                                        <span class="text-gray-500">Berat:</span>
                                        <span class="font-medium text-gray-800">{{ number_format($selectedWh->berat, 2) }} kg</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Ukuran:</span>
                                        <span class="font-medium text-gray-800">{{ $selectedWh->ukuran_box }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Box Select --}}
                        <div>
                            <label class="ds-label">Pilih Box <span class="text-red-500">*</span></label>
                            <select wire:model="boxId" class="ds-input @error('boxId') ds-input-error @enderror">
                                <option value="">Pilih box tujuan...</option>
                                @foreach($openBoxes as $box)
                                    <option value="{{ $box->id }}">{{ $box->display_name }} — {{ strtoupper($box->type) }} / {{ strtoupper($box->method) }}</option>
                                @endforeach
                            </select>
                            @error('boxId') <p class="ds-error">{{ $message }}</p> @enderror
                        </div>

                        {{-- Item Name --}}
                        <div>
                            <label class="ds-label">Nama Barang <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="itemName" placeholder="Contoh: iPhone 15 Case" class="ds-input @error('itemName') ds-input-error @enderror" />
                            @error('itemName') <p class="ds-error">{{ $message }}</p> @enderror
                        </div>

                        {{-- Quantity + Price --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="ds-label">Jumlah <span class="text-red-500">*</span></label>
                                <input type="number" wire:model="quantity" min="1" max="9999" class="ds-input @error('quantity') ds-input-error @enderror" />
                                @error('quantity') <p class="ds-error">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="ds-label">Harga (¥) <span class="text-red-500">*</span></label>
                                <input type="number" wire:model="priceYuan" min="0.01" step="0.01" placeholder="0.00" class="ds-input @error('priceYuan') ds-input-error @enderror" />
                                @error('priceYuan') <p class="ds-error">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Sensitive --}}
                        <div>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" wire:model="isSensitive" class="rounded border-gray-300 text-accent focus:ring-accent/20">
                                <span class="text-body font-medium text-gray-700">Barang Sensitive</span>
                            </label>
                            @if($isSensitive)
                                <div class="mt-3">
                                    <label class="ds-label">Jenis Sensitive <span class="text-red-500">*</span></label>
                                    <select wire:model="sensitiveType" class="ds-input @error('sensitiveType') ds-input-error @enderror">
                                        <option value="">Pilih jenis...</option>
                                        @foreach($sensitiveTypes as $type)
                                            <option value="{{ $type }}">{{ $type }}</option>
                                        @endforeach
                                    </select>
                                    @error('sensitiveType') <p class="ds-error">{{ $message }}</p> @enderror
                                </div>
                            @endif
                        </div>

                        {{-- Proof Photo --}}
                        <div>
                            <label class="ds-label">Foto Bukti Barang (CO) <span class="text-red-500">*</span></label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed border-gray-200 rounded-card transition-colors hover:border-accent/30 hover:bg-accent/5">
                                @if($proofCo)
                                    <div class="relative inline-block">
                                        <img src="{{ $proofCo->temporaryUrl() }}" class="max-h-32 rounded-lg shadow-sm" alt="Preview" />
                                        <button type="button" wire:click="$set('proofCo', null)" class="absolute -top-3 -right-3 w-8 h-8 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 transition-colors" aria-label="Hapus foto">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                @else
                                    <div class="space-y-2 text-center">
                                        <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <label class="relative cursor-pointer">
                                            <span class="ds-btn-secondary ds-btn-sm">Pilih Foto</span>
                                            <input type="file" wire:model="proofCo" accept="image/jpeg,image/png,image/webp" class="sr-only" />
                                        </label>
                                        <p class="text-caption text-gray-400">JPG, PNG, atau WebP. Maks 5MB</p>
                                    </div>
                                @endif
                            </div>
                            @error('proofCo') <p class="ds-error">{{ $message }}</p> @enderror
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center justify-end gap-3 pt-2">
                            <button type="button" wire:click="cancelClaim" class="ds-btn-secondary">Batal</button>
                            <button type="submit" class="ds-btn-primary" wire:loading.attr="disabled">
                                <svg wire:loading class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                <span wire:loading.remove>Klaim Resi</span>
                                <span wire:loading>Mengklaim...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
