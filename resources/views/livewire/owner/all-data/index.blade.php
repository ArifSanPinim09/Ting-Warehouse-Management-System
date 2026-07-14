<div class="min-h-screen bg-[#f8fafc]">
    {{-- Page Header --}}
    <div class="bg-white border-b border-gray-100">
        <div class="mx-auto w-full max-w-[1500px] px-4 md:px-6 lg:px-8 xl:px-10 2xl:px-12 py-6">
            <div>
                <h1 class="text-[22px] font-bold text-gray-900 tracking-tight">All Data</h1>
                <p class="text-body text-gray-500 mt-0.5">Seluruh data operasional dalam satu halaman</p>
            </div>
        </div>
    </div>

    <div class="mx-auto w-full max-w-[1500px] px-4 md:px-6 lg:px-8 xl:px-10 2xl:px-12 py-6 space-y-4">

        {{-- Tabs --}}
        <div class="bg-white rounded-[12px] border border-gray-100 p-1.5 flex gap-1 overflow-x-auto">
            @php
                $tabs = [
                    ['key' => 'customers', 'label' => 'Customers', 'count' => \App\Models\User::where('role', 'customer')->count()],
                    ['key' => 'boxes', 'label' => 'Boxes', 'count' => \App\Models\Box::count()],
                    ['key' => 'invoices', 'label' => 'Invoices', 'count' => \App\Models\Invoice::count()],
                    ['key' => 'complains', 'label' => 'Complains', 'count' => \App\Models\Complain::count()],
                ];
            @endphp
            @foreach($tabs as $tab)
                <button
                    wire:click="setActiveTab('{{ $tab['key'] }}')"
                    class="flex items-center gap-2 px-4 py-2 text-body font-medium rounded-[8px] transition-all whitespace-nowrap {{ $activeTab === $tab['key'] ? 'bg-primary text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                >
                    {{ $tab['label'] }}
                    <span class="text-caption px-1.5 py-0.5 rounded-full {{ $activeTab === $tab['key'] ? 'bg-white/20' : 'bg-gray-100 text-gray-500' }}">{{ $tab['count'] }}</span>
                </button>
            @endforeach
        </div>

        {{-- Search --}}
        <div class="bg-white rounded-[12px] border border-gray-100 p-4">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari di {{ ucfirst($activeTab) }}..."
                    class="w-full pl-10 pr-4 py-2.5 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors placeholder:text-gray-400"
                >
            </div>
        </div>

        {{-- Content --}}
        @if($activeTab === 'customers')
            {{-- Customers Tab --}}
            @if($items->isEmpty())
                <div class="bg-white rounded-[12px] border border-gray-100">
                    <div class="flex flex-col items-center py-16 text-center px-4">
                        <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <p class="text-body font-medium text-gray-500">Tidak ada customer</p>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-[12px] border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-100">
                                    <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Nama</th>
                                    <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Email</th>
                                    <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Telepon</th>
                                    <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                                    <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Terdaftar</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($items as $user)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-5 py-3.5">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                                                    <span class="text-caption font-bold text-primary">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                                </div>
                                                <span class="text-body font-medium text-gray-900">{{ $user->name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-5 py-3.5 text-body text-gray-700">{{ $user->email }}</td>
                                        <td class="px-5 py-3.5 text-body text-gray-700">{{ $user->phone ?? '-' }}</td>
                                        <td class="px-5 py-3.5">
                                            @if($user->status === 'active')
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-caption font-medium bg-emerald-50 text-emerald-700 rounded-full"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active</span>
                                            @elseif($user->status === 'pending')
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-caption font-medium bg-amber-50 text-amber-700 rounded-full"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Pending</span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-caption font-medium bg-red-50 text-red-700 rounded-full"><span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Inactive</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3.5 text-caption text-gray-500">{{ $user->created_at->format('d M Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($items->hasPages())
                        <div class="px-5 py-3 border-t border-gray-100">{{ $items->links() }}</div>
                    @endif
                </div>
            @endif

        @elseif($activeTab === 'boxes')
            {{-- Boxes Tab --}}
            @if($items->isEmpty())
                <div class="bg-white rounded-[12px] border border-gray-100">
                    <div class="flex flex-col items-center py-16 text-center px-4">
                        <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        <p class="text-body font-medium text-gray-500">Tidak ada box</p>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-[12px] border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-100">
                                    <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Box</th>
                                    <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Customer</th>
                                    <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Tipe</th>
                                    <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Metode</th>
                                    <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                                    <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($items as $box)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-5 py-3.5">
                                            <p class="text-body font-semibold text-gray-900">{{ $box->display_name }}</p>
                                        </td>
                                        <td class="px-5 py-3.5 text-body text-gray-700">{{ $box->customer->name ?? '-' }}</td>
                                        <td class="px-5 py-3.5"><span class="text-caption font-medium text-gray-600 capitalize">{{ $box->type }}</span></td>
                                        <td class="px-5 py-3.5"><span class="text-caption font-medium text-gray-600 uppercase">{{ $box->method }}</span></td>
                                        <td class="px-5 py-3.5"><x-status-badge :status="$box->status" /></td>
                                        <td class="px-5 py-3.5 text-caption text-gray-500">{{ $box->created_at->format('d M Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($items->hasPages())
                        <div class="px-5 py-3 border-t border-gray-100">{{ $items->links() }}</div>
                    @endif
                </div>
            @endif

        @elseif($activeTab === 'invoices')
            {{-- Invoices Tab --}}
            @if($items->isEmpty())
                <div class="bg-white rounded-[12px] border border-gray-100">
                    <div class="flex flex-col items-center py-16 text-center px-4">
                        <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <p class="text-body font-medium text-gray-500">Tidak ada invoice</p>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-[12px] border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-100">
                                    <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Invoice</th>
                                    <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Customer</th>
                                    <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Box</th>
                                    <th class="text-right px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Berat</th>
                                    <th class="text-right px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Grand Total</th>
                                    <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                                    <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($items as $invoice)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-5 py-3.5 text-body font-semibold text-gray-900">{{ $invoice->invoice_number }}</td>
                                        <td class="px-5 py-3.5 text-body text-gray-700">{{ $invoice->customer->name ?? '-' }}</td>
                                        <td class="px-5 py-3.5 text-body text-gray-700">{{ $invoice->box->tracking_number ?? '-' }}</td>
                                        <td class="px-5 py-3.5 text-right text-body text-gray-700">{{ $invoice->weight ? $invoice->weight . ' kg' : '-' }}</td>
                                        <td class="px-5 py-3.5 text-right text-body font-semibold text-gray-900">Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</td>
                                        <td class="px-5 py-3.5"><x-status-badge :status="$invoice->status" /></td>
                                        <td class="px-5 py-3.5 text-caption text-gray-500">{{ $invoice->created_at->format('d M Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($items->hasPages())
                        <div class="px-5 py-3 border-t border-gray-100">{{ $items->links() }}</div>
                    @endif
                </div>
            @endif

        @elseif($activeTab === 'complains')
            {{-- Complains Tab --}}
            @if($items->isEmpty())
                <div class="bg-white rounded-[12px] border border-gray-100">
                    <div class="flex flex-col items-center py-16 text-center px-4">
                        <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-body font-medium text-gray-500">Tidak ada komplain</p>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-[12px] border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-100">
                                    <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Customer</th>
                                    <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Jenis</th>
                                    <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Resolusi</th>
                                    <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                                    <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Deskripsi</th>
                                    <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($items as $complain)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-5 py-3.5 text-body font-medium text-gray-900">{{ $complain->customer->name ?? '-' }}</td>
                                        <td class="px-5 py-3.5 text-body text-gray-700">{{ $complain->type }}</td>
                                        <td class="px-5 py-3.5 text-body text-gray-700 capitalize">{{ $complain->resolution ?? '-' }}</td>
                                        <td class="px-5 py-3.5"><x-status-badge :status="$complain->status" /></td>
                                        <td class="px-5 py-3.5 text-body text-gray-700 max-w-xs truncate">{{ $complain->description }}</td>
                                        <td class="px-5 py-3.5 text-caption text-gray-500">{{ $complain->created_at->format('d M Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($items->hasPages())
                        <div class="px-5 py-3 border-t border-gray-100">{{ $items->links() }}</div>
                    @endif
                </div>
            @endif
        @endif
    </div>
</div>
