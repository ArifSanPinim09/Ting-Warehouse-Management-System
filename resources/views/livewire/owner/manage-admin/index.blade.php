<div class="min-h-screen bg-[#f8fafc]">
    {{-- Page Header --}}
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div>
                <h1 class="text-[22px] font-bold text-gray-900 tracking-tight">Manage Admin</h1>
                <p class="text-body text-gray-500 mt-0.5">Kelola akun admin dan hak akses</p>
            </div>
        </div>
    </div>

    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-4">

        {{-- Filters --}}
        <div class="bg-white rounded-[12px] border border-gray-100 p-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="sm:col-span-2 relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama atau email admin..." class="w-full pl-10 pr-4 py-2.5 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors placeholder:text-gray-400">
                </div>
                <select wire:model.live="filterStatus" class="py-2.5 px-3 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors text-gray-600">
                    <option value="">Semua Status</option>
                    <option value="active">Aktif</option>
                    <option value="inactive">Nonaktif</option>
                </select>
            </div>
        </div>

        {{-- Content --}}
        <div class="flex gap-6">
            <div class="flex-1 min-w-0">
                @if($admins->isEmpty())
                    <div class="bg-white rounded-[12px] border border-gray-100">
                        <x-empty-state
                            icon="checklist"
                            title="Tidak ada admin ditemukan"
                            text="Coba ubah filter atau kata kunci pencarian."
                        />
                    </div>
                @else
                    {{-- Desktop Table --}}
                    <div class="hidden md:block bg-white rounded-[12px] border border-gray-100 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-gray-100">
                                        <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Admin</th>
                                        <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Kontak</th>
                                        <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Role</th>
                                        <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                                        <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Terdaftar</th>
                                        <th class="text-right px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($admins as $admin)
                                        <tr class="hover:bg-gray-50/50 transition-colors cursor-pointer" wire:click="selectAdmin({{ $admin->id }})">
                                            <td class="px-5 py-3.5">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                                                        <span class="text-caption font-bold text-primary">{{ strtoupper(substr($admin->name, 0, 1)) }}</span>
                                                    </div>
                                                    <div>
                                                        <p class="text-body font-semibold text-gray-900">{{ $admin->name }}</p>
                                                        <p class="text-caption text-gray-400">{{ $admin->email }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-5 py-3.5">
                                                <span class="text-caption text-gray-600">{{ $admin->phone ?? '-' }}</span>
                                            </td>
                                            <td class="px-5 py-3.5">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-caption font-medium {{ $admin->role === 'owner' ? 'bg-amber-50 text-amber-700' : 'bg-blue-50 text-blue-700' }}">
                                                    {{ ucfirst($admin->role) }}
                                                </span>
                                            </td>
                                            <td class="px-5 py-3.5">
                                                <x-status-badge :status="$admin->status" />
                                            </td>
                                            <td class="px-5 py-3.5">
                                                <span class="text-caption text-gray-500">{{ $admin->created_at->format('d M Y') }}</span>
                                            </td>
                                            <td class="px-5 py-3.5 text-right">
                                                @if($admin->role !== 'owner')
                                                    @if($admin->status === 'active')
                                                        <button wire:click.stop="confirmDeactivate({{ $admin->id }})" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-caption font-medium text-red-600 hover:bg-red-50 rounded-[6px] transition-colors">
                                                            Nonaktifkan
                                                        </button>
                                                    @elseif($admin->status === 'inactive')
                                                        <button wire:click.stop="confirmActivate({{ $admin->id }})" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-caption font-medium text-emerald-600 hover:bg-emerald-50 rounded-[6px] transition-colors">
                                                            Aktifkan
                                                        </button>
                                                    @endif
                                                @else
                                                    <span class="text-caption text-gray-400">Owner</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($admins->hasPages())
                            <div class="px-5 py-3 border-t border-gray-100">
                                {{ $admins->links() }}
                            </div>
                        @endif
                    </div>

                    {{-- Mobile Cards --}}
                    <div class="md:hidden space-y-3">
                        @foreach($admins as $admin)
                            <div wire:click="selectAdmin({{ $admin->id }})" class="bg-white rounded-[12px] border border-gray-100 p-4 hover:shadow-card-hover transition-shadow cursor-pointer">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                                            <span class="text-caption font-bold text-primary">{{ strtoupper(substr($admin->name, 0, 1)) }}</span>
                                        </div>
                                        <div>
                                            <p class="text-body font-semibold text-gray-900">{{ $admin->name }}</p>
                                            <p class="text-caption text-gray-500">{{ $admin->email }}</p>
                                        </div>
                                    </div>
                                    <x-status-badge :status="$admin->status" />
                                </div>
                                <div class="flex items-center gap-3 pt-3 border-t border-gray-100">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-caption font-medium {{ $admin->role === 'owner' ? 'bg-amber-50 text-amber-700' : 'bg-blue-50 text-blue-700' }}">
                                        {{ ucfirst($admin->role) }}
                                    </span>
                                    <span class="text-caption text-gray-400">{{ $admin->created_at->format('d M Y') }}</span>
                                    @if($admin->role !== 'owner')
                                        <div class="ml-auto">
                                            @if($admin->status === 'active')
                                                <button wire:click.stop="confirmDeactivate({{ $admin->id }})" class="text-caption font-medium text-red-600">Nonaktifkan</button>
                                            @elseif($admin->status === 'inactive')
                                                <button wire:click.stop="confirmActivate({{ $admin->id }})" class="text-caption font-medium text-emerald-600">Aktifkan</button>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        @if($admins->hasPages())
                            <div class="px-1">
                                {{ $admins->links() }}
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Detail Panel --}}
            @if($showDetail && $selectedAdmin)
                <div class="w-full lg:w-[420px] flex-shrink-0">
                    <div class="bg-white rounded-[12px] border border-gray-100 sticky top-[88px]">
                        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="text-body font-semibold text-gray-900">Detail Admin</h3>
                            <button wire:click="closeDetail" class="p-2 min-w-[44px] min-h-[44px] flex items-center justify-center rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <div class="p-5 space-y-5">
                            {{-- Profile --}}
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                                    <span class="text-[18px] font-bold text-primary">{{ strtoupper(substr($selectedAdmin->name, 0, 1)) }}</span>
                                </div>
                                <div>
                                    <p class="text-[16px] font-semibold text-gray-900">{{ $selectedAdmin->name }}</p>
                                    <p class="text-body text-gray-500">{{ $selectedAdmin->email }}</p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <x-status-badge :status="$selectedAdmin->status" />
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-caption font-medium {{ $selectedAdmin->role === 'owner' ? 'bg-amber-50 text-amber-700' : 'bg-blue-50 text-blue-700' }}">
                                            {{ ucfirst($selectedAdmin->role) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Info --}}
                            <div class="space-y-3 border-t border-gray-100 pt-4">
                                <div class="flex justify-between text-body">
                                    <span class="text-gray-500">Telepon</span>
                                    <span class="font-medium text-gray-800">{{ $selectedAdmin->phone ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between text-body">
                                    <span class="text-gray-500">Terdaftar</span>
                                    <span class="font-medium text-gray-800">{{ $selectedAdmin->created_at->format('d M Y H:i') }}</span>
                                </div>
                                <div class="flex justify-between text-body">
                                    <span class="text-gray-500">Terakhir Update</span>
                                    <span class="font-medium text-gray-800">{{ $selectedAdmin->updated_at->format('d M Y H:i') }}</span>
                                </div>
                            </div>

                            {{-- Activity History --}}
                            <div class="border-t border-gray-100 pt-4">
                                <p class="text-caption font-semibold text-gray-500 uppercase tracking-wide mb-3">Aktivitas Terbaru</p>
                                @if($adminActivities->isEmpty())
                                    <p class="text-caption text-gray-400 text-center py-4">Belum ada aktivitas</p>
                                @else
                                    <div class="space-y-2 max-h-[240px] overflow-y-auto">
                                        @foreach($adminActivities as $activity)
                                            <div class="flex items-start gap-2.5 p-2.5 rounded-[8px] bg-gray-50">
                                                <div class="w-6 h-6 rounded-full bg-white flex items-center justify-center flex-shrink-0 mt-0.5">
                                                    @php
                                                        $iconColor = match($activity->event) {
                                                            'created', 'generated', 'activated' => 'text-emerald-500',
                                                            'updated', 'verified' => 'text-blue-500',
                                                            'rejected', 'deleted', 'deactivated' => 'text-red-500',
                                                            default => 'text-gray-400',
                                                        };
                                                    @endphp
                                                    <svg class="w-3 h-3 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-caption text-gray-700">
                                                        <span class="font-medium">{{ $activity->event }}</span>
                                                        <span class="text-gray-400">{{ class_basename($activity->subject_type) }}</span>
                                                    </p>
                                                    <p class="text-caption text-gray-400 mt-0.5">{{ $activity->created_at->diffForHumans() }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            {{-- Action Buttons --}}
                            @if($selectedAdmin->role !== 'owner')
                                <div class="border-t border-gray-100 pt-4 space-y-2">
                                    @if($selectedAdmin->status === 'active')
                                        <button
                                            wire:click="confirmDeactivate({{ $selectedAdmin->id }})"
                                            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-white text-red-600 text-body font-medium rounded-[8px] border border-red-200 hover:bg-red-50 transition-colors"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                            Nonaktifkan Admin
                                        </button>
                                    @elseif($selectedAdmin->status === 'inactive')
                                        <button
                                            wire:click="confirmActivate({{ $selectedAdmin->id }})"
                                            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 text-white text-body font-medium rounded-[8px] hover:bg-emerald-700 transition-colors"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Aktifkan Admin
                                        </button>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Confirmation Modal --}}
    @if($showConfirm)
        <div class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0">
            <div class="fixed inset-0 bg-gray-500/75" wire:click="cancelConfirm"></div>
            <div class="relative bg-white rounded-[16px] shadow-modal max-w-md mx-auto mt-[10vh] p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full {{ $confirmAction === 'activate' ? 'bg-emerald-50' : 'bg-amber-50' }} flex items-center justify-center flex-shrink-0">
                        @if($confirmAction === 'activate')
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        @else
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                        @endif
                    </div>
                    <div>
                        <h3 class="text-[16px] font-semibold text-gray-900">
                            {{ $confirmAction === 'activate' ? 'Aktifkan Admin' : 'Nonaktifkan Admin' }}
                        </h3>
                    </div>
                </div>
                <p class="text-body text-gray-600 mb-6">{{ $confirmMessage }}</p>
                <div class="flex items-center gap-3 justify-end">
                    <button wire:click="cancelConfirm" class="px-4 py-2.5 text-body font-medium text-gray-700 bg-white border border-gray-200 rounded-[8px] hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button
                        wire:click="executeConfirm"
                        class="px-4 py-2.5 text-body font-medium text-white rounded-[8px] transition-colors {{ $confirmAction === 'activate' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-amber-600 hover:bg-amber-700' }}"
                    >
                        {{ $confirmAction === 'activate' ? 'Ya, Aktifkan' : 'Ya, Nonaktifkan' }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
