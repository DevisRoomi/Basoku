@php
    $initialNav = 'beranda';
    if (request()->routeIs('bakso.history')) {
        $initialNav = 'sejarah';
    } elseif (request()->routeIs('calculator')) {
        $initialNav = 'kalkulator';
    } elseif (request()->routeIs('catalog')) {
        $initialNav = 'daftar-harga';
    } elseif (request()->is('racikan-saya*')) {
        $initialNav = 'racikan-saya';
    }
@endphp

<!-- Top Marquee Ticker Tape (Ref 3: Bake Today) + Soft Editorial Sticky Navbar (Ref 1: Pist & Zerna) -->
<header
    id="main-header"
    class="sticky top-0 z-50 w-full transition-all duration-300"
    x-data="{
        activeNav: @if(request()->is('/')) (function() {
            const h = window.location.hash;
            if (h === '#lokasi-kios') return '#lokasi-kios';
            if (h === '#daftar-harga') return 'daftar-harga';
            if (h === '#racikan-saya') return 'kalkulator';
            if (h === '#cara-kerja') return '#cara-kerja';
            return 'beranda';
        })() @elseif(request()->routeIs('bakso.history')) 'sejarah' @elseif(request()->routeIs('calculator')) 'kalkulator' @elseif(request()->routeIs('catalog')) 'daftar-harga' @elseif(request()->is('racikan-saya*')) 'racikan-saya' @else '{{ $initialNav }}' @endif,
        isClicking: false,
        @if(request()->is('/'))
        syncFromHash() {
            const h = window.location.hash;
            if (h === '#lokasi-kios') {
                this.activeNav = '#lokasi-kios';
            } else if (h === '#daftar-harga') {
                this.activeNav = 'daftar-harga';
            } else if (h === '#racikan-saya') {
                this.activeNav = 'kalkulator';
            } else if (h === '#cara-kerja') {
                this.activeNav = '#cara-kerja';
            } else {
                this.activeNav = 'beranda';
            }
        },
        checkScroll() {
            if (this.isClicking) return;
            if (window.scrollY < 80) {
                this.activeNav = 'beranda';
                return;
            }
            const isBottom = (window.innerHeight + window.scrollY) >= (document.documentElement.scrollHeight - 80);
            if (isBottom) {
                this.activeNav = '#lokasi-kios';
                return;
            }
            const elLokasi = document.querySelector('#lokasi-kios');
            const elDaftar = document.querySelector('#daftar-harga');
            const elRacikan = document.querySelector('#racikan-saya');
            const elCara = document.querySelector('#cara-kerja');

            if (elLokasi && elLokasi.getBoundingClientRect().top <= 250) {
                this.activeNav = '#lokasi-kios';
            } else if (elDaftar && elDaftar.getBoundingClientRect().top <= 250) {
                this.activeNav = 'daftar-harga';
            } else if (elRacikan && elRacikan.getBoundingClientRect().top <= 250) {
                this.activeNav = 'kalkulator';
            } else if (elCara && elCara.getBoundingClientRect().top <= 250) {
                this.activeNav = '#cara-kerja';
            } else {
                this.activeNav = 'beranda';
            }
        },
        init() {
            if (window.location.hash) {
                this.syncFromHash();
            } else if (window.scrollY > 100) {
                this.checkScroll();
            }
        }
        @endif
    }"
    @if(request()->is('/'))
    @hashchange.window="syncFromHash()"
    @scroll.window.throttle.100ms="checkScroll()"
    @endif
>
    
    <!-- 1. Running Marquee Ticker Tape Bar -->
    <div class="bg-[#8B231A] text-[#F4EFE6] text-[11px] font-semibold tracking-[0.18em] uppercase py-2 overflow-hidden border-b border-[#26221E]/10 select-none">
        <div class="animate-marquee whitespace-nowrap flex items-center gap-8">
            <span class="flex items-center gap-4">
                <span>✦ BASOKU — BELANJA & SELEPAN BAKSO PASAR</span>
                <span>✦ HITUNG TAKARAN PRESISI & ANGGARAN</span>
                <span>✦ 2 ESTIMASI TRANSPARAN: RESEP VS KEMASAN UTUH</span>
                <span>✦ LOKASI KIOS: 7°59'31.5"S 113°14'58.9"E</span>
                <span>✦ PESAN INSTAN VIA WHATSAPP</span>
            </span>
            <span class="flex items-center gap-4" aria-hidden="true">
                <span>✦ BASOKU — BELANJA & SELEPAN BAKSO PASAR</span>
                <span>✦ HITUNG TAKARAN PRESISI & ANGGARAN</span>
                <span>✦ 2 ESTIMASI TRANSPARAN: RESEP VS KEMASAN UTUH</span>
                <span>✦ LOKASI KIOS: 7°59'31.5"S 113°14'58.9"E</span>
                <span>✦ PESAN INSTAN VIA WHATSAPP</span>
            </span>
        </div>
    </div>

    <!-- 2. Soft Oatmeal Sticky Navbar -->
    <nav class="bg-[#F4EFE6]/95 backdrop-blur-md border-b border-[#26221E]/10 px-4 sm:px-6 lg:px-12 py-3.5 transition-all duration-300" aria-label="Navigasi Utama">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            
            <!-- Brand Identity (Ref 1: Sage Circle Monogram B. + Warm Serif Wordmark) -->
            <a
                href="{{ url('/') }}"
                @click="activeNav = 'beranda'"
                class="group flex items-center gap-3.5 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#D96B43] rounded-2xl"
                aria-label="Beranda BASOKU"
            >
                <div class="w-10 h-10 rounded-full bg-[#2D4235] text-[#F4EFE6] font-serif font-bold text-lg flex items-center justify-center shadow-sm transition-transform group-hover:scale-105 shrink-0 border border-[#2D4235]/30">
                    B.
                </div>
                <div>
                    <span class="text-[#26221E] text-2xl font-bold tracking-tight block leading-none" style="font-family: 'Playfair Display', Georgia, serif;">
                        BASOKU<span class="text-[#D96B43]">.</span>
                    </span>
                    <span class="text-[10px] uppercase font-mono tracking-wider text-[#5A534A] block mt-1">
                        spesialis bahan & selepan bakso
                    </span>
                </div>
            </a>

            <!-- Center: Desktop Navigation Links (Ref 1 Delicate Pill Links) -->
            <div class="hidden lg:flex items-center gap-1.5 bg-[#FAF7F2] border border-[#26221E]/10 rounded-full px-3 py-1.5 shadow-xs">
                <a
                    href="{{ url('/') }}"
                    @click="activeNav = 'beranda'; isClicking = true; setTimeout(() => isClicking = false, 800)"
                    class="rounded-full px-4 py-1.5 text-xs font-medium tracking-wide transition-colors"
                    :class="activeNav === 'beranda' ? 'bg-[#2D4235] text-[#F4EFE6] shadow-sm' : 'text-[#5A534A] hover:text-[#26221E] hover:bg-[#26221E]/5'"
                >
                    Beranda
                </a>
                <a
                    href="{{ route('bakso.history') }}"
                    @click="activeNav = 'sejarah'; isClicking = true; setTimeout(() => isClicking = false, 800)"
                    class="rounded-full px-4 py-1.5 text-xs font-medium tracking-wide transition-colors"
                    :class="activeNav === 'sejarah' ? 'bg-[#2D4235] text-[#F4EFE6] shadow-sm' : 'text-[#5A534A] hover:text-[#26221E] hover:bg-[#26221E]/5'"
                >
                    Sejarah &amp; Dokumentasi
                </a>
                <a
                    href="{{ url('/') }}#cara-kerja"
                    @click="activeNav = '#cara-kerja'; isClicking = true; setTimeout(() => isClicking = false, 800)"
                    class="rounded-full px-4 py-1.5 text-xs font-medium tracking-wide transition-colors"
                    :class="activeNav === '#cara-kerja' ? 'bg-[#2D4235] text-[#F4EFE6] shadow-sm' : 'text-[#5A534A] hover:text-[#26221E] hover:bg-[#26221E]/5'"
                >
                    Cara Kerja
                </a>
                <a
                    href="{{ route('calculator') }}"
                    @click="activeNav = 'kalkulator'; isClicking = true; setTimeout(() => isClicking = false, 800)"
                    class="rounded-full px-4 py-1.5 text-xs font-medium tracking-wide transition-colors"
                    :class="activeNav === 'kalkulator' ? 'bg-[#2D4235] text-[#F4EFE6] shadow-sm' : 'text-[#5A534A] hover:text-[#26221E] hover:bg-[#26221E]/5'"
                >
                    Kalkulator
                </a>
                <a
                    href="{{ route('catalog') }}"
                    @click="activeNav = 'daftar-harga'; isClicking = true; setTimeout(() => isClicking = false, 800)"
                    class="rounded-full px-4 py-1.5 text-xs font-medium tracking-wide transition-colors"
                    :class="activeNav === 'daftar-harga' ? 'bg-[#2D4235] text-[#F4EFE6] shadow-sm' : 'text-[#5A534A] hover:text-[#26221E] hover:bg-[#26221E]/5'"
                >
                    Daftar Harga
                </a>
                <a
                    href="{{ url('/') }}#lokasi-kios"
                    @click="activeNav = '#lokasi-kios'; isClicking = true; setTimeout(() => isClicking = false, 800)"
                    class="rounded-full px-4 py-1.5 text-xs font-medium tracking-wide transition-colors"
                    :class="activeNav === '#lokasi-kios' ? 'bg-[#2D4235] text-[#F4EFE6] shadow-sm' : 'text-[#5A534A] hover:text-[#26221E] hover:bg-[#26221E]/5'"
                >
                    Lokasi Kios
                </a>
                @if (auth()->check() && auth()->user()->role === 'customer')
                    <a
                        href="{{ url('/racikan-saya') }}"
                        @click="activeNav = 'racikan-saya'; isClicking = true; setTimeout(() => isClicking = false, 800)"
                        class="rounded-full px-4 py-1.5 text-xs font-medium tracking-wide transition-colors flex items-center gap-1.5"
                        :class="activeNav === 'racikan-saya' ? 'bg-[#2D4235] text-[#F4EFE6] shadow-sm' : 'text-[#5A534A] hover:text-[#26221E] hover:bg-[#26221E]/5'"
                    >
                        <span>Racikan Saya</span>
                        <span class="w-1.5 h-1.5 rounded-full bg-[#D96B43] animate-pulse"></span>
                    </a>
                @endif
            </div>

            <!-- Right: Actions & User Session (Ref 1 Terracotta Pill Button) -->
            <div class="hidden lg:flex items-center gap-3">
                @auth
                    @if (auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="text-xs font-semibold px-4 py-2 rounded-full bg-[#FAF7F2] hover:bg-[#F0EBE1] text-[#26221E] transition-colors border border-[#26221E]/15 shadow-xs">
                            Panel Admin
                        </a>
                    @else
                        <a href="{{ route('customer.recipes') }}" class="text-xs font-semibold px-4 py-2 rounded-full bg-[#FAF7F2] hover:bg-[#F0EBE1] text-[#26221E] transition-colors flex items-center gap-2 border border-[#26221E]/15 shadow-xs">
                            <span class="w-2 h-2 rounded-full bg-[#2D4235]"></span>
                            <span class="truncate max-w-[120px]">{{ auth()->user()->name }}</span>
                        </a>
                    @endif

                    <!-- Logout Global Form -->
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-xs font-medium text-[#26221E]/70 hover:text-[#8B231A] px-2 py-1 transition-colors cursor-pointer">
                            Keluar
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-xs font-medium text-[#26221E]/80 hover:text-[#26221E] px-3 py-2 transition-colors">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="text-xs font-medium px-4 py-2 rounded-full border border-[#26221E]/20 hover:border-[#26221E]/40 text-[#26221E] transition-colors">
                        Daftar
                    </a>
                @endauth

                <!-- Terracotta Primary CTA: Mulai Racik -->
                <a
                    href="{{ route('calculator') }}"
                    @click="activeNav = 'kalkulator'"
                    class="inline-flex items-center justify-center gap-2 bg-[#D96B43] hover:bg-[#C25932] text-white px-6 py-2.5 rounded-full text-xs font-semibold shadow-sm hover:shadow-md transition-all duration-200 active:scale-[0.98]"
                >
                    <span>Mulai Racik</span>
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </a>
            </div>

            <!-- Mobile Hamburger Button -->
            <div class="flex lg:hidden items-center gap-2">
                <a
                    href="{{ route('calculator') }}"
                    @click="activeNav = 'kalkulator'"
                    class="px-4 py-1.5 rounded-full bg-[#D96B43] text-white text-xs font-semibold shadow-xs"
                >
                    Racik
                </a>

                <button
                    id="mobile-menu-btn"
                    type="button"
                    class="p-2 rounded-xl text-[#26221E]/80 hover:text-[#26221E] hover:bg-black/5 transition-colors focus:outline-none cursor-pointer"
                    aria-label="Buka Menu"
                    aria-expanded="false"
                    aria-controls="mobile-menu"
                >
                    <svg id="menu-icon" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                    <svg id="close-icon" class="w-6 h-6 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

        </div>

        <!-- Mobile Drawer Container -->
        <div
            id="mobile-menu"
            class="hidden mt-3 bg-[#FAF7F2] border border-[#26221E]/10 rounded-2xl p-5 shadow-xl flex flex-col gap-2.5 transition-all duration-300 lg:hidden"
        >
            <a
                href="{{ url('/') }}"
                @click="activeNav = 'beranda'; isClicking = true; setTimeout(() => isClicking = false, 800)"
                class="block py-2 px-3 rounded-xl text-sm font-medium transition-colors"
                :class="activeNav === 'beranda' ? 'bg-[#2D4235] text-[#F4EFE6] shadow-sm' : 'text-[#5A534A] hover:text-[#26221E] hover:bg-[#26221E]/5'"
            >
                Beranda
            </a>
            <a
                href="{{ route('bakso.history') }}"
                @click="activeNav = 'sejarah'; isClicking = true; setTimeout(() => isClicking = false, 800)"
                class="block py-2 px-3 rounded-xl text-sm font-medium transition-colors"
                :class="activeNav === 'sejarah' ? 'bg-[#2D4235] text-[#F4EFE6] shadow-sm' : 'text-[#5A534A] hover:text-[#26221E] hover:bg-[#26221E]/5'"
            >
                Sejarah &amp; Dokumentasi
            </a>
            <a
                href="{{ url('/') }}#cara-kerja"
                @click="activeNav = '#cara-kerja'; isClicking = true; setTimeout(() => isClicking = false, 800)"
                class="block py-2 px-3 rounded-xl text-sm font-medium transition-colors"
                :class="activeNav === '#cara-kerja' ? 'bg-[#2D4235] text-[#F4EFE6] shadow-sm' : 'text-[#5A534A] hover:text-[#26221E] hover:bg-[#26221E]/5'"
            >
                Cara Kerja
            </a>
            <a
                href="{{ route('calculator') }}"
                @click="activeNav = 'kalkulator'; isClicking = true; setTimeout(() => isClicking = false, 800)"
                class="block py-2 px-3 rounded-xl text-sm font-medium transition-colors"
                :class="activeNav === 'kalkulator' ? 'bg-[#2D4235] text-[#F4EFE6] shadow-sm' : 'text-[#5A534A] hover:text-[#26221E] hover:bg-[#26221E]/5'"
            >
                Kalkulator
            </a>
            <a
                href="{{ route('catalog') }}"
                @click="activeNav = 'daftar-harga'; isClicking = true; setTimeout(() => isClicking = false, 800)"
                class="block py-2 px-3 rounded-xl text-sm font-medium transition-colors"
                :class="activeNav === 'daftar-harga' ? 'bg-[#2D4235] text-[#F4EFE6] shadow-sm' : 'text-[#5A534A] hover:text-[#26221E] hover:bg-[#26221E]/5'"
            >
                Daftar Harga
            </a>
            <a
                href="{{ url('/') }}#lokasi-kios"
                @click="activeNav = '#lokasi-kios'; isClicking = true; setTimeout(() => isClicking = false, 800)"
                class="block py-2 px-3 rounded-xl text-sm font-medium transition-colors"
                :class="activeNav === '#lokasi-kios' ? 'bg-[#2D4235] text-[#F4EFE6] shadow-sm' : 'text-[#5A534A] hover:text-[#26221E] hover:bg-[#26221E]/5'"
            >
                Lokasi Kios
            </a>

            @if (auth()->check() && auth()->user()->role === 'customer')
                <a
                    href="{{ url('/racikan-saya') }}"
                    @click="activeNav = 'racikan-saya'; isClicking = true; setTimeout(() => isClicking = false, 800)"
                    class="block py-2 px-3 rounded-xl text-sm font-medium transition-colors"
                    :class="activeNav === 'racikan-saya' ? 'bg-[#2D4235] text-[#F4EFE6] shadow-sm' : 'text-[#5A534A] hover:text-[#26221E] hover:bg-[#26221E]/5'"
                >
                    Racikan Saya
                </a>
            @endif

            <div class="h-px bg-[#26221E]/10 my-1"></div>

            @auth
                @if (auth()->user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="block py-2 px-3 rounded-xl text-sm font-semibold text-[#2D4235]">
                        Panel Admin
                    </a>
                @endif
                <form method="POST" action="{{ route('logout') }}" class="block">
                    @csrf
                    <button type="submit" class="w-full text-left py-2 px-3 rounded-xl text-sm font-medium text-[#8B231A] hover:bg-black/5">
                        Keluar
                    </button>
                </form>
            @else
                <div class="grid grid-cols-2 gap-2 pt-1">
                    <a href="{{ route('login') }}" class="text-center py-2 px-3 rounded-full text-sm font-medium border border-[#26221E]/20 text-[#26221E]">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="text-center py-2 px-3 rounded-full text-sm font-medium bg-[#2D4235] text-white">
                        Daftar
                    </a>
                </div>
            @endauth
        </div>
    </nav>
</header>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const btn = document.getElementById('mobile-menu-btn');
        const menu = document.getElementById('mobile-menu');
        const menuIcon = document.getElementById('menu-icon');
        const closeIcon = document.getElementById('close-icon');

        if (btn && menu) {
            btn.addEventListener('click', () => {
                const isHidden = menu.classList.contains('hidden');
                menu.classList.toggle('hidden', !isHidden);
                btn.setAttribute('aria-expanded', isHidden);
                if (menuIcon && closeIcon) {
                    menuIcon.classList.toggle('hidden', isHidden);
                    closeIcon.classList.toggle('hidden', !isHidden);
                }
            });

            menu.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', () => {
                    menu.classList.add('hidden');
                    btn.setAttribute('aria-expanded', 'false');
                    if (menuIcon && closeIcon) {
                        menuIcon.classList.remove('hidden');
                        closeIcon.classList.add('hidden');
                    }
                });
            });
        }
    });
</script>
