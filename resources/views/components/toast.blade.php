@props(['type' => 'success', 'title' => '', 'message' => '', 'show' => false])

@php
    $colors = match($type) {
        'success' => ['bg' => 'bg-emerald-50', 'border' => 'border-emerald-200', 'icon' => 'text-emerald-500', 'title' => 'text-emerald-800'],
        'error' => ['bg' => 'bg-red-50', 'border' => 'border-red-200', 'icon' => 'text-red-500', 'title' => 'text-red-800'],
        'warning' => ['bg' => 'bg-amber-50', 'border' => 'border-amber-200', 'icon' => 'text-amber-500', 'title' => 'text-amber-800'],
        'info' => ['bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'icon' => 'text-blue-500', 'title' => 'text-blue-800'],
        default => ['bg' => 'bg-gray-50', 'border' => 'border-gray-200', 'icon' => 'text-gray-500', 'title' => 'text-gray-800'],
    };

    $icons = match($type) {
        'success' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        'error' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        'warning' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>',
        'info' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        default => '',
    };
@endphp

<div
    x-data="{{ json_encode(['show' => $show]) }}"
    x-show="show"
    x-on:toast.window="
        if ($event.detail.type === '{{ $type }}') {
            title = $event.detail.title || '{{ $title }}';
            message = $event.detail.message || '{{ $message }}';
            show = true;
            setTimeout(() => show = false, 4000);
        }
    "
    x-on:close-toast.window="show = false"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 -translate-y-4 scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
    x-transition:leave-end="opacity-0 -translate-y-4 scale-95"
    class="fixed top-4 right-4 z-[60] max-w-sm w-full pointer-events-auto"
    style="display: none;"
>
    <div class="rounded-card shadow-modal border {{ $colors['border'] }} {{ $colors['bg'] }} p-4">
        <div class="flex items-start gap-3">
            <div class="flex-shrink-0 {{ $colors['icon'] }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $icons !!}</svg>
            </div>
            <div class="flex-1 min-w-0">
                @if($title || $attributes->has('title'))
                    <p class="text-body font-semibold {{ $colors['title'] }}" x-text="title"></p>
                @endif
                <p class="text-body text-gray-600 mt-0.5" x-text="message"></p>
            </div>
            <button @click="show = false" class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>
</div>
