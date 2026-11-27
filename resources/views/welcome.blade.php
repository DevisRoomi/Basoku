<x-layouts.app title="BASOKU — Spesialis Bahan Baku & Selepan Bakso Pasar Terpercaya">
    <!-- 1. Top Running Marquee & Soft Oatmeal Sticky Navbar -->
    <x-navbar />

    <!-- Konten Beranda Utama (Conversion-First Funnel: Hero -> Kalkulator -> Katalog -> Edukasi -> Arsip Ringkas) -->
    <main class="relative w-full text-[#26221E]">
        <!-- 2. Asymmetric Hero Section with Real Video Showcase -->
        <x-sections.hero />

        <!-- ═══════════════════════════════════════════════════════════════ -->
        <!-- 3. PRIORITAS #1: KALKULATOR RACIKAN INTERAKTIF                 -->
        <!-- ═══════════════════════════════════════════════════════════════ -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12">
            <!-- Section Divider 1: Precision Measurement Ticks (Workbench Blueprint Style) -->
            <div class="my-10 sm:my-14 motion-draw-line" aria-hidden="true">
                <div class="flex items-center gap-3">
                    <span class="font-mono text-xs text-[#D96B43] font-bold">+01</span>
                    <div class="flex-1 h-[1px] bg-gradient-to-r from-[#D96B43]/50 via-[#26221E]/20 to-[#2D4235]/50"></div>
                    <div class="px-4 py-1.5 rounded-full bg-[#2D4235] text-[#F4EFE6] text-[11px] font-mono tracking-widest uppercase font-semibold shadow-xs flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#E5A93C] animate-pulse"></span>
                        <span>MEJA KERJA &bull; KALKULATOR TAKARAN</span>
                    </div>
                    <div class="flex-1 h-[1px] bg-gradient-to-r from-[#2D4235]/50 via-[#26221E]/20 to-[#D96B43]/50"></div>
                    <span class="font-mono text-xs text-[#2D4235] font-bold">+02</span>
                </div>
            </div>

            <section id="racikan-saya" class="my-8 sm:my-12 scroll-mt-24">
                <livewire:recipe-calculator :embedded="true" />
            </section>
        </div>

        <!-- ═══════════════════════════════════════════════════════════════ -->
        <!-- 4. PRIORITAS #2: KATALOG BAHAN BAKU & DAFTAR HARGA PASAR       -->
        <!-- ═══════════════════════════════════════════════════════════════ -->
        <div class="my-12 sm:my-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-12 flex items-center justify-center gap-6 motion-draw-line" aria-hidden="true">
            <div class="flex-1 border-b-2 border-dotted border-[#26221E]/25"></div>
            <div class="flex items-center gap-2.5 px-4 py-1.5 rounded-full bg-[#FAF7F2] border border-[#26221E]/15 text-xs font-mono tracking-widest text-[#5A534A] shadow-xs">
                <span class="text-[#D96B43]">&#10022;</span>
                <span class="font-bold text-[#26221E] uppercase">LOS HARGA PASAR AKTIF</span>
                <span class="text-[#2D4235]">&#10022;</span>
            </div>
            <div class="flex-1 border-b-2 border-dotted border-[#26221E]/25"></div>
        </div>

        <div class="motion-reveal">
            <livewire:material-catalog />
        </div>

        <!-- ═══════════════════════════════════════════════════════════════ -->
        <!-- 5. PENGUAT KEPERCAYAAN: ALUR CARA KERJA KIOS                   -->
        <!-- ═══════════════════════════════════════════════════════════════ -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12">
            <!-- Alur Cara Kerja -->
            <section id="cara-kerja" class="my-12 sm:my-16 scroll-mt-24 relative motion-reveal">
                <!-- Deep Culinary Maroon Editorial Philosophy Banner -->
                <div class="bg-[#8B231A] text-[#F4EFE6] py-12 sm:py-14 px-6 sm:px-12 rounded-3xl text-center relative overflow-hidden shadow-xl mb-10">
                    <div class="max-w-2xl mx-auto space-y-4 relative z-10">
                        <span class="text-xs uppercase font-mono tracking-[0.25em] text-[#E5A93C] font-semibold block">
                            TRANSPARANSI BELANJA KELUARGA
                        </span>
                        <h2 class="text-2xl sm:text-4xl font-serif font-normal leading-tight text-white tracking-tight" style="font-family: 'Playfair Display', Georgia, serif;">
                            &ldquo;Meracik bakso enak kadang terasa rumit, tapi belanja bahan &amp; menghitung anggarannya harusnya semudah ini.&rdquo;
                        </h2>
                        <p class="text-xs sm:text-sm text-[#F4EFE6]/85 max-w-lg mx-auto font-normal">
                            Hitung bahan dari rumah secara tenang, ketahui nilai riil adonan, lalu pesan langsung ke los selepan pasar tanpa bingung saat di depan timbangan.
                        </p>
                    </div>
                </div>

                <!-- 3 Minimalist Editorial Step Index Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8 relative z-10">
                    <!-- STEP 1 Card -->
                    <div class="bg-[#FAF7F2] border border-[#26221E]/12 rounded-3xl p-8 shadow-xs flex flex-col justify-between group relative motion-reveal stagger-1">
                        <span class="absolute top-4 right-4 text-xs font-mono text-[#26221E]/20 select-none">+</span>
                        <div>
                            <div class="flex items-center justify-between mb-6">
                                <span class="bg-[#2D4235] text-[#F4EFE6] px-3.5 py-1 rounded-full text-xs font-mono font-bold tracking-wider">
                                    STEP 1
                                </span>
                                <span class="text-5xl font-serif italic text-[#D96B43]/30 select-none">01</span>
                            </div>
                            <h3 class="text-xl font-bold text-[#26221E] leading-snug mb-3" style="font-family: 'Playfair Display', Georgia, serif;">
                                Pilih Mode &amp; Bahan
                            </h3>
                            <div class="w-10 h-[1px] bg-[#26221E]/20 mb-4"></div>
                            <p class="text-xs sm:text-sm text-[#5A534A] leading-relaxed">
                                Tentukan apakah ingin membeli daging sapi/ayam segar di kios kami atau membawa daging sendiri dari rumah. Pilih karakter rasa: Enak, Seimbang, atau Ekonomis.
                            </p>
                        </div>
                        <div class="mt-8 pt-4 border-t border-[#26221E]/10 text-[11px] font-mono text-[#B84E26] font-semibold flex items-center justify-between">
                            <span>Formula Tersedia</span>
                            <span>Sapi &amp; Ayam</span>
                        </div>
                    </div>

                    <!-- STEP 2 Card -->
                    <div class="bg-[#FAF7F2] border border-[#26221E]/12 rounded-3xl p-8 shadow-xs flex flex-col justify-between group relative motion-reveal stagger-2">
                        <span class="absolute top-4 right-4 text-xs font-mono text-[#26221E]/20 select-none">+</span>
                        <div>
                            <div class="flex items-center justify-between mb-6">
                                <span class="bg-[#2D4235] text-[#F4EFE6] px-3.5 py-1 rounded-full text-xs font-mono font-bold tracking-wider">
                                    STEP 2
                                </span>
                                <span class="text-5xl font-serif italic text-[#D96B43]/30 select-none">02</span>
                            </div>
                            <h3 class="text-xl font-bold text-[#26221E] leading-snug mb-3" style="font-family: 'Playfair Display', Georgia, serif;">
                                Hitung Berat / Anggaran
                            </h3>
                            <div class="w-10 h-[1px] bg-[#26221E]/20 mb-4"></div>
                            <p class="text-xs sm:text-sm text-[#5A534A] leading-relaxed">
                                Masukkan berat daging yang ingin diolah (mulai 500g) atau kunci batas anggaran belanja Anda. Sistem menghitung tepung tapioka, bawang putih, garam, dan ongkos selepan.
                            </p>
                        </div>
                        <div class="mt-8 pt-4 border-t border-[#26221E]/10 text-[11px] font-mono text-[#B84E26] font-semibold flex items-center justify-between">
                            <span>Dua Estimasi Biaya</span>
                            <span>Resep vs Kemasan</span>
                        </div>
                    </div>

                    <!-- STEP 3 Card -->
                    <div class="bg-[#FAF7F2] border border-[#26221E]/12 rounded-3xl p-8 shadow-xs flex flex-col justify-between group relative motion-reveal stagger-3">
                        <span class="absolute top-4 right-4 text-xs font-mono text-[#26221E]/20 select-none">+</span>
                        <div>
                            <div class="flex items-center justify-between mb-6">
                                <span class="bg-[#2D4235] text-[#F4EFE6] px-3.5 py-1 rounded-full text-xs font-mono font-bold tracking-wider">
                                    STEP 3
                                </span>
                                <span class="text-5xl font-serif italic text-[#D96B43]/30 select-none">03</span>
                            </div>
                            <h3 class="text-xl font-bold text-[#26221E] leading-snug mb-3" style="font-family: 'Playfair Display', Georgia, serif;">
                                Simpan atau Pesan WA
                            </h3>
                            <div class="w-10 h-[1px] bg-[#26221E]/20 mb-4"></div>
                            <p class="text-xs sm:text-sm text-[#5A534A] leading-relaxed">
                                Simpan racikan ke laboratorium resep pribadi Anda atau kirim format rincian ke WhatsApp toko agar bahan disiapkan lebih dulu sebelum Anda tiba di los selepan.
                            </p>
                        </div>
                        <div class="mt-8 pt-4 border-t border-[#26221E]/10 text-[11px] font-mono text-[#B84E26] font-semibold flex items-center justify-between">
                            <span>Bebas Antre di Pasar</span>
                            <span>Siap Ambil</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ═══════════════════════════════════════════════════════════════ -->
            <!-- 6. COMPACT ARCHIVAL TEASER STRIP (TEPAT DI ATAS FOOTER)        -->
            <!-- ═══════════════════════════════════════════════════════════════ -->
            <section class="my-12 sm:my-16 motion-reveal" aria-label="Arsip Sejarah dan Sains Bakso">
                <div class="bg-[#FAF7F2] border border-[#26221E]/15 rounded-3xl p-5 sm:p-7 shadow-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-5">
                    <div class="flex items-center gap-4 sm:gap-5 min-w-0">
                        <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl overflow-hidden border border-[#26221E]/15 shrink-0 bg-[#26221E]">
                            <img
                                src="{{ asset('images/curated/bakso-asli.webp') }}"
                                alt="Dokumentasi Semangkuk Bakso Nusantara Asli"
                                width="96"
                                height="96"
                                class="w-full h-full object-cover"
                                loading="lazy"
                            >
                        </div>
                        <div class="min-w-0 space-y-1">
                            <span class="inline-block text-[10px] font-mono font-bold uppercase tracking-widest text-[#B84E26]">
                                Arsip Kuliner &bull; Sains Miosin &bull; Logika BASOKU
                            </span>
                            <h2 class="text-xl sm:text-2xl font-serif font-bold text-[#26221E] leading-snug" style="font-family: 'Playfair Display', Georgia, serif;">
                                Jejak &amp; Dokumentasi Bakso Nusantara
                            </h2>
                            <p class="text-xs sm:text-sm text-[#5A534A] leading-relaxed max-w-xl">
                                Ingin tahu kisah Meng Bo abad ke-17, rahasia protein miosin dingin, dan dokumentasi formula takaran kami? Baca arsip lengkapnya di halaman khusus.
                            </p>
                        </div>
                    </div>

                    <a
                        href="{{ route('bakso.history') }}"
                        class="inline-flex items-center gap-2.5 px-6 py-3 rounded-full bg-[#2D4235] hover:bg-[#1F3025] text-white font-semibold text-xs sm:text-sm shadow-sm transition-all active:scale-[0.98] whitespace-nowrap shrink-0"
                    >
                        <span>Baca Dokumentasi</span>
                        <svg class="w-4 h-4 text-[#D8E0D3]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                </div>
            </section>
        </div>
    </main>

    <!-- 7. Footer Publik Informatif & Lokasi Kios -->
    <x-footer />
</x-layouts.app>
