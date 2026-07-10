<div class="min-h-screen bg-[#f8fafc]">
    {{-- Page Header --}}
    <div class="bg-white border-b border-gray-100">
        <div class="mx-auto w-full max-w-[1500px] px-4 md:px-6 lg:px-8 xl:px-10 2xl:px-12 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-[22px] font-bold text-gray-900 tracking-tight">History Kurs</h1>
                    <p class="text-body text-gray-500 mt-0.5">Kelola kurs Yuan → Rupiah berdasarkan tanggal</p>
                </div>
                <div class="flex items-center gap-3">
                    @if($latestKurs)
                        <span class="hidden sm:flex items-center gap-1.5 text-caption text-gray-400">
                            Kurs terbaru: <span class="font-semibold text-gray-700">Rp {{ number_format($latestKurs->kurs_value, 0, ',', '.') }}</span>
                        </span>
                    @endif
                    <button
                        wire:click="toggleForm"
                        class="inline-flex items-center gap-2 px-4 py-2.5 text-body font-medium text-white bg-primary rounded-[8px] hover:bg-primary-light transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $showForm ? 'M6 18L18 6M6 6l12 12' : 'M12 4v16m8-8H4' }}"/>
                        </svg>
                        {{ $showForm ? 'Tutup' : 'Input Kurs' }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="mx-auto w-full max-w-[1500px] px-4 md:px-6 lg:px-8 xl:px-10 2xl:px-12 py-6 space-y-6">

        {{-- Input Form (§7.2) --}}
        @if($showForm)
            <div class="bg-white rounded-[12px] border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h3 class="text-[15px] font-semibold text-gray-900">{{ $editingId ? 'Edit Kurs' : 'Input Kurs Baru' }}</h3>
                    <p class="text-body text-gray-500 mt-0.5">Masukkan nilai kurs dan tanggal berlaku</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-lg">
                        {{-- kurs_value --}}
                        <div>
                            <label for="kurs_value" class="block text-caption font-medium text-gray-700 mb-1.5">
                                Nilai Kurs <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-body text-gray-400">Rp</span>
                                <input
                                    id="kurs_value"
                                    type="number"
                                    wire:model="kurs_value"
                                    min="1"
                                    max="99999"
                                    placeholder="2660"
                                    class="w-full pl-10 pr-4 py-2.5 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors"
                                >
                            </div>
                            @error('kurs_value') <p class="text-caption text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- effective_date --}}
                        <div>
                            <label for="effective_date" class="block text-caption font-medium text-gray-700 mb-1.5">
                                Tanggal Berlaku <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="effective_date"
                                type="date"
                                wire:model="effective_date"
                                max="{{ now()->format('Y-m-d') }}"
                                class="w-full px-4 py-2.5 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors"
                            >
                            @error('effective_date') <p class="text-caption text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-gray-100 flex items-center justify-end">
                        <button
                            wire:click="saveKurs"
                            class="px-5 py-2.5 text-body font-medium text-white bg-primary rounded-[8px] hover:bg-primary-light transition-colors"
                        >
                            {{ $editingId ? 'Update Kurs' : 'Simpan Kurs' }}
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- Kurs History Table --}}
        <div class="bg-white rounded-[12px] border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <h3 class="text-[15px] font-semibold text-gray-900">Riwayat Kurs</h3>
                <p class="text-body text-gray-500 mt-0.5">Daftar semua kurs yang pernah diinput</p>
            </div>

            @if($history->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                        <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-body font-semibold text-gray-700 mb-1">Belum ada history kurs</h3>
                    <p class="text-body text-gray-500">Klik "Input Kurs" untuk menambahkan kurs pertama</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-gray-100 bg-gray-50/50">
                                <th class="px-6 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-6 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wider">Nilai Kurs</th>
                                <th class="px-6 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wider">Tanggal Berlaku</th>
                                <th class="px-6 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wider">Diinput Oleh</th>
                                <th class="px-6 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wider">Waktu Input</th>
                                <th class="px-6 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($history as $i => $kurs)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-3.5 text-body text-gray-500">
                                        {{ $history->total() - $history->firstItem() - $i + 1 }}
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <span class="text-body font-semibold text-gray-900">Rp {{ number_format($kurs->kurs_value, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="px-6 py-3.5 text-body text-gray-700">
                                        {{ $kurs->effective_date->translatedFormat('d F Y') }}
                                    </td>
                                    <td class="px-6 py-3.5 text-body text-gray-700">
                                        {{ $kurs->inputBy?->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-3.5 text-body text-gray-500">
                                        {{ $kurs->created_at->format('d M Y H:i') }}
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <button
                                            wire:click="editKurs({{ $kurs->id }})"
                                            class="text-accent hover:text-accent-dark text-caption font-medium transition-colors"
                                        >
                                            Edit
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($history->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $history->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
