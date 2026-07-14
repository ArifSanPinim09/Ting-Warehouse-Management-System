<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        {{-- Ngrok compatibility: skip browser warning for AJAX/Upload requests --}}
        @if(str_contains(config('app.url'), 'ngrok'))
        <script>
            (function() {
                var origOpen = XMLHttpRequest.prototype.open;
                XMLHttpRequest.prototype.open = function() {
                    origOpen.apply(this, arguments);
                    try { this.setRequestHeader('ngrok-skip-browser-warning', 'true'); } catch(e) {}
                };
                var origFetch = window.fetch;
                window.fetch = function(url, opts) {
                    opts = opts || {};
                    opts.headers = Object.assign({}, opts.headers instanceof Headers ? Object.fromEntries(opts.headers.entries()) : opts.headers, {'ngrok-skip-browser-warning': 'true'});
                    return origFetch.call(this, url, opts);
                };
            })();
        </script>
        @endif
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            <livewire:layout.navigation />

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
