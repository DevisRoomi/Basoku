<section id="daftar-harga" class="bg-motif-kawung text-[#26221E] py-20 lg:py-28 scroll-mt-20 relative overflow-hidden border-t border-[#26221E]/10">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12 relative z-10">

        <!-- Ornamental Botanical Section Header (Kinfolk / Specialty Apothecary Aesthetic) -->
        <div class="text-center max-w-3xl mx-auto mb-16 lg:mb-20">
            <!-- Center Botanical Emblem / Pill -->
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#FAF7F2] border border-[#26221E]/10 shadow-xs mb-4">
                <span class="w-2 h-2 rounded-full bg-[#2D4235]"></span>
                <span class="text-xs font-mono font-medium tracking-[0.2em] uppercase text-[#2D4235]">
                    Katalog Pasar &bull; Update Riil
                </span>
            </div>

            <h2 class="text-4xl lg:text-5xl font-bold leading-tight text-[#26221E] tracking-tight" style="font-family: 'Playfair Display', Georgia, serif;">
                Daftar Harga Transparan
            </h2>

            <!-- Decorative Flourish Divider -->
            <div class="flex items-center justify-center gap-3 my-4">
                <span class="w-16 h-[1px] bg-[#26221E]/20"></span>
                <span class="text-xs text-[#D96B43]">✦</span>
                <span class="w-16 h-[1px] bg-[#26221E]/20"></span>
            </div>

            <p class="text-[#5A534A] text-sm sm:text-base leading-relaxed font-normal">
                Harga bahan baku dan jasa selepan selalu kami perbarui mengikuti harga pasar riil hari ini. Transparan, adil, tanpa biaya tersembunyi.
            </p>
        </div>

        <!-- Grouped Categories Grid -->
        <div class="space-y-16">
            @forelse ($groupedMaterials as $category => $items)
                @php
                    $catLower = strtolower($category);
                    $isDaging = str_contains($catLower, 'daging');
                    $isTepung = str_contains($catLower, 'tepung');
                    $isBumbu = str_contains($catLower, 'bumbu');

                    $categoryMonogram = $isDaging ? 'DG' : ($isTepung ? 'TP' : 'BM');

                    $categoryDesc = $isDaging
                        ? 'Daging sapi paha/gandik segar dan dada ayam fillet bersih siap giling'
                        : ($isTepung
                            ? 'Tepung tapioka kualitas super untuk tekstur kenyal dan renyah sempurna'
                            : 'Bawang putih kupas pilihan, garam kasar, dan rempah aromatik');
                @endphp

                <div class="space-y-6">
                    
                    <!-- Category Header Bar (Pure Editorial Header) -->
                    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3 pb-4 border-b border-[#26221E]/15">
                        <div class="flex items-center gap-3.5">
                            <div class="w-10 h-10 rounded-2xl bg-[#2D4235] text-[#F4EFE6] flex items-center justify-center font-mono font-bold text-xs shadow-xs">
                                {{ $categoryMonogram }}
                            </div>
                            <div>
                                <h3 class="text-2xl sm:text-3xl font-bold tracking-tight text-[#26221E]" style="font-family: 'Playfair Display', Georgia, serif;">
                                    Kategori: {{ ucfirst($category) }}
                                </h3>
                                <p class="text-xs text-[#5A534A] mt-0.5">{{ $categoryDesc }}</p>
                            </div>
                        </div>

                        <span class="text-xs font-mono font-medium px-3.5 py-1.5 rounded-full bg-[#FAF7F2] text-[#2D4235] border border-[#26221E]/10 self-start sm:self-auto">
                            {{ count($items) }} Bahan Baku
                        </span>
                    </div>

                    <!-- Items Grid (Minimalist Apothecary / Specialty Market Cards) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach ($items as $material)
                            @php
                                $words = explode(' ', $material->name);
                                $monogram = '';
                                foreach (array_slice($words, 0, 2) as $w) {
                                    $monogram .= strtoupper(mb_substr($w, 0, 1));
                                }

                                $hasPackage = !empty($material->package_size) && (float) $material->package_size > 0 && !empty($material->package_unit);
                                $isGram = strtolower($material->unit) === 'gram';

                                // Clean Indonesian market price formatting without ',00'
                                if ($isGram) {
                                    if ($isDaging) {
                                        $mainPriceFormatted = 'Rp ' . number_format($material->price * 1000, 0, ',', '.');
                                        $mainUnitLabel = '/ kg';
                                        $baseBadge = 'Rp ' . number_format($material->price, 0, ',', '.') . ' / ' . $material->unit;
                                    } elseif ($isBumbu) {
                                        if ($hasPackage && (float)$material->package_size < 1000) {
                                            $mainPriceFormatted = 'Rp ' . number_format($material->price * $material->package_size, 0, ',', '.');
                                            $mainUnitLabel = '/ bks (' . number_format($material->package_size, 0, ',', '.') . ' ' . $material->package_unit . ')';
                                            $baseBadge = 'Rp ' . number_format($material->price, 0, ',', '.') . ' / ' . $material->unit;
                                        } else {
                                            $mainPriceFormatted = 'Rp ' . number_format($material->price * 100, 0, ',', '.');
                                            $mainUnitLabel = '/ 100 gr';
                                            $baseBadge = 'Rp ' . number_format($material->price, 0, ',', '.') . ' / ' . $material->unit;
                                        }
                                    } elseif ($isTepung) {
                                        if ($hasPackage) {
                                            $mainPriceFormatted = 'Rp ' . number_format($material->price * $material->package_size, 0, ',', '.');
                                            $mainUnitLabel = '/ bks (' . number_format($material->package_size, 0, ',', '.') . ' ' . $material->package_unit . ')';
                                            $baseBadge = 'Rp ' . number_format($material->price, 0, ',', '.') . ' / ' . $material->unit;
                                        } else {
                                            $mainPriceFormatted = 'Rp ' . number_format($material->price * 1000, 0, ',', '.');
                                            $mainUnitLabel = '/ kg';
                                            $baseBadge = 'Rp ' . number_format($material->price, 0, ',', '.') . ' / ' . $material->unit;
                                        }
                                    } else {
                                        $mainPriceFormatted = 'Rp ' . number_format($material->price * 1000, 0, ',', '.');
                                        $mainUnitLabel = '/ kg';
                                        $baseBadge = 'Rp ' . number_format($material->price, 0, ',', '.') . ' / ' . $material->unit;
                                    }
                                } else {
                                    $mainPriceFormatted = 'Rp ' . number_format($material->price, 0, ',', '.');
                                    $mainUnitLabel = '/ ' . $material->unit;
                                    $baseBadge = 'Rp ' . number_format($material->price, 0, ',', '.') . ' / ' . $material->unit;
                                }
                            @endphp

                            <div class="bg-[#FAF7F2] border border-[#26221E]/12 hover:border-[#2D4235]/40 rounded-2xl p-5 flex items-center justify-between gap-4 shadow-xs hover:shadow-md transition-all duration-200 group">
                                
                                <!-- Left: Typographic Monogram Square + Ingredient Specs (No Truncate) -->
                                <div class="flex items-center gap-3.5 min-w-0">
                                    <div class="w-12 h-12 rounded-xl bg-[#EAE3D5] text-[#2D4235] font-serif font-bold text-base flex items-center justify-center shrink-0 border border-[#2D4235]/15 group-hover:bg-[#2D4235] group-hover:text-white transition-colors">
                                        {{ $monogram }}
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="font-bold text-sm sm:text-base text-[#26221E] group-hover:text-[#D96B43] transition-colors leading-snug">
                                            {{ $material->name }}
                                        </h4>
                                        <span class="text-[11px] text-[#5A534A] block mt-0.5 leading-normal">
                                            @if ($hasPackage)
                                                Kemasan: {{ number_format($material->package_size, 0, ',', '.') }} {{ $material->package_unit }}
                                            @else
                                                Harga satuan terverifikasi pasar &bull; Satuan {{ $material->unit }}
                                            @endif
                                        </span>
                                    </div>
                                </div>

                                <!-- Right: Clean Price + Base Unit Badge + Action Button -->
                                <div class="text-right shrink-0 flex items-center gap-3">
                                    <div class="text-right">
                                        <div class="flex items-baseline justify-end gap-1">
                                            <span class="text-sm sm:text-base font-bold text-[#26221E] tabular-nums font-mono">
                                                {{ $mainPriceFormatted }}
                                            </span>
                                            <span class="text-[10px] font-mono text-[#5A534A]">{{ $mainUnitLabel }}</span>
                                        </div>
                                        <span class="inline-block mt-0.5 text-[10px] font-mono text-[#5A534A] bg-[#EAE3D5]/80 px-2 py-0.5 rounded border border-[#26221E]/10">
                                            {{ $baseBadge }}
                                        </span>
                                    </div>

                                    <!-- Circular Sage '+' Button -->
                                    <a
                                        href="#racikan-saya"
                                        class="w-8 h-8 rounded-full bg-[#2D4235] hover:bg-[#D96B43] text-white flex items-center justify-center text-sm font-bold shadow-xs transition-colors shrink-0"
                                        title="Hitung di Kalkulator"
                                        aria-label="Racik {{ $material->name }}"
                                    >
                                        +
                                    </a>
                                </div>

                            </div>
                        @endforeach
                    </div>

                </div>
            @empty
                <div class="rounded-3xl border border-[#26221E]/10 bg-[#FAF7F2] p-12 text-center max-w-md mx-auto">
                    <p class="text-sm text-[#5A534A]">Data bahan baku belum tersedia.</p>
                </div>
            @endforelse
        </div>

        <!-- ═══════════════════════════════════════════════════════════════ -->
        <!-- BOTTOM BANNER: JASA SELEPAN PASAR (Architectural Spec Showcase) -->
        <!-- ═══════════════════════════════════════════════════════════════ -->
        <div class="mt-16 sm:mt-20 rounded-3xl bg-[#D8E0D3] p-8 sm:p-12 border border-[#2D4235]/20 shadow-md relative overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center relative z-10">
                
                <!-- Left Details (7 Kolom) -->
                <div class="lg:col-span-7 space-y-4">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#FAF7F2] text-[#2D4235] text-[11px] font-mono font-bold uppercase tracking-wider">
                        Layanan Los Pasar
                    </div>
                    <h3 class="text-2xl sm:text-4xl font-bold text-[#1F3025] tracking-tight" style="font-family: 'Playfair Display', Georgia, serif;">
                        Jasa Selepan (Penggilingan)
                    </h3>
                    <p class="text-sm sm:text-base text-[#2D4235]/90 max-w-xl leading-relaxed">
                        Mesin selepan higienis dengan pisau giling baja tajam dan mangkok pendingin es batu. Menghasilkan serat adonan bakso yang kalis, kenyal membal, dan mengembang sempurna.
                    </p>

                    <!-- Tariffs & CTA -->
                    <div class="flex flex-wrap items-center gap-4 pt-2">
                        <div class="bg-[#FAF7F2] px-5 py-2.5 rounded-2xl border border-[#26221E]/10 shadow-xs">
                            <span class="text-[10px] uppercase font-mono text-[#5A534A] block">Tarif Tetap</span>
                            <span class="text-lg font-bold font-mono text-[#26221E] tabular-nums">Rp 15.000</span>
                            <span class="text-xs text-[#5A534A]">/ kg daging</span>
                        </div>

                        <a
                            href="#racikan-saya"
                            class="inline-flex items-center gap-2 px-7 py-3.5 rounded-full bg-[#2D4235] hover:bg-[#1F3025] text-white font-semibold text-xs sm:text-sm shadow-md transition-all active:scale-[0.98]"
                        >
                            <span>Gunakan di Kalkulator</span>
                            <svg class="w-4 h-4 text-[#D8E0D3]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Right Technical Spec Grid (5 Kolom: No Fake Photos) -->
                <div class="lg:col-span-5 grid grid-cols-2 gap-3.5">
                    <div class="bg-[#FAF7F2] p-4 rounded-2xl border border-[#26221E]/10">
                        <span class="text-[10px] font-mono uppercase text-[#5A534A] block">Suhu Operasional</span>
                        <span class="font-bold text-[#26221E] text-sm block mt-1">&lt; 15&deg;C (Es Batu)</span>
                        <span class="text-[11px] text-[#5A534A] mt-0.5 block">Cegah pecah lemak</span>
                    </div>
                    <div class="bg-[#FAF7F2] p-4 rounded-2xl border border-[#26221E]/10">
                        <span class="text-[10px] font-mono uppercase text-[#5A534A] block">Batas Mesin</span>
                        <span class="font-bold text-[#26221E] text-sm block mt-1">Min. 500 Gram</span>
                        <span class="text-[11px] text-[#5A534A] mt-0.5 block">Adonan rata gilas</span>
                    </div>
                    <div class="bg-[#FAF7F2] p-4 rounded-2xl border border-[#26221E]/10">
                        <span class="text-[10px] font-mono uppercase text-[#5A534A] block">Tipe Pisau</span>
                        <span class="font-bold text-[#26221E] text-sm block mt-1">Baja Pahat Asah</span>
                        <span class="text-[11px] text-[#5A534A] mt-0.5 block">Urai serat daging</span>
                    </div>
                    <div class="bg-[#FAF7F2] p-4 rounded-2xl border border-[#26221E]/10">
                        <span class="text-[10px] font-mono uppercase text-[#5A534A] block">Higienitas</span>
                        <span class="font-bold text-[#26221E] text-sm block mt-1">Bilas Berkala</span>
                        <span class="text-[11px] text-[#5A534A] mt-0.5 block">Los pasar bersih</span>
                    </div>
                </div>

            </div>
        </div>

    </div>

</section>
