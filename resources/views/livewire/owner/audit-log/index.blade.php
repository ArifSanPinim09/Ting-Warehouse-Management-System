<div class="min-h-screen bg-[#f8fafc]">
    {{-- Page Header --}}
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div>
                <h1 class="text-[22px] font-bold text-gray-900 tracking-tight">Audit Log</h1>
                <p class="text-body text-gray-500 mt-0.5">Riwayat lengkap seluruh aktivitas sistem</p>
            </div>
        </div>
    </div>

    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-4">

        {{-- Filters --}}
        <div class="bg-white rounded-[12px] border border-gray-100 p-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-3">
                {{-- Search --}}
                <div class="sm:col-span-2 relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari event, model, atau user..." class="w-full pl-10 pr-4 py-2.5 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors placeholder:text-gray-400">
                </div>

                {{-- Model Filter --}}
                <div>
                    <select wire:model.live="filterSubject" class="w-full py-2.5 px-3 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors text-gray-600">
                        <option value="">Semua Model</option>
                        @foreach ($subjectTypes as $type)
                            <option value="{{ $type }}">{{ class_basename($type) }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Event Filter --}}
                <div>
                    <select wire:model.live="filterEvent" class="w-full py-2.5 px-3 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors text-gray-600">
                        <option value="">Semua Event</option>
                        @foreach ($eventTypes as $event)
                            <option value="{{ $event }}">{{ ucfirst($event) }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- User Filter --}}
                <div>
                    <select wire:model.live="filterUser" class="w-full py-2.5 px-3 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors text-gray-600">
                        <option value="">Semua User</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role }})</option>
                        @endforeach
                    </select>
                </div>

                {{-- Date From --}}
                <div>
                    <input type="date" wire:model.live="filterDateFrom" class="w-full py-2.5 px-3 text-body bg-white border border-gray-200 rounded-[8px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors text-gray-600">
                </div>
            </div>

            {{-- Active filters & Reset --}}
            <div class="flex items-center gap-3 mt-3">
                @if($filterDateTo)
                    <div class="flex items-center gap-2">
                        <span class="text-caption text-gray-400">sampai</span>
                        <input type="date" wire:model.live="filterDateTo" class="py-1.5 px-2 text-caption bg-white border border-gray-200 rounded-[6px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors text-gray-600">
                    </div>
                @else
                    <div class="flex items-center gap-2">
                        <span class="text-caption text-gray-400">sampai</span>
                        <input type="date" wire:model.live="filterDateTo" class="py-1.5 px-2 text-caption bg-white border border-gray-200 rounded-[6px] focus:border-accent focus:ring-2 focus:ring-accent/40 transition-colors text-gray-600">
                    </div>
                @endif

                @if($search || $filterSubject || $filterEvent || $filterUser || $filterDateFrom || $filterDateTo)
                    <button wire:click="resetFilters" class="inline-flex items-center gap-1 px-3 py-2 text-caption text-gray-500 hover:text-gray-700 transition-colors min-h-[44px]">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Reset Semua Filter
                    </button>
                @endif
            </div>
        </div>

        {{-- Content --}}
        <div class="flex gap-6">
            <div class="flex-1 min-w-0">
                @if($logs->isEmpty())
                    <div class="bg-white rounded-[12px] border border-gray-100">
                        <x-empty-state
                            icon="chart"
                            title="Belum ada log aktivitas"
                            text="Log aktivitas akan muncul ketika ada perubahan data dalam sistem."
                        />
                    </div>
                @else
                    {{-- Desktop Table --}}
                    <div class="hidden md:block bg-white rounded-[12px] border border-gray-100 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-gray-100">
                                        <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Waktu</th>
                                        <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">User</th>
                                        <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Model</th>
                                        <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Event</th>
                                        <th class="text-left px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Ringkasan</th>
                                        <th class="text-right px-5 py-3 text-caption font-semibold text-gray-500 uppercase tracking-wide">Detail</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($logs as $log)
                                        <tr class="hover:bg-gray-50/50 transition-colors cursor-pointer" wire:click="selectLog({{ $log->id }})">
                                            <td class="px-5 py-3.5">
                                                <div>
                                                    <p class="text-body text-gray-800">{{ $log->created_at->format('d M Y H:i') }}</p>
                                                    <p class="text-caption text-gray-400">{{ $log->created_at->diffForHumans() }}</p>
                                                </div>
                                            </td>
                                            <td class="px-5 py-3.5">
                                                @if($log->user)
                                                    <div class="flex items-center gap-2">
                                                        <div class="w-7 h-7 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                                                            <span class="text-caption font-bold text-primary">{{ strtoupper(substr($log->user->name, 0, 1)) }}</span>
                                                        </div>
                                                        <div>
                                                            <p class="text-body font-medium text-gray-800">{{ $log->user->name }}</p>
                                                            <p class="text-caption text-gray-400">{{ ucfirst($log->user->role) }}</p>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-body text-gray-400 italic">System</span>
                                                @endif
                                            </td>
                                            <td class="px-5 py-3.5">
                                                <div class="flex items-center gap-1.5">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-[4px] text-caption font-medium bg-gray-100 text-gray-700">
                                                        {{ class_basename($log->subject_type) }}
                                                    </span>
                                                    <span class="text-caption text-gray-400">#{{ $log->subject_id }}</span>
                                                </div>
                                            </td>
                                            <td class="px-5 py-3.5">
                                                @php
                                                    $eventConfig = match ($log->event) {
                                                        'created', 'generated', 'activated' => ['class' => 'bg-emerald-50 text-emerald-700', 'icon' => 'M12 6v6m0 0v6m0-6h6m-6 0H6'],
                                                        'updated', 'verified' => ['class' => 'bg-blue-50 text-blue-700', 'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
                                                        'rejected', 'deleted', 'deactivated' => ['class' => 'bg-red-50 text-red-700', 'icon' => 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16'],
                                                        'export_finance' => ['class' => 'bg-violet-50 text-violet-700', 'icon' => 'M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                                                        default => ['class' => 'bg-gray-100 text-gray-600', 'icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                                                    };
                                                @endphp
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-caption font-medium {{ $eventConfig['class'] }}">
                                                    {{ $log->event }}
                                                </span>
                                            </td>
                                            <td class="px-5 py-3.5">
                                                @if($log->new_values && isset($log->new_values['_description']))
                                                    <p class="text-caption text-gray-600 line-clamp-2">{{ $log->new_values['_description'] }}</p>
                                                @elseif($log->old_values || $log->new_values)
                                                    <p class="text-caption text-gray-500">
                                                        {{ count($log->new_values ?? []) }} field berubah
                                                    </p>
                                                @else
                                                    <span class="text-caption text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-5 py-3.5 text-right">
                                                <button wire:click.stop="selectLog({{ $log->id }})" class="text-caption font-medium text-accent hover:text-primary transition-colors">
                                                    Detail
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($logs->hasPages())
                            <div class="px-5 py-3 border-t border-gray-100">
                                {{ $logs->links() }}
                            </div>
                        @endif
                    </div>

                    {{-- Mobile Cards --}}
                    <div class="md:hidden space-y-3">
                        @foreach($logs as $log)
                            <div wire:click="selectLog({{ $log->id }})" class="bg-white rounded-[12px] border border-gray-100 p-4 hover:shadow-card-hover transition-shadow cursor-pointer">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        @if($log->user)
                                            <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                                                <span class="text-caption font-bold text-primary">{{ strtoupper(substr($log->user->name, 0, 1)) }}</span>
                                            </div>
                                            <div>
                                                <p class="text-body font-medium text-gray-800">{{ $log->user->name }}</p>
                                                <p class="text-caption text-gray-400">{{ ucfirst($log->user->role) }}</p>
                                            </div>
                                        @else
                                            <span class="text-body text-gray-400 italic">System</span>
                                        @endif
                                    </div>
                                    @php
                                        $eventConfig = match ($log->event) {
                                            'created', 'generated', 'activated' => 'bg-emerald-50 text-emerald-700',
                                            'updated', 'verified' => 'bg-blue-50 text-blue-700',
                                            'rejected', 'deleted', 'deactivated' => 'bg-red-50 text-red-700',
                                            default => 'bg-gray-100 text-gray-600',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-caption font-medium {{ $eventConfig }}">{{ $log->event }}</span>
                                </div>
                                <div class="flex items-center gap-2 pt-2 border-t border-gray-100">
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-caption font-medium bg-gray-100 text-gray-600">{{ class_basename($log->subject_type) }}</span>
                                    <span class="text-caption text-gray-400">#{{ $log->subject_id }}</span>
                                    <span class="ml-auto text-caption text-gray-400">{{ $log->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        @endforeach

                        @if($logs->hasPages())
                            <div class="px-1">
                                {{ $logs->links() }}
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Detail Panel --}}
            @if($selectedLog)
                <div class="w-full lg:w-[420px] flex-shrink-0">
                    <div class="bg-white rounded-[12px] border border-gray-100 sticky top-[88px]">
                        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="text-body font-semibold text-gray-900">Detail Log</h3>
                            <button wire:click="closeDetail" class="p-2 min-w-[44px] min-h-[44px] flex items-center justify-center rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <div class="p-5 space-y-4">
                            {{-- User Info --}}
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                                    <span class="text-body font-bold text-primary">{{ $selectedLog->user ? strtoupper(substr($selectedLog->user->name, 0, 1)) : '?' }}</span>
                                </div>
                                <div>
                                    <p class="text-body font-semibold text-gray-900">{{ $selectedLog->user->name ?? 'System' }}</p>
                                    <p class="text-caption text-gray-500">{{ $selectedLog->user ? ucfirst($selectedLog->user->role) : 'Automated' }}</p>
                                </div>
                            </div>

                            {{-- Metadata --}}
                            <div class="space-y-3 border-t border-gray-100 pt-4">
                                <div class="flex justify-between text-body">
                                    <span class="text-gray-500">Waktu</span>
                                    <span class="font-medium text-gray-800">{{ $selectedLog->created_at->format('d M Y H:i:s') }}</span>
                                </div>
                                <div class="flex justify-between text-body">
                                    <span class="text-gray-500">Event</span>
                                    @php
                                        $eventColor = match ($selectedLog->event) {
                                            'created', 'generated', 'activated' => 'text-emerald-700',
                                            'updated', 'verified' => 'text-blue-700',
                                            'rejected', 'deleted', 'deactivated' => 'text-red-700',
                                            default => 'text-gray-700',
                                        };
                                    @endphp
                                    <span class="font-medium {{ $eventColor }}">{{ $selectedLog->event }}</span>
                                </div>
                                <div class="flex justify-between text-body">
                                    <span class="text-gray-500">Model</span>
                                    <span class="font-medium text-gray-800">{{ class_basename($selectedLog->subject_type) }}</span>
                                </div>
                                <div class="flex justify-between text-body">
                                    <span class="text-gray-500">ID</span>
                                    <span class="font-medium text-gray-800">#{{ $selectedLog->subject_id }}</span>
                                </div>
                            </div>

                            {{-- Description --}}
                            @if($selectedLog->new_values && isset($selectedLog->new_values['_description']))
                                <div class="border-t border-gray-100 pt-4">
                                    <p class="text-caption font-semibold text-gray-500 uppercase tracking-wide mb-2">Deskripsi</p>
                                    <p class="text-body text-gray-700">{{ $selectedLog->new_values['_description'] }}</p>
                                </div>
                            @endif

                            {{-- Changes --}}
                            @if($selectedLog->event === 'created' || $selectedLog->event === 'generated' || $selectedLog->event === 'activated' || $selectedLog->event === 'export_finance')
                                @if($selectedLog->new_values)
                                    <div class="border-t border-gray-100 pt-4">
                                        <p class="text-caption font-semibold text-gray-500 uppercase tracking-wide mb-2">Data Baru</p>
                                        <div class="space-y-2 max-h-[200px] overflow-y-auto">
                                            @foreach($selectedLog->new_values as $key => $value)
                                                @if($key !== '_description')
                                                    <div class="bg-gray-50 rounded-[6px] p-2.5">
                                                        <p class="text-caption text-gray-400 mb-0.5">{{ $key }}</p>
                                                        <p class="text-caption font-medium text-gray-800 break-all">{{ is_array($value) ? json_encode($value, JSON_PRETTY_PRINT) : $value }}</p>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @elseif($selectedLog->event === 'deleted' || $selectedLog->event === 'deactivated')
                                @if($selectedLog->old_values)
                                    <div class="border-t border-gray-100 pt-4">
                                        <p class="text-caption font-semibold text-red-500 uppercase tracking-wide mb-2">Data Lama (Dihapus)</p>
                                        <div class="space-y-2 max-h-[200px] overflow-y-auto">
                                            @foreach($selectedLog->old_values as $key => $value)
                                                <div class="bg-red-50 rounded-[6px] p-2.5">
                                                    <p class="text-caption text-gray-400 mb-0.5">{{ $key }}</p>
                                                    <p class="text-caption font-medium text-red-700 line-through break-all">{{ is_array($value) ? json_encode($value) : $value }}</p>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @else
                                {{-- Updated: show before/after --}}
                                @if($selectedLog->old_values || $selectedLog->new_values)
                                    <div class="border-t border-gray-100 pt-4">
                                        <p class="text-caption font-semibold text-gray-500 uppercase tracking-wide mb-2">Perubahan</p>
                                        <div class="space-y-2 max-h-[200px] overflow-y-auto">
                                            @foreach(($selectedLog->new_values ?? []) as $key => $newValue)
                                                @if($key !== '_description')
                                                    <div class="bg-gray-50 rounded-[6px] p-2.5">
                                                        <p class="text-caption text-gray-400 mb-1">{{ $key }}</p>
                                                        <div class="flex items-center gap-2">
                                                            @if(isset($selectedLog->old_values[$key]))
                                                                <span class="text-caption text-red-500 line-through">{{ is_array($selectedLog->old_values[$key]) ? json_encode($selectedLog->old_values[$key]) : $selectedLog->old_values[$key] }}</span>
                                                                <svg class="w-3 h-3 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                                            @endif
                                                            <span class="text-caption font-medium text-emerald-600 break-all">{{ is_array($newValue) ? json_encode($newValue) : $newValue }}</span>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
