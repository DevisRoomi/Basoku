<x-layouts.app title="Kalkulator Takaran & Anggaran Bakso — BASOKU">
    <!-- Top Running Marquee & Soft Oatmeal Sticky Navbar -->
    <x-navbar />

    <!-- Editorial Workspace Masthead -->
    <header class="border-b border-[#26221E]/10 pt-8 pb-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12">
            <!-- Breadcrumbs / Top Quick Link -->
            <div class="flex flex-wrap items-center justify-between gap-4 mb-4 text-xs font-mono uppercase tracking-[0.18em] text-[#5A534A]">
                <nav class="flex items-center gap-2" aria-label="Breadcrumb">
                    <a href="{{ url('/') }}" class="hover:text-[#26221E] transition-colors">Beranda</a>
                    <span class="text-[#26221E]/30">&bull;</span>
                    <span class="text-[#2D4235] font-bold">Kalkulator</span>
                </nav>
                <a href="{{ route('catalog') }}" class="inline-flex items-center gap-1.5 text-[#D96B43] hover:text-[#C25932] font-semibold transition-colors">
                    <span>Lihat Katalog Harga Pasar</span>
                    <span aria-hidden="true">&rarr;</span>
                </a>
            </div>

            <!-- Masthead Content -->
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                <div class="max-w-3xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#FAF7F2] border border-[#26221E]/10 text-[10px] font-mono font-bold tracking-[0.2em] uppercase text-[#2D4235] mb-3 shadow-xs">
                        <span class="w-2 h-2 rounded-full bg-[#2D4235]"></span>
                        <span>RUANG KERJA RACIKAN • MODE FOKUS</span>
                    </div>
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-serif font-bold text-[#26221E] tracking-tight leading-[1.15]" style="font-family: 'Playfair Display', Georgia, serif;">
                        Kalkulator Takaran &amp; Anggaran Bakso.
                    </h1>
                    <p class="text-xs sm:text-sm text-[#5A534A] mt-2.5 max-w-2xl leading-relaxed font-normal">
                        Hitung presisi rasio daging, tepung tapioka, dan bumbu secara tenang dari meja kerja Anda. Dilengkapi simulasi belanja pasar riil sebelum bertolak ke los selepan.
                    </p>
                </div>

                <div class="hidden md:flex items-center gap-3 pb-1">
                    <span class="text-xs font-mono text-[#5A534A]">Batas Minimal Mesin Selepan:</span>
                    <span class="text-xs font-mono font-bold bg-[#FAF7F2] px-3.5 py-1.5 rounded-full border border-[#26221E]/10 text-[#26221E]">
                        500 Gram Daging
                    </span>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Workspace Container -->
    <main class="w-full text-[#26221E] py-10 sm:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12">
            <livewire:recipe-calculator />
        </div>
    </main>

    <!-- Footer Publik -->
    <x-footer />
</x-layouts.app>
