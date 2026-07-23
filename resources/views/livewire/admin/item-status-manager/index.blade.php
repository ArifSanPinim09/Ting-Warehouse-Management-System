<div class="space-y-6">
    <div>
        <h1 class="text-xl font-bold text-gray-800">Edit Status Barang</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola status semua barang dalam sistem</p>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4 flex flex-wrap gap-3">
        <div class="flex-1 min-w-[200px] relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari resi atau nama barang..."
                class="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:border-accent focus:ring-2 focus:ring-accent/30">
        </div>
        <select wire:model.live="filterStatus" class="px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:border-accent">
            <option value="">Semua Status</option>
            @foreach($statuses as $status)
                <option value="{{ $status }}">{{ \App\Models\Item::find(0)?->status_label ?? ucfirst(str_replace('_', ' ', $status)) }}</option>
            @endforeach
        </select>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Resi</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Nama</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Customer</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Box</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($items as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono text-gray-800">{{ $item->resi_number }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $item->name }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $item->customer?->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $item->box?->box_code ?? '-' }}</td>
                        <td class="px-4 py-3">
                            @php
                                $statusColors = [
                                    'active' => 'bg-green-100 text-green-700',
                                    'no_tuan' => 'bg-gray-100 text-gray-700',
                                    'claimed' => 'bg-blue-100 text-blue-700',
                                    'klaim_wh' => 'bg-yellow-100 text-yellow-700',
                                    'shipped' => 'bg-indigo-100 text-indigo-700',
                                    'hold' => 'bg-orange-100 text-orange-700',
                                    'dijual' => 'bg-red-100 text-red-700',
                                    'lelang' => 'bg-purple-100 text-purple-700',
                                    'otw' => 'bg-cyan-100 text-cyan-700',
                                    'send_back_to_seller' => 'bg-red-100 text-red-700',
                                    'send_to_diff_addr' => 'bg-amber-100 text-amber-700',
                                    'never_arrived' => 'bg-red-200 text-red-800',
                                    'wrong_address' => 'bg-pink-100 text-pink-700',
                                ];
                                $color = $statusColors[$item->status] ?? 'bg-gray-100 text-gray-700';
                            @endphp
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $color }}">
                                {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <button wire:click="openEditModal({{ $item->id }})" class="text-xs font-medium px-3 py-1.5 rounded-[6px] bg-accent text-white hover:bg-accent-dark">
                                Edit Status
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-gray-400">Tidak ada barang ditemukan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($items->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $items->links() }}
        </div>
        @endif
    </div>

    {{-- Edit Status Modal --}}
    @if($showEditModal)
    <div class="fixed inset-0 z-[60] flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-gray-900/50" wire:click="closeEditModal"></div>
        <div class="relative bg-white rounded-[12px] shadow-xl max-w-md w-full p-6 z-10">
            <h3 class="text-[16px] font-semibold text-gray-900 mb-4">Edit Status Barang</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-[13px] font-medium text-gray-700 mb-1.5">Status <span class="text-red-500">*</span></label>
                    <select wire:model="editStatus" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-[8px] focus:border-accent">
                        @foreach($statuses as $status)
                            <option value="{{ $status }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[13px] font-medium text-gray-700 mb-1.5">Catatan (opsional)</label>
                    <textarea wire:model="editNote" rows="2" placeholder="Alasan perubahan status..."
                        class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-[8px] focus:border-accent"></textarea>
                </div>
            </div>
            <div class="flex items-center gap-3 justify-end mt-6">
                <button wire:click="closeEditModal" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-[8px] hover:bg-gray-50">Batal</button>
                <button wire:click="saveStatus" class="px-4 py-2 text-sm font-medium text-white bg-accent rounded-[8px] hover:bg-accent-dark">Simpan</button>
            </div>
        </div>
    </div>
    @endif
</div>
