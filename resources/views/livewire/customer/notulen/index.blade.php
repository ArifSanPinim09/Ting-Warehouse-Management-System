<div class="space-y-6">
    {{-- Header --}}
    <div>
        <h1 class="text-xl font-bold text-gray-800">Notulen</h1>
        <p class="text-sm text-gray-500 mt-1">Barang Anda yang sudah sampai di Indonesia</p>
    </div>

    {{-- Search --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Cari resi atau nama barang..."
                class="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:border-accent focus:ring-2 focus:ring-accent/30"
            >
        </div>
    </div>

    {{-- List --}}
    @if($items->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
            <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-sm text-gray-500 mt-3">Belum ada barang yang sampai di INA</p>
            <p class="text-xs text-gray-400 mt-1">Barang akan muncul di sini setelah box tiba di Indonesia</p>
        </div>
    @else
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left px-4 py-3 font-medium text-gray-600">No. Resi</th>
                            <th class="text-left px-4 py-3 font-medium text-gray-600">Nama Barang</th>
                            <th class="text-left px-4 py-3 font-medium text-gray-600">Jumlah</th>
                            <th class="text-left px-4 py-3 font-medium text-gray-600">Box</th>
                            <th class="text-left px-4 py-3 font-medium text-gray-600">Status Box</th>
                            <th class="text-left px-4 py-3 font-medium text-gray-600">Tags</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($items as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-mono text-gray-800">{{ $item->resi_number }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $item->name }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $item->quantity }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $item->box?->box_code }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        @if(in_array($item->box?->status, [\App\Models\Box::STATUS_ARRIVED_INA, \App\Models\Box::STATUS_REDLINE]))
                                            bg-blue-100 text-blue-700
                                        @elseif(in_array($item->box?->status, [\App\Models\Box::STATUS_STEVEDORING, \App\Models\Box::STATUS_CHECKED_BY_WH]))
                                            bg-yellow-100 text-yellow-700
                                        @else
                                            bg-green-100 text-green-700
                                        @endif
                                    ">
                                        {{ $item->box?->getStatusLabelAttribute() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-1">
                                        @if($item->is_sensitive)
                                            <span class="px-2 py-0.5 text-xs text-orange-700 bg-orange-100 rounded-full">Sensitive</span>
                                        @endif
                                        @if($item->is_garment)
                                            <span class="px-2 py-0.5 text-xs text-blue-700 bg-blue-100 rounded-full">Garment</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $items->links() }}
        </div>
    @endif
</div>
