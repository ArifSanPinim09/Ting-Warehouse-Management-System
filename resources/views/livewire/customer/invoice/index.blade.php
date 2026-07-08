<div class="min-h-screen bg-[#f8fafc]">
    {{-- Page Header --}}
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div>
                <h1 class="text-display text-primary">Invoice</h1>
                <p class="text-body text-gray-500 mt-1">Lihat dan bayar invoice pengiriman Anda</p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-4">
        {{-- Filter Bar --}}
        <div class="ds-card p-4">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nomor invoice atau tracking..." class="ds-input" />
                </div>
                <select wire:model.live="filterStatus" class="ds-input sm:w-52">
                    <option value="">Semua Status</option>
                    <option value="waiting_payment">Menunggu Pembayaran</option>
                    <option value="waiting_verification">Menunggu Verifikasi</option>
                    <option value="verified">Terverifikasi</option>
                </select>
            </div>
        </div>

        {{-- Invoice List --}}
        @if($invoices->isEmpty())
            <x-empty-state
                icon="invoice"
                title="Belum ada invoice"
                text="Invoice akan muncul setelah admin menghitung berat dan dimensi barang Anda."
            />
        @else
            {{-- Desktop Table --}}
            <div class="ds-card overflow-hidden hidden md:block">
                <table class="ds-table">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Box</th>
                            <th class="text-right">Berat</th>
                            <th class="text-right">Volume</th>
                            <th class="text-right">Fee TAX</th>
                            <th class="text-right">Fee WH</th>
                            <th class="text-right">Packing</th>
                            <th class="text-right">Grand Total</th>
                            <th>Status</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoices as $invoice)
                            <tr>
                                <td class="font-semibold text-primary">{{ $invoice->invoice_number }}</td>
                                <td>
                                    <span class="text-body">{{ $invoice->box->tracking_number ?? '-' }}</span>
                                    <span class="text-caption text-gray-400 block">{{ strtoupper($invoice->box->method ?? '') }}</span>
                                </td>
                                <td class="text-right">{{ $invoice->weight ? number_format($invoice->weight, 2) . ' kg' : '-' }}</td>
                                <td class="text-right">{{ $invoice->volume ? number_format($invoice->volume, 2) . ' m³' : '-' }}</td>
                                <td class="text-right">Rp {{ number_format($invoice->fee_tax, 0, ',', '.') }}</td>
                                <td class="text-right">Rp {{ number_format($invoice->fee_wh, 0, ',', '.') }}</td>
                                <td class="text-right">Rp {{ number_format($invoice->fee_packing, 0, ',', '.') }}</td>
                                <td class="text-right font-semibold text-primary">Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</td>
                                <td><x-status-badge :status="$invoice->status" /></td>
                                <td class="text-right">
                                    @if($invoice->status === \App\Models\Invoice::STATUS_WAITING_PAYMENT)
                                        <button wire:click="openPayModal({{ $invoice->id }})" class="ds-btn-primary ds-btn-sm">
                                            Bayar
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="space-y-3 md:hidden">
                @foreach($invoices as $invoice)
                    <div class="ds-card p-4 space-y-3">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-body font-semibold text-primary">{{ $invoice->invoice_number }}</p>
                                <p class="text-caption text-gray-500 mt-0.5">{{ $invoice->box->tracking_number ?? '-' }} · {{ strtoupper($invoice->box->method ?? '') }}</p>
                            </div>
                            <x-status-badge :status="$invoice->status" />
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-caption">
                            <div><span class="text-gray-400">Berat:</span> <span class="text-gray-700">{{ $invoice->weight ? $invoice->weight . ' kg' : '-' }}</span></div>
                            <div><span class="text-gray-400">Volume:</span> <span class="text-gray-700">{{ $invoice->volume ? $invoice->volume . ' m³' : '-' }}</span></div>
                            <div><span class="text-gray-400">Fee TAX:</span> <span class="text-gray-700">Rp {{ number_format($invoice->fee_tax, 0, ',', '.') }}</span></div>
                            <div><span class="text-gray-400">Fee WH:</span> <span class="text-gray-700">Rp {{ number_format($invoice->fee_wh, 0, ',', '.') }}</span></div>
                            <div><span class="text-gray-400">Packing:</span> <span class="text-gray-700">Rp {{ number_format($invoice->fee_packing, 0, ',', '.') }}</span></div>
                        </div>

                        <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                            <div>
                                <span class="text-caption text-gray-400">Grand Total</span>
                                <p class="text-subtitle font-bold text-primary">Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</p>
                            </div>
                            @if($invoice->status === \App\Models\Invoice::STATUS_WAITING_PAYMENT)
                                <button wire:click="openPayModal({{ $invoice->id }})" class="ds-btn-primary ds-btn-sm">
                                    Bayar Sekarang
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">{{ $invoices->links() }}</div>
        @endif
    </div>

    {{-- Payment Modal --}}
    @if($payingInvoiceId)
        @php
            $payInvoice = $invoices->firstWhere('id', $payingInvoiceId);
        @endphp
        <div class="fixed inset-0 z-50 overflow-y-auto" wire:click.self="closePayModal">
            <div class="fixed inset-0 bg-gray-500/75 transition-opacity"></div>
            <div class="flex min-h-full items-end sm:items-center justify-center p-4">
                <div class="relative bg-white rounded-t-modal sm:rounded-modal shadow-modal w-full max-w-lg animate-slide-up" wire:click.stop>
                    {{-- Modal Header --}}
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                        <div>
                            <h3 class="text-title font-semibold text-primary">Bayar Invoice</h3>
                            <p class="text-caption text-gray-500 mt-0.5">{{ $payInvoice->invoice_number ?? '' }}</p>
                        </div>
                        <button wire:click="closePayModal" class="p-2 rounded-button text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    {{-- Modal Body --}}
                    <form wire:submit="submitPayment" class="px-6 py-5 space-y-5">
                        {{-- Total --}}
                        @if($payInvoice)
                            <div class="rounded-card bg-gray-50 p-4 text-center">
                                <span class="text-caption text-gray-500">Total Pembayaran</span>
                                <p class="text-display font-bold text-primary mt-1">Rp {{ number_format($payInvoice->grand_total, 0, ',', '.') }}</p>
                            </div>
                        @endif

                        {{-- Payment Method --}}
                        <div>
                            <label class="ds-label">Metode Pembayaran <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="relative cursor-pointer">
                                    <input type="radio" wire:model="paymentMethod" value="transfer" class="sr-only peer" />
                                    <div class="flex items-center gap-3 p-3 rounded-card border-2 border-gray-200 transition-all peer-checked:border-accent peer-checked:bg-accent/5 hover:border-gray-300">
                                        <div class="w-10 h-10 rounded-button bg-blue-50 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                        </div>
                                        <span class="text-body font-medium text-gray-700">Transfer</span>
                                    </div>
                                </label>
                                <label class="relative cursor-pointer">
                                    <input type="radio" wire:model="paymentMethod" value="qris" class="sr-only peer" />
                                    <div class="flex items-center gap-3 p-3 rounded-card border-2 border-gray-200 transition-all peer-checked:border-accent peer-checked:bg-accent/5 hover:border-gray-300">
                                        <div class="w-10 h-10 rounded-button bg-violet-50 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                        </div>
                                        <span class="text-body font-medium text-gray-700">QRIS</span>
                                    </div>
                                </label>
                            </div>
                            @error('paymentMethod') <p class="ds-error mt-2">{{ $message }}</p> @enderror
                        </div>

                        {{-- Upload Proof --}}
                        <div>
                            <label class="ds-label">Bukti Transfer <span class="text-red-500">*</span></label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed border-gray-200 rounded-card transition-colors hover:border-accent/30 hover:bg-accent/5">
                                @if($paymentProof)
                                    <div class="relative inline-block">
                                        <img src="{{ $paymentProof->temporaryUrl() }}" class="max-h-32 rounded-lg shadow-sm" alt="Preview" />
                                        <button type="button" wire:click="$set('paymentProof', null)" class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 transition-colors">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                @else
                                    <div class="space-y-2 text-center">
                                        <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <label class="relative cursor-pointer">
                                            <span class="ds-btn-secondary ds-btn-sm">Pilih Foto</span>
                                            <input type="file" wire:model="paymentProof" accept="image/jpeg,image/png" class="sr-only" />
                                        </label>
                                        <p class="text-caption text-gray-400">JPG atau PNG. Maks 5MB</p>
                                    </div>
                                @endif
                            </div>
                            @error('paymentProof') <p class="ds-error">{{ $message }}</p> @enderror
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center justify-end gap-3 pt-2">
                            <button type="button" wire:click="closePayModal" class="ds-btn-secondary">Batal</button>
                            <button type="submit" class="ds-btn-primary" wire:loading.attr="disabled" wire:target="submitPayment">
                                <svg wire:loading wire:target="submitPayment" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                <span wire:loading.remove wire:target="submitPayment">Kirim Bukti Bayar</span>
                                <span wire:loading wire:target="submitPayment">Mengirim...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
