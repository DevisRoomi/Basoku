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
            <div class="flex items-center justify-center gap-3 my-4" aria-hidden="true">
                <span class="w-16 h-[1px] bg-[#26221E]/20"></span>
                <span class="text-xs text-[#B84E26]">✦</span>
                <span class="w-16 h-[1px] bg-[#26221E]/20"></span>
            </div>

            <p class="text-[#5A534A] text-sm sm:text-base leading-relaxed font-normal">
                Pilih bahan langsung untuk pesan via WhatsApp atau teruskan ke Kalkulator Racikan untuk hitung takaran bakso pas.
            </p>

            <!-- Search Bar & Category Filter Pills -->
            <div class="mt-8 space-y-4">
                <div class="relative max-w-md mx-auto">
                    <label for="catalog-search-input" class="sr-only">Cari bahan baku pasar</label>
                    <input
                        id="catalog-search-input"
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari bahan (misal: Sapi, Tapioka, Sobaso)..."
                        class="w-full px-5 py-3 pl-11 rounded-2xl bg-[#FAF7F2] border border-[#26221E]/15 text-sm text-[#26221E] placeholder-[#5A534A]/60 focus:outline-hidden focus:border-[#2D4235] focus:ring-1 focus:ring-[#2D4235] shadow-xs font-medium"
                    />
                    <svg class="w-5 h-5 absolute left-3.5 top-3.5 text-[#5A534A]/70" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    @if (!empty($search))
                        <button
                            type="button"
                            wire:click="$set('search', '')"
                            aria-label="Hapus pencarian bahan"
                            class="absolute right-3.5 top-3 text-xs text-[#5A534A] hover:text-[#26221E] px-2 py-1 rounded bg-[#EAE3D5] cursor-pointer"
                        >
                            ✕
                        </button>
                    @endif
                </div>

                <!-- Category Filters -->
                <div class="flex flex-wrap items-center justify-center gap-2 pt-1">
                    @php
                        $categories = [
                            'all' => 'Semua Bahan',
                            'daging' => 'Daging',
                            'tepung' => 'Tepung',
                            'bumbu' => 'Bumbu Utama',
                            'pelengkap' => 'Pelengkap & Rempah',
                        ];
                    @endphp
                    @foreach ($categories as $catKey => $catLabel)
                        <button
                            type="button"
                            wire:click="$set('activeCategory', '{{ $catKey }}')"
                            class="px-4 py-1.5 rounded-full text-xs font-semibold font-mono transition-all {{ $activeCategory === $catKey ? 'bg-[#2D4235] text-white shadow-xs' : 'bg-[#FAF7F2] text-[#5A534A] hover:text-[#26221E] border border-[#26221E]/10' }}"
                        >
                            {{ $catLabel }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Grouped Categories Grid -->
        <div class="space-y-16">
            @forelse ($groupedMaterials as $category => $items)
                @php
                    $catLower = strtolower($category);
                    $categoryMeta = match (true) {
                        str_contains($catLower, 'daging') => [
                            'monogram' => 'DG',
                            'title' => 'Daging',
                            'desc' => 'Daging sapi paha/gandik segar dan dada ayam fillet bersih siap giling',
                        ],
                        str_contains($catLower, 'tepung') => [
                            'monogram' => 'TP',
                            'title' => 'Tepung',
                            'desc' => 'Tepung tapioka, premiks pengenyal, terigu, dan sagu untuk tekstur adonan pas',
                        ],
                        str_contains($catLower, 'bumbu') => [
                            'monogram' => 'BM',
                            'title' => 'Bumbu',
                            'desc' => 'Bawang putih kupas pilihan, garam kasar, bawang goreng, dan rempah aromatik',
                        ],
                        $catLower === 'pelengkap' => [
                            'monogram' => 'PL',
                            'title' => 'Pelengkap & Tambahan',
                            'desc' => 'Telur pengikat adonan, pengenyal bakso, dan penyedap rasa pasar',
                        ],
                        default => [
                            'monogram' => strtoupper(mb_substr($catLower, 0, 2)),
                            'title' => ucfirst($category),
                            'desc' => 'Bahan baku pendukung adonan bakso pilihan pasar',
                        ],
                    };
                @endphp

                <div wire:key="catalog-group-{{ $catLower }}" class="space-y-6">
                    
                    <!-- Category Header Bar (Pure Editorial Header) -->
                    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3 pb-4 border-b border-[#26221E]/15">
                        <div class="flex items-center gap-3.5">
                            <div class="w-10 h-10 rounded-2xl bg-[#2D4235] text-[#F4EFE6] flex items-center justify-center font-mono font-bold text-xs shadow-xs shrink-0">
                                {{ $categoryMeta['monogram'] }}
                            </div>
                            <div>
                                <h3 class="text-2xl sm:text-3xl font-bold tracking-tight text-[#26221E]" style="font-family: 'Playfair Display', Georgia, serif;">
                                    Kategori: {{ $categoryMeta['title'] }}
                                </h3>
                                <p class="text-xs text-[#5A534A] mt-0.5">{{ $categoryMeta['desc'] }}</p>
                            </div>
                        </div>

                        <span class="text-xs font-mono font-medium px-3.5 py-1.5 rounded-full bg-[#FAF7F2] text-[#2D4235] border border-[#26221E]/10 self-start sm:self-auto shrink-0">
                            {{ count($items) }} Bahan Baku
                        </span>
                    </div>

                    <!-- Items Grid (Modern Vertical-Split Market Cards) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach ($items as $material)
                            @php
                                $words = explode(' ', $material->name);
                                $monogram = '';
                                foreach (array_slice($words, 0, 2) as $w) {
                                    $monogram .= strtoupper(mb_substr($w, 0, 1));
                                }

                                $hasPackage = !empty($material->package_weight) && (float) $material->package_weight > 0 && !empty($material->package_unit);
                                $packagePrice = $material->effectivePackagePrice();

                                // Format harga utama kemasan pasar (retail)
                                $mainPriceFormatted = 'Rp ' . number_format($packagePrice, 0, ',', '.');

                                $pkgUnitLower = strtolower($material->effectivePackageUnit());
                                if ($pkgUnitLower === 'kg') {
                                    $mainUnitLabel = '/ kg';
                                } elseif (in_array($pkgUnitLower, ['pack', 'bks', 'bungkus'])) {
                                    $mainUnitLabel = '/ pack (' . number_format($material->effectivePackageWeight(), 0, ',', '.') . ' g)';
                                } elseif ($pkgUnitLower === 'sachet') {
                                    $mainUnitLabel = '/ sachet (' . number_format($material->effectivePackageWeight(), 0, ',', '.') . ' g)';
                                } elseif ($pkgUnitLower === 'butir') {
                                    $mainUnitLabel = '/ butir';
                                } else {
                                    $mainUnitLabel = '/ ' . $material->effectivePackageUnit();
                                }

                                // Badge harga dasar per gram atau per butir/sachet sesuai mode jual
                                if ($material->isUnitBased() || $pkgUnitLower === 'butir' || $pkgUnitLower === 'sachet') {
                                    $baseBadge = 'Rp ' . number_format($packagePrice, 0, ',', '.') . ' / ' . $pkgUnitLower;
                                } else {
                                    $baseBadge = 'Rp ' . number_format($material->effectivePricePerGram(), 0, ',', '.') . ' / g';
                                }

                                $sellingModeBadgeClass = match ($material->selling_mode ?? 'kemasan') {
                                    'curah' => 'bg-sky-50 text-sky-900 border-sky-200',
                                    'satuan' => 'bg-violet-50 text-violet-900 border-violet-200',
                                    default => 'bg-amber-50 text-amber-900 border-amber-200',
                                };

                                $currentQty = $selectedItems[$material->id] ?? 0;
                            @endphp

                            <div wire:key="material-card-{{ $material->id }}" class="bg-[#FAF7F2] border border-[#26221E]/12 hover:border-[#2D4235]/40 rounded-2xl p-5 flex flex-col justify-between gap-4 shadow-xs hover:shadow-md transition-all duration-200 group">
                                <!-- Top & Middle: Monogram, Full-Width Title, Selling Mode Pill, Clean Spec -->
                                <div class="space-y-3">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="w-10 h-10 rounded-xl bg-[#EAE3D5] text-[#2D4235] font-serif font-bold text-sm flex items-center justify-center shrink-0 border border-[#2D4235]/15 group-hover:bg-[#2D4235] group-hover:text-white transition-colors">
                                                {{ $monogram }}
                                            </div>
                                            <h4 class="font-bold text-[15px] text-[#26221E] group-hover:text-[#B84E26] transition-colors leading-snug">
                                                {{ $material->name }}
                                            </h4>
                                        </div>

                                        <span class="shrink-0 px-2 py-0.5 rounded-md border text-[9.5px] font-mono font-bold uppercase tracking-wider {{ $sellingModeBadgeClass }}">
                                            {{ $material->sellingModeLabel() }}
                                        </span>
                                    </div>

                                    <p class="text-xs text-[#5A534A] leading-relaxed">
                                        @if ($hasPackage)
                                            @if ($material->isLoose())
                                                Acuan {{ number_format($material->package_weight, 0, ',', '.') }}g &bull; bisa timbang pas di kalkulator
                                            @else
                                                Kemasan fisik retail: {{ number_format($material->package_weight, 0, ',', '.') }} {{ $pkgUnitLower === 'butir' ? 'gram / butir' : ($pkgUnitLower === 'kg' ? 'gram (1 kg)' : 'gram') }}
                                            @endif
                                        @else
                                            Harga satuan terverifikasi pasar &bull; Satuan {{ $material->unit }}
                                        @endif
                                    </p>
                                </div>

                                <!-- Bottom Footer: Price Info Left + Interactive Action Button Right -->
                                <div class="pt-3.5 border-t border-[#26221E]/10 flex items-end justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="flex items-baseline flex-wrap gap-x-1.5">
                                            <span class="text-base sm:text-lg font-bold text-[#26221E] tabular-nums font-mono">
                                                {{ $mainPriceFormatted }}
                                            </span>
                                            <span class="text-[11px] font-mono text-[#5A534A]">{{ $mainUnitLabel }}</span>
                                        </div>
                                        <span class="inline-block mt-1 text-[10px] font-mono text-[#5A534A] bg-[#EAE3D5]/80 px-2 py-0.5 rounded border border-[#26221E]/10">
                                            {{ $baseBadge }}
                                        </span>
                                    </div>

                                    <!-- Interactive Quantity Stepper / Add Button -->
                                    @if ($currentQty > 0)
                                        <div class="flex items-center gap-1 bg-white border border-[#2D4235] rounded-full p-0.5 shadow-xs shrink-0">
                                            <button
                                                type="button"
                                                wire:click="decrementItem({{ $material->id }})"
                                                class="w-9 h-9 rounded-full bg-[#EAE3D5] hover:bg-[#D8E0D3] text-[#26221E] flex items-center justify-center font-bold text-sm transition-colors cursor-pointer"
                                                title="Kurangi 1"
                                                aria-label="Kurangi {{ $material->name }}"
                                            >
                                                -
                                            </button>
                                            <span class="font-mono font-bold text-xs px-2 text-[#2D4235] tabular-nums">
                                                {{ $currentQty }}
                                            </span>
                                            <button
                                                type="button"
                                                wire:click="incrementItem({{ $material->id }})"
                                                class="w-9 h-9 rounded-full bg-[#2D4235] hover:bg-[#1F3025] text-white flex items-center justify-center font-bold text-sm transition-colors cursor-pointer"
                                                title="Tambah 1"
                                                aria-label="Tambah {{ $material->name }}"
                                            >
                                                +
                                            </button>
                                        </div>
                                    @else
                                        <button
                                            type="button"
                                            wire:click="incrementItem({{ $material->id }})"
                                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-full bg-[#2D4235] hover:bg-[#B84E26] text-white text-xs font-semibold shadow-xs transition-all active:scale-95 shrink-0 cursor-pointer"
                                            title="Pilih Bahan"
                                            aria-label="Pilih {{ $material->name }}"
                                        >
                                            <span class="font-bold text-sm leading-none">+</span>
                                            <span>Pilih</span>
                                        </button>
                                    @endif
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
                            <span class="text-lg font-bold font-mono text-[#26221E] tabular-nums">Rp {{ number_format((float) config('services.kiosk.grinding_fee_per_kg', 15000), 0, ',', '.') }}</span>
                            <span class="text-xs text-[#5A534A]">/ kg daging</span>
                        </div>

                        <a
                            href="{{ request()->is('/') ? '#racikan-saya' : route('calculator') }}"
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

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- FLOATING BOTTOM CART TRAY (Multi-item Picker Feedback)        -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    @if ($totalSelectedPacks > 0)
        <aside
            aria-label="Keranjang Belanja Bahan"
            class="fixed bottom-20 sm:bottom-24 lg:bottom-5 inset-x-4 sm:inset-x-6 lg:inset-x-auto lg:left-1/2 lg:-translate-x-1/2 md:max-w-xl md:mx-auto lg:mx-0 lg:w-auto lg:max-w-xl z-40 bg-[#26221E] text-[#F4EFE6] px-5 py-4 rounded-3xl shadow-2xl border border-[#D96B43]/30 flex flex-col sm:flex-row items-center justify-between gap-4 transition-all duration-200"
        >
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <div class="w-10 h-10 rounded-2xl bg-[#B84E26] text-white flex items-center justify-center font-bold text-base shrink-0 shadow-xs">
                    {{ $totalSelectedPacks }}
                </div>
                <div>
                    <div class="text-xs text-[#EAE3D5]/85">Bahan Terpilih di Katalog</div>
                    <div class="text-sm sm:text-base font-bold font-mono text-white tabular-nums">
                        Rp {{ number_format($totalSelectedCost, 0, ',', '.') }}
                    </div>
                </div>
                <button
                    type="button"
                    wire:click="clearCart"
                    class="ml-auto sm:ml-2 text-xs text-[#EAE3D5]/75 hover:text-white underline underline-offset-2 cursor-pointer"
                    title="Kosongkan pilihan"
                >
                    Reset
                </button>
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                <!-- Option A: Transfer to Calculator -->
                <button
                    type="button"
                    wire:click="transferToCalculator"
                    class="flex-1 sm:flex-none px-4 py-2.5 rounded-full bg-[#FAF7F2] hover:bg-[#EAE3D5] text-[#26221E] font-semibold text-xs transition-all active:scale-95 shadow-xs flex items-center justify-center gap-1.5 cursor-pointer"
                    title="Bawa bahan ke Kalkulator Racikan"
                >
                    <span aria-hidden="true">⚖️</span>
                    <span>Ke Kalkulator</span>
                </button>

                <!-- Option B: Direct WhatsApp Order -->
                <button
                    type="button"
                    wire:click="openDirectCheckout"
                    class="flex-1 sm:flex-none px-4 py-2.5 rounded-full bg-[#B84E26] hover:bg-[#9C3F1C] text-white font-semibold text-xs transition-all active:scale-95 shadow-xs flex items-center justify-center gap-1.5 cursor-pointer"
                    title="Langsung Pesan Bahan via WhatsApp"
                >
                    <span aria-hidden="true">🛒</span>
                    <span>Pesan Langsung</span>
                </button>
            </div>
        </aside>
    @endif

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- DIRECT CHECKOUT MODAL (Beli Bahan Tanpa Daging/Kalkulator)     -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    @if ($showDirectCheckoutModal)
        <div
            class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-start sm:items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
            aria-labelledby="direct-checkout-title"
            x-data
            x-init="window.basokuFocusFirst($el)"
            @keydown.tab="window.basokuTrapFocus($el, $event)"
            x-on:keydown.escape.window="$wire.closeDirectCheckout()"
        >
            <div
                class="bg-[#FAF7F2] rounded-3xl max-w-lg w-full my-auto max-h-[90dvh] overflow-y-auto p-6 sm:p-8 border border-[#26221E]/15 shadow-2xl relative text-[#26221E]"
            >
                <!-- Modal Close -->
                <button
                    type="button"
                    wire:click="closeDirectCheckout"
                    aria-label="Tutup modal konfirmasi pesanan bahan"
                    class="absolute top-5 right-5 w-8 h-8 rounded-full bg-[#EAE3D5] text-[#26221E] hover:bg-[#B84E26] hover:text-white flex items-center justify-center font-bold text-sm transition-colors cursor-pointer"
                >
                    ✕
                </button>

                @if ($confirmedOrderCode)
                    <!-- LAYAR KONFIRMASI SUKSES PASCA-CHECKOUT KATALOG -->
                    <div class="space-y-5 text-left" x-data="{ copied: false, copyCode(code) { if (navigator.clipboard && navigator.clipboard.writeText) { navigator.clipboard.writeText(code).then(() => { this.copied = true; setTimeout(() => this.copied = false, 2200); }); } } }">
                        <div class="pr-8 space-y-2">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-900 border border-emerald-300 text-[11px] font-mono font-bold uppercase tracking-wider">
                                <svg class="w-3.5 h-3.5 text-emerald-700" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                Pesanan Berhasil Dicatat
                            </span>
                            <h3 id="direct-checkout-title" class="text-2xl font-bold tracking-tight" style="font-family: 'Playfair Display', Georgia, serif;">
                                Simpan Kode &amp; Kirim ke WhatsApp
                            </h3>
                            <p class="text-xs text-[#5A534A] leading-relaxed">
                                Pesanan belanja bahan Anda sudah tersimpan di antrean kios BASOKU. Kirim rincian ke WhatsApp atau simpan nomor pesanan untuk melacak status.
                            </p>
                        </div>

                        <!-- Kartu Kode Pesanan + Salin -->
                        <div class="p-4 rounded-2xl bg-[#2D4235] text-white flex items-center justify-between gap-3 shadow-md">
                            <div>
                                <span class="text-[10px] font-mono uppercase tracking-wider text-[#D8E0D3] block">Nomor Pesanan Anda</span>
                                <span class="text-2xl font-mono font-extrabold tracking-wider text-white select-all">{{ $confirmedOrderCode }}</span>
                            </div>
                            <button
                                type="button"
                                x-on:click="copyCode(@js($confirmedOrderCode))"
                                class="px-3.5 py-2 rounded-xl bg-white/15 hover:bg-white/25 border border-white/20 text-xs font-semibold text-white transition-colors cursor-pointer shrink-0"
                            >
                                <span x-show="!copied">Salin Kode</span>
                                <span x-show="copied" x-cloak class="text-emerald-200">Tersalin ✓</span>
                            </button>
                        </div>

                        <!-- Ringkasan Konfirmasi -->
                        <div class="p-4 rounded-2xl bg-white border border-[#26221E]/10 text-xs space-y-2 shadow-xs">
                            <div class="flex justify-between text-[#5A534A]">
                                <span>Pemesan:</span>
                                <span class="font-bold text-[#26221E]">{{ $customerName }} ({{ $customerPhone }})</span>
                            </div>
                            <div class="flex justify-between text-[#5A534A]">
                                <span>Jadwal Pengambilan:</span>
                                <span class="font-mono font-bold text-[#26221E]">{{ $confirmedPickupLabel }}</span>
                            </div>
                            <div class="flex justify-between text-[#5A534A] pt-1.5 border-t border-[#26221E]/10">
                                <span>Total Pembayaran Kios:</span>
                                <span class="font-bold font-mono text-sm text-[#B84E26] tabular-nums">Rp {{ number_format($confirmedOrderTotal, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="space-y-2.5 pt-1">
                            <a
                                href="{{ $confirmedWaUrl }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="w-full py-3.5 px-5 rounded-2xl bg-[#15803D] hover:bg-[#166534] text-white font-bold text-sm shadow transition-all flex items-center justify-center gap-2"
                            >
                                <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                                </svg>
                                <span>Buka WhatsApp &amp; Kirim Rincian</span>
                            </a>

                            <div class="grid grid-cols-2 gap-2.5">
                                <a
                                    href="{{ $confirmedTrackUrl }}"
                                    class="py-2.5 px-4 rounded-xl bg-white hover:bg-[#EAE3D5]/70 border border-[#26221E]/20 text-[#26221E] font-semibold text-xs text-center transition-colors"
                                >
                                    Lacak Pesanan
                                </a>
                                <button
                                    type="button"
                                    wire:click="closeDirectCheckout"
                                    class="py-2.5 px-4 rounded-xl bg-[#EAE3D5]/70 hover:bg-[#EAE3D5] text-[#26221E] font-semibold text-xs transition-colors cursor-pointer"
                                >
                                    Selesai / Tutup
                                </button>
                            </div>
                        </div>
                    </div>
                @else
                <div class="text-left space-y-2 pr-8">
                    <span class="text-[11px] font-mono uppercase tracking-wider text-[#B84E26] bg-[#EAE3D5]/60 px-3 py-1 rounded-full border border-[#26221E]/10 font-bold">
                        Checkout Belanja Kios
                    </span>
                    <h3 id="direct-checkout-title" class="text-2xl font-bold tracking-tight" style="font-family: 'Playfair Display', Georgia, serif;">
                        Konfirmasi Pesanan Bahan
                    </h3>
                    <p class="text-xs text-[#5A534A]">
                        Bahan disiapkan di kios BASOKU dan dikonfirmasi langsung ke WhatsApp resmi.
                    </p>
                </div>

                <!-- Form Fields -->
                <form wire:submit.prevent="processDirectCheckout" class="mt-6 space-y-4">
                    <div>
                        <label for="catalog-customer-name" class="block text-xs font-mono font-bold uppercase text-[#5A534A] mb-1">
                            Nama Lengkap Pemesan *
                        </label>
                        <input
                            id="catalog-customer-name"
                            type="text"
                            wire:model="customerName"
                            placeholder="Contoh: Pak Budi"
                            class="w-full px-4 py-2.5 rounded-xl bg-white border border-[#26221E]/20 text-sm focus:outline-hidden focus:border-[#2D4235]"
                        />
                        @error('customerName')
                            <p class="text-xs font-semibold text-[#8B231A] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="catalog-customer-phone" class="block text-xs font-mono font-bold uppercase text-[#5A534A] mb-1">
                            Nomor WhatsApp Pemesan *
                        </label>
                        <input
                            id="catalog-customer-phone"
                            type="tel"
                            wire:model="customerPhone"
                            placeholder="0812-3456-7890 atau 628123456789"
                            class="w-full px-4 py-2.5 rounded-xl bg-white border border-[#26221E]/20 text-sm focus:outline-hidden focus:border-[#2D4235]"
                        />
                        @error('customerPhone')
                            <p class="text-xs font-semibold text-[#8B231A] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="catalog-pickup-time" class="block text-xs font-mono font-bold uppercase text-[#5A534A] mb-1.5">
                            Tanggal &amp; Jam Pengambilan di Kios *
                        </label>
                        <x-pickup-datetime-input id="catalog-pickup-time" model="pickupTime" :value="$pickupTime" />
                        @php
                            $catalogPickupShortcuts = \App\Support\CheckoutHelper::pickupShortcuts();
                        @endphp
                        <div class="flex flex-wrap items-center gap-1.5 mt-2">
                            <span class="text-[10.5px] font-semibold text-[#5A534A]">Pilih cepat:</span>
                            @foreach ($catalogPickupShortcuts as $shortcut)
                                <button
                                    type="button"
                                    wire:click="$set('pickupTime', '{{ $shortcut['value'] }}')"
                                    class="px-2.5 py-1 rounded-lg text-[11px] font-mono font-semibold border transition-colors cursor-pointer {{ $pickupTime === $shortcut['value'] ? 'bg-[#2D4235] text-white border-[#2D4235]' : 'bg-white text-[#26221E] border-[#26221E]/15 hover:bg-[#EAE3D5]/70' }}"
                                >
                                    {{ $shortcut['label'] }}
                                </button>
                            @endforeach
                        </div>
                        <p class="text-[11px] text-[#5A534A] mt-1 font-mono">
                            Jam operasional: {{ sprintf('%02d.00 – %02d.00 WIB', config('services.kiosk.open_hour', 4), config('services.kiosk.close_hour', 12)) }} (min. 1 jam dari pemesanan).
                        </p>
                        @error('pickupTime')
                            <p class="text-xs font-semibold text-[#8B231A] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Ringkasan Singkat Biaya -->
                    <div class="mt-4 p-4 rounded-2xl bg-[#EAE3D5]/50 border border-[#26221E]/10 space-y-2">
                        <div class="flex justify-between text-xs text-[#5A534A]">
                            <span>Jumlah Item Bahan:</span>
                            <span class="font-bold font-mono text-[#26221E]">{{ $totalSelectedPacks }} pack/satuan</span>
                        </div>
                        <div class="flex justify-between text-sm font-bold border-t border-[#26221E]/10 pt-2 text-[#26221E]">
                            <span>Total Pembayaran Kios:</span>
                            <span class="font-mono text-[#B84E26]">Rp {{ number_format($totalSelectedCost, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    @error('cart')
                        <p class="text-xs font-semibold text-[#8B231A] text-center">{{ $message }}</p>
                    @enderror

                    <!-- Action Buttons -->
                    <div class="pt-2 flex items-center justify-end gap-3">
                        <button
                            type="button"
                            wire:click="closeDirectCheckout"
                            class="px-5 py-2.5 rounded-full border border-[#26221E]/20 text-xs font-semibold text-[#5A534A] hover:bg-[#EAE3D5] cursor-pointer"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="processDirectCheckout"
                            class="px-6 py-2.5 rounded-full bg-[#2D4235] hover:bg-[#1F3025] text-white text-xs font-bold shadow-md transition-all active:scale-95 flex items-center gap-2 cursor-pointer disabled:opacity-50"
                        >
                            <span wire:loading.remove wire:target="processDirectCheckout">Simpan &amp; Siapkan WhatsApp</span>
                            <span wire:loading wire:target="processDirectCheckout">Memproses...</span>
                            <svg class="w-4 h-4 text-[#D8E0D3]" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </button>
                    </div>
                </form>
                @endif
            </div>
        </div>
    @endif
</section>
