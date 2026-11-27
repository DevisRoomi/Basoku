<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="{{ $description ?? 'Platform pintar kalkulator racikan bakso pasar dan pemesanan instan via WhatsApp. Hitung komposisi daging, tepung, dan bumbu secara presisi.' }}">
        <meta name="theme-color" content="#2D4235">

        <title>{{ $title ?? 'BASOKU — Asisten Racikan & Belanja Bakso Pasar' }}</title>
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="apple-touch-icon" href="{{ asset('favicon.svg') }}">
        <link rel="manifest" href="{{ asset('manifest.json') }}">

        <!-- Fonts -->
        @fonts
        <!-- Editorial Serif Font (Playfair Display via Bunny Fonts CDN) -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=playfair-display:400,500,600,700,800,900,400i,600i,700i&display=swap" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="min-h-screen bg-motif-kawung text-[#26221E] font-sans antialiased selection:bg-[#D96B43] selection:text-white overflow-x-clip">
        
        {{ $slot }}

        @livewire('ai-consultant')

        @livewireScripts
        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js').catch(() => {});
                });
            }
        </script>
    </body>
</html>
