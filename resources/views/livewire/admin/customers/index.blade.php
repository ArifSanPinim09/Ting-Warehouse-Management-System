<div class="min-h-screen bg-[#f8fafc]">
    {{-- Page Header --}}
    <div class="bg-white border-b border-gray-100">
        <div class="mx-auto w-full max-w-[1500px] px-4 md:px-6 lg:px-8 xl:px-10 2xl:px-12 py-6">
            <div>
                <h1 class="text-[22px] font-bold text-gray-900 tracking-tight">Info Customer</h1>
                <p class="text-body text-gray-500 mt-0.5">Kelola data dan status customer</p>
            </div>
        </div>
    </div>

    <div class="mx-auto w-full max-w-[1500px] px-4 md:px-6 lg:px-8 xl:px-10 2xl:px-12 py-6 space-y-4">

        {{-- Filters --}}
        <div class="bg-white rounded-[12px] border border-gray-100 p-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="sm:col-span-2 relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama, email, atau telepon..." class="w-full pl-10 pr-4 py-2.5 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors placeholder:text-gray-400">
                </div>
                <select wire:model.live="filterStatus" class="py-2.5 px-3 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors text-gray-600">
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
                                        <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Customer</th>
                                        <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Kontak</th>
                                        <th class="text-center px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Box</th>
                                        <th class="text-center px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Invoice</th>
                                        <th class="text-center px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Checkout</th>
                                        <th class="text-center px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Komplain</th>
                                        <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                                        <th class="text-right px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($customers as $cust)
                                        <tr class="hover:bg-gray-50/50 transition-colors cursor-pointer" wire:click="selectCustomer({{ $cust->id }})">
                                            <td class="px-5 py-3.5">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                                                        <span class="text-caption font-bold text-primary">{{ strtoupper(substr($cust->name, 0, 1)) }}</span>
                                                    </div>
                                                    <div>
                                                        <p class="text-body font-semibold text-gray-900">{{ $cust->name }}</p>
                                                        <p class="text-caption text-gray-400">{{ $cust->email }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-5 py-3.5">
                                                <span class="text-caption text-gray-600">{{ $cust->phone ?? '-' }}</span>
                                            </td>
                                            <td class="px-5 py-3.5 text-center">
                                                <span class="text-body text-gray-700 font-medium">{{ $cust->boxes_count }}</span>
                                            </td>
                                            <td class="px-5 py-3.5 text-center">
                                                <span class="text-body text-gray-700 font-medium">{{ $cust->invoices_count }}</span>
                                            </td>
                                            <td class="px-5 py-3.5 text-center">
                                                <span class="text-body text-gray-700 font-medium">{{ $cust->checkouts_count }}</span>
                                            </td>
                                            <td class="px-5 py-3.5 text-center">
                                                <span class="text-body text-gray-700 font-medium {{ $cust->complains_count > 0 ? 'text-red-500' : '' }}">{{ $cust->complains_count }}</span>
                                            </td>
                                            <td class="px-5 py-3.5">
                                                <x-status-badge :status="$cust->status" />
                                            </td>
                                            <td class="px-5 py-3.5 text-right">
                                                <button wire:click.stop="selectCustomer({{ $cust->id }})" class="text-caption font-medium text-accent hover:text-primary transition-colors">
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
                                            <span class="text-caption font-bold text-primary">{{ strtoupper(substr($cust->name, 0, 1)) }}</span>
                                        </div>
                                        <div>
                                            <p class="text-body font-semibold text-gray-900">{{ $cust->name }}</p>
                                            <p class="text-caption text-gray-500">{{ $cust->email }}</p>
                                        </div>
                                    </div>
                                    <x-status-badge :status="$cust->status" />
                                </div>
                                <div class="flex items-center gap-4 text-caption text-gray-500 pt-3 border-t border-gray-100">
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
                            <h3 class="text-body font-semibold text-gray-900">Detail Customer</h3>
                            <button wire:click="closeDetail" class="p-2 min-w-[44px] min-h-[44px] flex items-center justify-center rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
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
                                    <p class="text-body text-gray-500">{{ $selectedCustomer->email }}</p>
                                    <x-status-badge :status="$selectedCustomer->status" class="mt-1" />
                                </div>
                            </div>

                            {{-- Info --}}
                            <div class="space-y-3 border-t border-gray-100 pt-4">
                                <div class="flex justify-between text-body">
                                    <span class="text-gray-500">Telepon</span>
                                    <span class="font-medium text-gray-800">{{ $selectedCustomer->phone ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between text-body">
                                    <span class="text-gray-500">No KTP</span>
                                    <span class="font-medium text-gray-800">{{ $selectedCustomer->ktp_number ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between text-body">
                                    <span class="text-gray-500">LINE ID</span>
                                    <span class="font-medium text-gray-800">{{ $selectedCustomer->line_id ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between text-body">
                                    <span class="text-gray-500">Rate Air</span>
                                    <span class="font-medium text-gray-800">{{ $selectedCustomer->custom_rate_air ?? 'Global' }}</span>
                                </div>
                                <div class="flex justify-between text-body">
                                    <span class="text-gray-500">Rate Sea</span>
                                    <span class="font-medium text-gray-800">{{ $selectedCustomer->custom_rate_sea ?? 'Global' }}</span>
                                </div>
                                @if($selectedCustomer->address)
                                    <div>
                                        <span class="text-caption text-gray-500">Alamat</span>
                                        <p class="text-body text-gray-800 mt-1">{{ $selectedCustomer->address }}</p>
                                    </div>
                                @endif
                            </div>

                            {{-- Stats Summary --}}
                            <div class="grid grid-cols-2 gap-3 border-t border-gray-100 pt-4">
                                <div class="bg-gray-50 rounded-[8px] p-3 text-center">
                                    <p class="text-[20px] font-bold text-gray-900">{{ $selectedCustomer->boxes->count() }}</p>
                                    <p class="text-caption text-gray-500">Total Box</p>
                                </div>
                                <div class="bg-gray-50 rounded-[8px] p-3 text-center">
                                    <p class="text-[20px] font-bold text-gray-900">{{ $selectedCustomer->invoices->count() }}</p>
                                    <p class="text-caption text-gray-500">Total Invoice</p>
                                </div>
                                <div class="bg-gray-50 rounded-[8px] p-3 text-center">
                                    <p class="text-[20px] font-bold text-gray-900">{{ $selectedCustomer->checkouts->count() }}</p>
                                    <p class="text-caption text-gray-500">Total Checkout</p>
                                </div>
                                <div class="bg-gray-50 rounded-[8px] p-3 text-center">
                                    <p class="text-[20px] font-bold {{ $selectedCustomer->complains->count() > 0 ? 'text-red-500' : 'text-gray-900' }}">{{ $selectedCustomer->complains->count() }}</p>
                                    <p class="text-caption text-gray-500">Total Komplain</p>
                                </div>
                            </div>

                            {{-- Active Boxes --}}
                            @php $activeBoxes = $selectedCustomer->boxes->whereIn('status', ['OPEN', 'SENT_TO_CARGO', 'OTW_INA', 'UP_INVOICE']); @endphp
                            @if($activeBoxes->count() > 0)
                                <div class="border-t border-gray-100 pt-4">
                                    <p class="text-caption font-semibold text-gray-500 uppercase tracking-wide mb-3">Box Aktif</p>
                                    <div class="space-y-2 max-h-[160px] overflow-y-auto">
                                        @foreach($activeBoxes as $box)
                                            <div class="flex items-center justify-between p-2.5 rounded-[8px] bg-gray-50">
                                                <div class="min-w-0">
                                                    <p class="text-body font-medium text-gray-800 truncate">{{ $box->display_name }}</p>
                                                    <p class="text-caption text-gray-500 capitalize">{{ $box->type }} · {{ strtoupper($box->method) }}</p>
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
                                    <p class="text-caption font-semibold text-gray-500 uppercase tracking-wide mb-3">Invoice Aktif</p>
                                    <div class="space-y-2">
                                        @foreach($activeInvoices as $inv)
                                            <div class="flex items-center justify-between p-2.5 rounded-[8px] bg-gray-50">
                                                <div class="min-w-0">
                                                    <p class="text-body font-medium text-gray-800">{{ $inv->invoice_number }}</p>
                                                    <p class="text-caption text-gray-500">Rp {{ number_format($inv->grand_total, 0, ',', '.') }}</p>
                                                </div>
                                                <x-status-badge :status="$inv->status" />
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Action Buttons --}}
                            <div class="border-t border-gray-100 pt-4 space-y-2">
                                {{-- Edit Button --}}
                                <button
                                    wire:click="openEditModal"
                                    class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-primary text-white text-body font-medium rounded-[8px] hover:bg-primary-light transition-colors"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Edit Customer
                                </button>

                                @if($selectedCustomer->status === 'pending')
                                    <button
                                        wire:click="activateCustomer"
                                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 text-white text-body font-medium rounded-[8px] hover:bg-emerald-700 transition-colors"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Aktivasi Customer
                                    </button>
                                @elseif($selectedCustomer->status === 'active')
                                    <button
                                        wire:click="deactivateCustomer"
                                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-white text-red-600 text-body font-medium rounded-[8px] border border-red-200 hover:bg-red-50 transition-colors"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                        Nonaktifkan Customer
                                    </button>
                                @endif

                                {{-- Delete Button --}}
                                <button
                                    wire:click="openDeleteConfirm"
                                    class="w-full flex items-center justify-center gap-2 px-4 py-2.5 text-red-600 text-body font-medium rounded-[8px] border border-red-200 hover:bg-red-50 transition-colors"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Hapus Customer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Edit Customer Modal --}}
    @if($showEditModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" wire:click.self="closeEditModal">
            <div class="fixed inset-0 bg-black/30 transition-opacity"></div>
            <div class="flex min-h-full items-end sm:items-center justify-center p-4">
                <div class="relative bg-white rounded-[16px] shadow-lg w-full max-w-lg transform transition-all" @click.stop>
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-[10px] bg-primary/10 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <h3 class="text-[15px] font-semibold text-gray-900">Edit Customer</h3>
                        </div>
                        <button wire:click="closeEditModal" class="p-2 min-w-[40px] min-h-[40px] flex items-center justify-center rounded-[8px] text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="saveCustomer" class="p-6 space-y-4">
                        {{-- Name + Email --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[12px] font-medium text-gray-600 mb-1">Nama <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="editName" maxlength="255" placeholder="Nama customer"
                                    class="w-full px-3 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40">
                                @error('editName') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-[12px] font-medium text-gray-600 mb-1">Email <span class="text-red-500">*</span></label>
                                <input type="email" wire:model="editEmail" maxlength="255" placeholder="email@example.com"
                                    class="w-full px-3 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40">
                                @error('editEmail') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Phone + KTP --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[12px] font-medium text-gray-600 mb-1">Telepon</label>
                                <input type="text" wire:model="editPhone" maxlength="20" placeholder="08xxxxxxxxxx"
                                    class="w-full px-3 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40">
                                @error('editPhone') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-[12px] font-medium text-gray-600 mb-1">No KTP</label>
                                <input type="text" wire:model="editKtpNumber" maxlength="30" placeholder="Opsional"
                                    class="w-full px-3 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40">
                                @error('editKtpNumber') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Address --}}
                        <div>
                            <label class="block text-[12px] font-medium text-gray-600 mb-1">Alamat</label>
                            <textarea wire:model="editAddress" rows="2" maxlength="500" placeholder="Alamat customer"
                                class="w-full px-3 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 resize-none"></textarea>
                            @error('editAddress') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- LINE ID + Status --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[12px] font-medium text-gray-600 mb-1">LINE ID</label>
                                <input type="text" wire:model="editLineId" maxlength="50" placeholder="Opsional"
                                    class="w-full px-3 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40">
                            </div>
                            <div>
                                <label class="block text-[12px] font-medium text-gray-600 mb-1">Status <span class="text-red-500">*</span></label>
                                <select wire:model="editStatus" class="w-full px-3 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40">
                                    <option value="pending">Pending</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                @error('editStatus') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Custom Rates --}}
                        <div class="border-t border-gray-100 pt-4">
                            <p class="text-[12px] font-semibold text-gray-700 mb-3">💰 Custom Rate (Opsional)</p>
                            <p class="text-[11px] text-gray-500 mb-3">Kosongkan jika menggunakan rate global. Isi jika customer punya rate khusus.</p>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[12px] font-medium text-gray-600 mb-1">Rate Air</label>
                                    <input type="number" wire:model="editCustomRateAir" min="0" step="0.01" placeholder="Kosong = global"
                                        class="w-full px-3 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40">
                                    @error('editCustomRateAir') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-[12px] font-medium text-gray-600 mb-1">Rate Sea</label>
                                    <input type="number" wire:model="editCustomRateSea" min="0" step="0.01" placeholder="Kosong = global"
                                        class="w-full px-3 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40">
                                    @error('editCustomRateSea') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center justify-end gap-3 pt-2">
                            <button type="button" wire:click="closeEditModal"
                                class="px-4 py-2 text-[13px] font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-[8px] transition-colors">
                                Cancel
                            </button>
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2 text-[13px] font-medium text-white bg-primary rounded-[8px] hover:bg-primary-light transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Delete Confirmation Modal --}}
    @if($showDeleteConfirm)
        <div class="fixed inset-0 z-50 overflow-y-auto" wire:click.self="closeDeleteConfirm">
            <div class="fixed inset-0 bg-black/30 transition-opacity"></div>
            <div class="flex min-h-full items-end sm:items-center justify-center p-4">
                <div class="relative bg-white rounded-[16px] shadow-modal w-full max-w-md p-6 transform transition-all" @click.stop>
                    <div class="flex items-start gap-4 mb-5">
                        <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </div>
                        <div>
                            <h3 class="text-[16px] font-semibold text-gray-900">Hapus Customer</h3>
                            <p class="text-body text-gray-500 mt-1">
                                Anda yakin ingin menghapus customer
                                <span class="font-semibold text-gray-700">{{ $selectedCustomer->name ?? '' }}</span>?
                                Tindakan ini tidak dapat dibatalkan.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 justify-end">
                        <button wire:click="closeDeleteConfirm" class="px-4 py-2 text-body font-medium text-gray-700 bg-white border border-gray-200 rounded-[8px] hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button wire:click="deleteCustomer" class="px-4 py-2 text-body font-medium text-white bg-red-600 rounded-[8px] hover:bg-red-700 transition-colors">
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
