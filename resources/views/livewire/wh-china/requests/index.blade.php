<div class="min-h-screen bg-[#f8fafc]">

    {{-- Livewire Loading State --}}
    <div wire:loading class="fixed inset-x-0 top-16 h-0.5 bg-primary/20 z-50">
        <div class="h-full bg-primary animate-pulse w-1/3 rounded-full"></div>
    </div>

    {{-- Page Header --}}
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <h1 class="text-[20px] font-bold text-primary">Customer Requests</h1>
            <p class="text-[13px] text-gray-500 mt-1">Special handling instructions from customers</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

        {{-- Filter --}}
        <div class="bg-white rounded-[12px] border border-gray-100 p-4">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="relative sm:w-48">
                    <select wire:model.live="filterStatus"
                        class="w-full py-2.5 px-3 pr-8 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors text-gray-600 appearance-none">
                        <option value="">All Requests</option>
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Requests Table --}}
        <div class="bg-white rounded-[12px] border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-[15px] font-semibold text-gray-900">Request List</h2>
                <p class="text-[12px] text-gray-400 mt-0.5">Items with special handling requests</p>
            </div>

            @if($requests->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                        <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <h3 class="text-[14px] font-semibold text-gray-700 mb-1">No requests found</h3>
                    <p class="text-[13px] text-gray-500">No special handling requests at the moment.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-gray-100 bg-gray-50/50">
                                <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Resi</th>
                                <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Item Name</th>
                                <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Request Type</th>
                                <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Notes</th>
                                <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($requests as $i => $item)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-5 py-3 text-[13px] text-gray-500">{{ $requests->firstItem() + $i }}</td>
                                    <td class="px-5 py-3 text-[13px] font-semibold text-gray-900">{{ $item->resi_number ?? '—' }}</td>
                                    <td class="px-5 py-3 text-[13px] text-gray-700">{{ $item->customer->name ?? '—' }}</td>
                                    <td class="px-5 py-3 text-[13px] text-gray-700">{{ $item->name }}</td>
                                    <td class="px-5 py-3">
                                        <span class="text-[12px] font-medium px-2 py-0.5 rounded-full bg-blue-50 text-blue-700">
                                            {{ $item->request_type }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-[13px] text-gray-600 max-w-[200px] truncate">
                                        {{ $item->request_notes ?? '—' }}
                                    </td>
                                    <td class="px-5 py-3">
                                        @if($item->request_completed_at)
                                            <span class="text-[12px] font-medium px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700">Done</span>
                                        @else
                                            <span class="text-[12px] font-medium px-2 py-0.5 rounded-full bg-amber-100 text-amber-700">Pending</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-[13px] text-gray-500">{{ $item->created_at->format('d M Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($requests->hasPages())
                    <div class="px-5 py-4 border-t border-gray-100">
                        {{ $requests->links() }}
                    </div>
                @endif
            @endif
        </div>

    </div>
</div>
