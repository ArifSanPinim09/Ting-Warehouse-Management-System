<div class="relative" x-data="{ open: @entangle('showDropdown') }" @click.outside="open = false; @this.closeDropdown()">
    <!-- Bell Button -->
    <button
        @click="open = !open; @this.toggleDropdown()"
        class="relative p-3 min-w-[44px] min-h-[44px] flex items-center justify-center text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-accent/40 transition ease-in-out duration-150"
    >
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>

        <!-- Unread Badge -->
        @if ($unreadCount > 0)
            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-caption font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-badge">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    <!-- Dropdown Panel -->
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 mt-2 w-80 sm:w-96 rounded-card shadow-dropdown bg-white z-50"
        style="display: none;"
    >
        <!-- Header -->
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
            <h3 class="text-body font-semibold text-gray-800">Notifikasi</h3>
            @if ($unreadCount > 0)
                <button
                    wire:click="markAllAsRead"
                    class="text-caption text-accent hover:text-primary font-medium min-h-[44px] flex items-center"
                >
                    Tandai semua dibaca
                </button>
            @endif
        </div>

        <!-- Notification List -->
        <div class="max-h-96 overflow-y-auto">
            @if ($loading)
                {{-- PRD §17.1: Skeleton loader for notifications (3 rows) --}}
                @for ($i = 0; $i < 3; $i++)
                    <div class="px-4 py-3 border-b border-gray-50 animate-pulse">
                        <div class="flex items-start gap-3">
                            <div class="h-2 w-2 mt-2 rounded-full bg-gray-200"></div>
                            <div class="flex-1 space-y-2">
                                <div class="h-3 bg-gray-200 rounded w-3/4"></div>
                                <div class="h-2 bg-gray-200 rounded w-1/2"></div>
                            </div>
                        </div>
                    </div>
                @endfor
            @elseif ($notifications->isEmpty())
                {{-- PRD §16: Empty state: "Tidak ada notifikasi" --}}
                <div class="px-4 py-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <p class="mt-2 text-body text-gray-500">Tidak ada notifikasi</p>
                </div>
            @else
                @foreach ($notifications as $notification)
                    <div
                        class="px-4 py-3 border-b border-gray-50 hover:bg-gray-50 transition ease-in-out duration-150 cursor-pointer min-h-[44px] {{ $notification->isRead() ? 'bg-white' : 'bg-blue-50' }}"
                        wire:click="markAsRead('{{ $notification->id }}')"
                    >
                        <div class="flex items-start gap-3">
                            <!-- Unread indicator dot -->
                            <div class="mt-1.5 flex-shrink-0">
                                @if ($notification->isRead())
                                    <div class="h-2 w-2 rounded-full bg-transparent"></div>
                                @else
                                    <div class="h-2 w-2 rounded-full bg-blue-500"></div>
                                @endif
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <p class="text-body font-medium text-gray-800">
                                    {{ $notification->data['title'] ?? 'Notifikasi' }}
                                </p>
                                <p class="text-caption text-gray-500 mt-0.5 line-clamp-2">
                                    {{ $notification->data['message'] ?? '' }}
                                </p>
                                <p class="text-caption text-gray-400 mt-1">
                                    {{ $notification->created_at->diffForHumans() }}
                                </p>
                            </div>

                            <!-- Type icon -->
                            <div class="flex-shrink-0 mt-1">
                                @php
                                    $iconColor = match ($notification->type) {
                                        'customer_register', 'new_complaint', 'payment_received' => 'text-orange-500',
                                        'account_activated', 'payment_verified' => 'text-green-500',
                                        'payment_rejected' => 'text-red-500',
                                        'box_status_changed', 'invoice_generated', 'checkout_processed', 'complaint_updated' => 'text-blue-500',
                                        default => 'text-gray-400',
                                    };
                                @endphp
                                <svg class="h-4 w-4 {{ $iconColor }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        {{-- Footer link to full notification page (when implemented) --}}
        @if ($notifications->isNotEmpty())
            <div class="px-4 py-2 border-t border-gray-100 text-center">
                <a href="#" class="text-caption text-accent hover:text-primary font-medium min-h-[44px] inline-flex items-center">
                    Lihat semua notifikasi
                </a>
            </div>
        @endif
    </div>
</div>
