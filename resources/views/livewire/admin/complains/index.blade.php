<div class="min-h-screen bg-[#f8fafc]">
    {{-- Page Header --}}
    <div class="bg-white border-b border-gray-100">
        <div class="mx-auto w-full max-w-[1500px] px-4 md:px-6 lg:px-8 xl:px-10 2xl:px-12 py-6">
            <div>
                <h1 class="text-[22px] font-bold text-gray-900 tracking-tight">Kelola Komplain</h1>
                <p class="text-body text-gray-500 mt-0.5">Review dan proses komplain dari customer</p>
            </div>
        </div>
    </div>

    <div class="mx-auto w-full max-w-[1500px] px-4 md:px-6 lg:px-8 xl:px-10 2xl:px-12 py-6 space-y-4">

        {{-- Filters --}}
        <div class="bg-white rounded-[12px] border border-gray-100 p-4">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari customer, jenis, invoice, atau resi..."
                        class="w-full pl-10 pr-4 py-2.5 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors placeholder:text-gray-400"
                    >
                </div>
                <select wire:model.live="filterStatus" class="py-2.5 px-3 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors text-gray-600 sm:w-48">
                    <option value="">Semua Status</option>
                    <option value="open">Open</option>
                    <option value="in_review">In Review</option>
                    <option value="processing">Processing</option>
                    <option value="resolved">Resolved</option>
                </select>
                {{-- Sprint 5C: Filter tanggal --}}
                <input type="date" wire:model.live="filterDateFrom" class="py-2.5 px-3 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors text-gray-600" placeholder="Dari">
                <input type="date" wire:model.live="filterDateTo" class="py-2.5 px-3 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors text-gray-600" placeholder="Sampai">
            </div>
        </div>

        {{-- Content --}}
        <div class="flex gap-6" x-data="{ detailOpen: @js($showDetail) }" x-effect="detailOpen = $wire.showDetail">
            {{-- Table / List --}}
            <div class="flex-1 min-w-0" :class="detailOpen && 'hidden lg:block'">
                @if($complains->isEmpty())
                    <div class="bg-white rounded-[12px] border border-gray-100">
                        <div class="flex flex-col items-center py-16 text-center px-4">
                            <div class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            </div>
                            <p class="text-body font-medium text-gray-500">Tidak ada komplain</p>
                            <p class="text-caption text-gray-400 mt-1">Komplain dari customer akan muncul di sini</p>
                        </div>
                    </div>
                @else
                    {{-- Desktop Table --}}
                    <div class="hidden md:block bg-white rounded-[12px] border border-gray-100 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-gray-100">
                                        <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Customer</th>
                                        <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Jenis</th>
                                        <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Resolusi</th>
                                        <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Invoice</th>
                                        <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                                        <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Tanggal</th>
                                        <th class="text-right px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($complains as $complain)
                                        <tr class="hover:bg-gray-50/50 transition-colors cursor-pointer" wire:click="selectComplain({{ $complain->id }})">
                                            <td class="px-5 py-3.5">
                                                <p class="text-body font-semibold text-gray-900">{{ $complain->customer->name ?? '-' }}</p>
                                                @if($complain->resi_number)
                                                    <p class="text-caption text-gray-400">Resi: {{ $complain->resi_number }}</p>
                                                @endif
                                            </td>
                                            <td class="px-5 py-3.5">
                                                <span class="text-body text-gray-700">{{ $complain->type }}</span>
                                            </td>
                                            <td class="px-5 py-3.5">
                                                <span class="text-body text-gray-700 capitalize">{{ $complain->resolution ?? '-' }}</span>
                                            </td>
                                            <td class="px-5 py-3.5">
                                                <span class="text-body font-mono text-gray-700">{{ $complain->invoice_number ?? '-' }}</span>
                                            </td>
                                            <td class="px-5 py-3.5">
                                                <x-status-badge :status="$complain->status" />
                                            </td>
                                            <td class="px-5 py-3.5">
                                                <span class="text-caption text-gray-500">{{ $complain->created_at->format('d M Y') }}</span>
                                            </td>
                                            <td class="px-5 py-3.5 text-right">
                                                <button wire:click.stop="selectComplain({{ $complain->id }})" class="text-caption font-medium text-accent hover:text-primary transition-colors">
                                                    Detail
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($complains->hasPages())
                            <div class="px-5 py-3 border-t border-gray-100">{{ $complains->links() }}</div>
                        @endif
                    </div>

                    {{-- Mobile Cards --}}
                    <div class="md:hidden space-y-3">
                        @foreach($complains as $complain)
                            <div wire:click="selectComplain({{ $complain->id }})" class="bg-white rounded-[12px] border border-gray-100 p-4 hover:shadow-card-hover transition-shadow cursor-pointer">
                                <div class="flex items-start justify-between mb-3">
                                    <div>
                                        <p class="text-body font-semibold text-gray-900">{{ $complain->customer->name ?? '-' }}</p>
                                        <p class="text-caption text-gray-500 mt-0.5">{{ $complain->type }}</p>
                                    </div>
                                    <x-status-badge :status="$complain->status" />
                                </div>
                                <p class="text-body text-gray-600 line-clamp-2 mb-2">{{ $complain->description }}</p>
                                <div class="flex items-center gap-3 text-caption text-gray-400">
                                    @if($complain->resolution)
                                        <span>Resolusi: <span class="text-gray-600 capitalize">{{ $complain->resolution }}</span></span>
                                    @endif
                                    <span>{{ $complain->created_at->format('d M Y') }}</span>
                                </div>
                            </div>
                        @endforeach
                        @if($complains->hasPages())
                            <div class="py-2">{{ $complains->links() }}</div>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Detail Panel --}}
            @if($showDetail && $selectedComplain)
                <div class="w-full lg:w-[420px] flex-shrink-0" x-show="detailOpen" x-transition>
                    <div class="bg-white rounded-[12px] border border-gray-100 sticky top-[88px]">
                        {{-- Detail Header --}}
                        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="text-body font-semibold text-gray-900">Detail Komplain</h3>
                            <button wire:click="closeDetail" class="p-2 min-w-[44px] min-h-[44px] flex items-center justify-center rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <div class="p-5 space-y-5 max-h-[calc(100vh-200px)] overflow-y-auto">
                            {{-- Status & Type --}}
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-caption text-gray-500">Status</span>
                                    <x-status-badge :status="$selectedComplain->status" size="lg" />
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-caption text-gray-500">Jenis Komplain</span>
                                    <span class="text-body font-medium text-gray-900">{{ $selectedComplain->type }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-caption text-gray-500">Resolusi</span>
                                    <span class="text-body text-gray-700 capitalize">{{ $selectedComplain->resolution ?? '-' }}</span>
                                </div>
                            </div>

                            {{-- Customer Info --}}
                            <div class="p-3 bg-gray-50 rounded-[8px] space-y-2">
                                <p class="text-caption font-semibold text-gray-700 uppercase tracking-wide">Customer</p>
                                <div class="space-y-1.5">
                                    <div class="flex items-center justify-between">
                                        <span class="text-caption text-gray-500">Nama</span>
                                        <span class="text-body text-gray-700">{{ $selectedComplain->customer->name ?? '-' }}</span>
                                    </div>
                                    @if($selectedComplain->invoice_number)
                                        <div class="flex items-center justify-between">
                                            <span class="text-caption text-gray-500">Invoice</span>
                                            <span class="text-body font-mono text-gray-700">{{ $selectedComplain->invoice_number }}</span>
                                        </div>
                                    @endif
                                    @if($selectedComplain->resi_number)
                                        <div class="flex items-center justify-between">
                                            <span class="text-caption text-gray-500">Resi</span>
                                            <span class="text-body font-mono text-gray-700">{{ $selectedComplain->resi_number }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Description --}}
                            <div>
                                <p class="text-caption font-semibold text-gray-700 mb-2 uppercase tracking-wide">Deskripsi</p>
                                <p class="text-body text-gray-700 whitespace-pre-wrap">{{ $selectedComplain->description ?? '-' }}</p>
                            </div>

                            {{-- Evidence --}}
                            @if($selectedComplain->photo_url || $selectedComplain->video_url)
                                <div>
                                    <p class="text-caption font-semibold text-gray-700 mb-2 uppercase tracking-wide">Bukti</p>
                                    @if($selectedComplain->photo_url)
                                        <div class="mb-2">
                                            <img src="{{ Storage::url($selectedComplain->photo_url) }}" alt="Foto komplain" class="w-full rounded-[8px] border border-gray-100">
                                        </div>
                                    @endif
                                    @if($selectedComplain->video_url)
                                        <div>
                                            <video controls class="w-full rounded-[8px] border border-gray-100" preload="metadata">
                                                <source src="{{ Storage::url($selectedComplain->video_url) }}" type="video/mp4">
                                                Browser tidak mendukung video player.
                                            </video>
                                            <a href="{{ Storage::url($selectedComplain->video_url) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] text-accent hover:text-primary transition-colors mt-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                Download Video
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            {{-- Status Timeline --}}
                            <div>
                                <p class="text-caption font-semibold text-gray-700 mb-3 uppercase tracking-wide">Timeline Status</p>
                                <div class="space-y-0">
                                    @php
                                        $steps = [
                                            ['status' => 'open', 'label' => 'Open', 'desc' => 'Komplain diajukan'],
                                            ['status' => 'in_review', 'label' => 'In Review', 'desc' => 'Sedang ditinjau'],
                                            ['status' => 'processing', 'label' => 'Processing', 'desc' => 'Sedang diproses'],
                                            ['status' => 'resolved', 'label' => 'Resolved', 'desc' => 'Selesai'],
                                        ];
                                        $currentIndex = collect($steps)->search(fn($s) => $s['status'] === $selectedComplain->status);
                                    @endphp
                                    @foreach($steps as $i => $step)
                                        @php
                                            $isCompleted = $i < $currentIndex;
                                            $isCurrent = $i === $currentIndex;
                                            $isPending = $i > $currentIndex;
                                        @endphp
                                        <div class="flex gap-3">
                                            <div class="flex flex-col items-center">
                                                <div class="w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0 {{ $isCompleted ? 'bg-emerald-500' : ($isCurrent ? 'bg-blue-500 ring-4 ring-blue-100' : 'bg-gray-200') }}">
                                                    @if($isCompleted)
                                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                    @elseif($isCurrent)
                                                        <div class="w-2 h-2 rounded-full bg-white"></div>
                                                    @endif
                                                </div>
                                                @if($i < count($steps) - 1)
                                                    <div class="w-0.5 h-8 {{ $isCompleted ? 'bg-emerald-300' : 'bg-gray-200' }}"></div>
                                                @endif
                                            </div>
                                            <div class="pb-4">
                                                <p class="text-body font-medium {{ $isCurrent ? 'text-blue-700' : ($isCompleted ? 'text-gray-700' : 'text-gray-400') }}">{{ $step['label'] }}</p>
                                                <p class="text-caption text-gray-400">{{ $step['desc'] }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            @php
                                $nextStatus = match($selectedComplain->status) {
                                    'open' => 'in_review',
                                    'in_review' => 'processing',
                                    'processing' => 'resolved',
                                    default => null,
                                };
                                $nextLabel = match($nextStatus) {
                                    'in_review' => 'Mulai Review',
                                    'processing' => 'Mulai Proses',
                                    'resolved' => 'Selesaikan',
                                    default => null,
                                };
                            @endphp
                            @if($nextStatus)
                                <button
                                    wire:click="updateStatus('{{ $nextStatus }}')"
                                    class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-primary text-white text-body font-medium rounded-[8px] hover:bg-primary-light transition-colors"
                                    wire:loading.attr="disabled"
                                >
                                    <svg wire:loading class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                    <svg wire:loading.remove class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                    <span wire:loading.remove>{{ $nextLabel }}</span>
                                    <span wire:loading>Mengubah...</span>
                                </button>
                            @else
                                <div class="p-3 bg-emerald-50 rounded-[8px] text-center">
                                    <p class="text-body font-medium text-emerald-700">Komplain telah selesai</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
