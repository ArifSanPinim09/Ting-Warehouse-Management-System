<div class="min-h-screen bg-[#f8fafc]">

    {{-- Livewire Loading State --}}
    <div wire:loading class="fixed inset-x-0 top-16 h-0.5 bg-primary/20 z-50">
        <div class="h-full bg-primary animate-pulse w-1/3 rounded-full"></div>
    </div>

    {{-- Page Header --}}
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <h1 class="text-[20px] font-bold text-primary">New Batch</h1>
            <p class="text-[13px] text-gray-500 mt-1">Create a new shipping batch</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

        {{-- ═══════════════════════════════════════════════════ --}}
        {{-- CREATE BATCH FORM                                   --}}
        {{-- ═══════════════════════════════════════════════════ --}}
        <div class="bg-white rounded-[12px] border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-[15px] font-semibold text-gray-900">Create Batch</h2>
                <p class="text-[12px] text-gray-400 mt-0.5">Define batch details and open/close dates</p>
            </div>

            <form wire:submit.prevent="createBatch" class="p-6 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    {{-- Batch Name --}}
                    <div>
                        <label class="block text-[12px] font-medium text-gray-600 mb-1">Batch Name <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="batchName" maxlength="100" placeholder="e.g. 129, TW-2026-SEA-002"
                            class="w-full px-3 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors">
                        @error('batchName') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Huruf Box --}}
                    <div>
                        <label class="block text-[12px] font-medium text-gray-600 mb-1">Box Letter <span class="text-gray-400 font-normal">optional</span></label>
                        <input type="text" wire:model="hurufBox" maxlength="10" placeholder="e.g. H, J, A"
                            class="w-full px-3 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors">
                        @error('hurufBox') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Open Date --}}
                    <div>
                        <label class="block text-[12px] font-medium text-gray-600 mb-1">Open Date <span class="text-red-500">*</span></label>
                        <input type="date" wire:model="openDate"
                            class="w-full px-3 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors">
                        @error('openDate') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Close Date --}}
                    <div>
                        <label class="block text-[12px] font-medium text-gray-600 mb-1">Close Date <span class="text-gray-400 font-normal">optional</span></label>
                        <input type="date" wire:model="closeDate"
                            class="w-full px-3 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors">
                        @error('closeDate') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Method --}}
                    <div>
                        <label class="block text-[12px] font-medium text-gray-600 mb-1">Method <span class="text-red-500">*</span></label>
                        <select wire:model="method"
                            class="w-full px-3 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors">
                            <option value="air">Air</option>
                            <option value="sea">Sea</option>
                        </select>
                        @error('method') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Type --}}
                    <div>
                        <label class="block text-[12px] font-medium text-gray-600 mb-1">Type <span class="text-red-500">*</span></label>
                        <select wire:model="type"
                            class="w-full px-3 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors">
                            <option value="sharing">Sharing</option>
                            <option value="direct">Direct</option>
                        </select>
                        @error('type') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end pt-2 border-t border-gray-100">
                    <button type="submit"
                        class="px-4 py-2 text-[13px] font-medium text-white bg-primary rounded-[8px] hover:bg-primary-light transition-colors inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Create Batch
                    </button>
                </div>
            </form>
        </div>

        {{-- ═══════════════════════════════════════════════════ --}}
        {{-- BATCH LIST                                          --}}
        {{-- ═══════════════════════════════════════════════════ --}}
        <div class="bg-white rounded-[12px] border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-[15px] font-semibold text-gray-900">All Batches</h2>
                <p class="text-[12px] text-gray-400 mt-0.5">List of all created batches</p>
            </div>

            @if($batches->isEmpty())
                <div class="p-12 text-center">
                    <h3 class="text-[14px] font-semibold text-gray-700 mb-1">No batches yet</h3>
                    <p class="text-[13px] text-gray-500">Create your first batch using the form above.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-gray-100 bg-gray-50/50">
                                <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Batch Name</th>
                                <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Box</th>
                                <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Method</th>
                                <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Open Date</th>
                                <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Close Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($batches as $batch)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-5 py-3 text-[13px] font-semibold text-gray-900">{{ $batch->batch_name }}</td>
                                    <td class="px-5 py-3 text-[13px] text-gray-700">{{ $batch->huruf_box ?? '—' }}</td>
                                    <td class="px-5 py-3">
                                        <span class="text-[12px] font-medium px-2 py-0.5 rounded-full {{ $batch->type === 'sharing' ? 'bg-blue-50 text-blue-700' : 'bg-purple-50 text-purple-700' }}">
                                            {{ ucfirst($batch->type) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3">
                                        <span class="text-[12px] font-medium px-2 py-0.5 rounded-full {{ $batch->method === 'air' ? 'bg-blue-50 text-blue-700' : 'bg-cyan-50 text-cyan-700' }}">
                                            {{ strtoupper($batch->method) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3">
                                        @php
                                            $statusColors = [
                                                'open' => 'bg-emerald-100 text-emerald-700',
                                                'sent_to_cargo' => 'bg-blue-100 text-blue-700',
                                                'otw_ina' => 'bg-amber-100 text-amber-700',
                                                'up_invoice' => 'bg-purple-100 text-purple-700',
                                                'done' => 'bg-gray-100 text-gray-700',
                                            ];
                                            $color = $statusColors[$batch->status] ?? 'bg-gray-100 text-gray-700';
                                        @endphp
                                        <span class="text-[12px] font-medium px-2 py-0.5 rounded-full {{ $color }}">
                                            {{ str_replace('_', ' ', Str::title(Str::lower($batch->status))) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-[13px] text-gray-700">
                                        {{ $batch->open_date ? \Carbon\Carbon::parse($batch->open_date)->format('d M Y') : '—' }}
                                    </td>
                                    <td class="px-5 py-3 text-[13px] text-gray-700">
                                        {{ $batch->close_date ? \Carbon\Carbon::parse($batch->close_date)->format('d M Y') : '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($batches->hasPages())
                    <div class="px-5 py-4 border-t border-gray-100">
                        {{ $batches->links() }}
                    </div>
                @endif
            @endif
        </div>

    </div>
</div>
