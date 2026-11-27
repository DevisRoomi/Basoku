<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#FAF6F0]">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex, nofollow">

        <title>{{ $title ?? 'Autentikasi — BASOKU' }}</title>
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

        <!-- Fonts -->
        @fonts
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=playfair-display:400,600,700,800,900&display=swap" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="h-full bg-motif-kawung text-[#26221E] font-sans antialiased selection:bg-[#D96B43] selection:text-white overflow-x-hidden">

        <main class="min-h-screen w-full flex">
            <!-- Sisi Kiri: Visual Split Editorial Sinematik (Shared Auth Shell) -->
            <div class="hidden lg:flex lg:w-1/2 relative bg-[#2D4235] overflow-hidden flex-col justify-between p-12 lg:p-16 text-[#F4EFE6]">
                <!-- Video Latar Sinematik -->
                <video
                    class="absolute inset-0 w-full h-full object-cover object-center opacity-30 pointer-events-none select-none"
                    autoplay
                    muted
                    loop
                    playsinline
                    preload="metadata"
                    poster="{{ asset('videos/basoku-hero-poster.webp') }}"
                    aria-hidden="true"
                >
                    <source src="{{ asset('videos/basoku-hero-opt.mp4') }}" type="video/mp4">
                </video>

                <!-- Dark Scrim Overlay -->
                <div class="absolute inset-0 bg-gradient-to-t from-[#2D4235] via-[#2D4235]/75 to-transparent"></div>

                <!-- Top Brand Mark -->
                <div class="relative z-10">
                    <a href="{{ url('/') }}" wire:navigate class="inline-flex items-center gap-3.5 group focus:outline-none">
                        <div class="w-10 h-10 rounded-full bg-[#FAF7F2] text-[#2D4235] flex items-center justify-center font-serif font-bold text-lg shadow-md transition-transform group-hover:scale-105 shrink-0">
                            B.
                        </div>
                        <div>
                            <span class="text-white text-2xl font-bold tracking-tight block leading-none" style="font-family: 'Playfair Display', Georgia, serif;">
                                BASOKU<span class="text-[#D96B43]">.</span>
                            </span>
                            <span class="text-[10px] uppercase font-mono tracking-widest text-[#D8E0D3] block mt-1">
                                Toko Bahan & Selepan Keluarga
                            </span>
                        </div>
                    </a>
                </div>

                <!-- Center Quote & Store Philosophy -->
                <div class="relative z-10 max-w-md my-auto py-12">
                    <span class="w-10 h-[2px] bg-[#D96B43] block mb-6"></span>
                    <blockquote class="text-2xl lg:text-3xl font-normal leading-relaxed text-[#F4EFE6]" style="font-family: 'Playfair Display', Georgia, serif;">
                        &ldquo;Meracik bakso yang jujur dimulai dari takaran yang terukur dan bahan pasar pilihan.&rdquo;
                    </blockquote>
                    <p class="text-xs uppercase font-mono tracking-widest text-[#D8E0D3] mt-6">
                        Los Selepan & Pasar Tradisional
                    </p>
                </div>

                <!-- Bottom Store Details -->
                <div class="relative z-10 pt-6 border-t border-white/10 flex items-center justify-between text-xs text-[#D8E0D3]/70 font-mono">
                    <span>PASAR KELUARGA SEJAK DULU</span>
                    <span>KEJUJURAN TIMBANGAN</span>
                </div>
            </div>

            <!-- Sisi Kanan: Form Card Dinamis -->
            <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12 min-h-screen relative">
                <div class="w-full max-w-md">
                    {{ $slot }}
                </div>
            </div>
        </main>

        @livewireScripts
    </body>
</html>
