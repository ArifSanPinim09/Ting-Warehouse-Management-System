<div class="min-h-screen bg-[#f8fafc]">

    <div class="bg-white border-b border-gray-100">
        <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <h1 class="text-[20px] font-bold text-primary">Kurs Management 汇率</h1>
            <p class="text-[13px] text-gray-500 mt-1">Set and track Yuan → IDR exchange rate history</p>
        </div>
    </div>

    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

        {{-- Current Kurs --}}
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-[12px] p-6 text-white">
            <p class="text-[13px] text-blue-100 font-medium">Current Kurs (Yuan → IDR)</p>
            <p class="text-[32px] font-bold mt-1">¥1 = Rp {{ number_format($currentKurs?->kurs_value ?? 0, 0, ',', '.') }}</p>
            <p class="text-[12px] text-blue-200 mt-1">Effective: {{ $currentKurs?->effective_date?->format('d M Y') ?? '-' }}</p>
        </div>

        {{-- Action Button --}}
        <div class="flex justify-end">
            <button wire:click="openForm" class="bg-primary text-white text-[13px] font-medium rounded-[8px] px-4 py-2 hover:bg-primary/90">
                + Update Kurs
            </button>
        </div>

        {{-- Form Modal --}}
        @if($showForm)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="fixed inset-0 bg-black/30" wire:click="closeForm"></div>
                <div class="relative bg-white rounded-[12px] shadow-xl w-full max-w-md z-10">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-[15px] font-semibold text-gray-900">Update Kurs</h3>
                    </div>
                    <form wire:submit="save" class="p-6 space-y-4">
                        <div>
                            <label class="block text-[12px] font-medium text-gray-600 mb-1">Kurs Value (Rp per ¥1)</label>
                            <input wire:model="kursValue" type="number" step="0.01" placeholder="2460" class="w-full border border-gray-200 rounded-[8px] px-3 py-2 text-[14px] focus:border-primary outline-none">
                            @error('kursValue') <p class="text-[12px] text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-[12px] font-medium text-gray-600 mb-1">Effective Date</label>
                            <input wire:model="effectiveDate" type="date" class="w-full border border-gray-200 rounded-[8px] px-3 py-2 text-[14px] focus:border-primary outline-none">
                            @error('effectiveDate') <p class="text-[12px] text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="flex gap-2 pt-2">
                            <button type="button" wire:click="closeForm" class="flex-1 border border-gray-200 text-gray-600 text-[13px] font-medium rounded-[8px] py-2 hover:bg-gray-50">Cancel</button>
                            <button type="submit" class="flex-1 bg-primary text-white text-[13px] font-medium rounded-[8px] py-2 hover:bg-primary/90">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        {{-- History Table --}}
        <div class="bg-white rounded-[12px] border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-[15px] font-semibold text-gray-900">Kurs History</h2>
            </div>
            @if($history->isEmpty())
                <div class="p-12 text-center text-[13px] text-gray-400">No kurs history yet.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/80 border-b border-gray-100">
                                <th class="px-4 py-3 text-[12px] font-semibold text-gray-600 uppercase">Date</th>
                                <th class="px-4 py-3 text-[12px] font-semibold text-gray-600 uppercase">Kurs (Rp/¥)</th>
                                <th class="px-4 py-3 text-[12px] font-semibold text-gray-600 uppercase">Input By</th>
                                <th class="px-4 py-3 text-[12px] font-semibold text-gray-600 uppercase">Created</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($history as $kurs)
                                <tr class="hover:bg-gray-50/50 {{ $kurs->id === $currentKurs?->id ? 'bg-blue-50/50' : '' }}">
                                    <td class="px-4 py-3 text-[13px] font-medium text-gray-900">{{ $kurs->effective_date?->format('d M Y') }}</td>
                                    <td class="px-4 py-3 text-[13px] text-gray-700">Rp {{ number_format($kurs->kurs_value, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-[13px] text-gray-600">{{ $kurs->inputBy?->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-[13px] text-gray-400">{{ $kurs->created_at->format('d M Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-3 border-t border-gray-100">{{ $history->links() }}</div>
            @endif
        </div>
    </div>
</div>
