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
                Pilih bahan langsung untuk pesan via WhatsApp atau teruskan ke Kalkulator Racikan untuk hitung takaran bakso pas.
            </p>

            <!-- Search Bar & Category Filter Pills -->
            <div class="mt-8 space-y-4">
                <div class="relative max-w-md mx-auto">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari bahan (misal: Sapi, Tapioka, Sobaso)..."
                        class="w-full px-5 py-3 pl-11 rounded-2xl bg-[#FAF7F2] border border-[#26221E]/15 text-sm text-[#26221E] placeholder-[#5A534A]/60 focus:outline-hidden focus:border-[#2D4235] focus:ring-1 focus:ring-[#2D4235] shadow-xs font-medium"
                    />
                    <svg class="w-5 h-5 absolute left-3.5 top-3.5 text-[#5A534A]/70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    @if (!empty($search))
                        <button
                            type="button"
                            wire:click="$set('search', '')"
                            class="absolute right-3.5 top-3 text-xs text-[#5A534A] hover:text-[#26221E] px-2 py-1 rounded bg-[#EAE3D5]"
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

                                $hasPackage = !empty($material->package_weight) && (float) $material->package_weight > 0 && !empty($material->package_unit);
                                $packagePrice = (float) ($material->package_price > 0 ? $material->package_price : (strtolower($material->unit) === 'kg' ? $material->price * 1000 : $material->price));

                                // Format harga utama kemasan pasar (retail)
                                $mainPriceFormatted = 'Rp ' . number_format($packagePrice, 0, ',', '.');

                                $pkgUnitLower = strtolower($material->package_unit ?? $material->unit);
                                if ($pkgUnitLower === 'kg') {
                                    $mainUnitLabel = '/ kg';
                                } elseif (in_array($pkgUnitLower, ['pack', 'bks', 'bungkus'])) {
                                    $mainUnitLabel = '/ pack (' . number_format($material->package_weight, 0, ',', '.') . ' g)';
                                } elseif ($pkgUnitLower === 'sachet') {
                                    $mainUnitLabel = '/ sachet (' . number_format($material->package_weight, 0, ',', '.') . ' g)';
                                } elseif ($pkgUnitLower === 'butir') {
                                    $mainUnitLabel = '/ butir';
                                } else {
                                    $mainUnitLabel = '/ ' . ($material->package_unit ?? $material->unit);
                                }

                                // Badge harga dasar per gram atau per butir/sachet
                                if ($pkgUnitLower === 'butir') {
                                    $baseBadge = 'Rp ' . number_format($packagePrice, 0, ',', '.') . ' / butir';
                                } elseif ($pkgUnitLower === 'sachet') {
                                    $baseBadge = 'Rp ' . number_format($packagePrice, 0, ',', '.') . ' / sachet';
                                } elseif ($material->price >= 1000) {
                                    $baseBadge = 'Rp ' . number_format($packagePrice, 0, ',', '.') . ' / ' . $pkgUnitLower;
                                } else {
                                    $baseBadge = 'Rp ' . number_format($material->price, 0, ',', '.') . ' / g';
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
                                                Kemasan fisik retail: {{ number_format($material->package_weight, 0, ',', '.') }} {{ $pkgUnitLower === 'butir' ? 'gram / butir' : ($pkgUnitLower === 'kg' ? 'gram (1 kg)' : 'gram') }}
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

                                    <!-- Interactive Quantity Stepper / Add Button -->
                                    @php
                                        $currentQty = $selectedItems[$material->id] ?? 0;
                                    @endphp

                                    @if ($currentQty > 0)
                                        <div class="flex items-center gap-1 bg-[#FAF7F2] border border-[#2D4235] rounded-full p-0.5 shadow-xs shrink-0">
                                            <button
                                                type="button"
                                                wire:click="decrementItem({{ $material->id }})"
                                                class="w-6 h-6 rounded-full bg-[#EAE3D5] hover:bg-[#D8E0D3] text-[#26221E] flex items-center justify-center font-bold text-xs transition-colors"
                                                title="Kurangi 1"
                                                aria-label="Kurangi {{ $material->name }}"
                                            >
                                                -
                                            </button>
                                            <span class="font-mono font-bold text-xs px-1.5 text-[#2D4235] tabular-nums">
                                                {{ $currentQty }}
                                            </span>
                                            <button
                                                type="button"
                                                wire:click="incrementItem({{ $material->id }})"
                                                class="w-6 h-6 rounded-full bg-[#2D4235] hover:bg-[#1F3025] text-white flex items-center justify-center font-bold text-xs transition-colors"
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
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-[#2D4235] hover:bg-[#D96B43] text-white text-xs font-semibold shadow-xs transition-all active:scale-95 shrink-0"
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
                            <span class="text-lg font-bold font-mono text-[#26221E] tabular-nums">Rp 15.000</span>
                            <span class="text-xs text-[#5A534A]">/ kg daging</span>
                        </div>

                        <a
                            href="{{ route('calculator') }}"
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
            class="fixed bottom-5 inset-x-4 sm:inset-x-auto sm:right-8 sm:max-w-xl z-40 bg-[#26221E] text-[#F4EFE6] px-5 py-4 rounded-3xl shadow-2xl border border-[#D96B43]/30 flex flex-col sm:flex-row items-center justify-between gap-4 animate-in slide-in-from-bottom duration-300"
        >
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <div class="w-10 h-10 rounded-2xl bg-[#D96B43] text-white flex items-center justify-center font-bold text-base shrink-0 shadow-xs">
                    {{ $totalSelectedPacks }}
                </div>
                <div>
                    <div class="text-xs text-[#EAE3D5]/80">Bahan Terpilih di Katalog</div>
                    <div class="text-sm sm:text-base font-bold font-mono text-white tabular-nums">
                        Rp {{ number_format($totalSelectedCost, 0, ',', '.') }}
                    </div>
                </div>
                <button
                    type="button"
                    wire:click="clearCart"
                    class="ml-auto sm:ml-2 text-xs text-[#EAE3D5]/60 hover:text-white underline underline-offset-2"
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
                    class="flex-1 sm:flex-none px-4 py-2.5 rounded-full bg-[#FAF7F2] hover:bg-[#EAE3D5] text-[#26221E] font-semibold text-xs transition-all active:scale-95 shadow-xs flex items-center justify-center gap-1.5"
                    title="Bawa bahan ke Kalkulator Racikan"
                >
                    <span>⚖️ Ke Kalkulator</span>
                </button>

                <!-- Option B: Direct WhatsApp Order -->
                <button
                    type="button"
                    wire:click="openDirectCheckout"
                    class="flex-1 sm:flex-none px-4 py-2.5 rounded-full bg-[#D96B43] hover:bg-[#c45a34] text-white font-semibold text-xs transition-all active:scale-95 shadow-xs flex items-center justify-center gap-1.5"
                    title="Langsung Pesan Bahan via WhatsApp"
                >
                    <span>🛒 Pesan Langsung</span>
                </button>
            </div>
        </aside>
    @endif

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- DIRECT CHECKOUT MODAL (Beli Bahan Tanpa Daging/Kalkulator)     -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    @if ($showDirectCheckoutModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div
                class="bg-[#FAF7F2] rounded-3xl max-w-lg w-full p-6 sm:p-8 border border-[#26221E]/15 shadow-2xl relative text-[#26221E] animate-in zoom-in-95 duration-200"
                x-on:keydown.escape.window="$wire.closeDirectCheckout()"
            >
                <!-- Modal Close -->
                <button
                    type="button"
                    wire:click="closeDirectCheckout"
                    class="absolute top-5 right-5 w-8 h-8 rounded-full bg-[#EAE3D5] text-[#26221E] hover:bg-[#D96B43] hover:text-white flex items-center justify-center font-bold text-sm transition-colors"
                >
                    ✕
                </button>

                <div class="text-left space-y-2">
                    <span class="text-[11px] font-mono uppercase tracking-wider text-[#D96B43] bg-[#EAE3D5]/60 px-3 py-1 rounded-full border border-[#26221E]/10">
                        Checkout Belanja Kios
                    </span>
                    <h3 class="text-2xl font-bold tracking-tight" style="font-family: 'Playfair Display', Georgia, serif;">
                        Konfirmasi Pesanan Bahan
                    </h3>
                    <p class="text-xs text-[#5A534A]">
                        Bahan disiapkan di kios BASOKU dan dikonfirmasi langsung ke WhatsApp resmi.
                    </p>
                </div>

                <!-- Form Fields -->
                <form wire:submit.prevent="processDirectCheckout" class="mt-6 space-y-4">
                    <div>
                        <label class="block text-xs font-mono font-bold uppercase text-[#5A534A] mb-1">
                            Nama Lengkap Pemesan *
                        </label>
                        <input
                            type="text"
                            wire:model="customerName"
                            placeholder="Contoh: Pak Budi"
                            class="w-full px-4 py-2.5 rounded-xl bg-white border border-[#26221E]/20 text-sm focus:outline-hidden focus:border-[#2D4235]"
                        />
                        @error('customerName')
                            <p class="text-xs text-[#D96B43] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-mono font-bold uppercase text-[#5A534A] mb-1">
                            Nomor WhatsApp Pemesan *
                        </label>
                        <input
                            type="tel"
                            wire:model="customerPhone"
                            placeholder="08123456789 atau 628123456789"
                            class="w-full px-4 py-2.5 rounded-xl bg-white border border-[#26221E]/20 text-sm focus:outline-hidden focus:border-[#2D4235]"
                        />
                        @error('customerPhone')
                            <p class="text-xs text-[#D96B43] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-mono font-bold uppercase text-[#5A534A] mb-1">
                            Jadwal Pengambilan di Kios *
                        </label>
                        <input
                            type="datetime-local"
                            wire:model="pickupTime"
                            class="w-full px-4 py-2.5 rounded-xl bg-white border border-[#26221E]/20 text-sm focus:outline-hidden focus:border-[#2D4235]"
                        />
                        <p class="text-[11px] text-[#5A534A] mt-1 font-mono">
                            Jam operasional: 05.00 – 12.00 WIB (min. 1 jam dari pemesanan).
                        </p>
                        @error('pickupTime')
                            <p class="text-xs text-[#D96B43] mt-1">{{ $message }}</p>
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
                            <span class="font-mono text-[#D96B43]">Rp {{ number_format($totalSelectedCost, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    @error('cart')
                        <p class="text-xs text-[#D96B43] text-center">{{ $message }}</p>
                    @enderror

                    <!-- Action Buttons -->
                    <div class="pt-2 flex items-center justify-end gap-3">
                        <button
                            type="button"
                            wire:click="closeDirectCheckout"
                            class="px-5 py-2.5 rounded-full border border-[#26221E]/20 text-xs font-semibold text-[#5A534A] hover:bg-[#EAE3D5]"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            class="px-6 py-2.5 rounded-full bg-[#2D4235] hover:bg-[#1F3025] text-white text-xs font-bold shadow-md transition-all active:scale-95 flex items-center gap-2"
                        >
                            <span wire:loading.remove>Lanjutkan ke WhatsApp</span>
                            <span wire:loading>Memproses...</span>
                            <svg class="w-4 h-4 text-[#D8E0D3]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
