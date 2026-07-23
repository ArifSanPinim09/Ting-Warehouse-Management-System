<div class="space-y-6">
    {{-- Header --}}
    <div>
        <h1 class="text-xl font-bold text-gray-800">Mesin Pencari Resi</h1>
        <p class="text-sm text-gray-500 mt-1">Cari resi untuk cek kelengkapan data customer</p>
    </div>

    {{-- Search Bar --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex gap-3">
            <input
                type="text"
                wire:model.live.debounce.300ms="searchQuery"
                wire:keydown.enter="search"
                placeholder="Masukkan nomor resi... (minimal 3 karakter)"
                class="flex-1 px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:border-accent focus:ring-2 focus:ring-accent/30"
            >
            <button
                wire:click="search"
                class="px-6 py-2.5 text-sm font-medium text-white bg-accent rounded-lg hover:bg-accent/90 transition-colors"
            >
                Cari
            </button>
        </div>
    </div>

    {{-- Results --}}
    @if($searched)
        @if($result)
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-green-50 border-b border-green-100">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="font-medium text-green-700">Resi ditemukan!</span>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    {{-- Resi Number --}}
                    <div class="flex items-center gap-3 pb-3 border-b border-gray-100">
                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">No. Resi</span>
                        <span class="text-lg font-mono font-bold text-gray-800">{{ $result['resi_number'] }}</span>
                    </div>

                    {{-- Grid Data --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-xs text-gray-500">Nama Barang</span>
                            <p class="text-sm font-medium text-gray-800">{{ $result['name'] }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500">Jumlah</span>
                            <p class="text-sm font-medium text-gray-800">{{ $result['quantity'] }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500">Customer</span>
                            <p class="text-sm font-medium text-gray-800">{{ $result['customer_name'] }}</p>
                            <p class="text-xs text-gray-400">Code: {{ $result['customer_code'] }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500">Status</span>
                            <p class="text-sm font-medium text-gray-800">{{ $result['status_display'] }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500">Box</span>
                            <p class="text-sm font-medium text-gray-800">{{ $result['box_code'] }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500">Method</span>
                            <p class="text-sm font-medium text-gray-800">{{ strtoupper($result['box_method']) }}</p>
                        </div>
                    </div>

                    {{-- Tags --}}
                    <div class="flex gap-2 pt-2">
                        @if($result['is_sensitive'])
                            <span class="px-2.5 py-1 text-xs font-medium text-orange-700 bg-orange-100 rounded-full">Sensitive</span>
                        @endif
                        @if($result['is_garment'])
                            <span class="px-2.5 py-1 text-xs font-medium text-blue-700 bg-blue-100 rounded-full">Garment</span>
                        @endif
                    </div>

                    {{-- Foto China --}}
                    @if($result['foto_china'])
                        <div class="pt-3 border-t border-gray-100">
                            <span class="text-xs text-gray-500">Foto Arrived China</span>
                            <div class="mt-2">
                                <img src="{{ $result['foto_china'] }}" alt="Foto China" class="w-32 h-32 object-cover rounded-lg border border-gray-200">
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @else
            <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
                <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <p class="text-sm text-gray-500 mt-3">Resi tidak ditemukan</p>
                <p class="text-xs text-gray-400 mt-1">Cek kembali nomor resi yang dimasukkan</p>
            </div>
        @endif
    @endif
</div>
