<div class="min-h-screen bg-[#f8fafc]">

    {{-- Page Header --}}
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-[1500px] mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <h1 class="text-[20px] font-bold text-primary">History 历史</h1>
            <p class="text-[13px] text-gray-500 mt-1">View all processed batches and their current status</p>
        </div>
    </div>

    <div class="max-w-[1500px] mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

        {{-- Filters --}}
        <div class="bg-white rounded-[12px] border border-gray-100 p-4">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                <div>
                    <label class="block text-[12px] font-medium text-gray-500 mb-1">Search Batch</label>
                    <input wire:model.live="searchBatch" type="text" placeholder="Batch name..." class="w-full border border-gray-200 rounded-[8px] px-3 py-2 text-[13px] focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                </div>
                <div>
                    <label class="block text-[12px] font-medium text-gray-500 mb-1">Method</label>
                    <select wire:model.live="filterMethod" class="w-full border border-gray-200 rounded-[8px] px-3 py-2 text-[13px] focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                        <option value="">All</option>
                        <option value="air">AIR</option>
                        <option value="sea">SEA</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[12px] font-medium text-gray-500 mb-1">Status</label>
                    <select wire:model.live="filterStatus" class="w-full border border-gray-200 rounded-[8px] px-3 py-2 text-[13px] focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                        <option value="">All</option>
                        @foreach($statusOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[12px] font-medium text-gray-500 mb-1">From</label>
                    <input wire:model.live="dateFrom" type="date" class="w-full border border-gray-200 rounded-[8px] px-3 py-2 text-[13px] focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                </div>
                <div>
                    <label class="block text-[12px] font-medium text-gray-500 mb-1">To</label>
                    <input wire:model.live="dateTo" type="date" class="w-full border border-gray-200 rounded-[8px] px-3 py-2 text-[13px] focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-[12px] border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-[15px] font-semibold text-gray-900">Batch History</h2>
                <p class="text-[12px] text-gray-400 mt-0.5">All boxes that have been processed beyond CLOSED status</p>
            </div>

            @if($boxes->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                        <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-[14px] font-semibold text-gray-700 mb-1">No history found</h3>
                    <p class="text-[13px] text-gray-500">Processed batches will appear here.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/80 border-b border-gray-100">
                                <th class="px-4 py-3 text-[12px] font-semibold text-gray-600 uppercase tracking-wider">Batch</th>
                                <th class="px-4 py-3 text-[12px] font-semibold text-gray-600 uppercase tracking-wider">Box</th>
                                <th class="px-4 py-3 text-[12px] font-semibold text-gray-600 uppercase tracking-wider">Type</th>
                                <th class="px-4 py-3 text-[12px] font-semibold text-gray-600 uppercase tracking-wider">Method</th>
                                <th class="px-4 py-3 text-[12px] font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-[12px] font-semibold text-gray-600 uppercase tracking-wider">Customer</th>
                                <th class="px-4 py-3 text-[12px] font-semibold text-gray-600 uppercase tracking-wider">Items</th>
                                <th class="px-4 py-3 text-[12px] font-semibold text-gray-600 uppercase tracking-wider">Updated</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($boxes as $box)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-4 py-3 text-[13px] font-medium text-gray-900">{{ $box->batch_name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-[13px] text-gray-600">{{ $box->huruf_box ?? '-' }}</td>
                                    <td class="px-4 py-3 text-[13px] text-gray-600">{{ strtoupper($box->type) }}</td>
                                    <td class="px-4 py-3 text-[13px] text-gray-600">{{ strtoupper($box->method) }}</td>
                                    <td class="px-4 py-3"><x-status-badge status="{{ $box->status }}" /></td>
                                    <td class="px-4 py-3 text-[13px] text-gray-600">{{ $box->customer?->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-[13px] text-gray-600">{{ $box->items->count() }}</td>
                                    <td class="px-4 py-3 text-[13px] text-gray-500">{{ $box->updated_at->format('d M Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-3 border-t border-gray-100">
                    {{ $boxes->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
