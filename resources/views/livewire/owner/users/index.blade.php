<div class="min-h-screen bg-[#f8fafc]">
    {{-- Page Header --}}
    <div class="bg-white border-b border-gray-100">
        <div class="mx-auto w-full max-w-[1500px] px-4 md:px-6 lg:px-8 xl:px-10 2xl:px-12 py-6">
            <div>
                <h1 class="text-[22px] font-bold text-gray-900 tracking-tight">Manage Users</h1>
                <p class="text-body text-gray-500 mt-0.5">Kelola semua pengguna sistem</p>
            </div>
        </div>
    </div>

    <div class="mx-auto w-full max-w-[1500px] px-4 md:px-6 lg:px-8 xl:px-10 2xl:px-12 py-6 space-y-4">

        {{-- Filters --}}
        <div class="bg-white rounded-[12px] border border-gray-100 p-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="sm:col-span-1 relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari nama, email, atau telepon..."
                        class="w-full pl-10 pr-4 py-2.5 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors placeholder:text-gray-400"
                    >
                </div>
                <select wire:model.live="filterRole" class="py-2.5 px-3 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors text-gray-600">
                    <option value="">Semua Role</option>
                    <option value="owner">Owner</option>
                    <option value="admin">Admin</option>
                    <option value="customer">Customer</option>
                </select>
                <select wire:model.live="filterStatus" class="py-2.5 px-3 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors text-gray-600">
                    <option value="">Semua Status</option>
                    <option value="active">Active</option>
                    <option value="pending">Pending</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @php
                $totalUsers = \App\Models\User::count();
                $activeUsers = \App\Models\User::where('status', 'active')->count();
                $pendingUsers = \App\Models\User::where('status', 'pending')->count();
                $adminUsers = \App\Models\User::whereIn('role', ['admin', 'owner'])->count();
            @endphp
            <div class="bg-white rounded-[12px] border border-gray-100 px-4 py-3.5 flex items-center gap-3">
                <div class="w-9 h-9 rounded-[8px] bg-gray-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <p class="text-[18px] font-bold text-gray-900 leading-none">{{ $totalUsers }}</p>
                    <p class="text-caption text-gray-500 mt-0.5">Total Users</p>
                </div>
            </div>
            <div class="bg-white rounded-[12px] border border-gray-100 px-4 py-3.5 flex items-center gap-3">
                <div class="w-9 h-9 rounded-[8px] bg-emerald-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-[18px] font-bold text-emerald-600 leading-none">{{ $activeUsers }}</p>
                    <p class="text-caption text-gray-500 mt-0.5">Active</p>
                </div>
            </div>
            <div class="bg-white rounded-[12px] border border-gray-100 px-4 py-3.5 flex items-center gap-3">
                <div class="w-9 h-9 rounded-[8px] bg-amber-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-[18px] font-bold text-amber-600 leading-none">{{ $pendingUsers }}</p>
                    <p class="text-caption text-gray-500 mt-0.5">Pending</p>
                </div>
            </div>
            <div class="bg-white rounded-[12px] border border-gray-100 px-4 py-3.5 flex items-center gap-3">
                <div class="w-9 h-9 rounded-[8px] bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <div>
                    <p class="text-[18px] font-bold text-blue-600 leading-none">{{ $adminUsers }}</p>
                    <p class="text-caption text-gray-500 mt-0.5">Admin/Owner</p>
                </div>
            </div>
        </div>

        {{-- Content --}}
        <div class="flex gap-6" x-data="{ detailOpen: @js($showDetail) }" x-effect="detailOpen = $wire.showDetail">
            {{-- Table / List --}}
            <div class="flex-1 min-w-0" :class="detailOpen && 'hidden lg:block'">
                @if($users->isEmpty())
                    <div class="bg-white rounded-[12px] border border-gray-100">
                        <div class="flex flex-col items-center py-16 text-center px-4">
                            <div class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <p class="text-body font-medium text-gray-500">Tidak ada pengguna</p>
                            <p class="text-caption text-gray-400 mt-1">Pengguna akan muncul di sini setelah registrasi</p>
                        </div>
                    </div>
                @else
                    {{-- Desktop Table --}}
                    <div class="hidden md:block bg-white rounded-[12px] border border-gray-100 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-gray-100">
                                        <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Pengguna</th>
                                        <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Email</th>
                                        <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Telepon</th>
                                        <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Role</th>
                                        <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                                        <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Terdaftar</th>
                                        <th class="text-right px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($users as $user)
                                        <tr class="hover:bg-gray-50/50 transition-colors cursor-pointer" wire:click="selectUser({{ $user->id }})">
                                            <td class="px-5 py-3.5">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                                                        <span class="text-caption font-bold text-primary">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                                    </div>
                                                    <span class="text-body font-semibold text-gray-900">{{ $user->name }}</span>
                                                </div>
                                            </td>
                                            <td class="px-5 py-3.5">
                                                <span class="text-body text-gray-700">{{ $user->email }}</span>
                                            </td>
                                            <td class="px-5 py-3.5">
                                                <span class="text-body text-gray-700">{{ $user->phone ?? '-' }}</span>
                                            </td>
                                            <td class="px-5 py-3.5">
                                                @if($user->role === 'owner')
                                                    <span class="inline-flex items-center px-2 py-0.5 text-caption font-bold bg-violet-100 text-violet-700 rounded-full">Owner</span>
                                                @elseif($user->role === 'admin')
                                                    <span class="inline-flex items-center px-2 py-0.5 text-caption font-bold bg-blue-100 text-blue-700 rounded-full">Admin</span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 text-caption font-medium bg-gray-100 text-gray-700 rounded-full">Customer</span>
                                                @endif
                                            </td>
                                            <td class="px-5 py-3.5">
                                                @if($user->status === 'active')
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 text-caption font-medium bg-emerald-50 text-emerald-700 rounded-full">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                                    </span>
                                                @elseif($user->status === 'pending')
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 text-caption font-medium bg-amber-50 text-amber-700 rounded-full">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Pending
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 text-caption font-medium bg-red-50 text-red-700 rounded-full">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Inactive
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-5 py-3.5">
                                                <span class="text-caption text-gray-500">{{ $user->created_at->format('d M Y') }}</span>
                                            </td>
                                            <td class="px-5 py-3.5 text-right">
                                                <button wire:click.stop="selectUser({{ $user->id }})" class="text-caption font-medium text-accent hover:text-primary transition-colors">
                                                    Detail
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($users->hasPages())
                            <div class="px-5 py-3 border-t border-gray-100">{{ $users->links() }}</div>
                        @endif
                    </div>

                    {{-- Mobile Cards --}}
                    <div class="md:hidden space-y-3">
                        @foreach($users as $user)
                            <div wire:click="selectUser({{ $user->id }})" class="bg-white rounded-[12px] border border-gray-100 p-4 hover:shadow-card-hover transition-shadow cursor-pointer">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                                            <span class="text-body font-bold text-primary">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                        </div>
                                        <div>
                                            <p class="text-body font-semibold text-gray-900">{{ $user->name }}</p>
                                            <p class="text-caption text-gray-500">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-end gap-1">
                                        @if($user->role === 'owner')
                                            <span class="inline-flex items-center px-2 py-0.5 text-caption font-bold bg-violet-100 text-violet-700 rounded-full">Owner</span>
                                        @elseif($user->role === 'admin')
                                            <span class="inline-flex items-center px-2 py-0.5 text-caption font-bold bg-blue-100 text-blue-700 rounded-full">Admin</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 text-caption font-medium bg-gray-100 text-gray-700 rounded-full">Customer</span>
                                        @endif
                                        @if($user->status === 'active')
                                            <span class="text-caption text-emerald-600">Active</span>
                                        @elseif($user->status === 'pending')
                                            <span class="text-caption text-amber-600">Pending</span>
                                        @else
                                            <span class="text-caption text-red-600">Inactive</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 text-caption text-gray-400">
                                    @if($user->phone)
                                        <span>{{ $user->phone }}</span>
                                    @endif
                                    <span>{{ $user->created_at->format('d M Y') }}</span>
                                </div>
                            </div>
                        @endforeach
                        @if($users->hasPages())
                            <div class="py-2">{{ $users->links() }}</div>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Detail Panel --}}
            @if($showDetail && $selectedUser)
                <div class="w-full lg:w-[380px] flex-shrink-0" x-show="detailOpen" x-transition>
                    <div class="bg-white rounded-[12px] border border-gray-100 sticky top-[88px]">
                        {{-- Detail Header --}}
                        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="text-body font-semibold text-gray-900">Detail Pengguna</h3>
                            <button wire:click="closeDetail" class="p-2 min-w-[44px] min-h-[44px] flex items-center justify-center rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <div class="p-5 space-y-5">
                            {{-- User Info --}}
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                                    <span class="text-[20px] font-bold text-primary">{{ strtoupper(substr($selectedUser->name, 0, 1)) }}</span>
                                </div>
                                <div>
                                    <p class="text-body font-semibold text-gray-900">{{ $selectedUser->name }}</p>
                                    <p class="text-caption text-gray-500">{{ $selectedUser->email }}</p>
                                </div>
                            </div>

                            {{-- Details --}}
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-caption text-gray-500">Role</span>
                                    @if($selectedUser->role === 'owner')
                                        <span class="inline-flex items-center px-2 py-0.5 text-caption font-bold bg-violet-100 text-violet-700 rounded-full">Owner</span>
                                    @elseif($selectedUser->role === 'admin')
                                        <span class="inline-flex items-center px-2 py-0.5 text-caption font-bold bg-blue-100 text-blue-700 rounded-full">Admin</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 text-caption font-medium bg-gray-100 text-gray-700 rounded-full">Customer</span>
                                    @endif
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-caption text-gray-500">Status</span>
                                    @if($selectedUser->status === 'active')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-caption font-medium bg-emerald-50 text-emerald-700 rounded-full">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                        </span>
                                    @elseif($selectedUser->status === 'pending')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-caption font-medium bg-amber-50 text-amber-700 rounded-full">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Pending
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-caption font-medium bg-red-50 text-red-700 rounded-full">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Inactive
                                        </span>
                                    @endif
                                </div>
                                @if($selectedUser->phone)
                                    <div class="flex items-center justify-between">
                                        <span class="text-caption text-gray-500">Telepon</span>
                                        <span class="text-body text-gray-700">{{ $selectedUser->phone }}</span>
                                    </div>
                                @endif
                                @if($selectedUser->ktp_number)
                                    <div class="flex items-center justify-between">
                                        <span class="text-caption text-gray-500">No KTP</span>
                                        <span class="text-body font-mono text-gray-700">{{ $selectedUser->ktp_number }}</span>
                                    </div>
                                @endif
                                @if($selectedUser->customer_code)
                                    <div class="flex items-center justify-between">
                                        <span class="text-caption text-gray-500">Customer Code</span>
                                        <span class="text-body font-mono text-gray-700">{{ $selectedUser->customer_code }}</span>
                                    </div>
                                @endif
                                @if($selectedUser->address)
                                    <div>
                                        <span class="text-caption text-gray-500">Alamat</span>
                                        <p class="text-body text-gray-700 mt-1">{{ $selectedUser->address }}</p>
                                    </div>
                                @endif
                                <div class="flex items-center justify-between">
                                    <span class="text-caption text-gray-500">Terdaftar</span>
                                    <span class="text-body text-gray-700">{{ $selectedUser->created_at->format('d M Y H:i') }}</span>
                                </div>
                            </div>

                            {{-- Actions (non-owner only) --}}
                            @if(!$selectedUser->isOwner())
                                <div class="pt-3 border-t border-gray-100 space-y-3">
                                    <button
                                        wire:click="openRoleModal"
                                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-white text-gray-700 text-body font-medium rounded-[8px] border border-gray-200 hover:bg-gray-50 transition-colors"
                                    >
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        Ubah Role
                                    </button>
                                    @if($selectedUser->status === 'active')
                                        <button
                                            wire:click="toggleStatus"
                                            wire:confirm="Yakin ingin menonaktifkan {{ $selectedUser->name }}?"
                                            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-red-50 text-red-600 text-body font-medium rounded-[8px] hover:bg-red-100 transition-colors"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                            Nonaktifkan
                                        </button>
                                    @else
                                        <button
                                            wire:click="toggleStatus"
                                            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-50 text-emerald-600 text-body font-medium rounded-[8px] hover:bg-emerald-100 transition-colors"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Aktifkan
                                        </button>
                                    @endif

                                    {{-- Sprint 4: Blacklist Button (customer only) --}}
                                    @if($selectedUser->role === 'customer')
                                        <button
                                            wire:click="openBlacklistModal"
                                            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 {{ $selectedUser->is_blacklisted ? 'bg-green-50 text-green-600 hover:bg-green-100' : 'bg-orange-50 text-orange-600 hover:bg-orange-100' }} text-body font-medium rounded-[8px] transition-colors mt-2"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                            {{ $selectedUser->is_blacklisted ? 'Unblacklist' : 'Blacklist' }}
                                        </button>
                                        @if($selectedUser->is_blacklisted)
                                            <p class="text-[11px] text-orange-500 mt-1 px-1">Reason: {{ $selectedUser->blacklist_reason }}</p>
                                        @endif
                                    @endif
                                </div>
                            @else
                                <div class="p-3 bg-violet-50 rounded-[8px] text-center">
                                    <p class="text-caption font-medium text-violet-700">Akun Owner tidak bisa diubah</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Role Change Modal --}}
    @if($showRoleModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" wire:click.self="closeRoleModal">
            <div class="fixed inset-0 bg-black/30 transition-opacity"></div>
            <div class="flex min-h-full items-end sm:items-center justify-center p-4">
                <div class="relative bg-white rounded-[16px] shadow-modal w-full max-w-md p-6 transform transition-all" @click.stop>
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-[16px] font-semibold text-gray-900">Ubah Role</h3>
                        <button wire:click="closeRoleModal" class="p-2 min-w-[44px] min-h-[44px] flex items-center justify-center rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-caption font-medium text-gray-700 mb-1.5">Role Baru</label>
                            <select wire:model="newRole" class="w-full py-2.5 px-3 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40">
                                <option value="customer">Customer</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 justify-end mt-6">
                        <button wire:click="closeRoleModal" class="px-4 py-2.5 text-body font-medium text-gray-700 bg-white border border-gray-200 rounded-[8px] hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button wire:click="updateRole" class="px-4 py-2.5 text-body font-medium text-white bg-primary rounded-[8px] hover:bg-primary-light transition-colors" wire:loading.attr="disabled">
                            Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Sprint 4: Blacklist Modal --}}
    @if($showBlacklistModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" wire:click.self="closeBlacklistModal">
            <div class="fixed inset-0 bg-black/30 transition-opacity"></div>
            <div class="flex min-h-full items-end sm:items-center justify-center p-4">
                <div class="relative bg-white rounded-[16px] shadow-modal w-full max-w-md p-6 transform transition-all" @click.stop>
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-[16px] font-semibold text-red-600">
                            {{ $selectedUser?->is_blacklisted ? 'Unblacklist User' : 'Blacklist User' }}
                        </h3>
                        <button wire:click="closeBlacklistModal" class="p-2 min-w-[44px] min-h-[44px] flex items-center justify-center rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    @if($selectedUser?->is_blacklisted)
                        <p class="text-[14px] text-gray-600 mb-4">Apakah Anda yakin ingin meng-unblacklist <strong>{{ $selectedUser?->name }}</strong>?</p>
                    @else
                        <p class="text-[14px] text-gray-600 mb-4">Blacklist <strong>{{ $selectedUser?->name }}</strong>? User tidak akan bisa login atau menggunakan layanan.</p>
                        <div class="mb-4">
                            <label class="block text-caption font-medium text-gray-700 mb-1.5">Alasan Blacklist</label>
                            <textarea wire:model="blacklistReason" rows="3" class="w-full py-2.5 px-3 text-body border border-gray-200 rounded-[8px] focus:border-red-500 focus:ring-2 focus:ring-red-500/20 outline-none" placeholder="Contoh: Tidak membayar tagihan, kabur, dll..."></textarea>
                            @error('blacklistReason') <p class="text-[12px] text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    @endif

                    <div class="flex items-center gap-3 justify-end mt-6">
                        <button wire:click="closeBlacklistModal" class="px-4 py-2.5 text-body font-medium text-gray-700 bg-white border border-gray-200 rounded-[8px] hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button wire:click="toggleBlacklist" class="px-4 py-2.5 text-body font-medium text-white {{ $selectedUser?->is_blacklisted ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700' }} rounded-[8px] transition-colors" wire:loading.attr="disabled">
                            {{ $selectedUser?->is_blacklisted ? 'Unblacklist' : 'Blacklist' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
