<aside class="w-full md:w-64 bg-[#161210] text-[#FAF6F0] flex flex-col justify-between shrink-0 shadow-2xl md:min-h-screen">
    <div>
        <!-- Header Brand Admin -->
        <div class="px-6 py-6 border-b border-white/10 flex items-center justify-between">
            <a href="{{ route('admin.dashboard') }}" wire:navigate class="group flex items-center gap-2 focus:outline-none">
                <span class="text-2xl font-extrabold tracking-tight" style="font-family: 'Playfair Display', Georgia, serif;">
                    BASOKU<span class="text-[#C83B2B]">.</span>
                </span>
                <span class="text-[10px] uppercase font-bold tracking-widest px-2 py-0.5 rounded bg-[#C83B2B]/20 text-[#C83B2B] border border-[#C83B2B]/30">
                    Admin
                </span>
            </a>
            <a href="/" target="_blank" title="Buka Toko Publik" class="text-xs text-white/50 hover:text-white transition-colors p-1.5 rounded-lg hover:bg-white/5">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
            </a>
        </div>

        <!-- Menu Navigasi -->
        <nav class="p-4 space-y-1.5" aria-label="Navigasi Panel Admin">
            <!-- 1. Beranda -->
            <a
                href="{{ route('admin.dashboard') }}"
                wire:navigate
                class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-150 cursor-pointer {{ request()->routeIs('admin.dashboard', 'admin.analytics') ? 'bg-[#C83B2B] text-white shadow-md shadow-[#C83B2B]/30' : 'text-[#FAF6F0]/70 hover:bg-white/5 hover:text-white' }}"
            >
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>Beranda</span>
            </a>

            <!-- 2. Kelola Harga -->
            <a
                href="{{ route('admin.materials') }}"
                wire:navigate
                class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-150 cursor-pointer {{ request()->routeIs('admin.materials*') ? 'bg-[#C83B2B] text-white shadow-md shadow-[#C83B2B]/30' : 'text-[#FAF6F0]/70 hover:bg-white/5 hover:text-white' }}"
            >
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
                <span>Kelola Harga</span>
            </a>

            <!-- 3. Pesanan -->
            <a
                href="{{ route('admin.orders') }}"
                wire:navigate
                class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-150 cursor-pointer {{ request()->routeIs('admin.orders*') ? 'bg-[#C83B2B] text-white shadow-md shadow-[#C83B2B]/30' : 'text-[#FAF6F0]/70 hover:bg-white/5 hover:text-white' }}"
            >
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <span>Pesanan</span>
            </a>
        </nav>
    </div>

    <!-- Profil Admin & Tombol Logout -->
    <div class="p-4 border-t border-white/10 bg-black/20">
        <div class="flex items-center gap-3 px-2 py-2 mb-2">
            <div class="w-9 h-9 rounded-full bg-[#C83B2B] text-white font-bold flex items-center justify-center text-xs shrink-0 shadow">
                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
            </div>
            <div class="overflow-hidden">
                <p class="text-xs font-bold text-white truncate">
                    {{ auth()->user()->name ?? 'Admin Toko' }}
                </p>
                <p class="text-[11px] text-white/50 truncate">
                    {{ auth()->user()->email ?? 'admin@basoku.com' }}
                </p>
            </div>
        </div>

        <!-- Tombol Logout -->
        <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <button
                type="submit"
                class="w-full flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl text-xs font-semibold text-red-300 hover:text-white hover:bg-red-900/40 transition-colors cursor-pointer border border-red-500/20 active:scale-[0.99]"
            >
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span>Keluar (Logout)</span>
            </button>
        </form>
    </div>
</aside>
