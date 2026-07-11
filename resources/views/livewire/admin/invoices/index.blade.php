<div class="min-h-screen bg-[#f8fafc]">
    {{-- Page Header --}}
    <div class="bg-white border-b border-gray-100">
        <div class="mx-auto w-full max-w-[1500px] px-4 md:px-6 lg:px-8 xl:px-10 2xl:px-12 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-[22px] font-bold text-gray-900 tracking-tight">Generate Invoice</h1>
                    <p class="text-body text-gray-500 mt-0.5">Buat invoice untuk box yang sudah tiba di Indonesia</p>
                </div>
                <button
                    wire:click="openGenerateModal"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary text-white text-body font-medium rounded-[8px] hover:bg-primary-light transition-colors shadow-sm"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span class="hidden sm:inline">Buat Invoice</span>
                </button>
            </div>
        </div>
    </div>

    <div class="mx-auto w-full max-w-[1500px] px-4 md:px-6 lg:px-8 xl:px-10 2xl:px-12 py-6 space-y-4">

        {{-- Filters --}}
        <div class="bg-white rounded-[12px] border border-gray-100 p-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="sm:col-span-2 relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari invoice number, customer, atau tracking..." class="w-full pl-10 pr-4 py-2.5 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors placeholder:text-gray-400">
                </div>
                <select wire:model.live="filterStatus" class="py-2.5 px-3 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors text-gray-600">
                    <option value="">Semua Status</option>
                    <option value="waiting_payment">Menunggu Pembayaran</option>
                    <option value="waiting_verification">Menunggu Verifikasi</option>
                    <option value="verified">Terverifikasi</option>
                </select>
            </div>
        </div>

        {{-- Invoice List --}}
        <div class="flex gap-6">
            <div class="flex-1 min-w-0">
                @if($invoices->isEmpty())
                    <div class="bg-white rounded-[12px] border border-gray-100">
                        <x-empty-state
                            icon="invoice"
                            title="Belum ada invoice"
                            text="Invoice akan muncul setelah Anda membuatnya dari box yang sudah tiba."
                        />
                    </div>
                @else
                    {{-- Desktop Table --}}
                    <div class="hidden md:block bg-white rounded-[12px] border border-gray-100 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-gray-100">
                                        <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Invoice</th>
                                        <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Customer</th>
                                        <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Box</th>
                                        <th class="text-right px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Fee TAX</th>
                                        <th class="text-right px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Fee WH</th>
                                        <th class="text-right px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Fee Packing</th>
                                        <th class="text-right px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Grand Total</th>
                                        <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                                        <th class="text-right px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($invoices as $inv)
                                        <tr class="hover:bg-gray-50/50 transition-colors cursor-pointer" wire:click="selectInvoice({{ $inv->id }})">
                                            <td class="px-5 py-3.5">
                                                <span class="text-body font-semibold text-gray-900">{{ $inv->invoice_number }}</span>
                                                <p class="text-caption text-gray-400 mt-0.5">{{ $inv->created_at->format('d M Y') }}</p>
                                            </td>
                                            <td class="px-5 py-3.5">
                                                <span class="text-body text-gray-700">{{ $inv->customer->name ?? '-' }}</span>
                                            </td>
                                            <td class="px-5 py-3.5">
                                                <span class="text-body text-gray-600">{{ $inv->box->tracking_number ?? $inv->box->batch_name ?? 'Box #' . $inv->box_id }}</span>
                                            </td>
                                            <td class="px-5 py-3.5 text-right">
                                                <span class="text-body text-gray-600">Rp {{ number_format($inv->fee_tax, 0, ',', '.') }}</span>
                                            </td>
                                            <td class="px-5 py-3.5 text-right">
                                                <span class="text-body text-gray-600">Rp {{ number_format($inv->fee_wh, 0, ',', '.') }}</span>
                                            </td>
                                            <td class="px-5 py-3.5 text-right">
                                                <span class="text-body text-gray-600">Rp {{ number_format($inv->fee_packing, 0, ',', '.') }}</span>
                                            </td>
                                            <td class="px-5 py-3.5 text-right">
                                                <span class="text-body font-bold text-gray-900">Rp {{ number_format($inv->grand_total, 0, ',', '.') }}</span>
                                            </td>
                                            <td class="px-5 py-3.5">
                                                <x-status-badge :status="$inv->status" />
                                            </td>
                                            <td class="px-5 py-3.5 text-right">
                                                <button wire:click.stop="selectInvoice({{ $inv->id }})" class="text-caption font-medium text-accent hover:text-primary transition-colors">
                                                    Detail
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($invoices->hasPages())
                            <div class="px-5 py-3 border-t border-gray-100">
                                {{ $invoices->links() }}
                            </div>
                        @endif
                    </div>

                    {{-- Mobile Cards --}}
                    <div class="md:hidden space-y-3">
                        @foreach($invoices as $inv)
                            <div wire:click="selectInvoice({{ $inv->id }})" class="bg-white rounded-[12px] border border-gray-100 p-4 hover:shadow-card-hover transition-shadow cursor-pointer">
                                <div class="flex items-start justify-between mb-3">
                                    <div>
                                        <p class="text-body font-semibold text-gray-900">{{ $inv->invoice_number }}</p>
                                        <p class="text-caption text-gray-500 mt-0.5">{{ $inv->customer->name ?? '-' }}</p>
                                    </div>
                                    <x-status-badge :status="$inv->status" />
                                </div>
                                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                                    <span class="text-caption text-gray-500">{{ $inv->box->tracking_number ?? 'Box #' . $inv->box_id }}</span>
                                    <span class="text-[15px] font-bold text-gray-900">Rp {{ number_format($inv->grand_total, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Detail Panel --}}
            @if($showDetail && $selectedInvoice)
                <div class="w-full lg:w-[380px] flex-shrink-0">
                    <div class="bg-white rounded-[12px] border border-gray-100 sticky top-[88px]">
                        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="text-body font-semibold text-gray-900">Detail Invoice</h3>
                            <button wire:click="closeDetail" class="p-2 min-w-[44px] min-h-[44px] flex items-center justify-center rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <div class="p-5 space-y-4">
                            <div class="text-center pb-4 border-b border-gray-100">
                                <p class="text-caption text-gray-400 uppercase tracking-wide mb-1">Invoice Number</p>
                                <p class="text-[18px] font-bold text-gray-900">{{ $selectedInvoice->invoice_number }}</p>
                                <x-status-badge :status="$selectedInvoice->status" size="lg" class="mt-2" />
                            </div>

                            <div class="space-y-2.5">
                                <div class="flex justify-between text-body">
                                    <span class="text-gray-500">Customer</span>
                                    <span class="font-medium text-gray-800">{{ $selectedInvoice->customer->name ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between text-body">
                                    <span class="text-gray-500">Box</span>
                                    <span class="font-medium text-gray-800">{{ $selectedInvoice->box->tracking_number ?? 'Box #' . $selectedInvoice->box_id }}</span>
                                </div>
                                <div class="flex justify-between text-body">
                                    <span class="text-gray-500">Berat</span>
                                    <span class="font-medium text-gray-800">{{ $selectedInvoice->weight }} kg</span>
                                </div>
                                <div class="flex justify-between text-body">
                                    <span class="text-gray-500">Volume</span>
                                    <span class="font-medium text-gray-800">{{ $selectedInvoice->volume }} m³</span>
                                </div>
                            </div>

                            <div class="border-t border-gray-100 pt-4 space-y-2">
                                <div class="flex justify-between text-body">
                                    <span class="text-gray-500">Fee TAX</span>
                                    <span class="font-medium text-gray-800">Rp {{ number_format($selectedInvoice->fee_tax, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between text-body">
                                    <span class="text-gray-500">Fee WH</span>
                                    <span class="font-medium text-gray-800">Rp {{ number_format($selectedInvoice->fee_wh, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between text-body">
                                    <span class="text-gray-500">Fee Packing</span>
                                    <span class="font-medium text-gray-800">Rp {{ number_format($selectedInvoice->fee_packing, 0, ',', '.') }}</span>
                                </div>
                                @if($selectedInvoice->add_on > 0)
                                    <div class="flex justify-between text-body">
                                        <span class="text-gray-500">Add On</span>
                                        <span class="font-medium text-gray-800">Rp {{ number_format($selectedInvoice->add_on, 0, ',', '.') }}</span>
                                    </div>
                                @endif
                                @if($selectedInvoice->denda_total > 0)
                                    <div class="flex justify-between text-body">
                                        <span class="text-gray-500">Denda Klaim</span>
                                        <span class="font-medium text-amber-600">Rp {{ number_format($selectedInvoice->denda_total, 0, ',', '.') }}</span>
                                    </div>
                                @endif
                                <div class="flex justify-between text-[15px] font-bold pt-2 border-t border-gray-100">
                                    <span class="text-gray-700">Grand Total</span>
                                    <span class="text-primary">Rp {{ number_format($selectedInvoice->grand_total, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            {{-- Payment Proof --}}
                            @if($selectedInvoice->payment_proof)
                                <div class="border-t border-gray-100 pt-4">
                                    <p class="text-caption font-semibold text-gray-700 mb-2 uppercase tracking-wide">Bukti Transfer</p>
                                    <div class="rounded-[8px] overflow-hidden border border-gray-200">
                                        <img src="{{ Storage::url($selectedInvoice->payment_proof) }}" alt="Bukti Transfer" class="w-full h-auto" loading="lazy">
                                    </div>
                                    @if($selectedInvoice->payment_method)
                                        <p class="text-caption text-gray-500 mt-2">Metode: <span class="font-medium capitalize">{{ $selectedInvoice->payment_method }}</span></p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Generate Invoice Modal --}}
    @if($showGenerateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" wire:click.self="closeGenerateModal">
            <div class="fixed inset-0 bg-black/30 transition-opacity"></div>
            <div class="flex min-h-full items-end sm:items-center justify-center p-4">
                <div class="relative bg-white rounded-[16px] shadow-modal w-full max-w-lg p-6 transform transition-all" @click.stop>
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-[16px] font-semibold text-gray-900">Generate Invoice</h3>
                        <button wire:click="closeGenerateModal" class="p-2 min-w-[44px] min-h-[44px] flex items-center justify-center rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="space-y-4">
                        {{-- Box Select --}}
                        <div>
                            <label class="block text-caption font-medium text-gray-700 mb-1.5">Pilih Box (OTW Indonesia) <span class="text-red-500">*</span></label>
                            <select wire:model.live="selectedBoxId" class="w-full py-2.5 px-3 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40">
                                <option value="">Pilih box</option>
                                @foreach($availableBoxes as $box)
                                    <option value="{{ $box->id }}">{{ $box->tracking_number ?? $box->batch_name ?? 'Box #' . $box->id }} — {{ $box->customer->name ?? '-' }} ({{ ucfirst($box->type) }}/{{ strtoupper($box->method) }})</option>
                                @endforeach
                            </select>
                            @error('selectedBoxId') <p class="text-caption text-red-500 mt-1">{{ $message }}</p> @enderror
                            @if($availableBoxes->isEmpty())
                                <p class="text-caption text-amber-600 mt-1">Tidak ada box dengan status OTW Indonesia.</p>
                            @endif
                            @if($pending_denda_info)
                                <div class="mt-2 flex items-center gap-2 p-2.5 bg-amber-50 border border-amber-200 rounded-[8px]">
                                    <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                    <p class="text-caption text-amber-700">{{ $pending_denda_info['count'] }} denda klaim pending (Rp {{ number_format($pending_denda_info['total'], 0, ',', '.') }}) akan otomatis ditambahkan ke invoice ini.</p>
                                </div>
                            @endif
                        </div>

                        {{-- Weight & Dimensions --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-caption font-medium text-gray-700 mb-1.5">Berat (gram) <span class="text-red-500">*</span></label>
                                <input type="number" wire:model.live="weight" step="0.1" min="0.1" max="99999" placeholder="Berat (gram)" class="w-full px-3 py-2.5 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40">
                                @error('weight') <p class="text-caption text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-caption font-medium text-gray-700 mb-1.5">Add On (Rp)</label>
                                <input type="number" wire:model.live="addOn" min="0" max="999999" placeholder="0" class="w-full px-3 py-2.5 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40">
                                @error('addOn') <p class="text-caption text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-caption font-medium text-gray-700 mb-1.5">Panjang (cm) <span class="text-red-500">*</span></label>
                                <input type="number" wire:model.live="length" min="1" max="999" placeholder="P" class="w-full px-3 py-2.5 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40">
                                @error('length') <p class="text-caption text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-caption font-medium text-gray-700 mb-1.5">Lebar (cm) <span class="text-red-500">*</span></label>
                                <input type="number" wire:model.live="width" min="1" max="999" placeholder="L" class="w-full px-3 py-2.5 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40">
                                @error('width') <p class="text-caption text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-caption font-medium text-gray-700 mb-1.5">Tinggi (cm) <span class="text-red-500">*</span></label>
                                <input type="number" wire:model.live="height" min="1" max="999" placeholder="T" class="w-full px-3 py-2.5 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40">
                                @error('height') <p class="text-caption text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Live Preview --}}
                        @if($preview)
                            <div class="bg-gray-50 rounded-[10px] p-4 border border-gray-100">
                                <p class="text-caption font-semibold text-gray-500 uppercase tracking-wide mb-3">Preview Perhitungan</p>
                                <div class="space-y-2">
                                    <div class="flex justify-between text-body">
                                        <span class="text-gray-500">Volume</span>
                                        <span class="font-medium text-gray-800">{{ number_format($preview['volume'], 2, ',', '.') }} m³</span>
                                    </div>
                                    <div class="flex justify-between text-body">
                                        <span class="text-gray-500">Dasar (max berat/volume)</span>
                                        <span class="font-medium text-gray-800">{{ number_format($preview['basis'], 2, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between text-body">
                                        <span class="text-gray-500">Rate Key</span>
                                        <span class="font-medium text-gray-600 text-caption">{{ $preview['rate_key'] }}</span>
                                    </div>
                                    <div class="border-t border-gray-200 pt-2 mt-2 space-y-1.5">
                                        <div class="flex justify-between text-body">
                                            <span class="text-gray-500">Fee TAX</span>
                                            <span class="font-medium text-gray-800">Rp {{ number_format($preview['fee_tax'], 0, ',', '.') }}</span>
                                        </div>
                                        <div class="flex justify-between text-body">
                                            <span class="text-gray-500">Fee WH</span>
                                            <span class="font-medium text-gray-800">Rp {{ number_format($preview['fee_wh'], 0, ',', '.') }}</span>
                                        </div>
                                        <div class="flex justify-between text-body">
                                            <span class="text-gray-500">Fee Packing</span>
                                            <span class="font-medium text-gray-800">Rp {{ number_format($preview['fee_packing'], 0, ',', '.') }}</span>
                                        </div>
                                        @if($preview['add_on'] > 0)
                                            <div class="flex justify-between text-body">
                                                <span class="text-gray-500">Add On</span>
                                                <span class="font-medium text-gray-800">Rp {{ number_format($preview['add_on'], 0, ',', '.') }}</span>
                                            </div>
                                        @endif
                                        @if($preview['denda_total'] > 0)
                                            <div class="flex justify-between text-body">
                                                <span class="text-gray-500">Denda Klaim</span>
                                                <span class="font-medium text-amber-600">Rp {{ number_format($preview['denda_total'], 0, ',', '.') }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex justify-between text-[15px] font-bold pt-2 border-t border-gray-200">
                                        <span class="text-gray-700">Grand Total</span>
                                        <span class="text-primary">Rp {{ number_format($preview['grand_total'], 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center gap-3 justify-end mt-6">
                        <button wire:click="closeGenerateModal" class="px-4 py-2.5 text-body font-medium text-gray-700 bg-white border border-gray-200 rounded-[8px] hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button
                            wire:click="generateInvoice"
                            @if(!$preview) disabled @endif
                            class="px-4 py-2.5 text-body font-medium text-white bg-primary rounded-[8px] hover:bg-primary-light transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Generate Invoice
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
