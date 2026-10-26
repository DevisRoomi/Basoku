<x-layouts.app title="Katalog Harga Bahan Baku & Jasa Selepan — BASOKU">
    <!-- Top Running Marquee & Soft Oatmeal Sticky Navbar -->
    <x-navbar />

    <!-- Editorial Market Price Masthead -->
    <header class="border-b border-[#26221E]/10 pt-8 pb-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12">
            <!-- Breadcrumbs / Top Info Strip -->
            <div class="flex flex-wrap items-center justify-between gap-4 mb-4 text-xs font-mono uppercase tracking-[0.18em] text-[#5A534A]">
                <nav class="flex items-center gap-2" aria-label="Breadcrumb">
                    <a href="{{ url('/') }}" class="hover:text-[#26221E] transition-colors">Beranda</a>
                    <span class="text-[#26221E]/30">&bull;</span>
                    <span class="text-[#2D4235] font-bold">Daftar Harga</span>
                </nav>
                <div class="flex items-center gap-2 text-[#2D4235] font-semibold">
                    <span class="w-2 h-2 rounded-full bg-[#2D4235] animate-pulse"></span>
                    <span>STANDAR PASAR TRADISIONAL AKTIF</span>
                </div>
            </div>

            <!-- Masthead Content -->
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
                <div class="max-w-3xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#FAF7F2] border border-[#26221E]/10 text-[10px] font-mono font-bold tracking-[0.2em] uppercase text-[#D96B43] mb-3 shadow-xs">
                        <span class="w-2 h-2 rounded-full bg-[#D96B43]"></span>
                        <span>INFORMASI HARGA LOS PASAR • REAL-TIME DATABASE</span>
                    </div>
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-serif font-bold text-[#26221E] tracking-tight leading-[1.15]" style="font-family: 'Playfair Display', Georgia, serif;">
                        Katalog Harga Bahan Baku &amp; Jasa Selepan.
                    </h1>
                    <p class="text-xs sm:text-sm text-[#5A534A] mt-2.5 max-w-2xl leading-relaxed font-normal">
                        Daftar harga bahan baku segar dan ongkos giling mesin selepan pasar tradisional hari ini. Transparan untuk acuan belanja dan perhitungan racikan adonan Anda.
                    </p>
                </div>

                <div class="shrink-0 pb-1">
                    <a
                        href="{{ route('calculator') }}"
                        class="inline-flex items-center gap-2.5 px-6 py-3 rounded-full bg-[#D96B43] hover:bg-[#C25932] text-white font-semibold text-xs sm:text-sm shadow-md transition-all duration-200 active:scale-[0.98]"
                    >
                        <span>Buka Kalkulator Racikan &rarr;</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Catalog View -->
    <main class="w-full text-[#26221E]">
        <livewire:material-catalog />
    </main>

    <!-- Footer Publik -->
    <x-footer />
</x-layouts.app>
