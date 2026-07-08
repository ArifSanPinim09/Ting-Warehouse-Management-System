<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Audit Log</h2>
            <p class="text-sm text-gray-500 mt-1">Riwayat perubahan data penting dalam sistem.</p>
        </div>

        {{-- Filters --}}
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-4 py-4 sm:px-6">
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                    {{-- Subject Type Filter --}}
                    <div>
                        <label for="filterSubject" class="block text-xs font-medium text-gray-500 mb-1">Model</label>
                        <select
                            id="filterSubject"
                            wire:model.live="filterSubject"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                        >
                            <option value="">Semua Model</option>
                            @foreach ($subjectTypes as $type)
                                <option value="{{ $type }}">
                                    {{ class_basename($type) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Event Type Filter --}}
                    <div>
                        <label for="filterEvent" class="block text-xs font-medium text-gray-500 mb-1">Event</label>
                        <select
                            id="filterEvent"
                            wire:model.live="filterEvent"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                        >
                            <option value="">Semua Event</option>
                            @foreach ($eventTypes as $event)
                                <option value="{{ $event }}">{{ $event }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- User Filter --}}
                    <div>
                        <label for="filterUser" class="block text-xs font-medium text-gray-500 mb-1">User</label>
                        <select
                            id="filterUser"
                            wire:model.live="filterUser"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                        >
                            <option value="">Semua User</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role }})</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Reset Button --}}
                    <div class="flex items-end">
                        <button
                            wire:click="resetFilters"
                            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            Reset Filter
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Audit Log Table --}}
        <div class="bg-white shadow rounded-lg overflow-hidden">
            @if ($logs->isEmpty())
                {{-- PRD §16: Empty state --}}
                <div class="px-4 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">Belum ada log aktivitas.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Waktu
                                </th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    User
                                </th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Model
                                </th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Event
                                </th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Perubahan
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($logs as $log)
                                <tr class="hover:bg-gray-50 transition ease-in-out duration-150">
                                    {{-- Timestamp --}}
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                        <div>{{ $log->created_at->format('d M Y H:i') }}</div>
                                        <div class="text-xs text-gray-400">{{ $log->created_at->diffForHumans() }}</div>
                                    </td>

                                    {{-- User --}}
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-800">
                                        @if ($log->user)
                                            <div class="font-medium">{{ $log->user->name }}</div>
                                            <div class="text-xs text-gray-400">{{ $log->user->role }}</div>
                                        @else
                                            <span class="text-gray-400 italic">System</span>
                                        @endif
                                    </td>

                                    {{-- Model --}}
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-800">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ class_basename($log->subject_type) }}
                                        </span>
                                        <span class="text-xs text-gray-400 ml-1">#{{ $log->subject_id }}</span>
                                    </td>

                                    {{-- Event --}}
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @php
                                            $eventColor = match ($log->event) {
                                                'created', 'generated' => 'bg-green-100 text-green-800',
                                                'updated', 'verified' => 'bg-blue-100 text-blue-800',
                                                'rejected', 'deleted' => 'bg-red-100 text-red-800',
                                                default => 'bg-gray-100 text-gray-800',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $eventColor }}">
                                            {{ $log->event }}
                                        </span>
                                    </td>

                                    {{-- Changes --}}
                                    <td class="px-4 py-3 text-sm text-gray-600 max-w-md">
                                        @if ($log->event === 'created' || $log->event === 'generated')
                                            {{-- Show new values --}}
                                            @if ($log->new_values)
                                                @if (isset($log->new_values['_description']))
                                                    <div class="text-xs text-gray-500 mb-1">{{ $log->new_values['_description'] }}</div>
                                                @endif
                                                <div class="space-y-0.5">
                                                    @foreach ($log->new_values as $key => $value)
                                                        @if ($key !== '_description')
                                                            <div class="text-xs">
                                                                <span class="text-gray-400">{{ $key }}:</span>
                                                                <span class="font-medium text-gray-700">{{ is_array($value) ? json_encode($value) : $value }}</span>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @endif
                                        @elseif ($log->event === 'deleted')
                                            {{-- Show old values --}}
                                            @if ($log->old_values)
                                                <div class="space-y-0.5">
                                                    @foreach ($log->old_values as $key => $value)
                                                        <div class="text-xs">
                                                            <span class="text-gray-400">{{ $key }}:</span>
                                                            <span class="line-through text-red-500">{{ is_array($value) ? json_encode($value) : $value }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        @else
                                            {{-- Updated/verified/rejected: show old → new --}}
                                            @if ($log->old_values || $log->new_values)
                                                <div class="space-y-1">
                                                    @foreach (($log->new_values ?? []) as $key => $newValue)
                                                        @if ($key !== '_description')
                                                            <div class="text-xs flex items-center gap-1">
                                                                <span class="text-gray-400 font-medium">{{ $key }}:</span>
                                                                @if (isset($log->old_values[$key]))
                                                                    <span class="line-through text-red-400">{{ is_array($log->old_values[$key]) ? json_encode($log->old_values[$key]) : $log->old_values[$key] }}</span>
                                                                    <span class="text-gray-400">→</span>
                                                                @endif
                                                                <span class="text-green-600 font-medium">{{ is_array($newValue) ? json_encode($newValue) : $newValue }}</span>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-gray-400 text-xs">-</span>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="px-4 py-3 border-t border-gray-200">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
