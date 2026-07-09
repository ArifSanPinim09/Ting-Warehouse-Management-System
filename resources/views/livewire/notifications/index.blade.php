<div class="min-h-screen bg-[#f8fafc]">
    {{-- Page Header --}}
    <div class="bg-white border-b border-gray-100">
        <div class="mx-auto w-full max-w-[1500px] px-4 md:px-6 lg:px-8 xl:px-10 2xl:px-12 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-[22px] font-bold text-gray-900 tracking-tight">Notifikasi</h1>
                    <p class="text-body text-gray-500 mt-0.5">
                        {{ $unreadCount > 0 ? "{$unreadCount} notifikasi belum dibaca" : 'Semua notifikasi sudah dibaca' }}
                    </p>
                </div>
                @if($unreadCount > 0)
                    <button
                        wire:click="markAllAsRead"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-white text-gray-700 text-body font-medium rounded-[8px] border border-gray-200 hover:bg-gray-50 transition-colors shadow-sm"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Tandai Semua Dibaca
                    </button>
                @endif
            </div>
        </div>
    </div>

    <div class="mx-auto w-full max-w-[1500px] px-4 md:px-6 lg:px-8 xl:px-10 2xl:px-12 py-6">

        @if($notifications->isEmpty())
            <div class="bg-white rounded-[12px] border border-gray-100">
                <x-empty-state
                    icon="bell"
                    title="Belum ada notifikasi"
                    text="Notifikasi akan muncul di sini ketika ada update terbaru."
                />
            </div>
        @else
            <div class="bg-white rounded-[12px] border border-gray-100 overflow-hidden">
                <div class="divide-y divide-gray-100">
                    @foreach($notifications as $notification)
                        <div
                            class="px-5 py-4 hover:bg-gray-50/50 transition-colors {{ !$notification->isRead() ? 'bg-blue-50/30' : '' }}"
                        >
                            <div class="flex items-start gap-4">
                                {{-- Icon --}}
                                <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 {{ !$notification->isRead() ? 'bg-blue-100' : 'bg-gray-100' }}">
                                    @if(str_contains($notification->type, 'payment') || str_contains($notification->type, 'invoice'))
                                        <svg class="w-5 h-5 {{ !$notification->isRead() ? 'text-blue-600' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    @elseif(str_contains($notification->type, 'box'))
                                        <svg class="w-5 h-5 {{ !$notification->isRead() ? 'text-blue-600' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    @elseif(str_contains($notification->type, 'complaint') || str_contains($notification->type, 'complain'))
                                        <svg class="w-5 h-5 {{ !$notification->isRead() ? 'text-blue-600' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    @elseif(str_contains($notification->type, 'claim') || str_contains($notification->type, 'klaim'))
                                        <svg class="w-5 h-5 {{ !$notification->isRead() ? 'text-blue-600' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    @else
                                        <svg class="w-5 h-5 {{ !$notification->isRead() ? 'text-blue-600' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                    @endif
                                </div>

                                {{-- Content --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-body font-semibold text-gray-900 {{ !$notification->isRead() ? 'font-bold' : '' }}">
                                                {{ $notification->title ?? $notification->data['title'] ?? 'Notifikasi' }}
                                            </p>
                                            <p class="text-body text-gray-600 mt-0.5">
                                                {{ $notification->message ?? $notification->data['message'] ?? '' }}
                                            </p>
                                        </div>
                                        <div class="flex items-center gap-2 flex-shrink-0">
                                            <span class="text-caption text-gray-400">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </span>
                                            @if(!$notification->isRead())
                                                <button
                                                    wire:click="markAsRead('{{ $notification->id }}')"
                                                    class="p-1.5 rounded-md text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-colors"
                                                    title="Tandai sudah dibaca"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                    @if(!$notification->isRead())
                                        <div class="w-2 h-2 bg-blue-500 rounded-full absolute top-4 right-4"></div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($notifications->hasPages())
                    <div class="px-5 py-3 border-t border-gray-100">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
