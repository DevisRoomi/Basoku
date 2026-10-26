<x-layouts.app title="BASOKU — Spesialis Bahan Baku & Selepan Bakso Pasar Terpercaya">
    <!-- 1. Top Running Marquee & Soft Oatmeal Sticky Navbar (Ref 3 & Ref 1) -->
    <x-navbar />

    <!-- Konten Beranda Utama -->
    <main class="relative w-full text-[#26221E]">
        <!-- 2. Asymmetric Hero Section with Real Video Showcase (Ref 1 Editorial Aesthetic) -->
        <x-sections.hero />

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12">
            
            <!-- Section Divider 1: Architectural Double Rule with Heritage Seal -->
            <div class="my-10 sm:my-14 flex items-center justify-center gap-4 text-[#26221E]/30 motion-draw-line" aria-hidden="true">
                <div class="flex-1 h-[3px] border-t-2 border-b border-[#26221E]/15"></div>
                <div class="flex items-center gap-2 px-3 py-1 rounded-full border border-[#26221E]/15 bg-[#FAF7F2] text-[10px] font-mono tracking-[0.25em] uppercase text-[#D96B43] font-bold shadow-xs">
                    <span>✦</span>
                    <span>STANDAR FORMULA &amp; RASIO</span>
                    <span>✦</span>
                </div>
                <div class="flex-1 h-[3px] border-t-2 border-b border-[#26221E]/15"></div>
            </div>

            <!-- ═══════════════════════════════════════════════════════════════ -->
            <!-- 3. SAGE SHOWCASE BANNER: ARCHITECTURAL RATIO CARDS             -->
            <!-- ═══════════════════════════════════════════════════════════════ -->
            <section class="my-14 sm:my-20 motion-reveal">
                <div class="bg-[#96A690] text-[#26221E] rounded-3xl p-8 sm:p-12 lg:p-14 shadow-lg border border-[#2D4235]/15">
                    
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-center">
                        
                        <!-- Left: Editorial Pitch & CTA (4 Kolom) -->
                        <div class="lg:col-span-4 space-y-5">
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#FAF7F2]/90 backdrop-blur-sm text-[#2D4235] text-[11px] font-bold uppercase tracking-widest font-mono shadow-xs">
                                Formula Teruji Pasar
                            </div>
                            <h2 class="text-3xl sm:text-4xl font-serif font-bold text-[#1F3025] leading-tight" style="font-family: 'Playfair Display', Georgia, serif;">
                                Karakter racikan favorit
                            </h2>
                            <p class="text-[#2D4235]/85 text-sm leading-relaxed">
                                Pilih komposisi adonan sesuai selera keluarga atau target modal usaha Anda. Setiap varian memiliki rasio takaran yang telah teruji lezat.
                            </p>
                            <div class="pt-2">
                                <a
                                    href="#racikan-saya"
                                    class="inline-flex items-center gap-2.5 bg-[#FAF7F2] hover:bg-[#F0EBE1] text-[#26221E] px-7 py-3.5 rounded-full font-semibold text-xs sm:text-sm shadow-md transition-all duration-200 active:scale-[0.98]"
                                >
                                    <span>Coba di Kalkulator</span>
                                    <svg class="w-4 h-4 text-[#D96B43]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                    </svg>
                                </a>
                            </div>
                        </div>

                        <!-- Right: 3 Pure Editorial Architectural Culinary Spec Cards (8 Kolom, Zero Clipping) -->
                        <div class="lg:col-span-8 grid grid-cols-1 sm:grid-cols-3 gap-5">
                            
                            <!-- Spec Card 1: Racikan Enak -->
                            <div class="bg-[#FAF7F2] text-[#26221E] rounded-3xl p-6 sm:p-7 border border-[#26221E]/10 flex flex-col justify-between shadow-xs hover:shadow-md transition-all motion-scale stagger-1">
                                <div>
                                    <div class="flex items-center justify-between mb-4">
                                        <span class="text-xs font-mono font-bold text-[#D96B43]">01 / PREMIER</span>
                                        <span class="w-2.5 h-2.5 rounded-full bg-[#D96B43]"></span>
                                    </div>
                                    <span class="text-3xl font-serif font-bold text-[#26221E] block mb-1" style="font-family: 'Playfair Display', Georgia, serif;">
                                        85% Daging
                                    </span>
                                    <h3 class="text-sm font-bold text-[#2D4235] mb-2">Racikan Enak</h3>
                                    <p class="text-xs text-[#5A534A] leading-relaxed">
                                        Dominan serat daging sapi/ayam murni, tekstur padat berserat, dan kuah kaldu gurih pekat.
                                    </p>
                                </div>
                                <div class="mt-6 pt-3 border-t border-[#26221E]/10 flex items-center justify-between text-[11px] font-mono text-[#5A534A]">
                                    <span>Rasio Daging-Tepung</span>
                                    <span class="font-bold text-[#26221E]">5 : 1</span>
                                </div>
                            </div>

                            <!-- Spec Card 2: Racikan Seimbang -->
                            <div class="bg-[#FAF7F2] text-[#26221E] rounded-3xl p-6 sm:p-7 border border-[#26221E]/10 flex flex-col justify-between shadow-xs hover:shadow-md transition-all motion-scale stagger-2">
                                <div>
                                    <div class="flex items-center justify-between mb-4">
                                        <span class="text-xs font-mono font-bold text-[#E5A93C]">02 / KELUARGA</span>
                                        <span class="w-2.5 h-2.5 rounded-full bg-[#E5A93C]"></span>
                                    </div>
                                    <span class="text-3xl font-serif font-bold text-[#26221E] block mb-1" style="font-family: 'Playfair Display', Georgia, serif;">
                                        Proporsi Pas
                                    </span>
                                    <h3 class="text-sm font-bold text-[#2D4235] mb-2">Racikan Seimbang</h3>
                                    <p class="text-xs text-[#5A534A] leading-relaxed">
                                        Keseimbangan kelembutan daging dan kekenyalan tapioka yang membal sempurna untuk hidangan harian.
                                    </p>
                                </div>
                                <div class="mt-6 pt-3 border-t border-[#26221E]/10 flex items-center justify-between text-[11px] font-mono text-[#5A534A]">
                                    <span>Standar Pasar</span>
                                    <span class="font-bold text-[#26221E]">Favorit</span>
                                </div>
                            </div>

                            <!-- Spec Card 3: Racikan Ekonomis -->
                            <div class="bg-[#FAF7F2] text-[#26221E] rounded-3xl p-6 sm:p-7 border border-[#26221E]/10 flex flex-col justify-between shadow-xs hover:shadow-md transition-all motion-scale stagger-3">
                                <div>
                                    <div class="flex items-center justify-between mb-4">
                                        <span class="text-xs font-mono font-bold text-[#2D4235]">03 / USAHA</span>
                                        <span class="w-2.5 h-2.5 rounded-full bg-[#2D4235]"></span>
                                    </div>
                                    <span class="text-3xl font-serif font-bold text-[#26221E] block mb-1" style="font-family: 'Playfair Display', Georgia, serif;">
                                        Efisiensi
                                    </span>
                                    <h3 class="text-sm font-bold text-[#2D4235] mb-2">Racikan Ekonomis</h3>
                                    <p class="text-xs text-[#5A534A] leading-relaxed">
                                        Komposisi tepung terukur untuk volume adonan maksimal dan porsi melimpah bagi pedagang kuliner.
                                    </p>
                                </div>
                                <div class="mt-6 pt-3 border-t border-[#26221E]/10 flex items-center justify-between text-[11px] font-mono text-[#5A534A]">
                                    <span>Target Modal</span>
                                    <span class="font-bold text-[#26221E]">Hemat</span>
                                </div>
                            </div>

                        </div>

                    </div>

                </div>
            </section>

            <!-- Section Divider 2: Artisanal Stitched Dashed Rule with Stepper Stamp -->
            <div class="my-12 sm:my-16 flex items-center justify-between gap-4" aria-hidden="true">
                <div class="flex-1 border-t-2 border-dashed border-[#26221E]/20"></div>
                <div class="flex items-center gap-3 px-4 py-1.5 rounded-full bg-[#FAF7F2] border border-[#26221E]/15 text-xs font-mono text-[#5A534A] tracking-widest uppercase shadow-xs">
                    <span class="w-2 h-2 rotate-45 bg-[#D96B43]"></span>
                    <span class="text-[#26221E] font-bold">FASE 02 &bull; TATA CARA &bull; LOS SELEPAN</span>
                    <span class="w-2 h-2 rotate-45 bg-[#2D4235]"></span>
                </div>
                <div class="flex-1 border-t-2 border-dashed border-[#26221E]/20"></div>
            </div>

            <!-- ═══════════════════════════════════════════════════════════════ -->
            <!-- 4. ALUR CARA KERJA: PURE EDITORIAL INDEX CARDS (Ref 3 & Kinfolk)-->
            <!-- ═══════════════════════════════════════════════════════════════ -->
            <section id="cara-kerja" class="my-16 sm:my-24 scroll-mt-20 relative motion-reveal">
                
                <!-- Deep Culinary Maroon Editorial Philosophy Banner (Ref 3) -->
                <div class="bg-[#8B231A] text-[#F4EFE6] py-14 sm:py-16 px-6 sm:px-12 rounded-3xl text-center relative overflow-hidden shadow-xl mb-12">
                    <div class="max-w-2xl mx-auto space-y-4 relative z-10">
                        <span class="text-xs uppercase font-mono tracking-[0.25em] text-[#E5A93C] font-semibold block">
                            TRANSPARANSI BELANJA KELUARGA
                        </span>
                        <h2 class="text-2xl sm:text-4xl font-serif font-normal leading-tight text-white tracking-tight" style="font-family: 'Playfair Display', Georgia, serif;">
                            &ldquo;Meracik bakso enak kadang terasa rumit, tapi belanja bahan & menghitung anggarannya harusnya semudah ini.&rdquo;
                        </h2>
                        <p class="text-xs sm:text-sm text-[#F4EFE6]/80 max-w-lg mx-auto font-normal">
                            Hitung bahan dari rumah secara tenang, ketahui nilai riil adonan, lalu pesan langsung ke los selepan pasar tanpa bingung saat di depan timbangan.
                        </p>
                    </div>
                </div>

                <!-- 3 Minimalist Editorial Step Index Cards (No Fake Stock Photos) -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8 relative z-10">
                    
                    <!-- STEP 1 Card -->
                    <div class="bg-[#FAF7F2] border border-[#26221E]/12 rounded-3xl p-8 shadow-xs flex flex-col justify-between group relative motion-reveal stagger-1">
                        <span class="absolute top-4 right-4 text-xs font-mono text-[#26221E]/20 select-none">+</span>
                        <div>
                            <div class="flex items-center justify-between mb-6">
                                <span class="bg-[#2D4235] text-[#F4EFE6] px-3.5 py-1 rounded-full text-xs font-mono font-bold tracking-wider">
                                    STEP 1
                                </span>
                                <span class="text-5xl font-serif italic text-[#D96B43]/30 select-none">
                                    01
                                </span>
                            </div>
                            <h3 class="text-xl font-bold text-[#26221E] leading-snug mb-3" style="font-family: 'Playfair Display', Georgia, serif;">
                                Pilih Mode & Bahan
                            </h3>
                            <div class="w-10 h-[1px] bg-[#26221E]/20 mb-4"></div>
                            <p class="text-xs sm:text-sm text-[#5A534A] leading-relaxed">
                                Tentukan apakah ingin membeli daging sapi/ayam segar di kios kami atau membawa daging sendiri dari rumah. Pilih karakter rasa: Enak, Seimbang, atau Ekonomis.
                            </p>
                        </div>
                        <div class="mt-8 pt-4 border-t border-[#26221E]/10 text-[11px] font-mono text-[#D96B43] font-semibold flex items-center justify-between">
                            <span>Formula Tersedia</span>
                            <span>Sapi & Ayam</span>
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
                                <span class="text-5xl font-serif italic text-[#D96B43]/30 select-none">
                                    02
                                </span>
                            </div>
                            <h3 class="text-xl font-bold text-[#26221E] leading-snug mb-3" style="font-family: 'Playfair Display', Georgia, serif;">
                                Hitung Berat / Anggaran
                            </h3>
                            <div class="w-10 h-[1px] bg-[#26221E]/20 mb-4"></div>
                            <p class="text-xs sm:text-sm text-[#5A534A] leading-relaxed">
                                Masukkan berat daging yang ingin diolah (mulai 500g) atau kunci batas anggaran belanja Anda. Sistem menghitung tepung tapioka, bawang putih, garam, dan ongkos selepan.
                            </p>
                        </div>
                        <div class="mt-8 pt-4 border-t border-[#26221E]/10 text-[11px] font-mono text-[#D96B43] font-semibold flex items-center justify-between">
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
                                <span class="text-5xl font-serif italic text-[#D96B43]/30 select-none">
                                    03
                                </span>
                            </div>
                            <h3 class="text-xl font-bold text-[#26221E] leading-snug mb-3" style="font-family: 'Playfair Display', Georgia, serif;">
                                Simpan atau Pesan WA
                            </h3>
                            <div class="w-10 h-[1px] bg-[#26221E]/20 mb-4"></div>
                            <p class="text-xs sm:text-sm text-[#5A534A] leading-relaxed">
                                Simpan racikan ke laboratorium resep pribadi Anda atau kirim format rincian ke WhatsApp toko agar bahan disiapkan lebih dulu sebelum Anda tiba di los selepan.
                            </p>
                        </div>
                        <div class="mt-8 pt-4 border-t border-[#26221E]/10 text-[11px] font-mono text-[#D96B43] font-semibold flex items-center justify-between">
                            <span>Bebas Antre di Pasar</span>
                            <span>Siap Ambil</span>
                        </div>
                    </div>

                </div>

            </section>

            <!-- Section Divider 3: Vintage Archival Diamond Heritage Bar -->
            <div class="my-12 sm:my-16 motion-draw-line" aria-hidden="true">
                <div class="relative flex items-center justify-center">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-[#26221E]/20"></div>
                    </div>
                    <div class="relative bg-[#FAF7F2] border border-[#26221E]/15 px-5 py-1.5 rounded-full flex items-center gap-3 text-xs font-mono text-[#5A534A] shadow-xs">
                        <span class="text-[#D96B43] text-sm">&diams;</span>
                        <span class="tracking-[0.2em] uppercase font-bold text-[#26221E]">ARSIP &bull; AKULTURASI &bull; MIOSIN</span>
                        <span class="text-[#2D4235] text-sm">&diams;</span>
                    </div>
                </div>
            </div>

            <!-- ═══════════════════════════════════════════════════════════════ -->
            <!-- 5. HOMEPAGE EDITORIAL MAGAZINE SPREAD: JEJAK & SEJARAH BAKSO   -->
            <!-- ═══════════════════════════════════════════════════════════════ -->
            <section class="my-16 sm:my-24 relative motion-reveal">
                <div class="bg-[#FAF7F2] border border-[#26221E]/15 rounded-3xl p-8 sm:p-12 lg:p-16 shadow-md relative overflow-hidden">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-center relative z-10">
                        
                        <!-- Left: Curated Authentic Bakso Photo Frame with Archival Museum Badge -->
                        <div class="lg:col-span-5 relative group motion-scale">
                            <div class="relative rounded-3xl overflow-hidden border-2 border-[#26221E]/15 shadow-md bg-[#26221E] aspect-[4/3] sm:aspect-square">
                                <img
                                    src="{{ asset('images/curated/bakso-asli.jpg') }}"
                                    alt="Dokumentasi Semangkuk Bakso Nusantara Asli"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                                    loading="lazy"
                                >
                                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent pointer-events-none"></div>
                                
                                <!-- Museum Metadata Card -->
                                <div class="absolute bottom-4 left-4 right-4 text-white">
                                    <div class="flex items-center gap-2 mb-1.5">
                                        <span class="text-[9px] font-mono tracking-widest uppercase bg-[#8B231A] text-white px-2 py-0.5 rounded font-bold">
                                            DOKUMEN FIG. 01
                                        </span>
                                        <span class="text-[10px] font-mono text-white/70">ARSIP KULINER NUSANTARA</span>
                                    </div>
                                    <p class="text-xs font-serif italic text-white/95 leading-snug">
                                        &ldquo;Pentol bakso urat dan kaldu sapi bening rempah &mdash; harmoni tradisi Fuzhou dan los pasar Jawa.&rdquo;
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Right: Editorial Article Pitch & Chapter TOC (7 Kolom) -->
                        <div class="lg:col-span-7 space-y-5">
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#2D4235] text-[#F4EFE6] text-[11px] font-mono font-bold uppercase tracking-wider">
                                ARSIP KULINER &amp; DOKUMENTASI
                            </div>
                            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-serif font-bold text-[#26221E] tracking-tight leading-[1.12]" style="font-family: 'Playfair Display', Georgia, serif;">
                                Jejak & Dokumentasi Bakso Nusantara
                            </h2>
                            <p class="text-sm sm:text-base text-[#5A534A] leading-relaxed font-normal">
                                Dari kisah bakti Meng Bo di Fuzhou abad ke-17 hingga sains ekstraksi miosin di los selepan pasar tradisional Jawa. Telusuri anatomi biokimia adonan dan dokumentasi logika kalkulasi BASOKU secara lengkap.
                            </p>

                            <!-- 4 Chapter Quick Pills -->
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-1 pb-2">
                                <div class="bg-[#EAE3D5]/70 p-2.5 rounded-xl border border-[#26221E]/10 text-center">
                                    <span class="text-[10px] font-mono font-bold text-[#D96B43] block">BAB 01</span>
                                    <span class="text-[11px] font-bold text-[#26221E] block truncate">Meng Bo</span>
                                </div>
                                <div class="bg-[#EAE3D5]/70 p-2.5 rounded-xl border border-[#26221E]/10 text-center">
                                    <span class="text-[10px] font-mono font-bold text-[#D96B43] block">BAB 02</span>
                                    <span class="text-[11px] font-bold text-[#26221E] block truncate">Akulturasi</span>
                                </div>
                                <div class="bg-[#EAE3D5]/70 p-2.5 rounded-xl border border-[#26221E]/10 text-center">
                                    <span class="text-[10px] font-mono font-bold text-[#D96B43] block">BAB 03</span>
                                    <span class="text-[11px] font-bold text-[#26221E] block truncate">Sains Miosin</span>
                                </div>
                                <div class="bg-[#EAE3D5]/70 p-2.5 rounded-xl border border-[#26221E]/10 text-center">
                                    <span class="text-[10px] font-mono font-bold text-[#D96B43] block">BAB 04</span>
                                    <span class="text-[11px] font-bold text-[#26221E] block truncate">Logika BASOKU</span>
                                </div>
                            </div>

                            <div class="pt-2 flex items-center gap-4">
                                <a
                                    href="{{ route('bakso.history') }}"
                                    class="inline-flex items-center gap-3 px-8 py-3.5 rounded-full bg-[#2D4235] hover:bg-[#1F3025] text-white font-semibold text-xs sm:text-sm shadow-md transition-all active:scale-[0.98]"
                                >
                                    <span>Baca Dokumentasi Lengkap</span>
                                    <svg class="w-4 h-4 text-[#D8E0D3]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                    </svg>
                                </a>
                            </div>
                        </div>

                    </div>

                </div>
            </section>

            <!-- Section Divider 4: Precision Measurement Ticks (Workbench Blueprint Style) -->
            <div class="my-12 sm:my-16 motion-draw-line" aria-hidden="true">
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

            <!-- ═══════════════════════════════════════════════════════════════ -->
            <!-- 6. KALKULATOR RACIKAN INTERAKTIF                               -->
            <!-- ═══════════════════════════════════════════════════════════════ -->
            <section id="racikan-saya" class="my-16 sm:my-24 scroll-mt-20 motion-reveal">
                <livewire:recipe-calculator />
            </section>

        </div>

        <!-- Section Divider 5: Dotted Running Market Ledger Rule -->
        <div class="my-12 sm:my-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-12 flex items-center justify-center gap-6 motion-draw-line" aria-hidden="true">
            <div class="flex-1 border-b-2 border-dotted border-[#26221E]/25"></div>
            <div class="flex items-center gap-2.5 px-4 py-1.5 rounded-full bg-[#FAF7F2] border border-[#26221E]/15 text-xs font-mono tracking-widest text-[#5A534A] shadow-xs">
                <span class="text-[#D96B43]">&#10022;</span>
                <span class="font-bold text-[#26221E] uppercase">LOS HARGA PASAR AKTIF</span>
                <span class="text-[#2D4235]">&#10022;</span>
            </div>
            <div class="flex-1 border-b-2 border-dotted border-[#26221E]/25"></div>
        </div>

        <!-- ═══════════════════════════════════════════════════════════════ -->
        <!-- 7. KATALOG BAHAN BAKU & DAFTAR HARGA PASAR                     -->
        <!-- ═══════════════════════════════════════════════════════════════ -->
        <div class="motion-reveal">
            <livewire:material-catalog />
        </div>
    </main>

    <!-- 8. Footer Publik Informatif -->
    <x-footer />
</x-layouts.app>
