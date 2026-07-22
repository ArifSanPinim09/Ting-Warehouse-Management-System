<div class="min-h-screen bg-[#f8fafc]">

    {{-- Page Header --}}
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-[1500px] mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <h1 class="text-[20px] font-bold text-primary">Request to Send 请求发送</h1>
            <p class="text-[13px] text-gray-500 mt-1">Boxes ready to be sent to cargo — grouped by transport method</p>
        </div>
    </div>

    <div class="max-w-[1500px] mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

        {{-- AIR Section --}}
        <div class="bg-white rounded-[12px] border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-[15px] font-semibold text-gray-900">AIR ✈️</span>
                    <span class="text-[12px] text-gray-400">({{ $airBoxes->count() }} boxes)</span>
                </div>
            </div>

            @if($airBoxes->isEmpty())
                <div class="p-8 text-center text-[13px] text-gray-400">No AIR boxes waiting to send.</div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-4">
                    @foreach($airBoxes as $box)
                        <div class="border border-gray-100 rounded-[8px] p-4 hover:border-primary transition-colors">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[14px] font-bold text-gray-900">{{ $box->display_name }}</span>
                                <x-status-badge status="{{ $box->status }}" />
                            </div>
                            <p class="text-[12px] text-gray-500">{{ $box->customer?->name ?? '-' }}</p>
                            <p class="text-[12px] text-gray-400 mt-1">{{ $box->items->count() }} items</p>
                            <button wire:click="openSendModal({{ $box->id }})" class="mt-3 w-full bg-primary text-white text-[13px] font-medium rounded-[8px] py-2 hover:bg-primary/90 transition-colors">
                                SEND ➤
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- SEA Section --}}
        <div class="bg-white rounded-[12px] border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-[15px] font-semibold text-gray-900">SEA 🚢</span>
                    <span class="text-[12px] text-gray-400">({{ $seaBoxes->count() }} boxes)</span>
                </div>
            </div>

            @if($seaBoxes->isEmpty())
                <div class="p-8 text-center text-[13px] text-gray-400">No SEA boxes waiting to send.</div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-4">
                    @foreach($seaBoxes as $box)
                        <div class="border border-gray-100 rounded-[8px] p-4 hover:border-primary transition-colors">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[14px] font-bold text-gray-900">{{ $box->display_name }}</span>
                                <x-status-badge status="{{ $box->status }}" />
                            </div>
                            <p class="text-[12px] text-gray-500">{{ $box->customer?->name ?? '-' }}</p>
                            <p class="text-[12px] text-gray-400 mt-1">{{ $box->items->count() }} items</p>
                            <button wire:click="openSendModal({{ $box->id }})" class="mt-3 w-full bg-primary text-white text-[13px] font-medium rounded-[8px] py-2 hover:bg-primary/90 transition-colors">
                                SEND ➤
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Send Modal --}}
    @if($showSendModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" wire:keydown.escape="closeSendModal">
            <div class="fixed inset-0 bg-black/30" wire:click="closeSendModal"></div>
            <div class="relative bg-white rounded-[12px] shadow-xl w-full max-w-md max-h-[90vh] overflow-y-auto">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-[15px] font-semibold text-gray-900">Send to Cargo</h3>
                    <button wire:click="closeSendModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit="sendToCargo" class="p-6 space-y-4">
                    @if($selectedBox)
                        <div class="bg-gray-50 rounded-[8px] p-3">
                            <p class="text-[13px] font-medium text-gray-900">{{ $selectedBox->display_name }}</p>
                            <p class="text-[12px] text-gray-500">{{ $selectedBox->customer?->name }} • {{ strtoupper($selectedBox->method) }}</p>
                        </div>
                    @endif

                    <div>
                        <label class="block text-[12px] font-medium text-gray-600 mb-1">Cargo Resi / Tracking Number</label>
                        <input wire:model="cargoResi" type="text" class="w-full border border-gray-200 rounded-[8px] px-3 py-2 text-[13px] focus:border-primary outline-none">
                        @error('cargoResi') <p class="text-[12px] text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-[12px] font-medium text-gray-600 mb-1">Cargo Destination</label>
                        <select wire:model="cargoDestination" class="w-full border border-gray-200 rounded-[8px] px-3 py-2 text-[13px] focus:border-primary outline-none">
                            <option value="">Pilih destinasi...</option>
                            @foreach($destinations as $dest)
                                <option value="{{ $dest->code }}">{{ $dest->code }} — {{ $dest->name }}</option>
                            @endforeach
                        </select>
                        @error('cargoDestination') <p class="text-[12px] text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-[12px] font-medium text-gray-600 mb-1">Berat (kg)</label>
                        <input wire:model="berat" type="number" step="0.01" class="w-full border border-gray-200 rounded-[8px] px-3 py-2 text-[13px] focus:border-primary outline-none">
                        @error('berat') <p class="text-[12px] text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-[12px] font-medium text-gray-600 mb-1">Ukuran (P×L×T)</label>
                        <input wire:model="ukuran" type="text" placeholder="50×40×30" class="w-full border border-gray-200 rounded-[8px] px-3 py-2 text-[13px] focus:border-primary outline-none">
                        @error('ukuran') <p class="text-[12px] text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-[12px] font-medium text-gray-600 mb-1">Foto Cargo (optional)</label>
                        <input wire:model="cargoPhoto" type="file" accept="image/*" class="w-full text-[13px]">
                        @error('cargoPhoto') <p class="text-[12px] text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex gap-2 pt-2">
                        <button type="button" wire:click="closeSendModal" class="flex-1 border border-gray-200 text-gray-600 text-[13px] font-medium rounded-[8px] py-2 hover:bg-gray-50">Cancel</button>
                        <button type="submit" class="flex-1 bg-primary text-white text-[13px] font-medium rounded-[8px] py-2 hover:bg-primary/90">SEND</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
