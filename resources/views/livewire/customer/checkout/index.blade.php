<div class="min-h-screen bg-[#f8fafc]">
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-display text-primary">Checkout</h1>
                    <p class="text-body text-gray-500 mt-1">Request pengiriman barang ke alamat Anda</p>
                </div>
                @if($verifiedInvoices->isNotEmpty())
                    <button wire:click="openForm" class="ds-btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Request Checkout
                    </button>
                @endif
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-4">
        {{-- Filter --}}
        <div class="ds-card p-4">
            <select wire:model.live="filterStatus" class="ds-input sm:w-48">
                <option value="">Semua Status</option>
                <option value="request">Menunggu Proses</option>
                <option value="on_process">Sedang Diproses</option>
                <option value="sent">Terkirim</option>
            </select>
        </div>

        {{-- Checkout List --}}
        @if($checkouts->isEmpty())
            <x-empty-state
                icon="package"
                title="Belum ada checkout"
                text="Checkout akan tersedia setelah invoice Anda terverifikasi oleh admin."
            />
        @else
            {{-- Desktop --}}
            <div class="ds-card overflow-hidden hidden md:block">
                <table class="ds-table">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Penerima</th>
                            <th>Tipe</th>
                            <th>Telepon</th>
                            <th>Alamat</th>
                            <th>Status</th>
                            <th>Tracking</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($checkouts as $checkout)
                            <tr>
                                <td class="font-semibold text-primary">{{ $checkout->invoice->invoice_number ?? '-' }}</td>
                                <td>{{ $checkout->recipient_name }}</td>
                                <td>
                                    @if($checkout->address_type === 'personal')
                                        <span class="ds-badge-neutral">Personal</span>
                                    @else
                                        <span class="ds-badge-info">Dropship</span>
                                    @endif
                                </td>
                                <td>{{ $checkout->recipient_phone }}</td>
                                <td class="max-w-xs truncate">{{ $checkout->address }}</td>
                                <td><x-status-badge :status="$checkout->status" /></td>
                                <td>
                                    @if($checkout->tracking_number)
                                        <span class="text-body font-mono text-accent">{{ $checkout->tracking_number }}</span>
                                    @else
                                        <span class="text-caption text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile --}}
            <div class="space-y-3 md:hidden">
                @foreach($checkouts as $checkout)
                    <div class="ds-card p-4 space-y-3">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-body font-semibold text-primary">{{ $checkout->invoice->invoice_number ?? '-' }}</p>
                                <p class="text-caption text-gray-500 mt-0.5">{{ $checkout->recipient_name }}</p>
                            </div>
                            <x-status-badge :status="$checkout->status" />
                        </div>
                        <div class="text-caption text-gray-500 space-y-1">
                            <p>{{ $checkout->address }}</p>
                            <p>{{ $checkout->recipient_phone }}</p>
                        </div>
                        @if($checkout->tracking_number)
                            <div class="pt-2 border-t border-gray-100">
                                <span class="text-caption text-gray-400">Tracking:</span>
                                <span class="text-body font-mono text-accent">{{ $checkout->tracking_number }}</span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="mt-6">{{ $checkouts->links() }}</div>
        @endif
    </div>

    {{-- Checkout Form Modal --}}
    @if($showForm)
        <div class="fixed inset-0 z-50 overflow-y-auto" wire:click.self="closeForm">
            <div class="fixed inset-0 bg-gray-500/75 transition-opacity"></div>
            <div class="flex min-h-full items-end sm:items-center justify-center p-4">
                <div class="relative bg-white rounded-t-modal sm:rounded-modal shadow-modal w-full max-w-lg animate-slide-up" wire:click.stop>
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                        <h3 class="text-title font-semibold text-primary">Request Checkout</h3>
                        <button wire:click="closeForm" class="p-2 min-w-[44px] min-h-[44px] flex items-center justify-center rounded-button text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form wire:submit="submit" class="px-6 py-5 space-y-5">
                        {{-- Invoice Select --}}
                        <div>
                            <label class="ds-label">Invoice <span class="text-red-500">*</span></label>
                            <select wire:model="invoiceId" class="ds-input @error('invoiceId') ds-input-error @enderror">
                                <option value="">Pilih invoice...</option>
                                @foreach($verifiedInvoices as $inv)
                                    <option value="{{ $inv->id }}">{{ $inv->invoice_number }} — Rp {{ number_format($inv->grand_total, 0, ',', '.') }}</option>
                                @endforeach
                            </select>
                            @error('invoiceId') <p class="ds-error">{{ $message }}</p> @enderror
                        </div>

                        {{-- Address Type --}}
                        <div>
                            <label class="ds-label">Tipe Alamat <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="relative cursor-pointer">
                                    <input type="radio" wire:model="addressType" value="personal" class="sr-only peer" />
                                    <div class="flex items-center gap-3 p-3 rounded-card border-2 border-gray-200 transition-all peer-checked:border-accent peer-checked:bg-accent/5 hover:border-gray-300">
                                        <span class="text-body font-medium text-gray-700">Personal</span>
                                    </div>
                                </label>
                                <label class="relative cursor-pointer">
                                    <input type="radio" wire:model="addressType" value="dropship" class="sr-only peer" />
                                    <div class="flex items-center gap-3 p-3 rounded-card border-2 border-gray-200 transition-all peer-checked:border-accent peer-checked:bg-accent/5 hover:border-gray-300">
                                        <span class="text-body font-medium text-gray-700">Dropship</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="ds-label">Nama Penerima <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="recipientName" placeholder="Nama lengkap penerima" class="ds-input @error('recipientName') ds-input-error @enderror" />
                            @error('recipientName') <p class="ds-error">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="ds-label">No. Telepon <span class="text-red-500">*</span></label>
                            <input type="tel" wire:model="recipientPhone" placeholder="08123456789" class="ds-input @error('recipientPhone') ds-input-error @enderror" />
                            @error('recipientPhone') <p class="ds-error">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="ds-label">Alamat Lengkap <span class="text-red-500">*</span></label>
                            <textarea wire:model="address" rows="3" placeholder="Alamat lengkap termasuk kecamatan dan kode pos" class="ds-input @error('address') ds-input-error @enderror"></textarea>
                            @error('address') <p class="ds-error">{{ $message }}</p> @enderror
                        </div>

                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" wire:model="confirmation" class="mt-0.5 rounded border-gray-300 text-accent focus:ring-accent/20" />
                            <span class="text-body text-gray-600">Saya mengkonfirmasi data di atas sudah benar dan ingin mengajukan checkout.</span>
                        </label>
                        @error('confirmation') <p class="ds-error">{{ $message }}</p> @enderror

                        <div class="flex items-center justify-end gap-3 pt-2">
                            <button type="button" wire:click="closeForm" class="ds-btn-secondary">Batal</button>
                            <button type="submit" class="ds-btn-primary" wire:loading.attr="disabled">
                                <svg wire:loading class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                <span wire:loading.remove>Kirim Request</span>
                                <span wire:loading>Mengirim...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
