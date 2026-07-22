<div class="min-h-screen bg-[#f8fafc]">

    <div class="bg-white border-b border-gray-100">
        <div class="max-w-[1500px] mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <h1 class="text-[20px] font-bold text-primary">Audit Log</h1>
            <p class="text-[13px] text-gray-500 mt-1">Track all system activities and changes</p>
        </div>
    </div>

    <div class="max-w-[1500px] mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

        {{-- Filters --}}
        <div class="bg-white rounded-[12px] border border-gray-100 p-4">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                <div>
                    <label class="block text-[12px] font-medium text-gray-500 mb-1">Subject</label>
                    <select wire:model.live="filterSubject" class="w-full border border-gray-200 rounded-[8px] px-3 py-2 text-[13px] outline-none focus:border-primary">
                        <option value="">All</option>
                        @foreach($subjectTypes as $type)
                            <option value="{{ $type }}">{{ class_basename($type) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[12px] font-medium text-gray-500 mb-1">Event</label>
                    <select wire:model.live="filterEvent" class="w-full border border-gray-200 rounded-[8px] px-3 py-2 text-[13px] outline-none focus:border-primary">
                        <option value="">All</option>
                        @foreach($eventTypes as $event)
                            <option value="{{ $event }}">{{ $event }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[12px] font-medium text-gray-500 mb-1">User</label>
                    <select wire:model.live="filterUser" class="w-full border border-gray-200 rounded-[8px] px-3 py-2 text-[13px] outline-none focus:border-primary">
                        <option value="">All</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-[12px] border border-gray-100 overflow-hidden">
            @if($logs->isEmpty())
                <div class="p-12 text-center text-[13px] text-gray-400">Belum ada log aktivitas</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/80 border-b border-gray-100">
                                <th class="px-4 py-3 text-[12px] font-semibold text-gray-600 uppercase">Time</th>
                                <th class="px-4 py-3 text-[12px] font-semibold text-gray-600 uppercase">User</th>
                                <th class="px-4 py-3 text-[12px] font-semibold text-gray-600 uppercase">Event</th>
                                <th class="px-4 py-3 text-[12px] font-semibold text-gray-600 uppercase">Subject</th>
                                <th class="px-4 py-3 text-[12px] font-semibold text-gray-600 uppercase">Description</th>
                                <th class="px-4 py-3 text-[12px] font-semibold text-gray-600 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($logs as $log)
                                <tr class="hover:bg-gray-50/50">
                                    <td class="px-4 py-3 text-[12px] text-gray-400 whitespace-nowrap">{{ $log->created_at->format('d M Y H:i') }}</td>
                                    <td class="px-4 py-3 text-[13px] font-medium text-gray-900">{{ $log->user?->name ?? 'System' }}</td>
                                    <td class="px-4 py-3">
                                        @php
                                            $eventClass = 'bg-gray-100 text-gray-600';
                                            if (str_contains($log->event, 'created')) $eventClass = 'bg-green-100 text-green-700';
                                            elseif (str_contains($log->event, 'updated')) $eventClass = 'bg-blue-100 text-blue-700';
                                            elseif (str_contains($log->event, 'deleted')) $eventClass = 'bg-red-100 text-red-700';
                                        @endphp
                                        <span class="text-[11px] font-medium px-2 py-0.5 rounded {{ $eventClass }}">
                                            {{ $log->event }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-[13px] text-gray-600">{{ class_basename($log->subject_type ?? '') }}</td>
                                    <td class="px-4 py-3 text-[13px] text-gray-600">{{ $log->description ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        <button wire:click="selectLog({{ $log->id }})" class="text-[12px] text-blue-500 hover:text-blue-700">Detail</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-3 border-t border-gray-100">{{ $logs->links() }}</div>
            @endif
        </div>

        {{-- Detail Log Panel --}}
        @if($selectedLog)
            <div class="bg-white rounded-[12px] border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-[15px] font-semibold text-gray-900">Detail Log</h2>
                    <button wire:click="closeDetail" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="space-y-3">
                    <div>
                        <p class="text-[12px] text-gray-400 font-medium uppercase">User</p>
                        <p class="text-[14px] text-gray-900">{{ $selectedLog->user?->name ?? 'System' }}</p>
                    </div>
                    <div>
                        <p class="text-[12px] text-gray-400 font-medium uppercase">Event</p>
                        <p class="text-[14px] text-gray-900">{{ $selectedLog->event }}</p>
                    </div>
                    <div>
                        <p class="text-[12px] text-gray-400 font-medium uppercase">Description</p>
                        <p class="text-[14px] text-gray-600">{{ $selectedLog->description ?? '-' }}</p>
                    </div>
                    @if($selectedLog->old_values)
                        <div>
                            <p class="text-[12px] text-gray-400 font-medium uppercase">Old Values</p>
                            <pre class="text-[12px] text-gray-600 bg-gray-50 rounded-[8px] p-3 overflow-x-auto">{{ json_encode($selectedLog->old_values, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    @endif
                    @if($selectedLog->new_values)
                        <div>
                            <p class="text-[12px] text-gray-400 font-medium uppercase">New Values</p>
                            <pre class="text-[12px] text-gray-600 bg-gray-50 rounded-[8px] p-3 overflow-x-auto">{{ json_encode($selectedLog->new_values, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
