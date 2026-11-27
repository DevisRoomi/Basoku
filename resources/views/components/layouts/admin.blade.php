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
        <link href="https://fonts.bunny.net/css?family=playfair-display:400,600,700,800,900&display=swap" rel="stylesheet" />

        <!-- Chart.js via CDN (versi terpin untuk kestabilan produksi) -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js" defer></script>

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="h-full bg-[#FAF6F0] text-[#161210] font-sans antialiased selection:bg-[#C83B2B] selection:text-[#FAF6F0]">

        <div
            x-data="{
                sidebarCollapsed: (() => {
                    try {
                        const saved = localStorage.getItem('basoku_admin_sidebar');
                        if (saved !== null) return saved === 'true';
                        return window.innerWidth < 1280;
                    } catch (e) {
                        return false;
                    }
                })(),
                mobileMenuOpen: false,
                toggleDesktopSidebar() {
                    this.sidebarCollapsed = !this.sidebarCollapsed;
                    try { localStorage.setItem('basoku_admin_sidebar', String(this.sidebarCollapsed)); } catch (e) {}
                    setTimeout(() => window.dispatchEvent(new Event('resize')), 320);
                },
                toggleMobileMenu() {
                    this.mobileMenuOpen = !this.mobileMenuOpen;
                },
                closeMobileMenu() {
                    this.mobileMenuOpen = false;
                }
            }"
            @keydown.window.ctrl.b.prevent="if (window.innerWidth >= 768) { toggleDesktopSidebar(); } else { toggleMobileMenu(); }"
            @keydown.window.meta.b.prevent="if (window.innerWidth >= 768) { toggleDesktopSidebar(); } else { toggleMobileMenu(); }"
            @keydown.escape.window="closeMobileMenu()"
            class="min-h-screen h-screen bg-[#FAF6F0] flex flex-col md:flex-row text-[#161210] overflow-hidden relative"
        >
            <!-- Top Bar Khusus Mobile dengan Tombol Hamburger Unik BASOKU -->
            <div class="md:hidden bg-[#161210] text-[#FAF6F0] px-4 py-3 flex items-center justify-between border-b border-white/10 shrink-0 z-30 shadow-md">
                <div class="flex items-center gap-3">
                    <!-- Unique Morphing Heritage Hamburger Button (Mobile) -->
                    <button
                        type="button"
                        @click="toggleMobileMenu()"
                        :aria-expanded="mobileMenuOpen"
                        aria-label="Buka atau tutup menu navigasi admin"
                        class="group relative w-11 h-11 rounded-xl bg-white/5 hover:bg-[#C83B2B]/20 border border-white/15 hover:border-[#C83B2B]/60 flex items-center justify-center transition-all duration-300 cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C83B2B]"
                    >
                        <div class="w-5 h-3.5 relative flex flex-col justify-between items-start">
                            <span
                                class="block h-[2px] rounded-full bg-[#FAF6F0] transition-all duration-300 ease-out origin-center"
                                :class="mobileMenuOpen ? 'w-5 translate-y-[6px] rotate-45 bg-[#C83B2B]' : 'w-5'"
                            ></span>
                            <span
                                class="flex items-center gap-1 transition-all duration-300 ease-out"
                                :class="mobileMenuOpen ? 'opacity-0 scale-50' : 'opacity-100 scale-100'"
                            >
                                <span class="w-1.5 h-1.5 rounded-full bg-[#C83B2B]"></span>
                                <span class="w-2.5 h-[2px] rounded-full bg-[#FAF6F0]/90"></span>
                            </span>
                            <span
                                class="block h-[2px] rounded-full bg-[#FAF6F0] transition-all duration-300 ease-out origin-center"
                                :class="mobileMenuOpen ? 'w-5 -translate-y-[6px] -rotate-45 bg-[#C83B2B]' : 'w-3.5 group-hover:w-5'"
                            ></span>
                        </div>
                    </button>

                    <a href="{{ route('admin.dashboard') }}" wire:navigate class="flex items-center gap-2">
                        <span class="text-xl font-extrabold tracking-tight" style="font-family: 'Playfair Display', Georgia, serif;">
                            BASOKU<span class="text-[#C83B2B]">.</span>
                        </span>
                        <span class="text-[9px] uppercase font-bold tracking-widest px-1.5 py-0.5 rounded bg-[#C83B2B]/25 text-[#FCA5A5] border border-[#FCA5A5]/30">
                            Admin
                        </span>
                    </a>
                </div>

                <a
                    href="/"
                    target="_blank"
                    rel="noopener noreferrer"
                    title="Buka Toko Publik"
                    aria-label="Buka Toko Publik"
                    class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white/5 hover:bg-white/10 text-white/70 hover:text-white transition-colors"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                </a>
            </div>

            <!-- Backdrop Mobile Drawer -->
            <div
                x-show="mobileMenuOpen"
                x-transition:enter="transition-opacity ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="closeMobileMenu()"
                class="fixed inset-0 bg-[#161210]/65 backdrop-blur-xs z-40 md:hidden"
                style="display: none;"
                aria-hidden="true"
            ></div>

            <!-- Sidebar Reusable Hybrid Mini-Rail (Desktop) & Slide-Over Drawer (Mobile) -->
            <x-admin-sidebar />

            <!-- Area Konten Dinamis Scrollable Mandiri -->
            <div class="flex-1 flex flex-col min-w-0 h-full overflow-y-auto">
                {{ $slot }}
            </div>
        </div>

        @livewireScripts
    </body>
</html>
