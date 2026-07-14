<div class="min-h-screen bg-[#f8fafc]" x-data x-on:scrollToTop.window="window.scrollTo({top: 0, behavior: 'smooth'})">

    {{-- Livewire Loading State --}}
    <div wire:loading class="fixed inset-x-0 top-16 h-0.5 bg-primary/20 z-50">
        <div class="h-full bg-primary animate-pulse w-1/3 rounded-full"></div>
    </div>

    {{-- Page Header --}}
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <h1 class="text-[20px] font-bold text-primary">Warehouse China Dashboard</h1>
            <p class="text-[13px] text-gray-500 mt-1">Input and manage China warehouse data</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

        {{-- ═══════════════════════════════════════════════════ --}}
        {{-- SECTION A: INPUT FORM                               --}}
        {{-- ═══════════════════════════════════════════════════ --}}
        <div class="bg-white rounded-[12px] border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-[15px] font-semibold text-gray-900">
                    {{ $editingId ? 'Edit Data' : 'Input Data' }}
                </h2>
                <p class="text-[12px] text-gray-400 mt-0.5">Enter warehouse receipt data from China</p>
            </div>

            <form wire:submit.prevent="submitData" class="p-6 space-y-4">
                {{-- Row 1: Resi + Huruf Box --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[12px] font-medium text-gray-600 mb-1">Resi Number <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="resiNumber" maxlength="100" placeholder="e.g. SF1234567890"
                            class="w-full px-3 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors">
                        @error('resiNumber') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[12px] font-medium text-gray-600 mb-1">Box Letter <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="hurufBox" maxlength="10" placeholder="e.g. H, J, 129H"
                            class="w-full px-3 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors">
                        @error('hurufBox') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Row 2: Weight + Dimensions (optional) --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-[12px] font-medium text-gray-600 mb-1">Weight (kg) <span class="text-gray-400 font-normal">optional</span></label>
                        <input type="number" wire:model="berat" step="0.01" min="0.01" placeholder="0.00"
                            class="w-full px-3 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors tabular-nums">
                        @error('berat') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[12px] font-medium text-gray-600 mb-1">Length (cm) <span class="text-gray-400 font-normal">opt</span></label>
                        <input type="number" wire:model="panjang" step="0.01" min="0.01" placeholder="0"
                            class="w-full px-3 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors tabular-nums">
                    </div>
                    <div>
                        <label class="block text-[12px] font-medium text-gray-600 mb-1">Width (cm) <span class="text-gray-400 font-normal">opt</span></label>
                        <input type="number" wire:model="lebar" step="0.01" min="0.01" placeholder="0"
                            class="w-full px-3 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors tabular-nums">
                    </div>
                    <div>
                        <label class="block text-[12px] font-medium text-gray-600 mb-1">Height (cm) <span class="text-gray-400 font-normal">opt</span></label>
                        <input type="number" wire:model="tinggi" step="0.01" min="0.01" placeholder="0"
                            class="w-full px-3 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors tabular-nums">
                    </div>
                </div>

                {{-- Row 3: Service Fee (Yuan) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[12px] font-medium text-gray-600 mb-1">Service Fee (¥) <span class="text-red-500">*</span></label>
                        <input type="number" wire:model="serviceFeeYuan" step="0.01" min="0" placeholder="0.00"
                            class="w-full px-3 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors tabular-nums">
                        @if($this->serviceFeeIdr !== null)
                            <p class="text-[11px] text-gray-500 mt-1">= Rp {{ number_format($this->serviceFeeIdr, 0, ',', '.') }}</p>
                        @endif
                        @if($kursYuan > 0)
                            <p class="text-[11px] text-gray-400 mt-0.5">Rate: 1 ¥ = Rp {{ number_format($kursYuan, 0, ',', '.') }}</p>
                        @else
                            <p class="text-[11px] text-amber-500 mt-0.5">⚠ No exchange rate set. Please ask admin to set the rate.</p>
                        @endif
                        @error('serviceFeeYuan') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[12px] font-medium text-gray-600 mb-1">Photo "Arrive China" <span class="text-gray-400 font-normal">optional (max 2)</span></label>
                        <input type="file" wire:model="fotoArrivedChina" multiple accept="image/*"
                            class="w-full px-3 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:text-[12px] file:font-medium file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                        @error('fotoArrivedChina.*') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                        @if(!empty($fotoArrivedChina))
                            <div class="flex gap-2 mt-2">
                                @foreach($fotoArrivedChina as $foto)
                                    @if($foto)
                                        <img src="{{ $foto->temporaryUrl() }}" class="w-16 h-16 object-cover rounded-[8px] border border-gray-200">
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <p class="text-[11px] text-gray-400">Weight and dimensions can be filled later when goods arrive in Indonesia.</p>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                    @if($editingId)
                        <button type="button" wire:click="cancelEdit"
                            class="px-4 py-2 text-[13px] font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-[8px] transition-colors">
                            Cancel
                        </button>
                    @endif
                    <button type="submit"
                        class="px-4 py-2 text-[13px] font-medium text-white bg-primary rounded-[8px] hover:bg-primary-light transition-colors inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ $editingId ? 'Update Data' : 'Submit Data' }}
                    </button>
                </div>
            </form>
        </div>

        {{-- ═══════════════════════════════════════════════════ --}}
        {{-- SECTION B: DATA TABLE                               --}}
        {{-- ═══════════════════════════════════════════════════ --}}
        <div class="bg-white rounded-[12px] border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-[15px] font-semibold text-gray-900">WH China Data</h2>
                        <p class="text-[12px] text-gray-400 mt-0.5">All warehouse receipt records</p>
                    </div>
                    <div class="relative w-56">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" wire:model.live.debounce.300ms="search"
                            placeholder="Search resi or box letter..."
                            class="w-full pl-10 pr-4 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors placeholder:text-gray-400">
                    </div>
                </div>
            </div>

            @if($records->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                        <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <h3 class="text-[14px] font-semibold text-gray-700 mb-1">No data yet</h3>
                    <p class="text-[13px] text-gray-500">Submit your first warehouse receipt using the form above.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-gray-100 bg-gray-50/50">
                                <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Resi</th>
                                <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Box</th>
                                <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Weight CN</th>
                                <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Service Fee (IDR)</th>
                                <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Photo</th>
                                <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Input Date</th>
                                <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($records as $i => $record)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-5 py-3 text-[13px] text-gray-500">{{ $records->firstItem() + $i }}</td>
                                    <td class="px-5 py-3 text-[13px] font-semibold text-gray-900">{{ $record->resi_number }}</td>
                                    <td class="px-5 py-3">
                                        @if($record->huruf_box)
                                            <span class="text-[12px] font-semibold text-gray-700 bg-gray-100 px-2 py-0.5 rounded">{{ $record->huruf_box }}</span>
                                        @else
                                            <span class="text-[13px] text-gray-300">—</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-[13px] text-gray-700">
                                        {{ $record->berat ? number_format($record->berat, 2) . ' kg' : '—' }}
                                    </td>
                                    <td class="px-5 py-3 text-[13px] text-gray-700 tabular-nums">
                                        {{ $record->biaya_jasa ? 'Rp ' . number_format($record->biaya_jasa, 0, ',', '.') : '—' }}
                                    </td>
                                    <td class="px-5 py-3">
                                        @if($record->foto_arrived_china)
                                            <a href="{{ Storage::url($record->foto_arrived_china) }}" target="_blank"
                                                class="text-[12px] text-accent hover:underline font-medium">View</a>
                                        @else
                                            <span class="text-[13px] text-gray-300">—</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-[13px] text-gray-500">{{ $record->created_at->format('d M Y H:i') }}</td>
                                    <td class="px-5 py-3">
                                        <div class="flex items-center gap-1">
                                            <button wire:click="editData({{ $record->id }})"
                                                class="p-1.5 text-gray-400 hover:text-primary hover:bg-primary/5 rounded-[6px] transition-colors"
                                                title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>
                                            <button wire:click="deleteData({{ $record->id }})" wire:confirm="Are you sure you want to delete this record?"
                                                class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-[6px] transition-colors"
                                                title="Delete">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($records->hasPages())
                    <div class="px-5 py-4 border-t border-gray-100">
                        {{ $records->links() }}
                    </div>
                @endif
            @endif
        </div>

    </div>
</div>
