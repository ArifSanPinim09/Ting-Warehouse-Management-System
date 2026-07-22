<div class="min-h-screen bg-[#f8fafc]">

    {{-- Page Header --}}
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-[900px] mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <h1 class="text-[20px] font-bold text-primary">Cari Resi 🔍</h1>
            <p class="text-[13px] text-gray-500 mt-1">Cek status resi Anda — masukkan nomor resi untuk melihat detail</p>
        </div>
    </div>

    <div class="max-w-[900px] mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

        {{-- Search Box --}}
        <div class="bg-white rounded-[12px] border border-gray-100 p-6">
            <div class="flex gap-2">
                <input
                    wire:model="searchQuery"
                    wire:keydown.enter="search"
                    type="text"
                    placeholder="Masukkan nomor resi... (minimal 3 karakter)"
                    class="flex-1 border border-gray-200 rounded-[8px] px-4 py-3 text-[14px] focus:border-primary focus:ring-1 focus:ring-primary outline-none"
                >
                <button
                    wire:click="search"
                    class="bg-primary text-white text-[14px] font-medium rounded-[8px] px-6 py-3 hover:bg-primary/90 transition-colors"
                >
                    Cari
                </button>
            </div>
            @if($searched && !$result && strlen($searchQuery) >= 3)
                <div class="mt-4 bg-red-50 border border-red-100 rounded-[8px] p-4">
                    <p class="text-[13px] text-red-600 font-medium">❌ Resi tidak ditemukan</p>
                    <p class="text-[12px] text-red-400 mt-1">Pastikan nomor resi benar atau hubungi admin.</p>
                </div>
            @endif
            @if($searched && strlen($searchQuery) < 3)
                <div class="mt-4 bg-yellow-50 border border-yellow-100 rounded-[8px] p-4">
                    <p class="text-[13px] text-yellow-700 font-medium">⚠️ Minimal 3 karakter</p>
                </div>
            @endif
        </div>

        {{-- Result Card --}}
        @if($result)
            <div class="bg-white rounded-[12px] border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/80">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-[15px] font-bold text-gray-900">Resi: {{ $result['resi_number'] }}</h2>
                            <p class="text-[12px] text-gray-500 mt-0.5">{{ $result['name'] }} • {{ $result['quantity'] }} item(s)</p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[13px] font-semibold
                                {{ str_contains(strtolower($result['status_display']), 'lelang') ? 'bg-red-100 text-red-700' :
                                   str_contains(strtolower($result['status_display']), 'hold') ? 'bg-orange-100 text-orange-700' :
                                   str_contains(strtolower($result['status_display']), 'notuan') ? 'bg-yellow-100 text-yellow-700' :
                                   str_contains(strtolower($result['status_display']), 'belum') ? 'bg-blue-100 text-blue-700' :
                                   'bg-green-100 text-green-700' }}">
                                {{ $result['status_display'] }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="p-6 grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-[12px] text-gray-400 font-medium uppercase">Box</p>
                        <p class="text-[14px] font-medium text-gray-900">{{ $result['box_name'] }}</p>
                    </div>
                    <div>
                        <p class="text-[12px] text-gray-400 font-medium uppercase">Method</p>
                        <p class="text-[14px] font-medium text-gray-900">{{ strtoupper($result['box_method']) }}</p>
                    </div>
                    <div>
                        <p class="text-[12px] text-gray-400 font-medium uppercase">Box Status</p>
                        <p class="text-[14px] font-medium text-gray-900">{{ $result['box_status'] }}</p>
                    </div>
                    <div>
                        <p class="text-[12px] text-gray-400 font-medium uppercase">Kategori</p>
                        <div class="flex gap-1">
                            @if($result['is_sensitive'])
                                <span class="text-[11px] bg-purple-100 text-purple-700 px-2 py-0.5 rounded">Sensitive</span>
                            @endif
                            @if($result['is_garment'])
                                <span class="text-[11px] bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded">Garment</span>
                            @endif
                            @if(!$result['is_sensitive'] && !$result['is_garment'])
                                <span class="text-[14px] text-gray-600">Normal</span>
                            @endif
                        </div>
                    </div>
                </div>

                @if($result['foto_china'])
                    <div class="px-6 pb-6">
                        <p class="text-[12px] text-gray-400 font-medium uppercase mb-2">Foto Barang</p>
                        <img src="{{ $result['foto_china'] }}" alt="Foto barang" class="w-32 h-32 object-cover rounded-[8px] border border-gray-100">
                    </div>
                @endif
            </div>
        @endif

    </div>
</div>
