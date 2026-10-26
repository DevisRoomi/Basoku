<!-- ═══════════════════════════════════════════════════════════════════ -->
<!-- FULL-BLEED CINEMATIC VIDEO HERO SECTION (Kinfolk / Cereal Editorial)  -->
<!-- ═══════════════════════════════════════════════════════════════════ -->
<section id="hero" class="relative w-full overflow-hidden bg-[#1F1C18] text-white">

    <!-- Edge-to-Edge Cinematic Video Container (Layar Awal Penuh Kiri-Kanan) -->
    <div class="relative w-full min-h-[82vh] lg:min-h-[88vh] flex items-center justify-center overflow-hidden">
        
        <!-- Video Latar Penuh (Full-bleed Cover) -->
        <video
            id="hero-video"
            class="absolute inset-0 w-full h-full object-cover object-center scale-[1.02] filter brightness-[0.88] contrast-[1.05]"
            autoplay
            muted
            loop
            playsinline
            preload="auto"
            aria-hidden="true"
        >
            <source src="{{ asset('videos/basoku-hero.mp4') }}" type="video/mp4">
        </video>

        <!-- Dark Cinematic Editorial Scrim (Kontras Sempurna untuk Tipografi) -->
        <div class="absolute inset-0 bg-gradient-to-t from-[#1F1C18] via-[#1F1C18]/60 to-[#1F1C18]/45 pointer-events-none"></div>
        <div class="absolute inset-0 bg-radial-at-c from-transparent via-[#1F1C18]/30 to-[#1F1C18]/80 pointer-events-none"></div>

        <!-- Ambient Subtle Gradient Drift (CSS-only warm terracotta/maroon drift) -->
        <div class="hero-ambient-drift" aria-hidden="true"></div>

        <!-- Tipografi Minimalis, Elegan & Profesional di Atas Video (Anti-AI-Slop, Quiet Luxury) -->
        <div id="hero-content-cluster" class="relative z-10 max-w-3xl mx-auto px-4 sm:px-6 text-center py-16 sm:py-20 will-change-transform">
            
            <!-- Understated Kicker Monospace with Masked Reveal -->
            <div class="hero-mask-wrapper mb-4">
                <p id="hero-kicker" class="hero-kicker-anim text-[11px] font-mono tracking-[0.28em] text-[#E5A93C] uppercase">
                    PASAR TRADISIONAL &bull; SELEPAN BAKSO
                </p>
            </div>

            <!-- Headline Serif Bersahaja & Presisi (Masked Vertical Reveal) -->
            <h1 class="text-2xl sm:text-4xl lg:text-5xl font-serif font-normal text-white tracking-tight leading-tight space-y-1 sm:space-y-2" style="font-family: 'Playfair Display', Georgia, serif;">
                <div class="hero-mask-wrapper">
                    <span class="hero-masked-line">
                        Kejujuran Takaran,
                    </span>
                </div>
                <div class="hero-mask-wrapper">
                    <span class="hero-masked-line italic text-[#E5A93C]">
                        Kemurnian Rasa.
                    </span>
                </div>
            </h1>

            <!-- Subtitle 1 Kalimat Bersih with Masked Reveal -->
            <div class="hero-mask-wrapper mt-4">
                <p id="hero-desc" class="hero-desc-anim text-xs sm:text-sm text-white/80 max-w-lg mx-auto font-normal leading-relaxed">
                    Kalkulasi presisi daging, tepung, dan bumbu pasar. Belanja terukur kemasan utuh, bebas antre di los selepan.
                </p>
            </div>

            <!-- Aksi Bersih & Minimalis dengan CTA Magnetic Microinteraction -->
            <div id="hero-cta-group" class="hero-cta-anim mt-7 flex items-center justify-center gap-4">
                <a
                    id="hero-cta-btn"
                    href="#racikan-saya"
                    class="btn-magnetic px-6 py-2.5 rounded-full bg-[#D96B43] hover:bg-[#C25932] text-white font-medium text-xs shadow-md active:scale-[0.98] inline-flex items-center gap-2 group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#D96B43] focus-visible:ring-offset-2 focus-visible:ring-offset-[#1F1C18]"
                >
                    <span>Mulai Racik</span>
                    <svg class="w-3.5 h-3.5 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </a>
                <a
                    href="#daftar-harga"
                    class="text-xs text-white/80 hover:text-white font-mono underline underline-offset-4 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#D96B43] focus-visible:ring-offset-2 focus-visible:ring-offset-[#1F1C18] rounded-xs"
                >
                    Daftar Harga &rarr;
                </a>
            </div>

        </div>

        <!-- Floating Bottom Minimal Bar -->
        <div class="absolute bottom-4 left-0 right-0 max-w-6xl mx-auto px-4 sm:px-6 flex items-center justify-between text-[11px] font-mono text-white/60 border-t border-white/10 pt-3 z-10">
            <span>Los Pasar &bull; Min. 500g Daging</span>
            <a
                href="https://www.google.com/maps/search/?api=1&query=-7.992083,113.249694"
                target="_blank"
                rel="noopener noreferrer"
                class="hover:text-white transition-colors"
            >
                7°59'31.5"S 113°14'58.9"E &rarr;
            </a>
        </div>

    </div>

    <!-- ═══════════════════════════════════════════ -->
    <!-- 4-FEATURE STRIP (Transisi Elegan ke Konten)  -->
    <!-- ═══════════════════════════════════════════ -->
    <div class="bg-[#FAF7F2] text-[#26221E] border-b border-[#26221E]/10 py-10 px-4 sm:px-6 lg:px-12 relative z-20">
            <div class="max-w-7xl mx-auto grid grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8">
                
                <!-- Feature 1: Daging & Bahan Pilihan -->
                <div class="flex items-center gap-3.5 group motion-reveal stagger-1">
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
                <div class="flex items-center gap-3.5 group motion-reveal stagger-2">
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
                <div class="flex items-center gap-3.5 group motion-reveal stagger-3">
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
                <div class="flex items-center gap-3.5 group motion-reveal stagger-4">
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
