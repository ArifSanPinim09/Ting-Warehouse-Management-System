<div class="min-h-screen bg-[#f8fafc]">
    {{-- Page Header --}}
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div>
                <h1 class="text-display text-primary">Setor Resi</h1>
                <p class="text-body text-gray-500 mt-1">Daftarkan barang baru yang akan dikirim</p>
            </div>
        </div>
    </div>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @if($boxes->isEmpty())
            <x-empty-state
                icon="box"
                title="Tidak ada box tersedia"
                text="Anda belum memiliki box dengan status Terbuka. Hubungi admin untuk membuat box baru."
                action="Kembali ke Dashboard"
                :actionUrl="route('dashboard')"
            />
        @else
            <form wire:submit="submit" class="space-y-6">
                {{-- Success Alert --}}
                @if($showSuccess)
                    <div class="rounded-card bg-emerald-50 border border-emerald-200 p-4 flex items-start gap-3 animate-fade-in">
                        <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <p class="text-body font-semibold text-emerald-800">Barang berhasil didaftarkan</p>
                            <p class="text-caption text-emerald-600 mt-0.5">Barang Anda telah terdaftar. Admin akan segera memprosesnya.</p>
                        </div>
                    </div>
                @endif

                {{-- Box Select --}}
                <div class="ds-card p-5 space-y-5">
                    <h3 class="ds-section-subtitle">Pilih Box</h3>
                    <div>
                        <label class="ds-label">Box Tujuan <span class="text-red-500">*</span></label>
                        <select wire:model="boxId" class="ds-input @error('boxId') ds-input-error @enderror">
                            <option value="">Pilih box...</option>
                            @foreach($boxes as $box)
                                <option value="{{ $box->id }}">
                                    {{ $box->display_name }} — {{ strtoupper($box->method) }} · {{ ucfirst($box->type) }}
                                </option>
                            @endforeach
                        </select>
                        @error('boxId') <p class="ds-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Item Info --}}
                <div class="ds-card p-5 space-y-5">
                    <h3 class="ds-section-subtitle">Informasi Barang</h3>

                    <div>
                        <label class="ds-label">Nama Barang <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="name" placeholder="Contoh: Baju kaos, Sepatu, dll" class="ds-input @error('name') ds-input-error @enderror" />
                        @error('name') <p class="ds-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="ds-label">Jumlah <span class="text-red-500">*</span></label>
                            <input type="number" wire:model="quantity" min="1" max="9999" class="ds-input @error('quantity') ds-input-error @enderror" />
                            @error('quantity') <p class="ds-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="ds-label">Harga (Yuan) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-caption text-gray-400">¥</span>
                                <input type="number" wire:model="priceYuan" min="0.01" step="0.01" placeholder="0.00" class="ds-input pl-7 @error('priceYuan') ds-input-error @enderror" />
                            </div>
                            @error('priceYuan') <p class="ds-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="ds-label">Nomor Resi <span class="text-red-500">*</span></label>
                        <input type="text" wire:model.live.debounce.500ms="resiNumber" placeholder="Nomor resi dari supplier China" class="ds-input @error('resiNumber') ds-input-error @enderror" />
                        @error('resiNumber') <p class="ds-error">{{ $message }}</p> @enderror

                        {{-- WH China Match Indicator --}}
                        @if($whMatchInfo)
                            <div class="mt-2 p-3 bg-emerald-50 border border-emerald-200 rounded-[8px] animate-fade-in">
                                <div class="flex items-start gap-2.5">
                                    <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <div class="flex-1">
                                        <p class="text-body font-semibold text-emerald-800">Resi ditemukan di data gudang China!</p>
                                        <p class="text-caption text-emerald-700 mt-1">Data akan otomatis terhubung saat Anda submit.</p>
                                        <div class="mt-2 grid grid-cols-2 gap-2 text-caption">
                                            <div>
                                                <span class="text-emerald-600">Tanggal:</span>
                                                <span class="font-medium text-emerald-800">{{ $whMatchInfo['tanggal'] }}</span>
                                            </div>
                                        </div>
                                        @if($whMatchInfo['foto'])
                                            <div class="mt-2">
                                                <img src="{{ $whMatchInfo['foto'] }}" alt="Foto barang dari WH China" class="w-20 h-20 object-cover rounded-[6px] border border-emerald-200">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Proof of Goods --}}
                <div class="ds-card p-5 space-y-5">
                    <h3 class="ds-section-subtitle">Bukti Barang</h3>

                    {{-- Revisi Client: Add On --}}
                    <div>
                        <label class="ds-label">Add On (opsional)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-caption text-gray-400">Rp</span>
                            <input type="number" wire:model="addOn" min="0" step="0.01" placeholder="0" class="ds-input pl-9 @error('addOn') ds-input-error @enderror" />
                        </div>
                        @error('addOn') <p class="ds-error">{{ $message }}</p> @enderror
                        <p class="ds-hint">Biaya tambahan jika ada (opsional)</p>
                    </div>

                    {{-- Revisi Client: Catatan dari Klien --}}
                    <div>
                        <label class="ds-label">Request Barang</label>
                        <div class="mt-2 space-y-2">
                            @php
                                $presetRequests = [
                                    'extra_bubble_wrap' => 'Extra Bubble Wrap',
                                    'stripping' => 'Stripping',
                                    'pisahin_album' => 'Pisahin Album',
                                    'take_out_freebies' => 'Take Out Freebies',
                                ];
                            @endphp
                            @foreach($presetRequests as $value => $label)
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" wire:model="requestTypes" value="{{ $value }}"
                                        class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-accent">
                                    <span class="text-[13px] text-gray-700">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        <p class="ds-hint mt-2">Pilih request khusus jika ada (opsional)</p>
                    </div>

                    <div>
                        <label class="ds-label">Catatan Tambahan (opsional)</label>
                        <textarea wire:model="notes" rows="2" maxlength="500" placeholder="Catatan tambahan dari klien..." class="ds-input @error('notes') ds-input-error @enderror"></textarea>
                        @error('notes') <p class="ds-error">{{ $message }}</p> @enderror
                        <p class="ds-hint">Maksimal 500 karakter</p>
                    </div>

                    <div>
                        <label class="ds-label">Foto Bukti (Proof CO) <span class="text-gray-400 font-normal">(opsional)</span></label>
                        <div
                            class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed border-gray-200 rounded-card transition-colors hover:border-accent/30 hover:bg-accent/5"
                            x-data="{ dragging: false }"
                            x-on:dragover.prevent="dragging = true"
                            x-on:dragleave.prevent="dragging = false"
                            x-on:drop.prevent="dragging = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'))"
                            :class="dragging && 'border-accent bg-accent/5'"
                        >
                            <div class="space-y-2 text-center">
                                @if($proofCo)
                                    <div class="relative inline-block">
                                        <img src="{{ $proofCo->temporaryUrl() }}" class="max-h-40 rounded-lg shadow-sm" alt="Preview" />
                                        <button type="button" wire:click="$set('proofCo', null)" class="absolute -top-3 -right-3 w-8 h-8 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 transition-colors" aria-label="Hapus foto">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                @else
                                    <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <div class="flex flex-col items-center">
                                        <label class="relative cursor-pointer">
                                            <span class="ds-btn-secondary ds-btn-sm">Pilih Foto</span>
                                            <input type="file" wire:model="proofCo" accept="image/jpeg,image/png,image/webp" class="sr-only" x-ref="fileInput" />
                                        </label>
                                        <p class="text-caption text-gray-400 mt-2">JPG, PNG, atau WebP. Maks 5MB</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @error('proofCo') <p class="ds-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Sensitive Item --}}
                <div class="ds-card p-5 space-y-5">
                    <div class="flex items-start gap-3">
                        <div class="flex items-center h-5 mt-0.5">
                            <input type="checkbox" wire:model="isSensitive" id="is_sensitive" class="rounded border-gray-300 text-accent focus:ring-accent/20" />
                        </div>
                        <div>
                            <label for="is_sensitive" class="text-body font-medium text-gray-800">Barang Sensitive</label>
                            <p class="text-caption text-gray-500 mt-0.5">Centang jika barang termasuk kategori sensitive (elektronik, baterai, cairan, kosmetik, dll)</p>
                        </div>
                    </div>

                    @if($isSensitive)
                        <div wire:transition>
                            <label class="ds-label">Jenis Sensitive <span class="text-red-500">*</span></label>
                            <select wire:model="sensitiveType" class="ds-input @error('sensitiveType') ds-input-error @enderror">
                                <option value="">Pilih jenis...</option>
                                @foreach($sensitiveTypes as $type)
                                    <option value="{{ $type }}">{{ $type }}</option>
                                @endforeach
                            </select>
                            @error('sensitiveType') <p class="ds-error">{{ $message }}</p> @enderror
                            <p class="ds-hint">⚠️ Barang sensitive kena rate lebih mahal. Pastikan informasi ini benar agar tidak kena reject.</p>
                        </div>
                    @endif
                </div>

                {{-- Submit --}}
                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('dashboard') }}" wire:navigate class="ds-btn-secondary">Batal</a>
                    <button type="submit" class="ds-btn-primary" wire:loading.attr="disabled" wire:target="submit">
                        <svg wire:loading wire:target="submit" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="submit">Daftarkan Barang</span>
                        <span wire:loading wire:target="submit">Mendaftarkan...</span>
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>
