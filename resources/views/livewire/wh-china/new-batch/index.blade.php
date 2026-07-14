<div class="min-h-screen bg-[#f8fafc]">

    {{-- Livewire Loading State --}}
    <div wire:loading class="fixed inset-x-0 top-16 h-0.5 bg-primary/20 z-50">
        <div class="h-full bg-primary animate-pulse w-1/3 rounded-full"></div>
    </div>

    {{-- Page Header --}}
    <div class="bg-white border-b border-gray-100">
        <div class="mx-auto w-full max-w-[1500px] px-4 md:px-6 lg:px-8 xl:px-10 2xl:px-12 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-[20px] font-bold text-primary">New Batch</h1>
                    <p class="text-[13px] text-gray-500 mt-1">Create and manage shipping batches</p>
                </div>
                <button wire:click="openModal"
                    class="px-4 py-2 text-[13px] font-medium text-white bg-primary rounded-[8px] hover:bg-primary-light transition-colors inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Create Batch
                </button>
            </div>
        </div>
    </div>

    <div class="mx-auto w-full max-w-[1500px] px-4 md:px-6 lg:px-8 xl:px-10 2xl:px-12 py-6 space-y-6">

        {{-- ═══════════════════════════════════════════════════ --}}
        {{-- BATCH LIST                                        --}}
        {{-- ═══════════════════════════════════════════════════ --}}
        <div class="bg-white rounded-[12px] border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-[15px] font-semibold text-gray-900">All Batches</h2>
                <p class="text-[12px] text-gray-400 mt-0.5">List of all created batches</p>
            </div>

            @if($batches->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                        <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <h3 class="text-[14px] font-semibold text-gray-700 mb-1">No batches yet</h3>
                    <p class="text-[13px] text-gray-500">Click "Create Batch" to add your first batch.</p>
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

    {{-- ═══════════════════════════════════════════════════ --}}
    {{-- MODAL: Create Batch                               --}}
    {{-- ═══════════════════════════════════════════════════ --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" wire:click.self="closeModal">
            <div class="fixed inset-0 bg-black/30 transition-opacity"></div>
            <div class="flex min-h-full items-end sm:items-center justify-center p-4">
                <div class="relative bg-white rounded-[16px] shadow-lg w-full max-w-lg transform transition-all" @click.stop>
                    {{-- Modal Header --}}
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-[10px] bg-primary/10 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </div>
                            <div>
                                <h3 class="text-[15px] font-semibold text-gray-900">Create New Batch</h3>
                                <p class="text-[12px] text-gray-400 mt-0.5">Define batch details and open/close dates</p>
                            </div>
                        </div>
                        <button wire:click="closeModal" class="p-2 min-w-[40px] min-h-[40px] flex items-center justify-center rounded-[8px] text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    {{-- Modal Body --}}
                    <form wire:submit.prevent="createBatch" class="p-6 space-y-4">
                        {{-- Batch Name --}}
                        <div>
                            <label class="block text-[12px] font-medium text-gray-600 mb-1">Batch Name <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="batchName" maxlength="100" placeholder="e.g. 129, TW-2026-SEA-002"
                                class="w-full px-3 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors">
                            @error('batchName') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Box Letter --}}
                        <div>
                            <label class="block text-[12px] font-medium text-gray-600 mb-1">Box Letter <span class="text-gray-400 font-normal">optional</span></label>
                            <input type="text" wire:model="hurufBox" maxlength="10" placeholder="e.g. H, J, A"
                                class="w-full px-3 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors">
                            @error('hurufBox') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Dates --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[12px] font-medium text-gray-600 mb-1">Open Date <span class="text-red-500">*</span></label>
                                <input type="date" wire:model="openDate"
                                    class="w-full px-3 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors">
                                @error('openDate') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-[12px] font-medium text-gray-600 mb-1">Close Date <span class="text-gray-400 font-normal">optional</span></label>
                                <input type="date" wire:model="closeDate"
                                    class="w-full px-3 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors">
                                @error('closeDate') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Method + Type --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[12px] font-medium text-gray-600 mb-1">Method <span class="text-red-500">*</span></label>
                                <select wire:model="method"
                                    class="w-full px-3 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors">
                                    <option value="air">Air</option>
                                    <option value="sea">Sea</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[12px] font-medium text-gray-600 mb-1">Type <span class="text-red-500">*</span></label>
                                <select wire:model="type"
                                    class="w-full px-3 py-2 text-[13px] bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors">
                                    <option value="sharing">Sharing</option>
                                    <option value="direct">Direct</option>
                                </select>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                            <button type="button" wire:click="closeModal"
                                class="px-4 py-2 text-[13px] font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-[8px] transition-colors">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-4 py-2 text-[13px] font-medium text-white bg-primary rounded-[8px] hover:bg-primary-light transition-colors inline-flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Create
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

</div>
