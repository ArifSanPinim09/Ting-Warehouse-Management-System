<div class="min-h-screen bg-[#f8fafc]">
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div>
                <h1 class="text-display text-primary">Kalkulator Biaya</h1>
                <p class="text-body text-gray-500 mt-1">Hitung estimasi biaya pengiriman barang Anda</p>
            </div>
        </div>
    </div>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
            {{-- Input Form --}}
            <div class="lg:col-span-3 space-y-6">
                {{-- Shipment Type --}}
                <div class="ds-card p-5 space-y-5">
                    <h3 class="ds-section-subtitle">Tipe Pengiriman</h3>

                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative cursor-pointer">
                            <input type="radio" wire:model="method" value="air" class="sr-only peer" />
                            <div class="flex flex-col items-center gap-2 p-4 rounded-card border-2 border-gray-200 transition-all peer-checked:border-accent peer-checked:bg-accent/5 hover:border-gray-300">
                                <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                <span class="text-body font-semibold text-gray-700">Air Freight</span>
                                <span class="text-caption text-gray-400">Lebih cepat</span>
                            </div>
                        </label>
                        <label class="relative cursor-pointer">
                            <input type="radio" wire:model="method" value="sea" class="sr-only peer" />
                            <div class="flex flex-col items-center gap-2 p-4 rounded-card border-2 border-gray-200 transition-all peer-checked:border-accent peer-checked:bg-accent/5 hover:border-gray-300">
                                <svg class="w-8 h-8 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                                <span class="text-body font-semibold text-gray-700">Sea Freight</span>
                                <span class="text-caption text-gray-400">Lebih hemat</span>
                            </div>
                        </label>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative cursor-pointer">
                            <input type="radio" wire:model="type" value="sharing" class="sr-only peer" />
                            <div class="flex items-center gap-3 p-3 rounded-card border-2 border-gray-200 transition-all peer-checked:border-accent peer-checked:bg-accent/5 hover:border-gray-300">
                                <span class="text-body font-medium text-gray-700">Sharing</span>
                            </div>
                        </label>
                        <label class="relative cursor-pointer">
                            <input type="radio" wire:model="type" value="direct" class="sr-only peer" />
                            <div class="flex items-center gap-3 p-3 rounded-card border-2 border-gray-200 transition-all peer-checked:border-accent peer-checked:bg-accent/5 hover:border-gray-300">
                                <span class="text-body font-medium text-gray-700">Direct</span>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Weight & Dimensions --}}
                <div class="ds-card p-5 space-y-5">
                    <h3 class="ds-section-subtitle">Berat & Dimensi</h3>

                    <div>
                        <label class="ds-label">Berat Aktual (gram) <span class="text-red-500">*</span></label>
                        <input type="number" wire:model.live="weight" min="0.1" step="0.1" placeholder="0.0" class="ds-input @error('weight') ds-input-error @enderror" />
                        @error('weight') <p class="ds-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="ds-label">Dimensi (cm) <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <input type="number" wire:model.live="length" min="1" placeholder="P" class="ds-input text-center @error('length') ds-input-error @enderror" />
                                <span class="text-micro text-gray-400 text-center block mt-1">Panjang</span>
                            </div>
                            <div>
                                <input type="number" wire:model.live="width" min="1" placeholder="L" class="ds-input text-center @error('width') ds-input-error @enderror" />
                                <span class="text-micro text-gray-400 text-center block mt-1">Lebar</span>
                            </div>
                            <div>
                                <input type="number" wire:model.live="height" min="1" placeholder="T" class="ds-input text-center @error('height') ds-input-error @enderror" />
                                <span class="text-micro text-gray-400 text-center block mt-1">Tinggi</span>
                            </div>
                        </div>
                        @error('length') <p class="ds-error">{{ $message }}</p> @enderror
                        @error('width') <p class="ds-error">{{ $message }}</p> @enderror
                        @error('height') <p class="ds-error">{{ $message }}</p> @enderror
                    </div>

                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" wire:model.live="isSensitive" class="mt-0.5 rounded border-gray-300 text-accent focus:ring-accent/20" />
                        <div>
                            <span class="text-body font-medium text-gray-800">Barang Sensitive</span>
                            <p class="text-caption text-gray-500 mt-0.5">Kena rate berbeda untuk barang kategori khusus</p>
                        </div>
                    </label>
                </div>

                <button wire:click="calculate" class="ds-btn-primary ds-btn-lg w-full justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    Hitung Estimasi
                </button>
            </div>

            {{-- Result --}}
            <div class="lg:col-span-2">
                <div class="sticky top-24">
                    @if($calculated && $result)
                        <div class="ds-card p-5 space-y-4 animate-fade-in">
                            <h3 class="ds-section-subtitle">Hasil Estimasi</h3>

                            {{-- Grand Total --}}
                            <div class="rounded-card bg-primary p-5 text-center">
                                <span class="text-caption text-white/70">Total Estimasi</span>
                                <p class="text-display font-bold text-white mt-1">Rp {{ number_format($result['grand_total'], 0, ',', '.') }}</p>
                            </div>

                            {{-- Breakdown --}}
                            <div class="space-y-3 pt-2">
                                <div class="flex items-center justify-between py-2 border-b border-gray-50">
                                    <span class="text-body text-gray-500">Volume</span>
                                    <span class="text-body font-medium text-gray-700">{{ number_format($result['volume'], 2) }} m³</span>
                                </div>
                                <div class="flex items-center justify-between py-2 border-b border-gray-50">
                                    <span class="text-body text-gray-500">Dasar Perhitungan</span>
                                    <span class="text-body font-medium text-gray-700">{{ number_format($result['basis'], 2) }}</span>
                                </div>
                                <div class="flex items-center justify-between py-2 border-b border-gray-50">
                                    <div>
                                        <span class="text-body text-gray-500">Rate Digunakan</span>
                                        <span class="text-micro text-gray-400 block">{{ $result['rate_key'] }}</span>
                                    </div>
                                    <span class="text-body font-medium text-gray-700">Rp {{ number_format($result['rate_used'], 0, ',', '.') }}</span>
                                </div>
                                <div class="flex items-center justify-between py-2 border-b border-gray-50">
                                    <span class="text-body text-gray-500">Fee TAX</span>
                                    <span class="text-body font-semibold text-gray-700">Rp {{ number_format($result['fee_tax'], 0, ',', '.') }}</span>
                                </div>
                                <div class="flex items-center justify-between py-2 border-b border-gray-50">
                                    <span class="text-body text-gray-500">Fee Warehouse</span>
                                    <span class="text-body font-medium text-gray-700">Rp {{ number_format($result['fee_wh'], 0, ',', '.') }}</span>
                                </div>
                                <div class="flex items-center justify-between py-2 border-b border-gray-50">
                                    <span class="text-body text-gray-500">Fee Packing</span>
                                    <span class="text-body font-medium text-gray-700">Rp {{ number_format($result['fee_packing'], 0, ',', '.') }}</span>
                                </div>
                                @if($result['add_on'] > 0)
                                    <div class="flex items-center justify-between py-2 border-b border-gray-50">
                                        <span class="text-body text-gray-500">Add On</span>
                                        <span class="text-body font-medium text-gray-700">Rp {{ number_format($result['add_on'], 0, ',', '.') }}</span>
                                    </div>
                                @endif
                            </div>

                            <p class="text-caption text-gray-400 pt-2">* Ini adalah estimasi. Biaya aktual dapat berbeda tergantung berat dan dimensi sesungguhnya.</p>

                            <button wire:click="resetForm" class="ds-btn-ghost ds-btn-sm w-full justify-center mt-2">
                                Hitung Ulang
                            </button>
                        </div>
                    @else
                        <div class="ds-card p-8">
                            <div class="flex flex-col items-center text-center">
                                <svg class="w-16 h-16 text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                <p class="text-body text-gray-400">Masukkan data di sebelah kiri dan klik "Hitung Estimasi" untuk melihat hasil perhitungan.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
