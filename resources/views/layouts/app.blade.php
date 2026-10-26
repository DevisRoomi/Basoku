<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="{{ $description ?? 'Pilih bahan, atur racikan, dan ketahui perkiraan biaya belanja sebelum mulai membuat bakso dengan BASOKU.' }}">

        <title>{{ $title ?? 'BASOKU — Mau Racik Bakso? Hitung Dulu Biayanya.' }}</title>

        <!-- Fonts -->
        @fonts

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="min-h-screen bg-motif-kawung font-sans antialiased text-[#26221E] selection:bg-[#D96B43] selection:text-white overflow-x-hidden">
        
        {{ $slot }}

        @livewireScripts
    </body>
</html>
