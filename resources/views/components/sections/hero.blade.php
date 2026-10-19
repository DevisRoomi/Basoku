<!-- Hero Section: Pure Kinfolk / Cereal Architectural Editorial Aesthetic -->
<section id="hero" class="relative overflow-hidden bg-[#F4EFE6] pt-8 sm:pt-14 pb-16 lg:pb-20">
    
    <!-- Subtle Clean Background Ambience (No washed-out overlay on text) -->
    <div class="absolute -right-20 -top-20 w-[45vw] max-w-[600px] h-[45vw] max-h-[600px] rounded-full bg-[#D8E0D3]/40 blur-3xl pointer-events-none -z-0"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12 relative z-10">
        
        <!-- 2-Column Asymmetric Hero Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-10 items-center">
            
            <!-- ═══════════════════════════════════════════ -->
            <!-- SISI KIRI: Headline, Subtitle, CTA (6 Kolom) -->
            <!-- ═══════════════════════════════════════════ -->
            <div class="lg:col-span-6 space-y-6 sm:space-y-8">
                
                <!-- Editorial Kicker Pill -->
                <div class="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full bg-[#FAF7F2] border border-[#26221E]/10 shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-[#D96B43]"></span>
                    <span class="text-xs font-mono font-medium tracking-wide text-[#26221E]">
                        Tradisi Pasar Tradisional &bull; Terukur & Presisi
                    </span>
                </div>

                <!-- Main Bold Friendly Serif Headline -->
                <h1 class="text-4xl sm:text-5xl lg:text-[3.4rem] font-serif font-bold text-[#26221E] leading-[1.14] tracking-tight" style="font-family: 'Playfair Display', Georgia, serif;">
                    Racikan bakso yang bikin jatuh cinta sejak <span class="italic font-normal text-[#D96B43]">suapan pertama.</span>
                </h1>

                <!-- Subtitle (Warm, Clear, Honest) -->
                <p class="text-[#5A534A] text-base sm:text-lg leading-relaxed max-w-xl font-normal">
                    Bahan baku bakso pilihan, hitungan takaran daging & tepung yang presisi, serta transparansi biaya belanja pasar dalam satu tempat.
                </p>

                <!-- Primary & Secondary Pill Buttons -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 pt-2">
                    <!-- Primary Terracotta Pill Button -->
                    <a
                        href="#racikan-saya"
                        class="bg-[#D96B43] hover:bg-[#C25932] text-white px-8 py-4 rounded-full font-semibold shadow-md hover:shadow-lg transition-all duration-200 active:scale-[0.98] inline-flex items-center justify-center gap-3 group text-sm sm:text-base"
                    >
                        <span>Mulai Racik Bakso</span>
                        <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </a>

                    <!-- Secondary Oatmeal Pill Button -->
                    <a
                        href="#daftar-harga"
                        class="bg-[#FAF7F2] hover:bg-[#F0EBE1] text-[#26221E] border border-[#26221E]/15 px-7 py-4 rounded-full font-semibold text-sm sm:text-base transition-all duration-200 flex items-center justify-center gap-2 shadow-xs"
                    >
                        <span>Lihat Daftar Harga</span>
                    </a>
                </div>

                <!-- 3 Mini Checkmark Badges -->
                <div class="pt-6 border-t border-[#26221E]/10 grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-6 h-6 rounded-full bg-[#2D4235] text-[#F4EFE6] flex items-center justify-center text-xs font-bold shrink-0">
                            ✓
                        </div>
                        <span class="text-xs font-medium text-[#26221E]">Bahan segar pasar</span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <div class="w-6 h-6 rounded-full bg-[#2D4235] text-[#F4EFE6] flex items-center justify-center text-xs font-bold shrink-0">
                            ✓
                        </div>
                        <span class="text-xs font-medium text-[#26221E]">2 estimasi biaya jujur</span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <div class="w-6 h-6 rounded-full bg-[#2D4235] text-[#F4EFE6] flex items-center justify-center text-xs font-bold shrink-0">
                            ✓
                        </div>
                        <span class="text-xs font-medium text-[#26221E]">Mulai dari 500g daging</span>
                    </div>
                </div>

            </div>

            <!-- ═══════════════════════════════════════════ -->
            <!-- SISI KANAN: Real Video Showcase Frame       -->
            <!-- ═══════════════════════════════════════════ -->
            <div class="lg:col-span-6 relative mt-6 lg:mt-0">
                
                <!-- Main Video Showcase Frame (Clean, Museum-Grade Border, No 3D Emojis) -->
                <div class="relative rounded-[2.25rem] overflow-hidden shadow-2xl border-4 border-[#FAF7F2] aspect-[4/3] sm:aspect-[5/4] bg-[#26221E]">
                    <video
                        id="hero-video"
                        class="w-full h-full object-cover object-center"
                        autoplay
                        muted
                        loop
                        playsinline
                        preload="auto"
                        aria-hidden="true"
                    >
                        <source src="{{ asset('videos/basoku-hero.mp4') }}" type="video/mp4">
                    </video>

                    <!-- Cinematic Vignette Scrim -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-black/10 pointer-events-none"></div>
                </div>

                <!-- Refined Monospaced Architectural Caption Directly Below Video Frame -->
                <div class="mt-3.5 flex items-center justify-between px-2 text-[11px] font-mono text-[#5A534A]">
                    <span class="flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#D96B43]"></span>
                        <span>KOORDINAT KIOS PASAR: 7°59'31.5"S 113°14'58.9"E</span>
                    </span>
                    <a
                        href="https://www.google.com/maps/search/?api=1&query=-7.992083,113.249694"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="text-[10px] uppercase font-bold text-[#2D4235] hover:text-[#D96B43] transition-colors flex items-center gap-1"
                    >
                        <span>Google Maps</span>
                        <span>&rarr;</span>
                    </a>
                </div>

            </div>

        </div>

    </div>

    <!-- ═══════════════════════════════════════════ -->
    <!-- ORGANIC SVG WAVE DIVIDER + 4-ICON STRIP    -->
    <!-- ═══════════════════════════════════════════ -->
    <div class="mt-16 sm:mt-20 w-full relative z-20">
        <!-- Organic Soft Wave SVG Divider -->
        <div class="w-full overflow-hidden leading-none text-[#FAF7F2]">
            <svg class="relative block w-full h-7 sm:h-10" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M0,0 C150,90 400,100 600,40 C800,-20 1050,80 1200,20 L1200,120 L0,120 Z" fill="currentColor"></path>
            </svg>
        </div>

        <!-- 4-Column Feature Strip (Clean, Crisp, Legible, No Gradient Overlays) -->
        <div class="bg-[#FAF7F2] border-b border-[#26221E]/10 py-10 px-4 sm:px-6 lg:px-12 relative z-30">
            <div class="max-w-7xl mx-auto grid grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8">
                
                <!-- Feature 1: Daging & Bahan Pilihan -->
                <div class="flex items-center gap-3.5 group">
                    <div class="w-12 h-12 rounded-2xl bg-[#E8ECE4] text-[#2D4235] flex items-center justify-center shrink-0 border border-[#2D4235]/15 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-.778.099-1.533.284-2.253" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-[#26221E] leading-tight">Daging & Bahan Pilihan</h4>
                        <p class="text-xs text-[#5A534A] mt-0.5">Sapi & ayam segar pasar</p>
                    </div>
                </div>

                <!-- Feature 2: Takaran Resep Presisi -->
                <div class="flex items-center gap-3.5 group">
                    <div class="w-12 h-12 rounded-2xl bg-[#E8ECE4] text-[#2D4235] flex items-center justify-center shrink-0 border border-[#2D4235]/15 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75M18.75 4.97A48.416 48.416 0 0012 4.5c-2.291 0-4.545.16-6.75.47m13.5 0c1.01.143 2.01.317 3 .52m-16.5-.52c-1.01.143-2.01.317-3 .52m16.5 0a48.72 48.72 0 01-16.5 0" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-[#26221E] leading-tight">Takaran Resep Presisi</h4>
                        <p class="text-xs text-[#5A534A] mt-0.5">Akurasi gramasi bumbu & tepung</p>
                    </div>
                </div>

                <!-- Feature 3: Jasa Selepan Higienis -->
                <div class="flex items-center gap-3.5 group">
                    <div class="w-12 h-12 rounded-2xl bg-[#E8ECE4] text-[#2D4235] flex items-center justify-center shrink-0 border border-[#2D4235]/15 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12a7.5 7.5 0 0015 0m-15 0a7.5 7.5 0 1115 0m-15 0H3m16.5 0H21m-1.5 0H12m-8.25 0h1.5m10.5 0h1.5" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-[#26221E] leading-tight">Jasa Selepan Higienis</h4>
                        <p class="text-xs text-[#5A534A] mt-0.5">Mesin giling los pasar bersih</p>
                    </div>
                </div>

                <!-- Feature 4: Pesan Tanpa Antre -->
                <div class="flex items-center gap-3.5 group">
                    <div class="w-12 h-12 rounded-2xl bg-[#E8ECE4] text-[#2D4235] flex items-center justify-center shrink-0 border border-[#2D4235]/15 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 01.778-.332 48.294 48.294 0 005.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v5.018z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-[#26221E] leading-tight">Pesan Tanpa Antre</h4>
                        <p class="text-xs text-[#5A534A] mt-0.5">Kirim ke WhatsApp kios</p>
                    </div>
                </div>

            </div>
        </div>
    </div>

</section>
