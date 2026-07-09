<div class="min-h-screen bg-[#f8fafc]">
    {{-- Page Header --}}
    <div class="bg-white border-b border-gray-100">
        <div class="mx-auto w-full max-w-[1500px] px-4 md:px-6 lg:px-8 xl:px-10 2xl:px-12 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-[22px] font-bold text-gray-900 tracking-tight">Pengaturan Rate</h1>
                    <p class="text-body text-gray-500 mt-0.5">Kelola rate pengiriman dan biaya packing</p>
                </div>
                @if($lastUpdatedAt)
                    <span class="hidden sm:flex items-center gap-1.5 text-caption text-gray-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Terakhir diupdate: {{ $lastUpdatedAt }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="mx-auto w-full max-w-[1500px] px-4 md:px-6 lg:px-8 xl:px-10 2xl:px-12 py-6 space-y-6">

        {{-- Tabs --}}
        <div class="bg-white rounded-[12px] border border-gray-100 p-1 inline-flex gap-1">
            @php
                $tabs = [
                    ['key' => 'sharing', 'label' => 'Rate Sharing', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>'],
                    ['key' => 'direct', 'label' => 'Rate Direct', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>'],
                    ['key' => 'packing', 'label' => 'Fee Packing', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>'],
                ];
            @endphp
            @foreach($tabs as $tab)
                <button
                    wire:click="setTab('{{ $tab['key'] }}')"
                    class="flex items-center gap-2 px-4 py-2 rounded-[8px] text-body font-medium transition-all duration-150 {{ $activeTab === $tab['key'] ? 'bg-primary text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                >
                    <svg class="w-4 h-4 {{ $activeTab === $tab['key'] ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $tab['icon'] !!}</svg>
                    {{ $tab['label'] }}
                </button>
            @endforeach
        </div>

        {{-- Sharing Rates Tab --}}
        @if($activeTab === 'sharing')
            <div class="bg-white rounded-[12px] border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h3 class="text-[15px] font-semibold text-gray-900">Rate Sharing</h3>
                    <p class="text-body text-gray-500 mt-0.5">Rate per kg untuk pengiriman sharing (normal & sensitive)</p>
                </div>
                <div class="p-6 space-y-8">
                    {{-- Normal Sharing --}}
                    <div>
                        <h4 class="text-body font-semibold text-gray-700 mb-4 flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                            Sharing Normal
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            @php
                                $fields = [
                                    ['key' => 'rate_sharing_air_berat', 'label' => 'Air — Berat', 'color' => 'blue'],
                                    ['key' => 'rate_sharing_air_volume', 'label' => 'Air — Volume', 'color' => 'blue'],
                                    ['key' => 'rate_sharing_sea_berat', 'label' => 'Sea — Berat', 'color' => 'cyan'],
                                    ['key' => 'rate_sharing_sea_volume', 'label' => 'Sea — Volume', 'color' => 'cyan'],
                                ];
                            @endphp
                            @foreach($fields as $field)
                                <div>
                                    <label class="block text-caption font-medium text-gray-600 mb-1.5">{{ $field['label'] }}</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-caption text-gray-400">Rp</span>
                                        <input type="number" wire:model="{{ $field['key'] }}" min="1" max="99999" class="w-full pl-10 pr-3 py-2.5 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors">
                                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-caption text-gray-400">/kg</span>
                                    </div>
                                    @error($field['key']) <p class="text-caption text-red-500 mt-1">{{ $message }}</p> @enderror
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Sensitive Sharing --}}
                    <div>
                        <h4 class="text-body font-semibold text-gray-700 mb-4 flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-amber-500"></div>
                            Sharing Sensitive
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            @php
                                $sensitiveFields = [
                                    ['key' => 'rate_sharing_sensitive_air_berat', 'label' => 'Air — Berat'],
                                    ['key' => 'rate_sharing_sensitive_air_volume', 'label' => 'Air — Volume'],
                                    ['key' => 'rate_sharing_sensitive_sea_berat', 'label' => 'Sea — Berat'],
                                    ['key' => 'rate_sharing_sensitive_sea_volume', 'label' => 'Sea — Volume'],
                                ];
                            @endphp
                            @foreach($sensitiveFields as $field)
                                <div>
                                    <label class="block text-caption font-medium text-gray-600 mb-1.5">{{ $field['label'] }}</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-caption text-gray-400">Rp</span>
                                        <input type="number" wire:model="{{ $field['key'] }}" min="1" max="99999" class="w-full pl-10 pr-3 py-2.5 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors">
                                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-caption text-gray-400">/kg</span>
                                    </div>
                                    @error($field['key']) <p class="text-caption text-red-500 mt-1">{{ $message }}</p> @enderror
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-end">
                    <button
                        wire:click="confirmSave('sharing')"
                        class="px-5 py-2.5 text-body font-medium text-white bg-primary rounded-[8px] hover:bg-primary-light transition-colors"
                    >
                        Simpan Rate Sharing
                    </button>
                </div>
            </div>
        @endif

        {{-- Direct Rates Tab --}}
        @if($activeTab === 'direct')
            <div class="bg-white rounded-[12px] border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h3 class="text-[15px] font-semibold text-gray-900">Rate Direct</h3>
                    <p class="text-body text-gray-500 mt-0.5">Rate per kg untuk pengiriman direct (tanpa sensitive)</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        @php
                            $directFields = [
                                ['key' => 'rate_direct_air_berat', 'label' => 'Air — Berat', 'color' => 'blue'],
                                ['key' => 'rate_direct_air_volume', 'label' => 'Air — Volume', 'color' => 'blue'],
                                ['key' => 'rate_direct_sea_berat', 'label' => 'Sea — Berat', 'color' => 'cyan'],
                                ['key' => 'rate_direct_sea_volume', 'label' => 'Sea — Volume', 'color' => 'cyan'],
                            ];
                        @endphp
                        @foreach($directFields as $field)
                            <div>
                                <label class="block text-caption font-medium text-gray-600 mb-1.5">{{ $field['label'] }}</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-caption text-gray-400">Rp</span>
                                    <input type="number" wire:model="{{ $field['key'] }}" min="1" max="99999" class="w-full pl-10 pr-3 py-2.5 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-caption text-gray-400">/kg</span>
                                </div>
                                @error($field['key']) <p class="text-caption text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-end">
                    <button
                        wire:click="confirmSave('direct')"
                        class="px-5 py-2.5 text-body font-medium text-white bg-primary rounded-[8px] hover:bg-primary-light transition-colors"
                    >
                        Simpan Rate Direct
                    </button>
                </div>
            </div>
        @endif

        {{-- Fee Packing Tab --}}
        @if($activeTab === 'packing')
            <div class="bg-white rounded-[12px] border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h3 class="text-[15px] font-semibold text-gray-900">Fee Packing</h3>
                    <p class="text-body text-gray-500 mt-0.5">Tiered pricing berdasarkan berat barang</p>
                </div>
                <div class="p-6 space-y-6">
                    {{-- Tier Visual --}}
                    <div class="bg-gray-50 rounded-[10px] p-5">
                        <p class="text-caption font-semibold text-gray-500 uppercase tracking-wide mb-4">Struktur Tier</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div class="text-center">
                                <div class="w-full h-2 bg-emerald-200 rounded-full mb-2"></div>
                                <p class="text-caption font-semibold text-gray-700">0 — 150 kg</p>
                                <p class="text-caption text-gray-500">Flat rate</p>
                            </div>
                            <div class="text-center">
                                <div class="w-full h-2 bg-blue-200 rounded-full mb-2"></div>
                                <p class="text-caption font-semibold text-gray-700">151 — 1.000 kg</p>
                                <p class="text-caption text-gray-500">Flat rate</p>
                            </div>
                            <div class="text-center">
                                <div class="w-full h-2 bg-amber-200 rounded-full mb-2"></div>
                                <p class="text-caption font-semibold text-gray-700">1.001 — 2.000 kg</p>
                                <p class="text-caption text-gray-500">Flat rate</p>
                            </div>
                            <div class="text-center">
                                <div class="w-full h-2 bg-red-200 rounded-full mb-2"></div>
                                <p class="text-caption font-semibold text-gray-700">&gt; 2.000 kg</p>
                                <p class="text-caption text-gray-500">+ Extra per kg</p>
                            </div>
                        </div>
                    </div>

                    {{-- Fee Inputs --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        @php
                            $packingFields = [
                                ['key' => 'fee_packing_150', 'label' => 'Fee 0—150 kg', 'desc' => 'Flat rate untuk berat sampai 150 kg'],
                                ['key' => 'fee_packing_1000', 'label' => 'Fee 151—1.000 kg', 'desc' => 'Flat rate untuk berat 151–1000 kg'],
                                ['key' => 'fee_packing_2000', 'label' => 'Fee 1.001—2.000 kg', 'desc' => 'Flat rate untuk berat 1001–2000 kg'],
                                ['key' => 'fee_packing_extra_per_kg', 'label' => 'Extra per kg', 'desc' => 'Biaya per kg untuk kelebihan di atas 2000 kg'],
                            ];
                        @endphp
                        @foreach($packingFields as $field)
                            <div>
                                <label class="block text-caption font-medium text-gray-700 mb-1.5">{{ $field['label'] }}</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-caption text-gray-400">Rp</span>
                                    <input type="number" wire:model="{{ $field['key'] }}" min="0" max="999999" class="w-full pl-10 pr-3 py-2.5 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors">
                                </div>
                                <p class="text-caption text-gray-400 mt-1">{{ $field['desc'] }}</p>
                                @error($field['key']) <p class="text-caption text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-end">
                    <button
                        wire:click="confirmSave('packing')"
                        class="px-5 py-2.5 text-body font-medium text-white bg-primary rounded-[8px] hover:bg-primary-light transition-colors"
                    >
                        Simpan Fee Packing
                    </button>
                </div>
            </div>
        @endif
    </div>

    {{-- Confirmation Modal --}}
    @if($showConfirmModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" wire:click.self="$set('showConfirmModal', false)">
            <div class="fixed inset-0 bg-black/30 transition-opacity"></div>
            <div class="flex min-h-full items-end sm:items-center justify-center p-4">
                <div class="relative bg-white rounded-[16px] shadow-modal w-full max-w-md p-6 transform transition-all" @click.stop>
                    <div class="flex items-start gap-4 mb-5">
                        <div class="w-10 h-10 rounded-full bg-amber-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-[16px] font-semibold text-gray-900">Konfirmasi Perubahan Rate</h3>
                            <p class="text-body text-gray-500 mt-1">Perubahan rate akan langsung berlaku untuk invoice baru. Invoice yang sudah dibuat tidak akan terpengaruh.</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 justify-end">
                        <button wire:click="$set('showConfirmModal', false)" class="px-4 py-2.5 text-body font-medium text-gray-700 bg-white border border-gray-200 rounded-[8px] hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        @if($confirmSection === 'sharing')
                            <button wire:click="saveSharingRates" class="px-4 py-2.5 text-body font-medium text-white bg-primary rounded-[8px] hover:bg-primary-light transition-colors">Konfirmasi</button>
                        @elseif($confirmSection === 'direct')
                            <button wire:click="saveDirectRates" class="px-4 py-2.5 text-body font-medium text-white bg-primary rounded-[8px] hover:bg-primary-light transition-colors">Konfirmasi</button>
                        @elseif($confirmSection === 'packing')
                            <button wire:click="saveFeePacking" class="px-4 py-2.5 text-body font-medium text-white bg-primary rounded-[8px] hover:bg-primary-light transition-colors">Konfirmasi</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
