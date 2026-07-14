<div class="min-h-screen bg-[#f8fafc]">
    {{-- Page Header --}}
    <div class="bg-white border-b border-gray-100">
        <div class="mx-auto w-full max-w-[1500px] px-4 md:px-6 lg:px-8 xl:px-10 2xl:px-12 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-[22px] font-bold text-gray-900 tracking-tight">Input Barang No Tuan</h1>
                    <p class="text-body text-gray-500 mt-0.5">Input barang yang tiba di warehouse tanpa ada yang setor resi</p>
                </div>
                <a href="{{ route('admin.boxes') }}" wire:navigate
                    class="flex items-center gap-2 px-4 py-2 text-body font-medium text-gray-700 bg-white border border-gray-200 rounded-[8px] hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="mx-auto w-full max-w-[900px] px-4 md:px-6 lg:px-8 xl:px-10 2xl:px-12 py-6 space-y-6">

        {{-- Info Box --}}
        <div class="bg-amber-50 border border-amber-200 rounded-[12px] p-4">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                <div>
                    <p class="text-body font-semibold text-amber-800">Barang No Tuan</p>
                    <p class="text-body text-amber-700 mt-1">Barang yang tiba di warehouse tanpa ada customer yang setor resi. Barang akan otomatis tampil di halaman "No Tuan" customer dan bisa diklaim dengan denda Rp 5.000.</p>
                </div>
            </div>
        </div>

        {{-- Form Card --}}
        <div class="bg-white rounded-[12px] border border-gray-100 p-6">
            <form wire:submit.prevent="submit" class="space-y-5">

                {{-- Nama Barang --}}
                <div>
                    <label class="block text-body font-medium text-gray-700 mb-1.5">
                        Nama Barang <span class="text-red-500">*</span>
                    </label>
                    <input type="text" wire:model="name"
                        placeholder="Contoh: Sepatu Nike Air Max"
                        class="w-full px-3 py-2.5 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors">
                    @error('name') <p class="text-caption text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Jumlah --}}
                <div>
                    <label class="block text-body font-medium text-gray-700 mb-1.5">
                        Jumlah <span class="text-red-500">*</span>
                    </label>
                    <input type="number" wire:model="quantity" min="1" max="9999"
                        class="w-full px-3 py-2.5 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors">
                    @error('quantity') <p class="text-caption text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Box --}}
                <div>
                    <label class="block text-body font-medium text-gray-700 mb-1.5">
                        Box <span class="text-red-500">*</span>
                    </label>
                    <select wire:model="boxId"
                        class="w-full px-3 py-2.5 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors">
                        <option value="">Pilih box tempat barang ini berada</option>
                        @foreach($boxes as $box)
                            <option value="{{ $box->id }}">
                                {{ $box->display_name }}
                                — {{ strtoupper($box->type) }} / {{ strtoupper($box->method) }}
                                ({{ $box->status }})
                            </option>
                        @endforeach
                    </select>
                    @error('boxId') <p class="text-caption text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label class="block text-body font-medium text-gray-700 mb-1.5">
                        Deskripsi Barang <span class="text-gray-400">(opsional)</span>
                    </label>
                    <textarea wire:model="description" rows="3" maxlength="1000"
                        placeholder="Deskripsi barang, ciri-ciri, atau info lainnya..."
                        class="w-full px-3 py-2.5 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors resize-none"></textarea>
                    @error('description') <p class="text-caption text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Foto Barang --}}
                <div>
                    <label class="block text-body font-medium text-gray-700 mb-1.5">
                        Foto Barang <span class="text-gray-400">(opsional)</span>
                    </label>
                    <input type="file" wire:model="photo" accept="image/jpeg,image/png"
                        class="w-full px-3 py-2.5 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors file:mr-3 file:py-1 file:px-3 file:rounded-[6px] file:border-0 file:text-caption file:font-medium file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                    @error('photo') <p class="text-caption text-red-500 mt-1">{{ $message }}</p> @enderror

                    {{-- Preview --}}
                    @if($photo)
                        <div class="mt-3">
                            <img src="{{ $photo->temporaryUrl() }}" class="w-32 h-32 object-cover rounded-[8px] border border-gray-200">
                        </div>
                    @endif
                </div>

                {{-- Catatan --}}
                <div>
                    <label class="block text-body font-medium text-gray-700 mb-1.5">
                        Catatan <span class="text-gray-400">(opsional)</span>
                    </label>
                    <textarea wire:model="notes" rows="2" maxlength="500"
                        placeholder="Catatan tambahan untuk admin..."
                        class="w-full px-3 py-2.5 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors resize-none"></textarea>
                    @error('notes') <p class="text-caption text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Submit --}}
                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('admin.boxes') }}" wire:navigate
                        class="px-5 py-2.5 text-body font-medium text-gray-700 bg-white border border-gray-200 rounded-[8px] hover:bg-gray-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit" wire:loading.attr="disabled"
                        class="flex items-center gap-2 px-5 py-2.5 text-body font-medium text-white bg-primary rounded-[8px] hover:bg-primary-light transition-colors disabled:opacity-50">
                        <svg wire:loading.remove class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span wire:loading.remove>Input Barang No Tuan</span>
                        <span wire:loading>Menyimpan...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
