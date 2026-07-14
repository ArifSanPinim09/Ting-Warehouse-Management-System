<div class="min-h-screen bg-[#f8fafc]">
    {{-- Page Header --}}
    <div class="bg-white border-b border-gray-100">
        <div class="mx-auto w-full max-w-[1500px] px-4 md:px-6 lg:px-8 xl:px-10 2xl:px-12 py-6">
            <div>
                <h1 class="text-[22px] font-bold text-gray-900 tracking-tight">Est Update</h1>
                <p class="text-body text-gray-500 mt-0.5">Perbarui estimasi pengiriman (ETD/ETA) untuk box aktif</p>
            </div>
        </div>
    </div>

    <div class="mx-auto w-full max-w-[1500px] px-4 md:px-6 lg:px-8 xl:px-10 2xl:px-12 py-6 space-y-4">

        {{-- Search --}}
        <div class="bg-white rounded-[12px] border border-gray-100 p-4">
            <div class="relative max-w-lg">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari tracking number, batch, atau customer..." class="w-full pl-10 pr-4 py-2.5 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors placeholder:text-gray-400">
            </div>
        </div>

        {{-- Box List --}}
        @if($boxes->isEmpty())
            <div class="bg-white rounded-[12px] border border-gray-100">
                <x-empty-state
                    icon="box"
                    title="Tidak ada box aktif"
                    text="Tidak ada box yang perlu diupdate estimasinya saat ini."
                />
            </div>
        @else
            {{-- Desktop Table --}}
            <div class="hidden md:block bg-white rounded-[12px] border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Box</th>
                                <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Customer</th>
                                <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Tipe</th>
                                <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                                <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">ETD</th>
                                <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">ETA</th>
                                <th class="text-right px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($boxes as $box)
                                <tr class="hover:bg-gray-50/50 transition-colors {{ $selectedBoxId === $box->id ? 'bg-blue-50/30' : '' }}">
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-[8px] bg-gray-100 flex items-center justify-center flex-shrink-0">
                                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                            </div>
                                            <div>
                                                <p class="text-body font-semibold text-gray-900">{{ $box->display_name }}</p>
                                                <p class="text-caption text-gray-400 capitalize">{{ $box->type }} · {{ strtoupper($box->method) }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span class="text-body text-gray-700">{{ $box->customer->name ?? '-' }}</span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span class="text-caption font-medium text-gray-600 capitalize">{{ $box->type }}</span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <x-status-badge :status="$box->status" />
                                    </td>
                                    <td class="px-5 py-3.5">
                                        @if($box->etd)
                                            <span class="text-body text-gray-700">{{ $box->etd->format('d M Y') }}</span>
                                        @else
                                            <span class="text-caption text-gray-400 italic">Belum diset</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5">
                                        @if($box->eta)
                                            <span class="text-body text-gray-700">{{ $box->eta->format('d M Y') }}</span>
                                        @else
                                            <span class="text-caption text-gray-400 italic">Belum diset</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-right">
                                        <button wire:click="selectBox({{ $box->id }})" class="inline-flex items-center gap-1.5 px-3 py-2 min-h-[44px] text-caption font-medium text-accent hover:text-primary bg-accent/5 hover:bg-accent/10 rounded-[6px] transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Update
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($boxes->hasPages())
                    <div class="px-5 py-3 border-t border-gray-100">
                        {{ $boxes->links() }}
                    </div>
                @endif
            </div>

            {{-- Mobile Cards --}}
            <div class="md:hidden space-y-3">
                @foreach($boxes as $box)
                    <div class="bg-white rounded-[12px] border border-gray-100 p-4 {{ $selectedBoxId === $box->id ? 'border-blue-300 bg-blue-50/20' : '' }}">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-[10px] bg-gray-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                </div>
                                <div>
                                    <p class="text-body font-semibold text-gray-900">{{ $box->display_name }}</p>
                                    <p class="text-caption text-gray-500">{{ $box->customer->name ?? '-' }}</p>
                                </div>
                            </div>
                            <x-status-badge :status="$box->status" />
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-caption py-3 border-t border-gray-100">
                            <div>
                                <span class="text-gray-400">ETD:</span>
                                <span class="font-medium {{ $box->etd ? 'text-gray-700' : 'text-gray-400 italic' }}">{{ $box->etd ? $box->etd->format('d M Y') : 'Belum' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-400">ETA:</span>
                                <span class="font-medium {{ $box->eta ? 'text-gray-700' : 'text-gray-400 italic' }}">{{ $box->eta ? $box->eta->format('d M Y') : 'Belum' }}</span>
                            </div>
                        </div>
                        <button wire:click="selectBox({{ $box->id }})" class="w-full mt-2 flex items-center justify-center gap-1.5 px-3 py-2 text-caption font-medium text-accent hover:text-primary bg-accent/5 hover:bg-accent/10 rounded-[8px] transition-colors">
                            Update Estimasi
                        </button>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Edit Estimate Modal --}}
    @if($showForm)
        <div class="fixed inset-0 z-50 overflow-y-auto" wire:click.self="cancelForm">
            <div class="fixed inset-0 bg-black/30 transition-opacity"></div>
            <div class="flex min-h-full items-end sm:items-center justify-center p-4">
                <div class="relative bg-white rounded-[16px] shadow-modal w-full max-w-md p-6 transform transition-all" @click.stop>
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-[16px] font-semibold text-gray-900">Update Estimasi</h3>
                        <button wire:click="cancelForm" class="p-2 min-w-[44px] min-h-[44px] flex items-center justify-center rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-caption font-medium text-gray-700 mb-1.5">ETD (Estimated Time Departure)</label>
                            <input type="date" wire:model="etd" class="w-full px-3 py-2.5 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors">
                            @error('etd') <p class="text-caption text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-caption font-medium text-gray-700 mb-1.5">ETA (Estimated Time Arrival)</label>
                            <input type="date" wire:model="eta" class="w-full px-3 py-2.5 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors">
                            @error('eta') <p class="text-caption text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-caption font-medium text-gray-700 mb-1.5">Catatan (opsional)</label>
                            <textarea wire:model="estNote" rows="2" placeholder="Tambahkan catatan perubahan..." class="w-full px-3 py-2.5 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors resize-none"></textarea>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 justify-end mt-6">
                        <button wire:click="cancelForm" class="px-4 py-2.5 text-body font-medium text-gray-700 bg-white border border-gray-200 rounded-[8px] hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button wire:click="saveEstimate" class="px-4 py-2.5 text-body font-medium text-white bg-primary rounded-[8px] hover:bg-primary-light transition-colors">
                            Simpan Estimasi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
