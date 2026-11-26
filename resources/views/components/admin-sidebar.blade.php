<aside
    class="fixed inset-y-0 left-0 z-50 md:static md:z-30 bg-[#161210] text-[#FAF6F0] flex flex-col justify-between shrink-0 shadow-2xl h-screen md:sticky md:top-0 overflow-y-auto md:overflow-visible transition-[width,transform] duration-300 ease-[cubic-bezier(0.22,1,0.36,1)]"
    :class="[
        mobileMenuOpen ? 'translate-x-0 w-72' : '-translate-x-full md:translate-x-0',
        sidebarCollapsed ? 'md:w-[78px]' : 'md:w-64'
    ]"
>
    <div class="flex flex-col flex-1">
        <!-- Header Brand Admin + Unique Morphing Heritage Hamburger -->
        <div
            class="border-b border-white/10 transition-all duration-300"
            :class="sidebarCollapsed ? 'px-4 py-4 flex items-center justify-between gap-2 md:px-3 md:flex-col md:items-center md:gap-3' : 'px-4 py-4 flex items-center justify-between gap-2'"
        >
            <!-- Brand Monogram / Full Logo -->
            <a
                href="{{ route('admin.dashboard') }}"
                wire:navigate
                @click="closeMobileMenu()"
                class="group flex items-center gap-1.5 focus:outline-none min-w-0"
                title="Dasbor Admin BASOKU"
            >
                <!-- Compact Heritage Stamp saat Mini-Rail -->
                <span
                    x-show="sidebarCollapsed"
                    class="hidden md:flex w-10 h-10 rounded-xl bg-gradient-to-br from-[#C83B2B] to-[#8B231A] text-[#FAF6F0] font-bold text-lg items-center justify-center shadow-md shadow-[#C83B2B]/30 border border-white/15 shrink-0"
                    style="font-family: 'Playfair Display', Georgia, serif; display: none;"
                >
                    B<span class="text-[#FAF6F0]/70">.</span>
                </span>

                <!-- Full Brand saat Expanded / Mobile Drawer -->
                <div
                    class="flex items-center gap-1.5 min-w-0"
                    :class="sidebarCollapsed ? 'md:hidden' : 'flex'"
                >
                    <span class="text-[21px] leading-none font-extrabold tracking-tight whitespace-nowrap" style="font-family: 'Playfair Display', Georgia, serif;">
                        BASOKU<span class="text-[#C83B2B]">.</span>
                    </span>
                    <span class="text-[9px] uppercase font-bold tracking-widest px-1.5 py-0.5 rounded bg-[#C83B2B]/25 text-[#FCA5A5] border border-[#FCA5A5]/30 shrink-0">
                        Admin
                    </span>
                </div>
            </a>

            <!-- Right Controls: Unique Morphing Hamburger (Desktop) / Close (Mobile) -->
            <div class="flex items-center shrink-0">
                <!-- Desktop Unique Morphing Heritage Hamburger ("Tiga Batang Selepan") -->
                <button
                    type="button"
                    @click="toggleDesktopSidebar()"
                    :aria-expanded="!sidebarCollapsed"
                    aria-label="Ciutkan atau lebarkan sidebar admin (Ctrl+B)"
                    class="hidden md:flex group/burger relative w-9 h-9 rounded-xl bg-white/[0.06] hover:bg-[#C83B2B]/20 border border-white/15 hover:border-[#C83B2B]/60 items-center justify-center transition-all duration-300 cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C83B2B]"
                >
                    <div class="w-4 h-3.5 relative flex flex-col justify-between items-start">
                        <!-- Baris 1 -->
                        <span
                            class="block h-[2px] rounded-full bg-[#FAF6F0] transition-all duration-300 ease-out"
                            :class="sidebarCollapsed ? 'w-4 group-hover/burger:bg-[#FAF6F0]' : 'w-4 group-hover/burger:w-3 group-hover/burger:-translate-x-0.5'"
                        ></span>
                        <!-- Baris 2: Titik Aksen Terracotta + Batang Selepan -->
                        <span class="flex items-center gap-1 transition-all duration-300 ease-out">
                            <span
                                class="w-1.5 h-1.5 rounded-full bg-[#C83B2B] transition-transform duration-300"
                                :class="sidebarCollapsed ? 'scale-110 shadow-[0_0_8px_rgba(200,59,43,0.8)]' : 'scale-100'"
                            ></span>
                            <span
                                class="h-[2px] rounded-full bg-[#FAF6F0]/85 transition-all duration-300"
                                :class="sidebarCollapsed ? 'w-2 group-hover/burger:w-2.5' : 'w-2.5 group-hover/burger:w-1.5'"
                            ></span>
                        </span>
                        <!-- Baris 3 -->
                        <span
                            class="block h-[2px] rounded-full bg-[#FAF6F0] transition-all duration-300 ease-out"
                            :class="sidebarCollapsed ? 'w-3 group-hover/burger:w-4' : 'w-3 group-hover/burger:w-4'"
                        ></span>
                    </div>

                    <!-- Floating Tooltip Shortcut -->
                    <span class="pointer-events-none opacity-0 group-hover/burger:opacity-100 translate-x-1 group-hover/burger:translate-x-0 transition-all duration-200 absolute left-full ml-3 px-2.5 py-1.5 rounded-lg bg-[#161210] text-[#FAF6F0] text-[11px] font-medium whitespace-nowrap shadow-xl border border-white/15 z-50 flex items-center gap-2">
                        <span x-text="sidebarCollapsed ? 'Lebarkan Menu' : 'Ciutkan Menu'"></span>
                        <kbd class="px-1.5 py-0.5 text-[9px] font-mono rounded bg-white/10 text-white/75">Ctrl+B</kbd>
                    </span>
                </button>

                <!-- Mobile Close Button -->
                <button
                    type="button"
                    @click="closeMobileMenu()"
                    aria-label="Tutup menu navigasi"
                    class="md:hidden w-9 h-9 rounded-xl bg-white/5 hover:bg-white/15 text-white/75 hover:text-white flex items-center justify-center transition-colors cursor-pointer"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Menu Navigasi -->
        <nav
            class="space-y-1.5 transition-all duration-300"
            :class="sidebarCollapsed ? 'p-3' : 'p-4'"
            aria-label="Navigasi Panel Admin"
        >
            <!-- 1. Beranda -->
            <a
                href="{{ route('admin.dashboard') }}"
                wire:navigate
                @click="closeMobileMenu()"
                class="group/nav relative w-full flex items-center rounded-xl text-sm font-semibold transition-all duration-150 cursor-pointer {{ request()->routeIs('admin.dashboard', 'admin.analytics') ? 'bg-[#C83B2B] text-white shadow-md shadow-[#C83B2B]/30' : 'text-[#FAF6F0]/75 hover:bg-white/5 hover:text-white' }}"
                :class="sidebarCollapsed ? 'md:justify-center md:px-0 px-4 py-3 gap-3' : 'px-4 py-3 gap-3'"
            >
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span class="whitespace-nowrap truncate" :class="sidebarCollapsed ? 'md:hidden' : 'inline'">Beranda</span>
                <span
                    x-show="sidebarCollapsed"
                    class="hidden md:block pointer-events-none opacity-0 group-hover/nav:opacity-100 translate-x-1 group-hover/nav:translate-x-0 transition-all duration-150 absolute left-full ml-3.5 px-3 py-1.5 rounded-lg bg-[#161210] text-[#FAF6F0] text-xs font-semibold whitespace-nowrap shadow-xl border border-white/15 z-50"
                    style="display: none;"
                >
                    Beranda Analitik
                </span>
            </a>

            <!-- 2. Kelola Harga -->
            <a
                href="{{ route('admin.materials') }}"
                wire:navigate
                @click="closeMobileMenu()"
                class="group/nav relative w-full flex items-center rounded-xl text-sm font-semibold transition-all duration-150 cursor-pointer {{ request()->routeIs('admin.materials*') ? 'bg-[#C83B2B] text-white shadow-md shadow-[#C83B2B]/30' : 'text-[#FAF6F0]/75 hover:bg-white/5 hover:text-white' }}"
                :class="sidebarCollapsed ? 'md:justify-center md:px-0 px-4 py-3 gap-3' : 'px-4 py-3 gap-3'"
            >
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
                <span class="whitespace-nowrap truncate" :class="sidebarCollapsed ? 'md:hidden' : 'inline'">Kelola Harga</span>
                <span
                    x-show="sidebarCollapsed"
                    class="hidden md:block pointer-events-none opacity-0 group-hover/nav:opacity-100 translate-x-1 group-hover/nav:translate-x-0 transition-all duration-150 absolute left-full ml-3.5 px-3 py-1.5 rounded-lg bg-[#161210] text-[#FAF6F0] text-xs font-semibold whitespace-nowrap shadow-xl border border-white/15 z-50"
                    style="display: none;"
                >
                    Kelola Harga Bahan
                </span>
            </a>

            <!-- 3. Formula Resep -->
            <a
                href="{{ route('admin.recipes') }}"
                wire:navigate
                @click="closeMobileMenu()"
                class="group/nav relative w-full flex items-center rounded-xl text-sm font-semibold transition-all duration-150 cursor-pointer {{ request()->routeIs('admin.recipes*') ? 'bg-[#C83B2B] text-white shadow-md shadow-[#C83B2B]/30' : 'text-[#FAF6F0]/75 hover:bg-white/5 hover:text-white' }}"
                :class="sidebarCollapsed ? 'md:justify-center md:px-0 px-4 py-3 gap-3' : 'px-4 py-3 gap-3'"
            >
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                </svg>
                <span class="whitespace-nowrap truncate" :class="sidebarCollapsed ? 'md:hidden' : 'inline'">Formula Resep</span>
                <span
                    x-show="sidebarCollapsed"
                    class="hidden md:block pointer-events-none opacity-0 group-hover/nav:opacity-100 translate-x-1 group-hover/nav:translate-x-0 transition-all duration-150 absolute left-full ml-3.5 px-3 py-1.5 rounded-lg bg-[#161210] text-[#FAF6F0] text-xs font-semibold whitespace-nowrap shadow-xl border border-white/15 z-50"
                    style="display: none;"
                >
                    Formula Resep
                </span>
            </a>

            <!-- 4. Pesanan -->
            <a
                href="{{ route('admin.orders') }}"
                wire:navigate
                @click="closeMobileMenu()"
                class="group/nav relative w-full flex items-center rounded-xl text-sm font-semibold transition-all duration-150 cursor-pointer {{ request()->routeIs('admin.orders*') ? 'bg-[#C83B2B] text-white shadow-md shadow-[#C83B2B]/30' : 'text-[#FAF6F0]/75 hover:bg-white/5 hover:text-white' }}"
                :class="sidebarCollapsed ? 'md:justify-center md:px-0 px-4 py-3 gap-3' : 'px-4 py-3 gap-3'"
            >
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <span class="whitespace-nowrap truncate" :class="sidebarCollapsed ? 'md:hidden' : 'inline'">Pesanan</span>
                <span
                    x-show="sidebarCollapsed"
                    class="hidden md:block pointer-events-none opacity-0 group-hover/nav:opacity-100 translate-x-1 group-hover/nav:translate-x-0 transition-all duration-150 absolute left-full ml-3.5 px-3 py-1.5 rounded-lg bg-[#161210] text-[#FAF6F0] text-xs font-semibold whitespace-nowrap shadow-xl border border-white/15 z-50"
                    style="display: none;"
                >
                    Manajemen Pesanan
                </span>
            </a>

            <!-- Pintasan Toko Publik (Tersedia rapi di Expanded, Mobile Drawer, maupun Mini-Rail) -->
            <div class="border-t border-white/10 mt-3 pt-3">
                <a
                    href="/"
                    target="_blank"
                    rel="noopener noreferrer"
                    @click="closeMobileMenu()"
                    title="Buka Toko Publik"
                    aria-label="Buka Toko Publik"
                    class="group/nav relative w-full flex items-center rounded-xl text-sm font-medium text-[#FAF6F0]/60 hover:bg-white/5 hover:text-white transition-all duration-150 cursor-pointer"
                    :class="sidebarCollapsed ? 'md:justify-center md:px-0 px-4 py-2.5 gap-3' : 'px-4 py-2.5 gap-3'"
                >
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    <span class="whitespace-nowrap truncate" :class="sidebarCollapsed ? 'md:hidden' : 'inline'">Toko Publik</span>
                    <span
                        x-show="sidebarCollapsed"
                        class="hidden md:block pointer-events-none opacity-0 group-hover/nav:opacity-100 translate-x-1 group-hover/nav:translate-x-0 transition-all duration-150 absolute left-full ml-3.5 px-3 py-1.5 rounded-lg bg-[#161210] text-[#FAF6F0] text-xs font-semibold whitespace-nowrap shadow-xl border border-white/15 z-50"
                        style="display: none;"
                    >
                        Buka Toko Publik
                    </span>
                </a>
            </div>
        </nav>
    </div>

    <!-- Profil Admin & Tombol Logout -->
    <div
        class="border-t border-white/10 bg-black/20 shrink-0 sticky bottom-0 transition-all duration-300"
        :class="sidebarCollapsed ? 'p-4 md:p-3 md:flex md:flex-col md:items-center md:gap-2.5' : 'p-4'"
    >
        <div
            class="group/profile relative flex items-center"
            :class="sidebarCollapsed ? 'md:justify-center md:px-0 md:py-1 md:mb-0 gap-3 px-2 py-2 mb-2' : 'gap-3 px-2 py-2 mb-2'"
        >
            <div class="w-9 h-9 rounded-full bg-[#C83B2B] text-white font-bold flex items-center justify-center text-xs shrink-0 shadow">
                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
            </div>
            <div class="overflow-hidden" :class="sidebarCollapsed ? 'md:hidden' : 'block'">
                <p class="text-xs font-bold text-white truncate">
                    {{ auth()->user()->name ?? 'Admin Toko' }}
                </p>
                <p class="text-[11px] text-white/50 truncate">
                    {{ auth()->user()->email ?? 'admin@basoku.com' }}
                </p>
            </div>
            <div
                x-show="sidebarCollapsed"
                class="hidden md:block pointer-events-none opacity-0 group-hover/profile:opacity-100 translate-x-1 group-hover/profile:translate-x-0 transition-all duration-150 absolute left-full ml-3.5 px-3 py-2 rounded-lg bg-[#161210] text-[#FAF6F0] whitespace-nowrap shadow-xl border border-white/15 z-50"
                style="display: none;"
            >
                <p class="text-xs font-bold text-white">{{ auth()->user()->name ?? 'Admin Toko' }}</p>
                <p class="text-[10px] text-white/60">{{ auth()->user()->email ?? 'admin@basoku.com' }}</p>
            </div>
        </div>

        <!-- Tombol Logout -->
        <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <button
                type="submit"
                aria-label="Keluar (Logout)"
                class="group/logout relative w-full flex items-center justify-center gap-2 rounded-xl text-xs font-semibold text-red-300 hover:text-white hover:bg-red-900/40 transition-colors cursor-pointer border border-red-500/20 active:scale-[0.99]"
                :class="sidebarCollapsed ? 'md:px-0 md:py-2.5 px-3 py-2.5' : 'px-3 py-2.5'"
            >
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span class="whitespace-nowrap" :class="sidebarCollapsed ? 'md:hidden' : 'inline'">Keluar (Logout)</span>
                <span
                    x-show="sidebarCollapsed"
                    class="hidden md:block pointer-events-none opacity-0 group-hover/logout:opacity-100 translate-x-1 group-hover/logout:translate-x-0 transition-all duration-150 absolute left-full ml-3.5 px-3 py-1.5 rounded-lg bg-[#161210] text-red-200 text-xs font-semibold whitespace-nowrap shadow-xl border border-red-500/30 z-50"
                    style="display: none;"
                >
                    Keluar (Logout)
                </span>
            </button>
        </form>
    </div>
</aside>
