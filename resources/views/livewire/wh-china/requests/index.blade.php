<div class="min-h-screen bg-[#f8fafc]">

    {{-- Page Header --}}
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-[1500px] mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <h1 class="text-[20px] font-bold text-primary">Customer Requests</h1>
            <p class="text-[13px] text-gray-500 mt-1">Special handling instructions from customers</p>
        </div>
    </div>

    <div class="max-w-[1500px] mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

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
                    <h3 class="text-[14px] font-semibold text-gray-700 mb-1">No requests yet</h3>
                    <p class="text-[13px] text-gray-500">Customer requests will appear here once items with special handling are submitted.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/80 border-b border-gray-100">
                                <th class="px-4 py-3 text-[12px] font-semibold text-gray-600 uppercase tracking-wider">No</th>
                                <th class="px-4 py-3 text-[12px] font-semibold text-gray-600 uppercase tracking-wider">Customer</th>
                                <th class="px-4 py-3 text-[12px] font-semibold text-gray-600 uppercase tracking-wider">Barang</th>
                                <th class="px-4 py-3 text-[12px] font-semibold text-gray-600 uppercase tracking-wider">Resi</th>
                                <th class="px-4 py-3 text-[12px] font-semibold text-gray-600 uppercase tracking-wider">Box</th>
                                <th class="px-4 py-3 text-[12px] font-semibold text-gray-600 uppercase tracking-wider">Qty</th>
                                <th class="px-4 py-3 text-[12px] font-semibold text-gray-600 uppercase tracking-wider">Request</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($requests as $i => $item)
                                @php
                                    $reqTypes = is_array($item->request_type) ? $item->request_type : json_decode($item->request_type, true);
                                    $labels = [
                                        'extra_bubble_wrap' => 'Extra Bubble Wrap',
                                        'stripping' => 'Stripping',
                                        'pisahin_album' => 'Pisahin Album',
                                        'take_out_freebies' => 'Take Out Freebies',
                                    ];
                                @endphp
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-4 py-3 text-[13px] text-gray-500">{{ $i + 1 }}</td>
                                    <td class="px-4 py-3 text-[13px] font-medium text-gray-900">{{ $item->customer?->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-[13px] text-gray-900">{{ $item->name }}</td>
                                    <td class="px-4 py-3 text-[13px] font-mono text-gray-700">{{ $item->resi_number ?? '-' }}</td>
                                    <td class="px-4 py-3 text-[13px] text-gray-700">{{ $item->box?->display_name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-[13px] text-gray-700">{{ $item->quantity }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($reqTypes ?? [] as $req)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                                    {{ $labels[$req] ?? $req }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-4 py-3 border-t border-gray-100 text-[12px] text-gray-500">
                    {{ $requests->count() }} request{{ $requests->count() > 1 ? 's' : '' }} ditemukan
                </div>
            @endif
        </div>

    </div>
</div>
