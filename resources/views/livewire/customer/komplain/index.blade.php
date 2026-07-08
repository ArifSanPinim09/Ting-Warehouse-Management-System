<div class="min-h-screen bg-[#f8fafc]">
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-display text-primary">Komplain</h1>
                    <p class="text-body text-gray-500 mt-1">Ajukan dan lacak status komplain Anda</p>
                </div>
                <button wire:click="openForm" class="ds-btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Ajukan Komplain
                </button>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-4">
        <div class="ds-card p-4">
            <select wire:model.live="filterStatus" class="ds-input sm:w-48">
                <option value="">Semua Status</option>
                @foreach(\App\Models\Complain::getValidStatuses() as $status)
                    <option value="{{ $status }}">{{ $status }}</option>
                @endforeach
            </select>
        </div>

        @if($complaints->isEmpty())
            <x-empty-state
                icon="checklist"
                title="Belum ada komplain"
                text="Jika ada masalah dengan pengiriman Anda, silakan ajukan komplain."
                action="Ajukan Komplain"
            >
                <button wire:click="openForm" class="ds-btn-primary mt-2">Ajukan Komplain</button>
            </x-empty-state>
        @else
            <div class="space-y-3">
                @foreach($complaints as $complaint)
                    <div class="ds-card p-5 space-y-3">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 mb-1.5">
                                    <span class="ds-badge-neutral">#{{ $complaint->id }}</span>
                                    <x-status-badge :status="$complaint->status" />
                                </div>
                                <p class="text-subtitle font-semibold text-primary">{{ $complaint->type }}</p>
                                <p class="text-body text-gray-500 mt-1 line-clamp-3">{{ $complaint->description }}</p>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-4 pt-2 border-t border-gray-100 text-caption text-gray-500">
                            @if($complaint->resolution)
                                <span>Resolusi: <span class="font-medium text-gray-700">{{ ucfirst($complaint->resolution) }}</span></span>
                            @endif
                            @if($complaint->invoice_number)
                                <span>Invoice: <span class="font-mono text-gray-700">{{ $complaint->invoice_number }}</span></span>
                            @endif
                            @if($complaint->resi_number)
                                <span>Resi: <span class="font-mono text-gray-700">{{ $complaint->resi_number }}</span></span>
                            @endif
                            <span class="text-gray-400">{{ $complaint->created_at->format('d M Y H:i') }}</span>
                        </div>
                        @if($complaint->photo_url || $complaint->video_url)
                            <div class="flex items-center gap-2 pt-2">
                                @if($complaint->photo_url)
                                    <span class="ds-badge-neutral text-micro">📷 Foto terlampir</span>
                                @endif
                                @if($complaint->video_url)
                                    <span class="ds-badge-neutral text-micro">🎬 Video terlampir</span>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="mt-6">{{ $complaints->links() }}</div>
        @endif
    </div>

    {{-- Komplain Form Modal --}}
    @if($showForm)
        <div class="fixed inset-0 z-50 overflow-y-auto" wire:click.self="closeForm">
            <div class="fixed inset-0 bg-gray-500/75 transition-opacity"></div>
            <div class="flex min-h-full items-end sm:items-center justify-center p-4">
                <div class="relative bg-white rounded-t-modal sm:rounded-modal shadow-modal w-full max-w-lg max-h-[90vh] overflow-y-auto animate-slide-up" wire:click.stop>
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 sticky top-0 bg-white z-10">
                        <h3 class="text-title font-semibold text-primary">Ajukan Komplain</h3>
                        <button wire:click="closeForm" class="p-2 min-w-[44px] min-h-[44px] flex items-center justify-center rounded-button text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form wire:submit="submit" class="px-6 py-5 space-y-5">
                        <div>
                            <label class="ds-label">Jenis Komplain <span class="text-red-500">*</span></label>
                            <select wire:model="type" class="ds-input @error('type') ds-input-error @enderror">
                                <option value="">Pilih jenis komplain...</option>
                                @foreach($complaintTypes as $ct)
                                    <option value="{{ $ct }}">{{ $ct }}</option>
                                @endforeach
                            </select>
                            @error('type') <p class="ds-error">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="ds-label">Resolusi <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="relative cursor-pointer">
                                    <input type="radio" wire:model="resolution" value="refund" class="sr-only peer" />
                                    <div class="flex items-center gap-3 p-3 rounded-card border-2 border-gray-200 transition-all peer-checked:border-accent peer-checked:bg-accent/5 hover:border-gray-300">
                                        <span class="text-body font-medium text-gray-700">Refund</span>
                                    </div>
                                </label>
                                <label class="relative cursor-pointer">
                                    <input type="radio" wire:model="resolution" value="replacement" class="sr-only peer" />
                                    <div class="flex items-center gap-3 p-3 rounded-card border-2 border-gray-200 transition-all peer-checked:border-accent peer-checked:bg-accent/5 hover:border-gray-300">
                                        <span class="text-body font-medium text-gray-700">Penggantian</span>
                                    </div>
                                </label>
                            </div>
                            @error('resolution') <p class="ds-error mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="ds-label">No. Invoice</label>
                                <input type="text" wire:model="invoiceNumber" placeholder="Opsional" class="ds-input" />
                            </div>
                            <div>
                                <label class="ds-label">No. Resi</label>
                                <input type="text" wire:model="resiNumber" placeholder="Opsional" class="ds-input" />
                            </div>
                        </div>

                        <div>
                            <label class="ds-label">Deskripsi <span class="text-red-500">*</span></label>
                            <textarea wire:model="description" rows="4" placeholder="Jelaskan masalah Anda secara detail..." class="ds-input @error('description') ds-input-error @enderror"></textarea>
                            @error('description') <p class="ds-error">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="ds-label">Foto (opsional)</label>
                                <input type="file" wire:model="photoFile" accept="image/jpeg,image/png" class="ds-input text-caption file:mr-3 file:py-1 file:px-3 file:rounded-button file:border-0 file:text-caption file:font-medium file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200" />
                                @error('photoFile') <p class="ds-error">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="ds-label">Video (opsional)</label>
                                <input type="file" wire:model="videoFile" accept="video/mp4,video/quicktime" class="ds-input text-caption file:mr-3 file:py-1 file:px-3 file:rounded-button file:border-0 file:text-caption file:font-medium file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200" />
                                @error('videoFile') <p class="ds-error">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-2">
                            <button type="button" wire:click="closeForm" class="ds-btn-secondary">Batal</button>
                            <button type="submit" class="ds-btn-primary" wire:loading.attr="disabled">
                                <svg wire:loading class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                <span wire:loading.remove>Ajukan Komplain</span>
                                <span wire:loading>Mengirim...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
