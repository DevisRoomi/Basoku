<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#FAF6F0]">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex, nofollow">

        <title>{{ $title ?? 'Dasbor Admin — BASOKU' }}</title>

        <!-- Fonts -->
        @fonts
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|playfair-display:400,600,700,800,900&display=swap" rel="stylesheet" />

        <!-- Chart.js via CDN -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="h-full bg-[#FAF6F0] text-[#161210] font-sans antialiased selection:bg-[#C83B2B] selection:text-[#FAF6F0]">
        
        <div class="min-h-screen bg-[#FAF6F0] flex flex-col md:flex-row text-[#161210]">
            <!-- Sidebar Reusable -->
            <x-admin-sidebar />

            <!-- Area Konten Dinamis -->
            <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
                {{ $slot }}
            </div>
        </div>

        @livewireScripts
    </body>
</html>
