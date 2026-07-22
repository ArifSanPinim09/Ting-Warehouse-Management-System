<div class="min-h-screen bg-[#f8fafc]">

    <div class="bg-white border-b border-gray-100">
        <div class="max-w-[1500px] mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <h1 class="text-[20px] font-bold text-primary">Shipping & Material Fees 运费</h1>
            <p class="text-[13px] text-gray-500 mt-1">Manage shipping, material, and operational fees in Yuan</p>
        </div>
    </div>

    <div class="max-w-[1500px] mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

        {{-- Form --}}
        <div class="bg-white rounded-[12px] border border-gray-100 p-6">
            <h2 class="text-[15px] font-semibold text-gray-900 mb-4">{{ $editId ? 'Edit Fee' : 'Add New Fee' }}</h2>
            <form wire:submit="save" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-[12px] font-medium text-gray-600 mb-1">Category</label>
                    <select wire:model="category" class="w-full border border-gray-200 rounded-[8px] px-3 py-2 text-[13px] focus:border-primary outline-none">
                        <option value="">Pilih kategori...</option>
                        <option value="shipping">Shipping</option>
                        <option value="material">Material</option>
                        <option value="operational">Operational</option>
                        <option value="other">Other</option>
                    </select>
                    @error('category') <p class="text-[12px] text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-[12px] font-medium text-gray-600 mb-1">Name</label>
                    <input wire:model="name" type="text" class="w-full border border-gray-200 rounded-[8px] px-3 py-2 text-[13px] focus:border-primary outline-none">
                    @error('name') <p class="text-[12px] text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-[12px] font-medium text-gray-600 mb-1">Biaya (Yuan)</label>
                    <input wire:model="biayaYuan" type="number" step="0.01" class="w-full border border-gray-200 rounded-[8px] px-3 py-2 text-[13px] focus:border-primary outline-none">
                    @error('biayaYuan') <p class="text-[12px] text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-[12px] font-medium text-gray-600 mb-1">Status</label>
                    <select wire:model="status" class="w-full border border-gray-200 rounded-[8px] px-3 py-2 text-[13px] focus:border-primary outline-none">
                        <option value="UNPAID">UNPAID</option>
                        <option value="PAID">PAID</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[12px] font-medium text-gray-600 mb-1">Box (optional)</label>
                    <select wire:model="boxId" class="w-full border border-gray-200 rounded-[8px] px-3 py-2 text-[13px] focus:border-primary outline-none">
                        <option value="">No box linked</option>
                        @foreach($boxes as $box)
                            <option value="{{ $box->id }}">{{ $box->display_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[12px] font-medium text-gray-600 mb-1">Notes</label>
                    <input wire:model="notes" type="text" class="w-full border border-gray-200 rounded-[8px] px-3 py-2 text-[13px] focus:border-primary outline-none">
                </div>
                <div class="md:col-span-3 flex gap-2">
                    <button type="submit" class="bg-primary text-white text-[13px] font-medium rounded-[8px] px-4 py-2 hover:bg-primary/90">{{ $editId ? 'Update' : 'Add Fee' }}</button>
                    @if($editId)
                        <button type="button" wire:click="resetForm" class="border border-gray-200 text-gray-600 text-[13px] font-medium rounded-[8px] px-4 py-2 hover:bg-gray-50">Cancel</button>
                    @endif
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-[12px] border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-[15px] font-semibold text-gray-900">Fee List</h2>
                <div class="text-right">
                    <p class="text-[12px] text-gray-400">Total: <span class="font-semibold text-gray-900">¥{{ number_format($totalYuan, 2) }}</span> ≈ Rp {{ number_format($totalRupiah, 0, ',', '.') }}</p>
                </div>
            </div>

            @if($fees->isEmpty())
                <div class="p-12 text-center text-[13px] text-gray-400">No fees recorded yet.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/80 border-b border-gray-100">
                                <th class="px-4 py-3 text-[12px] font-semibold text-gray-600 uppercase">Category</th>
                                <th class="px-4 py-3 text-[12px] font-semibold text-gray-600 uppercase">Name</th>
                                <th class="px-4 py-3 text-[12px] font-semibold text-gray-600 uppercase">Yuan</th>
                                <th class="px-4 py-3 text-[12px] font-semibold text-gray-600 uppercase">Rupiah</th>
                                <th class="px-4 py-3 text-[12px] font-semibold text-gray-600 uppercase">Status</th>
                                <th class="px-4 py-3 text-[12px] font-semibold text-gray-600 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($fees as $fee)
                                <tr class="hover:bg-gray-50/50">
                                    <td class="px-4 py-3 text-[13px] capitalize text-gray-700">{{ $fee->category }}</td>
                                    <td class="px-4 py-3 text-[13px] font-medium text-gray-900">{{ $fee->name }}</td>
                                    <td class="px-4 py-3 text-[13px] text-gray-600">¥{{ number_format($fee->biaya_yuan, 2) }}</td>
                                    <td class="px-4 py-3 text-[13px] text-gray-600">Rp {{ number_format($fee->biaya_rupiah, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3">
                                        <button wire:click="toggleStatus({{ $fee->id }})" class="text-[12px] font-medium px-2 py-1 rounded {{ $fee->status === 'PAID' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                            {{ $fee->status }}
                                        </button>
                                    </td>
                                    <td class="px-4 py-3 flex gap-1">
                                        <button wire:click="edit({{ $fee->id }})" class="text-[12px] text-blue-500 hover:text-blue-700">Edit</button>
                                        <button wire:click="delete({{ $fee->id }})" wire:confirm="Hapus fee ini?" class="text-[12px] text-red-500 hover:text-red-700">Delete</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-3 border-t border-gray-100">{{ $fees->links() }}</div>
            @endif
        </div>
    </div>
</div>
