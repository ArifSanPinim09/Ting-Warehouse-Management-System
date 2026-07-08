<div class="min-h-screen bg-[#f8fafc]">
    {{-- Page Header --}}
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div>
                <h1 class="text-[22px] font-bold text-gray-900 tracking-tight">Info Customer</h1>
                <p class="text-[13px] text-gray-500 mt-0.5">Kelola data dan status customer</p>
            </div>
        </div>
    </div>

    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-4">

        {{-- Filters --}}
        <div class="bg-white rounded-[12px] border border-gray-100 p-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="sm:col-span-2 relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama, email, atau telepon..." class="w-full pl-10 pr-4 py-2.5 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-1 focus:ring-accent/20 transition-colors placeholder:text-gray-400">
                </div>
                <select wire:model.live="filterStatus" class="py-2.5 px-3 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-1 focus:ring-accent/20 transition-colors text-gray-600">
                    <option value="">Semua Status</option>
                    <option value="active">Aktif</option>
                    <option value="pending">Menunggu Aktivasi</option>
                    <option value="inactive">Nonaktif</option>
                </select>
            </div>
        </div>

        {{-- Content --}}
        <div class="flex gap-6">
            <div class="flex-1 min-w-0">
                @if($customers->isEmpty())
                    <div class="bg-white rounded-[12px] border border-gray-100">
                        <x-empty-state
                            icon="checklist"
                            title="Belum ada customer terdaftar"
                            text="Customer akan muncul setelah mendaftar melalui halaman registrasi."
                        />
                    </div>
                @else
                    {{-- Desktop Table --}}
                    <div class="hidden md:block bg-white rounded-[12px] border border-gray-100 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-gray-100">
                                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Customer</th>
                                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Kontak</th>
                                        <th class="text-center px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Box</th>
                                        <th class="text-center px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Invoice</th>
                                        <th class="text-center px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Checkout</th>
                                        <th class="text-center px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Komplain</th>
                                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                                        <th class="text-right px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($customers as $cust)
                                        <tr class="hover:bg-gray-50/50 transition-colors cursor-pointer" wire:click="selectCustomer({{ $cust->id }})">
                                            <td class="px-5 py-3.5">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                                                        <span class="text-[11px] font-bold text-primary">{{ strtoupper(substr($cust->name, 0, 1)) }}</span>
                                                    </div>
                                                    <div>
                                                        <p class="text-[13px] font-semibold text-gray-900">{{ $cust->name }}</p>
                                                        <p class="text-[11px] text-gray-400">{{ $cust->email }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-5 py-3.5">
                                                <span class="text-[12px] text-gray-600">{{ $cust->phone ?? '-' }}</span>
                                            </td>
                                            <td class="px-5 py-3.5 text-center">
                                                <span class="text-[13px] text-gray-700 font-medium">{{ $cust->boxes_count }}</span>
                                            </td>
                                            <td class="px-5 py-3.5 text-center">
                                                <span class="text-[13px] text-gray-700 font-medium">{{ $cust->invoices_count }}</span>
                                            </td>
                                            <td class="px-5 py-3.5 text-center">
                                                <span class="text-[13px] text-gray-700 font-medium">{{ $cust->checkouts_count }}</span>
                                            </td>
                                            <td class="px-5 py-3.5 text-center">
                                                <span class="text-[13px] text-gray-700 font-medium {{ $cust->complains_count > 0 ? 'text-red-500' : '' }}">{{ $cust->complains_count }}</span>
                                            </td>
                                            <td class="px-5 py-3.5">
                                                <x-status-badge :status="$cust->status" />
                                            </td>
                                            <td class="px-5 py-3.5 text-right">
                                                <button wire:click.stop="selectCustomer({{ $cust->id }})" class="text-[12px] font-medium text-accent hover:text-primary transition-colors">
                                                    Detail
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($customers->hasPages())
                            <div class="px-5 py-3 border-t border-gray-100">
                                {{ $customers->links() }}
                            </div>
                        @endif
                    </div>

                    {{-- Mobile Cards --}}
                    <div class="md:hidden space-y-3">
                        @foreach($customers as $cust)
                            <div wire:click="selectCustomer({{ $cust->id }})" class="bg-white rounded-[12px] border border-gray-100 p-4 hover:shadow-card-hover transition-shadow cursor-pointer">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                                            <span class="text-[12px] font-bold text-primary">{{ strtoupper(substr($cust->name, 0, 1)) }}</span>
                                        </div>
                                        <div>
                                            <p class="text-[14px] font-semibold text-gray-900">{{ $cust->name }}</p>
                                            <p class="text-[12px] text-gray-500">{{ $cust->email }}</p>
                                        </div>
                                    </div>
                                    <x-status-badge :status="$cust->status" />
                                </div>
                                <div class="flex items-center gap-4 text-[12px] text-gray-500 pt-3 border-t border-gray-100">
                                    <span>{{ $cust->boxes_count }} box</span>
                                    <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                                    <span>{{ $cust->invoices_count }} invoice</span>
                                    <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                                    <span>{{ $cust->complains_count }} komplain</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Detail Panel --}}
            @if($showDetail && $selectedCustomer)
                <div class="w-full lg:w-[420px] flex-shrink-0">
                    <div class="bg-white rounded-[12px] border border-gray-100 sticky top-[88px]">
                        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="text-[14px] font-semibold text-gray-900">Detail Customer</h3>
                            <button wire:click="closeDetail" class="p-1 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <div class="p-5 space-y-5">
                            {{-- Profile --}}
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                                    <span class="text-[18px] font-bold text-primary">{{ strtoupper(substr($selectedCustomer->name, 0, 1)) }}</span>
                                </div>
                                <div>
                                    <p class="text-[16px] font-semibold text-gray-900">{{ $selectedCustomer->name }}</p>
                                    <p class="text-[13px] text-gray-500">{{ $selectedCustomer->email }}</p>
                                    <x-status-badge :status="$selectedCustomer->status" class="mt-1" />
                                </div>
                            </div>

                            {{-- Info --}}
                            <div class="space-y-3 border-t border-gray-100 pt-4">
                                <div class="flex justify-between text-[13px]">
                                    <span class="text-gray-500">Telepon</span>
                                    <span class="font-medium text-gray-800">{{ $selectedCustomer->phone ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between text-[13px]">
                                    <span class="text-gray-500">No KTP</span>
                                    <span class="font-medium text-gray-800">{{ $selectedCustomer->ktp_number ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between text-[13px]">
                                    <span class="text-gray-500">LINE ID</span>
                                    <span class="font-medium text-gray-800">{{ $selectedCustomer->line_id ?? '-' }}</span>
                                </div>
                                @if($selectedCustomer->address)
                                    <div>
                                        <span class="text-[12px] text-gray-500">Alamat</span>
                                        <p class="text-[13px] text-gray-800 mt-1">{{ $selectedCustomer->address }}</p>
                                    </div>
                                @endif
                            </div>

                            {{-- Stats Summary --}}
                            <div class="grid grid-cols-2 gap-3 border-t border-gray-100 pt-4">
                                <div class="bg-gray-50 rounded-[8px] p-3 text-center">
                                    <p class="text-[20px] font-bold text-gray-900">{{ $selectedCustomer->boxes->count() }}</p>
                                    <p class="text-[11px] text-gray-500">Total Box</p>
                                </div>
                                <div class="bg-gray-50 rounded-[8px] p-3 text-center">
                                    <p class="text-[20px] font-bold text-gray-900">{{ $selectedCustomer->invoices->count() }}</p>
                                    <p class="text-[11px] text-gray-500">Total Invoice</p>
                                </div>
                                <div class="bg-gray-50 rounded-[8px] p-3 text-center">
                                    <p class="text-[20px] font-bold text-gray-900">{{ $selectedCustomer->checkouts->count() }}</p>
                                    <p class="text-[11px] text-gray-500">Total Checkout</p>
                                </div>
                                <div class="bg-gray-50 rounded-[8px] p-3 text-center">
                                    <p class="text-[20px] font-bold {{ $selectedCustomer->complains->count() > 0 ? 'text-red-500' : 'text-gray-900' }}">{{ $selectedCustomer->complains->count() }}</p>
                                    <p class="text-[11px] text-gray-500">Total Komplain</p>
                                </div>
                            </div>

                            {{-- Active Boxes --}}
                            @php $activeBoxes = $selectedCustomer->boxes->whereIn('status', ['OPEN', 'SENT_TO_CARGO', 'OTW_INA', 'UP_INVOICE']); @endphp
                            @if($activeBoxes->count() > 0)
                                <div class="border-t border-gray-100 pt-4">
                                    <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wide mb-3">Box Aktif</p>
                                    <div class="space-y-2 max-h-[160px] overflow-y-auto">
                                        @foreach($activeBoxes as $box)
                                            <div class="flex items-center justify-between p-2.5 rounded-[8px] bg-gray-50">
                                                <div class="min-w-0">
                                                    <p class="text-[13px] font-medium text-gray-800 truncate">{{ $box->tracking_number ?? $box->batch_name ?? 'Box #' . $box->id }}</p>
                                                    <p class="text-[11px] text-gray-500 capitalize">{{ $box->type }} · {{ strtoupper($box->method) }}</p>
                                                </div>
                                                <x-status-badge :status="$box->status" />
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Active Invoices --}}
                            @php $activeInvoices = $selectedCustomer->invoices->whereIn('status', ['waiting_payment', 'waiting_verification']); @endphp
                            @if($activeInvoices->count() > 0)
                                <div class="border-t border-gray-100 pt-4">
                                    <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wide mb-3">Invoice Aktif</p>
                                    <div class="space-y-2">
                                        @foreach($activeInvoices as $inv)
                                            <div class="flex items-center justify-between p-2.5 rounded-[8px] bg-gray-50">
                                                <div class="min-w-0">
                                                    <p class="text-[13px] font-medium text-gray-800">{{ $inv->invoice_number }}</p>
                                                    <p class="text-[11px] text-gray-500">Rp {{ number_format($inv->grand_total, 0, ',', '.') }}</p>
                                                </div>
                                                <x-status-badge :status="$inv->status" />
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Action Buttons --}}
                            <div class="border-t border-gray-100 pt-4 space-y-2">
                                @if($selectedCustomer->status === 'pending')
                                    <button
                                        wire:click="activateCustomer"
                                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 text-white text-[13px] font-medium rounded-[8px] hover:bg-emerald-700 transition-colors"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Aktivasi Customer
                                    </button>
                                @elseif($selectedCustomer->status === 'active')
                                    <button
                                        wire:click="deactivateCustomer"
                                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-white text-red-600 text-[13px] font-medium rounded-[8px] border border-red-200 hover:bg-red-50 transition-colors"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                        Nonaktifkan Customer
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
