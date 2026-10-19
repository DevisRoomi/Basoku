<!-- Footer Publik: Reference 1 (Pist & Zerna Cozy Store Card) + Reference 3 (Bake Today Giant Wordmark) -->
<footer class="bg-[#FAF7F2] text-[#26221E] pt-16 pb-12 border-t border-[#26221E]/10 text-sm overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12">
        
        <!-- ═══════════════════════════════════════════════════════════════ -->
        <!-- COZY ILLUSTRATED STORE CARD (Reference 1: Pist & Zerna)         -->
        <!-- ═══════════════════════════════════════════════════════════════ -->
        <!-- ═══════════════════════════════════════════════════════════════ -->
        <!-- 12-COLUMN ARCHITECTURAL STORE & MAP SHOWCASE                    -->
        <!-- ═══════════════════════════════════════════════════════════════ -->
        <div id="lokasi-kios" class="bg-[#FAF7F2] border border-[#26221E]/15 rounded-[2.5rem] p-6 sm:p-10 shadow-sm mb-16 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center scroll-mt-24 relative overflow-hidden">
            <!-- Architectural Background Watermark -->
            <div class="absolute -right-8 -bottom-8 editorial-watermark select-none pointer-events-none opacity-20 tracking-tighter" aria-hidden="true">
                PASAR
            </div>

            <!-- Left Column: Store Details & Coordinates (6 Kolom) -->
            <div class="lg:col-span-6 space-y-4 relative z-10">
                <!-- Coordinate Badge -->
                <div class="tabular-nums font-mono text-xs tracking-widest text-[#8B231A] bg-[#8B231A]/10 px-3.5 py-1.5 rounded-full inline-block">
                    TITIK KOORDINAT KIOS &bull; 7°59'31.5"S 113°14'58.9"E
                </div>

                <h3 class="text-2xl sm:text-4xl font-serif font-bold text-[#26221E] tracking-tight leading-tight" style="font-family: 'Playfair Display', Georgia, serif;">
                    Kami Tunggu di Los Selepan Pasar.
                </h3>

                <p class="text-[#5A534A] text-xs sm:text-sm leading-relaxed max-w-lg">
                    Bahan segar tiba setiap subuh dari los jagal. Bawa racikan hasil kalkulator Anda atau hubungi kami terlebih dahulu agar belanja &amp; giling adonan Anda lebih tenang dan cepat.
                </p>

                <!-- Store Spec Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pt-1 text-xs">
                    <div class="bg-white border border-[#26221E]/10 rounded-2xl p-3.5">
                        <span class="text-[10px] font-mono uppercase text-[#5A534A] block">Jam Operasional Pasar</span>
                        <span class="font-bold text-[#26221E] text-sm block mt-0.5">Setiap Hari &bull; 04.00 – 12.00 WIB</span>
                        <span class="text-[11px] text-[#5A534A] mt-0.5 block">Subuh hingga tengah hari</span>
                    </div>
                    <div class="bg-white border border-[#26221E]/10 rounded-2xl p-3.5">
                        <span class="text-[10px] font-mono uppercase text-[#5A534A] block">Layanan Kios</span>
                        <span class="font-bold text-[#26221E] text-sm block mt-0.5">Bahan Baku &amp; Selepan</span>
                        <span class="text-[11px] text-[#5A534A] mt-0.5 block">Belanja Bahan &amp; Giling Adonan</span>
                    </div>
                    <div class="sm:col-span-2 bg-white border border-[#26221E]/10 rounded-2xl p-3 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-mono uppercase text-[#5A534A] block">Format GPS Desimal</span>
                            <span class="font-mono font-bold text-xs text-[#26221E]">-7.992083, 113.249694</span>
                        </div>
                        <span class="text-[11px] font-mono text-[#D96B43] bg-[#D96B43]/10 px-2.5 py-1 rounded-md">Navigasi Terverifikasi</span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-wrap items-center gap-3 pt-2">
                    <a
                        href="https://www.google.com/maps/search/?api=1&query=-7.992083,113.249694"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-[#8B231A] hover:bg-[#701A13] text-white font-semibold text-xs sm:text-sm shadow-md transition-all active:scale-[0.98]"
                    >
                        <span>Buka Petunjuk Arah (Google Maps) &rarr;</span>
                    </a>
                    <a
                        href="{{ route('calculator') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-[#FAF7F2] hover:bg-[#EAE3D5] text-[#26221E] border border-[#26221E]/20 font-semibold text-xs sm:text-sm transition-all active:scale-[0.98]"
                    >
                        <span>Pesan Dulu via Kalkulator</span>
                    </a>
                </div>
            </div>

            <!-- Right Column: Interactive Embedded Google Map (6 Kolom) -->
            <div class="lg:col-span-6 relative z-10">
                <div class="rounded-[2rem] overflow-hidden border-2 border-[#26221E]/15 shadow-md bg-[#FAF7F2]">
                    <iframe
                        src="https://www.google.com/maps?q=-7.992083,113.249694&hl=id&z=17&output=embed"
                        class="w-full h-72 sm:h-80 rounded-[1.75rem] border-0"
                        style="filter: sepia(18%) contrast(98%) saturate(90%);"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        title="Lokasi Kios BASOKU 7°59'31.5&quot;S 113°14'58.9&quot;E"
                    ></iframe>
                </div>
            </div>
        </div>

        <!-- ═══════════════════════════════════════════════════════════════ -->
        <!-- 4-COLUMN FOOTER NAVIGATION & CREDITS                           -->
        <!-- ═══════════════════════════════════════════════════════════════ -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8 pb-12 border-b border-[#26221E]/10">
            
            <!-- Brand & Info (2 Kolom) -->
            <div class="lg:col-span-2 space-y-3">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-2.5 text-2xl font-bold tracking-tight text-[#26221E]" style="font-family: 'Playfair Display', Georgia, serif;">
                    <span>BASOKU</span><span class="text-[#D96B43]">.</span>
                </a>
                <p class="text-[#5A534A] text-xs leading-relaxed max-w-sm">
                    Aplikasi perencanaan racikan dan kalkulator biaya belanja bahan bakso pasar tradisional. Membantu keluarga dan pedagang menghitung takaran presisi serta memperkirakan uang belanja dengan jujur.
                </p>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#FAF7F2] border border-[#26221E]/10 text-xs text-[#2D4235]">
                    <span class="w-2 h-2 rounded-full bg-[#2D4235]"></span>
                    <span class="font-medium text-[11px]">Standar Takaran Pasar Teruji</span>
                </div>
            </div>

            <!-- Navigasi -->
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-[#26221E] mb-3 font-mono">Navigasi</h4>
                <ul class="space-y-2 text-xs text-[#5A534A]">
                    <li><a href="{{ url('/') }}" class="hover:text-[#D96B43] transition-colors">Beranda</a></li>
                    <li><a href="{{ route('bakso.history') }}" class="hover:text-[#D96B43] transition-colors">Sejarah &amp; Dokumentasi</a></li>
                    <li><a href="{{ url('/') }}#lokasi-kios" class="hover:text-[#D96B43] transition-colors">Lokasi Kios</a></li>
                    <li><a href="{{ url('/') }}#cara-kerja" class="hover:text-[#D96B43] transition-colors">Cara Kerja</a></li>
                    <li><a href="{{ route('calculator') }}" class="hover:text-[#D96B43] transition-colors">Kalkulator</a></li>
                    <li><a href="{{ route('catalog') }}" class="hover:text-[#D96B43] transition-colors">Daftar Harga</a></li>
                </ul>
            </div>

            <!-- Bahan Pasar -->
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-[#26221E] mb-3 font-mono">Bahan Baku</h4>
                <ul class="space-y-2 text-xs text-[#5A534A]">
                    <li><a href="{{ url('/') }}#daftar-harga" class="hover:text-[#D96B43] transition-colors">Daging Sapi &amp; Ayam</a></li>
                    <li><a href="{{ url('/') }}#daftar-harga" class="hover:text-[#D96B43] transition-colors">Tepung Tapioka Super</a></li>
                    <li><a href="{{ url('/') }}#daftar-harga" class="hover:text-[#D96B43] transition-colors">Bawang Putih &amp; Bawang Goreng</a></li>
                    <li><a href="{{ url('/') }}#daftar-harga" class="hover:text-[#D96B43] transition-colors">Garam &amp; Bumbu Racik</a></li>
                </ul>
            </div>

            <!-- Catatan -->
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-[#26221E] mb-3 font-mono">Transparansi</h4>
                <p class="text-xs text-[#5A534A] leading-relaxed">
                    Data harga mengacu pada harga pasar tradisional hari ini dan ukuran kemasan utuh di kios. Tidak ada biaya tersembunyi.
                </p>
            </div>

        </div>

        <!-- ═══════════════════════════════════════════════════════════════ -->
        <!-- OVERSIZED ARCHITECTURAL WORDMARK (Reference 3: Bake Today)     -->
        <!-- ═══════════════════════════════════════════════════════════════ -->
        <div class="py-10 text-center select-none overflow-hidden" aria-hidden="true">
            <span class="block text-6xl sm:text-8xl md:text-9xl lg:text-[13rem] font-serif font-black tracking-tight text-[#26221E]/[0.06] leading-none pointer-events-none" style="font-family: 'Playfair Display', Georgia, serif;">
                BASOKU
            </span>
        </div>

        <!-- Baris Bawah: Hak Cipta -->
        <div class="pt-4 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-[#5A534A]/80 border-t border-[#26221E]/10">
            <p>
                &copy; {{ date('Y') }} <strong>BASOKU</strong>. Hak Cipta Dilindungi.
            </p>
            <p>
                Spesialis Bahan Baku &amp; Selepan Bakso Tradisi Pasar
            </p>
        </div>

    </div>
</footer>
