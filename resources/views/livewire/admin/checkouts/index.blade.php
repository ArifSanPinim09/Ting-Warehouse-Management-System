<div class="min-h-screen bg-[#f8fafc]">
    {{-- Page Header --}}
    <div class="bg-white border-b border-gray-100">
        <div class="mx-auto w-full max-w-[1500px] px-4 md:px-6 lg:px-8 xl:px-10 2xl:px-12 py-6">
            <div>
                <h1 class="text-[22px] font-bold text-gray-900 tracking-tight">Checkout Requests</h1>
                <p class="text-body text-gray-500 mt-0.5">Proses request checkout dari customer</p>
            </div>
        </div>
    </div>

    <div class="mx-auto w-full max-w-[1500px] px-4 md:px-6 lg:px-8 xl:px-10 2xl:px-12 py-6 space-y-4">

        {{-- Filters --}}
        <div class="bg-white rounded-[12px] border border-gray-100 p-4">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari invoice, customer, atau tracking..."
                        class="w-full pl-10 pr-4 py-2.5 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors placeholder:text-gray-400"
                    >
                </div>
                <select wire:model.live="filterStatus" class="py-2.5 px-3 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors text-gray-600 sm:w-48">
                    <option value="">Semua Status</option>
                    <option value="request">Menunggu Proses</option>
                    <option value="on_process">Sedang Diproses</option>
                    <option value="sent">Terkirim</option>
                </select>
            </div>
        </div>

        {{-- Content --}}
        <div class="flex gap-6" x-data="{ detailOpen: @js($showDetail) }" x-effect="detailOpen = $wire.showDetail">
            {{-- Table / List --}}
            <div class="flex-1 min-w-0" :class="detailOpen && 'hidden lg:block'">
                @if($checkouts->isEmpty())
                    <div class="bg-white rounded-[12px] border border-gray-100">
                        <div class="flex flex-col items-center py-16 text-center px-4">
                            <div class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            </div>
                            <p class="text-body font-medium text-gray-500">Tidak ada checkout request</p>
                            <p class="text-caption text-gray-400 mt-1">Checkout dari customer akan muncul di sini</p>
                        </div>
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
                                        <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Penerima</th>
                                        <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Tipe</th>
                                        <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                                        <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Tracking</th>
                                        <th class="text-right px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($checkouts as $checkout)
                                        <tr class="hover:bg-gray-50/50 transition-colors cursor-pointer" wire:click="selectCheckout({{ $checkout->id }})">
                                            <td class="px-5 py-3.5">
                                                <p class="text-body font-semibold text-gray-900">{{ $checkout->invoice->invoice_number ?? '-' }}</p>
                                                <p class="text-caption text-gray-400">{{ $checkout->created_at->format('d M Y H:i') }}</p>
                                            </td>
                                            <td class="px-5 py-3.5">
                                                <span class="text-body text-gray-700">{{ $checkout->customer->name ?? '-' }}</span>
                                            </td>
                                            <td class="px-5 py-3.5">
                                                <p class="text-body text-gray-700">{{ $checkout->recipient_name }}</p>
                                                <p class="text-caption text-gray-400">{{ $checkout->recipient_phone }}</p>
                                            </td>
                                            <td class="px-5 py-3.5">
                                                @if($checkout->address_type === 'personal')
                                                    <span class="inline-flex items-center px-2 py-0.5 text-caption font-medium bg-gray-100 text-gray-700 rounded-full">Personal</span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 text-caption font-medium bg-blue-50 text-blue-700 rounded-full">Dropship</span>
                                                @endif
                                            </td>
                                            <td class="px-5 py-3.5">
                                                <x-status-badge :status="$checkout->status" />
                                            </td>
                                            <td class="px-5 py-3.5">
                                                @if($checkout->tracking_number)
                                                    <span class="text-body font-mono text-accent">{{ $checkout->tracking_number }}</span>
                                                @else
                                                    <span class="text-caption text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-5 py-3.5 text-right">
                                                <button wire:click.stop="selectCheckout({{ $checkout->id }})" class="text-caption font-medium text-accent hover:text-primary transition-colors">
                                                    Detail
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($checkouts->hasPages())
                            <div class="px-5 py-3 border-t border-gray-100">
                                {{ $checkouts->links() }}
                            </div>
                        @endif
                    </div>

                    {{-- Mobile Cards --}}
                    <div class="md:hidden space-y-3">
                        @foreach($checkouts as $checkout)
                            <div wire:click="selectCheckout({{ $checkout->id }})" class="bg-white rounded-[12px] border border-gray-100 p-4 hover:shadow-card-hover transition-shadow cursor-pointer">
                                <div class="flex items-start justify-between mb-3">
                                    <div>
                                        <p class="text-body font-semibold text-gray-900">{{ $checkout->invoice->invoice_number ?? '-' }}</p>
                                        <p class="text-caption text-gray-500 mt-0.5">{{ $checkout->customer->name ?? '-' }}</p>
                                    </div>
                                    <x-status-badge :status="$checkout->status" />
                                </div>
                                <div class="space-y-1 text-caption text-gray-500">
                                    <p>Penerima: <span class="text-gray-700">{{ $checkout->recipient_name }}</span></p>
                                    <p>Alamat: <span class="text-gray-700 line-clamp-1">{{ $checkout->address }}</span></p>
                                </div>
                                @if($checkout->tracking_number)
                                    <div class="mt-2 pt-2 border-t border-gray-100">
                                        <span class="text-caption text-gray-400">Tracking:</span>
                                        <span class="text-body font-mono text-accent">{{ $checkout->tracking_number }}</span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                        @if($checkouts->hasPages())
                            <div class="py-2">{{ $checkouts->links() }}</div>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Detail Panel --}}
            @if($showDetail && $selectedCheckout)
                <div class="w-full lg:w-[400px] flex-shrink-0" x-show="detailOpen" x-transition>
                    <div class="bg-white rounded-[12px] border border-gray-100 sticky top-[88px]">
                        {{-- Detail Header --}}
                        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="text-body font-semibold text-gray-900">Detail Checkout</h3>
                            <button wire:click="closeDetail" class="p-2 min-w-[44px] min-h-[44px] flex items-center justify-center rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <div class="p-5 space-y-5">
                            {{-- Checkout Info --}}
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-caption text-gray-500">Invoice</span>
                                    <span class="text-body font-semibold text-gray-900">{{ $selectedCheckout->invoice->invoice_number ?? '-' }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-caption text-gray-500">Customer</span>
                                    <span class="text-body text-gray-700">{{ $selectedCheckout->customer->name ?? '-' }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-caption text-gray-500">Tipe Alamat</span>
                                    <span class="text-body text-gray-700 capitalize">{{ $selectedCheckout->address_type }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-caption text-gray-500">Status</span>
                                    <x-status-badge :status="$selectedCheckout->status" size="lg" />
                                </div>
                            </div>

                            {{-- Recipient Info --}}
                            <div class="p-3 bg-gray-50 rounded-[8px] space-y-2">
                                <p class="text-caption font-semibold text-gray-700 uppercase tracking-wide">Info Penerima</p>
                                <div class="space-y-1.5">
                                    <div class="flex items-center justify-between">
                                        <span class="text-caption text-gray-500">Nama</span>
                                        <span class="text-body text-gray-700">{{ $selectedCheckout->recipient_name }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-caption text-gray-500">Telepon</span>
                                        <span class="text-body text-gray-700">{{ $selectedCheckout->recipient_phone }}</span>
                                    </div>
                                </div>
                                <div>
                                    <span class="text-caption text-gray-500">Alamat</span>
                                    <p class="text-body text-gray-700 mt-1">{{ $selectedCheckout->address }}</p>
                                </div>
                            </div>

                            {{-- Invoice Info --}}
                            @if($selectedCheckout->invoice)
                                <div class="p-3 bg-gray-50 rounded-[8px] space-y-2">
                                    <p class="text-caption font-semibold text-gray-700 uppercase tracking-wide">Info Invoice</p>
                                    <div class="space-y-1.5">
                                        <div class="flex items-center justify-between">
                                            <span class="text-caption text-gray-500">Box</span>
                                            <span class="text-body text-gray-700">{{ $selectedCheckout->invoice->box->tracking_number ?? '-' }}</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-caption text-gray-500">Grand Total</span>
                                            <span class="text-body font-semibold text-gray-900">Rp {{ number_format($selectedCheckout->invoice->grand_total, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Tracking Info (if processed) --}}
                            @if($selectedCheckout->tracking_number)
                                <div class="p-3 bg-emerald-50 rounded-[8px] space-y-2">
                                    <p class="text-caption font-semibold text-emerald-700 uppercase tracking-wide">Info Pengiriman</p>
                                    <div class="flex items-center justify-between">
                                        <span class="text-caption text-emerald-600">Tracking Number</span>
                                        <span class="text-body font-mono font-semibold text-emerald-800">{{ $selectedCheckout->tracking_number }}</span>
                                    </div>
                                    {{-- Sprint 5B: Download buttons --}}
                                    <div class="flex gap-2 pt-2 border-t border-emerald-100">
                                        <a href="{{ route('admin.export.thermal-label', $selectedCheckout->id) }}" target="_blank" class="text-[12px] font-medium px-3 py-1.5 rounded-[6px] bg-white border border-emerald-200 text-emerald-700 hover:bg-emerald-50">
                                            🖨 Thermal Label
                                        </a>
                                        <a href="{{ route('admin.export.faktur', $selectedCheckout->id) }}" target="_blank" class="text-[12px] font-medium px-3 py-1.5 rounded-[6px] bg-white border border-gray-200 text-gray-700 hover:bg-gray-50">
                                            📄 Faktur
                                        </a>
                                    </div>
                                </div>
                            @endif

                            {{-- Packing Photo --}}
                            @if($selectedCheckout->packing_photo)
                                <div>
                                    <p class="text-caption font-semibold text-gray-700 mb-2 uppercase tracking-wide">Foto Packing</p>
                                    <img src="{{ Storage::url($selectedCheckout->packing_photo) }}" alt="Packing photo" class="w-full rounded-[8px] border border-gray-100">
                                </div>
                            @endif

                            {{-- Action Button --}}
                            @if($selectedCheckout->status === \App\Models\Checkout::STATUS_REQUEST)
                                <button
                                    wire:click="openProcessModal"
                                    class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-primary text-white text-body font-medium rounded-[8px] hover:bg-primary-light transition-colors"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Proses Checkout
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Process Checkout Modal --}}
    @if($showProcessModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" wire:click.self="closeProcessModal">
            <div class="fixed inset-0 bg-black/30 transition-opacity"></div>
            <div class="flex min-h-full items-end sm:items-center justify-center p-4">
                <div class="relative bg-white rounded-[16px] shadow-modal w-full max-w-lg p-6 transform transition-all" @click.stop>
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-[16px] font-semibold text-gray-900">Proses Checkout</h3>
                        <button wire:click="closeProcessModal" class="p-2 min-w-[44px] min-h-[44px] flex items-center justify-center rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="space-y-4">
                        {{-- Tracking Number --}}
                        <div>
                            <label class="block text-caption font-medium text-gray-700 mb-1.5">Nomor Tracking <span class="text-red-500">*</span></label>
                            <input
                                type="text"
                                wire:model="trackingNumber"
                                placeholder="Contoh: JNE1234567890"
                                class="w-full px-3 py-2.5 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors @error('trackingNumber') border-red-300 @enderror"
                            >
                            @error('trackingNumber') <p class="text-caption text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Packing Photo --}}
                        <div>
                            <label class="block text-caption font-medium text-gray-700 mb-1.5">Foto Packing (opsional)</label>
                            @if($packingPhoto)
                                <div class="relative inline-block mb-2">
                                    <img src="{{ $packingPhoto->temporaryUrl() }}" class="max-h-32 rounded-lg shadow-sm" alt="Preview" />
                                    <button type="button" wire:click="$set('packingPhoto', null)" class="absolute -top-3 -right-3 w-8 h-8 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 transition-colors" aria-label="Hapus foto">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            @else
                                <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-dashed border-gray-200 rounded-[8px] transition-colors hover:border-accent/30 hover:bg-accent/5">
                                    <div class="space-y-2 text-center">
                                        <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <label class="relative cursor-pointer">
                                            <span class="inline-flex items-center px-3 py-1.5 text-caption font-medium text-gray-700 bg-white border border-gray-200 rounded-[6px] hover:bg-gray-50 transition-colors">Pilih Foto</span>
                                            <input type="file" wire:model="packingPhoto" accept="image/jpeg,image/png,image/webp" class="sr-only" />
                                        </label>
                                        <p class="text-caption text-gray-400">JPG, PNG, atau WebP. Maks 5MB</p>
                                    </div>
                                </div>
                            @endif
                            @error('packingPhoto') <p class="text-caption text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="flex items-center gap-3 justify-end mt-6">
                        <button wire:click="closeProcessModal" class="px-4 py-2.5 text-body font-medium text-gray-700 bg-white border border-gray-200 rounded-[8px] hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button wire:click="processCheckout" class="px-4 py-2.5 text-body font-medium text-white bg-primary rounded-[8px] hover:bg-primary-light transition-colors" wire:loading.attr="disabled">
                            <svg wire:loading class="animate-spin w-4 h-4 inline mr-1" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            Proses & Kirim
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
