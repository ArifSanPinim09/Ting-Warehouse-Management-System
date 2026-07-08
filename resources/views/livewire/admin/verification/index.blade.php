<div class="min-h-screen bg-[#f8fafc]">
    {{-- Page Header --}}
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div>
                <h1 class="text-[22px] font-bold text-gray-900 tracking-tight">Verifikasi Pembayaran</h1>
                <p class="text-[13px] text-gray-500 mt-0.5">Verifikasi bukti transfer dari customer</p>
            </div>
        </div>
    </div>

    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-4">

        {{-- Filters --}}
        <div class="bg-white rounded-[12px] border border-gray-100 p-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="sm:col-span-2 relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari invoice number atau customer..." class="w-full pl-10 pr-4 py-2.5 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-1 focus:ring-accent/20 transition-colors placeholder:text-gray-400">
                </div>
                <select wire:model.live="filterStatus" class="py-2.5 px-3 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-1 focus:ring-accent/20 transition-colors text-gray-600">
                    <option value="waiting_verification">Menunggu Verifikasi</option>
                    <option value="verified">Terverifikasi</option>
                    <option value="waiting_payment">Menunggu Pembayaran</option>
                    <option value="">Semua Status</option>
                </select>
            </div>
        </div>

        {{-- Content --}}
        <div class="flex gap-6">
            <div class="flex-1 min-w-0">
                @if($invoices->isEmpty())
                    <div class="bg-white rounded-[12px] border border-gray-100">
                        <x-empty-state
                            icon="checklist"
                            title="Tidak ada pembayaran menunggu"
                            text="Semua pembayaran sudah diverifikasi atau belum ada yang masuk."
                        />
                    </div>
                @else
                    {{-- Desktop Table --}}
                    <div class="hidden md:block bg-white rounded-[12px] border border-gray-100 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-gray-100">
                                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Invoice</th>
                                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Customer</th>
                                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Metode</th>
                                        <th class="text-right px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Grand Total</th>
                                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Tanggal</th>
                                        <th class="text-right px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($invoices as $inv)
                                        <tr class="hover:bg-gray-50/50 transition-colors cursor-pointer {{ $inv->status === 'waiting_verification' ? 'bg-amber-50/30' : '' }}" wire:click="selectInvoice({{ $inv->id }})">
                                            <td class="px-5 py-3.5">
                                                <span class="text-[13px] font-semibold text-gray-900">{{ $inv->invoice_number }}</span>
                                            </td>
                                            <td class="px-5 py-3.5">
                                                <span class="text-[13px] text-gray-700">{{ $inv->customer->name ?? '-' }}</span>
                                            </td>
                                            <td class="px-5 py-3.5">
                                                <span class="text-[12px] font-medium text-gray-600 capitalize">{{ $inv->payment_method ?? '-' }}</span>
                                            </td>
                                            <td class="px-5 py-3.5 text-right">
                                                <span class="text-[14px] font-bold text-gray-900">Rp {{ number_format($inv->grand_total, 0, ',', '.') }}</span>
                                            </td>
                                            <td class="px-5 py-3.5">
                                                <x-status-badge :status="$inv->status" />
                                            </td>
                                            <td class="px-5 py-3.5">
                                                <span class="text-[12px] text-gray-500">{{ $inv->updated_at->format('d M Y H:i') }}</span>
                                            </td>
                                            <td class="px-5 py-3.5 text-right">
                                                <button wire:click.stop="selectInvoice({{ $inv->id }})" class="text-[12px] font-medium text-accent hover:text-primary transition-colors">
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
                            <div wire:click="selectInvoice({{ $inv->id }})" class="bg-white rounded-[12px] border border-gray-100 p-4 hover:shadow-card-hover transition-shadow cursor-pointer {{ $inv->status === 'waiting_verification' ? 'border-l-4 border-l-amber-400' : '' }}">
                                <div class="flex items-start justify-between mb-3">
                                    <div>
                                        <p class="text-[14px] font-semibold text-gray-900">{{ $inv->invoice_number }}</p>
                                        <p class="text-[12px] text-gray-500 mt-0.5">{{ $inv->customer->name ?? '-' }}</p>
                                    </div>
                                    <x-status-badge :status="$inv->status" />
                                </div>
                                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                                    <span class="text-[12px] text-gray-500 capitalize">{{ $inv->payment_method ?? '-' }}</span>
                                    <span class="text-[15px] font-bold text-gray-900">Rp {{ number_format($inv->grand_total, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Detail Panel --}}
            @if($showDetail && $selectedInvoice)
                <div class="w-full lg:w-[420px] flex-shrink-0">
                    <div class="bg-white rounded-[12px] border border-gray-100 sticky top-[88px]">
                        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="text-[14px] font-semibold text-gray-900">Detail Pembayaran</h3>
                            <button wire:click="closeDetail" class="p-1 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <div class="p-5 space-y-5">
                            {{-- Invoice Info --}}
                            <div class="text-center pb-4 border-b border-gray-100">
                                <p class="text-[11px] text-gray-400 uppercase tracking-wide mb-1">Invoice</p>
                                <p class="text-[18px] font-bold text-gray-900">{{ $selectedInvoice->invoice_number }}</p>
                                <p class="text-[13px] text-gray-500 mt-1">{{ $selectedInvoice->customer->name ?? '-' }}</p>
                            </div>

                            {{-- Amount --}}
                            <div class="text-center py-4 bg-gray-50 rounded-[10px]">
                                <p class="text-[11px] text-gray-400 uppercase tracking-wide mb-1">Total Tagihan</p>
                                <p class="text-[24px] font-bold text-primary">Rp {{ number_format($selectedInvoice->grand_total, 0, ',', '.') }}</p>
                            </div>

                            {{-- Payment Info --}}
                            <div class="space-y-3">
                                <div class="flex justify-between text-[13px]">
                                    <span class="text-gray-500">Metode Bayar</span>
                                    <span class="font-medium text-gray-800 capitalize">{{ $selectedInvoice->payment_method ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between text-[13px]">
                                    <span class="text-gray-500">Status</span>
                                    <x-status-badge :status="$selectedInvoice->status" size="lg" />
                                </div>
                            </div>

                            {{-- Fee Breakdown --}}
                            <div class="border-t border-gray-100 pt-4">
                                <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wide mb-3">Rincian Biaya</p>
                                <div class="space-y-2">
                                    <div class="flex justify-between text-[13px]">
                                        <span class="text-gray-500">Fee TAX ({{ $selectedInvoice->weight }}kg)</span>
                                        <span class="text-gray-800">Rp {{ number_format($selectedInvoice->fee_tax, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between text-[13px]">
                                        <span class="text-gray-500">Fee WH</span>
                                        <span class="text-gray-800">Rp {{ number_format($selectedInvoice->fee_wh, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between text-[13px]">
                                        <span class="text-gray-500">Fee Packing</span>
                                        <span class="text-gray-800">Rp {{ number_format($selectedInvoice->fee_packing, 0, ',', '.') }}</span>
                                    </div>
                                    @if($selectedInvoice->add_on > 0)
                                        <div class="flex justify-between text-[13px]">
                                            <span class="text-gray-500">Add On</span>
                                            <span class="text-gray-800">Rp {{ number_format($selectedInvoice->add_on, 0, ',', '.') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Payment Proof --}}
                            @if($selectedInvoice->payment_proof)
                                <div class="border-t border-gray-100 pt-4">
                                    <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wide mb-3">Bukti Transfer</p>
                                    <div class="rounded-[10px] overflow-hidden border border-gray-200 bg-gray-50">
                                        <img
                                            src="{{ Storage::url($selectedInvoice->payment_proof) }}"
                                            alt="Bukti Transfer"
                                            class="w-full h-auto max-h-[300px] object-contain cursor-pointer"
                                            loading="lazy"
                                            onclick="window.open(this.src, '_blank')"
                                        >
                                    </div>
                                    <p class="text-[11px] text-gray-400 mt-2 text-center">Klik gambar untuk memperbesar</p>
                                </div>
                            @else
                                <div class="border-t border-gray-100 pt-4">
                                    <div class="flex flex-col items-center py-6 text-center">
                                        <svg class="w-10 h-10 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <p class="text-[13px] text-gray-400">Belum ada bukti transfer</p>
                                    </div>
                                </div>
                            @endif

                            {{-- Action Buttons --}}
                            @if($selectedInvoice->status === 'waiting_verification')
                                <div class="border-t border-gray-100 pt-4 space-y-3">
                                    <button
                                        wire:click="verifyPayment"
                                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 text-white text-[13px] font-medium rounded-[8px] hover:bg-emerald-700 transition-colors"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Verifikasi Pembayaran
                                    </button>
                                    <button
                                        wire:click="openRejectModal"
                                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-white text-red-600 text-[13px] font-medium rounded-[8px] border border-red-200 hover:bg-red-50 transition-colors"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        Tolak Pembayaran
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Reject Modal --}}
    @if($showRejectModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" wire:click.self="$set('showRejectModal', false)">
            <div class="fixed inset-0 bg-black/30 transition-opacity"></div>
            <div class="flex min-h-full items-end sm:items-center justify-center p-4">
                <div class="relative bg-white rounded-[16px] shadow-modal w-full max-w-md p-6 transform transition-all" @click.stop>
                    <div class="flex items-start gap-4 mb-5">
                        <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-[16px] font-semibold text-gray-900">Tolak Pembayaran</h3>
                            <p class="text-[13px] text-gray-500 mt-1">Pembayaran akan ditolak dan customer akan diminta mengupload ulang. Berikan alasan penolakan.</p>
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="block text-[12px] font-medium text-gray-700 mb-1.5">Alasan Penolakan <span class="text-red-500">*</span></label>
                        <textarea
                            wire:model="rejectReason"
                            rows="3"
                            placeholder="Jelaskan alasan penolakan..."
                            class="w-full px-3 py-2.5 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-1 focus:ring-accent/20 transition-colors resize-none"
                        ></textarea>
                        @error('rejectReason') <p class="text-[12px] text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center gap-3 justify-end">
                        <button wire:click="$set('showRejectModal', false)" class="px-4 py-2.5 text-[13px] font-medium text-gray-700 bg-white border border-gray-200 rounded-[8px] hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button
                            wire:click="rejectPayment"
                            class="px-4 py-2.5 text-[13px] font-medium text-white bg-red-600 rounded-[8px] hover:bg-red-700 transition-colors"
                        >
                            Tolak Pembayaran
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
