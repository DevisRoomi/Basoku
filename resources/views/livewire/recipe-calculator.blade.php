<div class="w-full">

    <!-- Compact Editorial Header Card -->
    <div class="rounded-2xl sm:rounded-3xl bg-[#FAF7F2] text-[#26221E] px-5 py-4 sm:px-7 sm:py-5 mb-6 border border-[#26221E]/10 relative overflow-hidden">
        <div class="relative z-10 flex flex-col md:flex-row md:items-end md:justify-between gap-2.5">
            <div>
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="w-5 h-[2px] bg-[#B84E26]"></span>
                    <span class="text-[11px] font-mono font-bold tracking-[0.18em] uppercase text-[#B84E26]">
                        Kalkulator Racikan &bull; Tradisi Pasar
                    </span>
                </div>
                @if ($embedded)
                    <h2 class="text-2xl sm:text-3xl font-bold text-[#26221E] tracking-tight leading-tight" style="font-family: 'Playfair Display', Georgia, serif;">
                        Rancang Racikanmu
                    </h2>
                @else
                    <h1 class="text-2xl sm:text-3xl font-bold text-[#26221E] tracking-tight leading-tight" style="font-family: 'Playfair Display', Georgia, serif;">
                        Rancang Racikanmu
                    </h1>
                @endif
            </div>
            <p class="text-[#5A534A] text-xs sm:text-sm leading-relaxed font-normal max-w-md">
                Kalkulasi transparan bahan bakso pasar: atur berat daging atau anggaran untuk estimasi belanja paling hemat &amp; presisi.
            </p>
        </div>
    </div>

    <!-- Grid Utama: Form Input Ringkas (Kiri) + Panel Struk Live Sticky 1-Layar (Kanan) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-7 items-start">

        <!-- ═══════════════════════════════════════════ -->
        <!-- KOLOM KIRI: FORMULIR INPUT RINGKAS (7 Kolom)-->
        <!-- ═══════════════════════════════════════════ -->
        <div class="lg:col-span-7 space-y-5">

            <!-- ──────────────────────────────────── -->
            <!-- Tab Mode Perhitungan (Khusus Racikan) -->
            <!-- ──────────────────────────────────── -->
            @if ($startType !== 'ingredients_only')
                <div class="p-1 rounded-full bg-[#FAF7F2] border border-[#26221E]/10 flex items-center gap-1.5 shadow-xs">
                    <button
                        type="button"
                        wire:click="$set('calculationMode', 'weight')"
                        class="flex-1 py-2.5 px-4 rounded-full text-xs sm:text-sm font-semibold transition-all duration-200 cursor-pointer flex items-center justify-center gap-2 focus:outline-none {{ $calculationMode === 'weight' ? 'bg-[#2D4235] text-white shadow-sm' : 'text-[#26221E]/70 hover:text-[#26221E] hover:bg-black/5' }}"
                    >
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75M18.75 4.97A48.416 48.416 0 0012 4.5c-2.291 0-4.545.16-6.75.47m13.5 0c1.01.143 2.01.317 3 .52m-16.5-.52c-1.01.143-2.01.317-3 .52m16.5 0a48.72 48.72 0 01-16.5 0" />
                        </svg>
                        <span>Hitung Berdasarkan Berat</span>
                    </button>
                    <button
                        type="button"
                        wire:click="$set('calculationMode', 'budget')"
                        class="flex-1 py-2.5 px-4 rounded-full text-xs sm:text-sm font-semibold transition-all duration-200 cursor-pointer flex items-center justify-center gap-2 focus:outline-none {{ $calculationMode === 'budget' ? 'bg-[#2D4235] text-white shadow-sm' : 'text-[#26221E]/70 hover:text-[#26221E] hover:bg-black/5' }}"
                    >
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6H2.25m0 0v1.5m0-1.5h1.5m0 0h16.5m-16.5 0a.75.75 0 01-.75-.75V3m0 3h1.5m16.5 0a.75.75 0 00.75-.75V3m0 3v1.5m0-1.5h-1.5m0 0H3.75" />
                        </svg>
                        <span>Sesuaikan Anggaran</span>
                    </button>
                </div>
            @endif

            <!-- ──────────────────────────────────── -->
            <!-- 1. Mulai dari Mana?                  -->
            <!-- ──────────────────────────────────── -->
            <fieldset>
                <legend class="text-xs font-bold text-[#26221E] uppercase tracking-wider mb-2.5 flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-[#2D4235] text-white text-[11px] font-bold">1</span>
                    Mulai dari Mana?
                </legend>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                    <!-- Opsi: Mulai dari Nol -->
                    <button
                        type="button"
                        wire:click="$set('startType', 'start_from_zero')"
                        class="text-left w-full rounded-2xl border p-3 cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-[#D96B43] transition-all duration-200 {{ $startType === 'start_from_zero' ? 'border-[#D96B43] bg-[#FAF7F2] ring-1 ring-[#D96B43]/30 shadow-xs' : 'border-[#26221E]/10 bg-white/90 hover:border-[#26221E]/25 hover:bg-white' }}"
                    >
                        <div class="flex items-start gap-2.5">
                            <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 transition-colors {{ $startType === 'start_from_zero' ? 'bg-[#D96B43] text-white' : 'bg-[#26221E]/5 text-[#26221E]/60' }}">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <span class="block text-[13px] font-bold text-[#26221E] leading-tight">Mulai dari Nol</span>
                                <span class="block text-[11px] text-[#5A534A] mt-0.5 leading-snug">Beli daging &amp; semua bahan racikan dari awal.</span>
                            </div>
                        </div>
                    </button>

                    <!-- Opsi: Bawa Daging Sendiri -->
                    <button
                        type="button"
                        wire:click="$set('startType', 'own_meat')"
                        class="text-left w-full rounded-2xl border p-3 cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-[#D96B43] transition-all duration-200 {{ $startType === 'own_meat' ? 'border-[#D96B43] bg-[#FAF7F2] ring-1 ring-[#D96B43]/30 shadow-xs' : 'border-[#26221E]/10 bg-white/90 hover:border-[#26221E]/25 hover:bg-white' }}"
                    >
                        <div class="flex items-start gap-2.5">
                            <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 transition-colors {{ $startType === 'own_meat' ? 'bg-[#D96B43] text-white' : 'bg-[#26221E]/5 text-[#26221E]/60' }}">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <span class="block text-[13px] font-bold text-[#26221E] leading-tight">Bawa Daging Sendiri</span>
                                <span class="block text-[11px] text-[#5A534A] mt-0.5 leading-snug">Punya daging sendiri. Beli bumbu &amp; selepan saja.</span>
                            </div>
                        </div>
                    </button>

                    <!-- Opsi: Beli Bahan Saja -->
                    <button
                        type="button"
                        wire:click="$set('startType', 'ingredients_only')"
                        class="text-left w-full rounded-2xl border p-3 cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-[#D96B43] transition-all duration-200 {{ $startType === 'ingredients_only' ? 'border-[#D96B43] bg-[#FAF7F2] ring-1 ring-[#D96B43]/30 shadow-xs' : 'border-[#26221E]/10 bg-white/90 hover:border-[#26221E]/25 hover:bg-white' }}"
                    >
                        <div class="flex items-start gap-2.5">
                            <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 transition-colors {{ $startType === 'ingredients_only' ? 'bg-[#D96B43] text-white' : 'bg-[#26221E]/5 text-[#26221E]/60' }}">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <span class="block text-[13px] font-bold text-[#26221E] leading-tight">Beli Bahan Saja</span>
                                <span class="block text-[11px] text-[#5A534A] mt-0.5 leading-snug">Pilih bebas bumbu, telur, pelengkap, atau tepung tanpa daging.</span>
                            </div>
                        </div>
                    </button>
                </div>
            </fieldset>

            @if ($startType !== 'ingredients_only')
            <!-- ──────────────────────────────────── -->
            <!-- 2. Pilih Daging (Compact Horizontal) -->
            <!-- ──────────────────────────────────── -->
            <fieldset>
                <legend class="text-xs font-bold text-[#26221E] uppercase tracking-wider mb-2.5 flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-[#2D4235] text-white text-[11px] font-bold">2</span>
                    Pilih Daging
                </legend>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <!-- Daging Sapi -->
                    <button
                        type="button"
                        wire:click="$set('meatType', 'sapi')"
                        class="flex items-center gap-3 p-3.5 rounded-2xl border-2 text-left cursor-pointer transition-all duration-200 {{ $meatType === 'sapi' ? 'border-[#D96B43] bg-[#FAF7F2] shadow-xs ring-1 ring-[#D96B43]/20' : 'border-[#26221E]/10 bg-white/90 hover:border-[#26221E]/25 hover:bg-white' }}"
                    >
                        <div class="w-10 h-10 rounded-xl bg-[#2D4235] text-[#F4EFE6] flex items-center justify-center font-mono font-bold text-xs shrink-0">
                            SP
                        </div>
                        <div class="min-w-0">
                            <span class="block font-bold text-sm text-[#26221E] leading-tight">Daging Sapi</span>
                            <span class="block text-[11px] text-[#5A534A] mt-0.5 truncate">
                                Sapi segar paha / gandik &bull;
                                @if (!($catalogPrices['beef_available'] ?? true))
                                    <strong class="text-[#8B231A]">Stok Habis</strong>
                                @else
                                    <strong class="text-[#B84E26]">Rp {{ number_format($catalogPrices['beef_per_kg'] ?? 140000, 0, ',', '.') }}/kg</strong>
                                @endif
                            </span>
                        </div>
                    </button>

                    <!-- Daging Ayam -->
                    <button
                        type="button"
                        wire:click="$set('meatType', 'ayam')"
                        class="flex items-center gap-3 p-3.5 rounded-2xl border-2 text-left cursor-pointer transition-all duration-200 {{ $meatType === 'ayam' ? 'border-[#D96B43] bg-[#FAF7F2] shadow-xs ring-1 ring-[#D96B43]/20' : 'border-[#26221E]/10 bg-white/90 hover:border-[#26221E]/25 hover:bg-white' }}"
                    >
                        <div class="w-10 h-10 rounded-xl bg-[#2D4235] text-[#F4EFE6] flex items-center justify-center font-mono font-bold text-xs shrink-0">
                            AY
                        </div>
                        <div class="min-w-0">
                            <span class="block font-bold text-sm text-[#26221E] leading-tight">Daging Ayam</span>
                            <span class="block text-[11px] text-[#5A534A] mt-0.5 truncate">
                                Dada ayam fillet bersih &bull;
                                @if (!($catalogPrices['chicken_available'] ?? true))
                                    <strong class="text-[#8B231A]">Stok Habis</strong>
                                @else
                                    <strong class="text-[#B84E26]">Rp {{ number_format($catalogPrices['chicken_per_kg'] ?? 40000, 0, ',', '.') }}/kg</strong>
                                @endif
                            </span>
                        </div>
                    </button>
                </div>
            </fieldset>

            <!-- ──────────────────────────────────── -->
            <!-- 3. Atur Berat / Anggaran             -->
            <!-- ──────────────────────────────────── -->
            @if ($calculationMode === 'weight')
                <fieldset>
                    <legend class="text-xs font-bold text-[#26221E] uppercase tracking-wider mb-2.5 flex items-center gap-2">
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-[#2D4235] text-white text-[11px] font-bold">3</span>
                        Berat Daging (Gram)
                    </legend>
                    
                    <!-- Compact Side-by-Side Analog Market Scale Dial & Slider (Haptic & Tactile) -->
                    <div
                        x-data="{
                            weight: {{ $meatWeight }},
                            lastTickWeight: {{ $meatWeight }},
                            audioCtx: null,
                            get needleAngle() {
                                const w = Math.min(Math.max(this.weight, 500), 5000);
                                return -80 + (w / 5000) * 160;
                            },
                            playHaptic() {
                                try {
                                    const AudioContextClass = window.AudioContext || window.webkitAudioContext;
                                    if (AudioContextClass && !this.audioCtx) {
                                        this.audioCtx = new AudioContextClass();
                                    }
                                    if (this.audioCtx && this.audioCtx.state === 'suspended') {
                                        this.audioCtx.resume();
                                    }
                                    if (this.audioCtx) {
                                        const osc = this.audioCtx.createOscillator();
                                        const gain = this.audioCtx.createGain();
                                        osc.type = 'triangle';
                                        osc.frequency.setValueAtTime(520, this.audioCtx.currentTime);
                                        osc.frequency.exponentialRampToValueAtTime(160, this.audioCtx.currentTime + 0.025);
                                        gain.gain.setValueAtTime(0.07, this.audioCtx.currentTime);
                                        gain.gain.exponentialRampToValueAtTime(0.001, this.audioCtx.currentTime + 0.025);
                                        osc.connect(gain);
                                        gain.connect(this.audioCtx.destination);
                                        osc.start();
                                        osc.stop(this.audioCtx.currentTime + 0.028);
                                    }
                                } catch(e) {}
                                if (navigator && typeof navigator.vibrate === 'function') {
                                    try { navigator.vibrate(8); } catch(e) {}
                                }
                            },
                            onSlide(val) {
                                this.weight = Number(val);
                                if (Math.abs(this.weight - this.lastTickWeight) >= 50) {
                                    this.playHaptic();
                                    this.lastTickWeight = this.weight;
                                }
                            }
                        }"
                        x-init="$watch('$wire.meatWeight', (val) => { weight = Number(val); })"
                        class="bg-white/95 rounded-2xl border border-[#26221E]/10 p-4 shadow-xs"
                    >
                        <!-- Side-by-Side: Jarum Timbangan (Kiri) + Angka Digital & Input Manual (Kanan) -->
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pb-3.5 mb-3.5 border-b border-[#26221E]/10 select-none">
                            <!-- Analog Scale Gauge -->
                            <div class="relative w-40 h-[88px] flex items-end justify-center shrink-0">
                                <svg class="w-40 h-[88px]" viewBox="0 0 200 110" fill="none">
                                    <path d="M 21.2 86.1 A 80 80 0 0 1 178.8 86.1" stroke="#26221E" stroke-width="1.5" stroke-dasharray="2 3" opacity="0.3" />
                                    <path d="M 28.1 64.9 A 80 80 0 0 1 178.8 86.1" stroke="#2D4235" stroke-width="3.5" stroke-linecap="round" opacity="0.25" />
                                    <line x1="28.1" y1="64.9" x2="37.1" y2="69.3" stroke="#D96B43" stroke-width="2" />
                                    <line x1="40.6" y1="46.5" x2="48.0" y2="53.2" stroke="#26221E" stroke-width="1.5" opacity="0.6" />
                                    <line x1="100" y1="20" x2="100" y2="30" stroke="#D96B43" stroke-width="2.5" />
                                    <line x1="159.4" y1="46.5" x2="152.0" y2="53.2" stroke="#26221E" stroke-width="1.5" opacity="0.6" />
                                    <line x1="178.8" y1="86.1" x2="168.9" y2="87.8" stroke="#D96B43" stroke-width="2" />

                                    <text x="18" y="60" font-size="8.5" fill="#5A534A" font-family="monospace" font-weight="bold">0.5kg</text>
                                    <text x="90" y="15" font-size="8.5" fill="#D96B43" font-family="monospace" font-weight="bold">2.5kg</text>
                                    <text x="156" y="82" font-size="8.5" fill="#5A534A" font-family="monospace" font-weight="bold">5.0kg</text>

                                    <g :transform="'rotate(' + needleAngle + ' 100 100)'" style="transition: transform 150ms ease-out;">
                                        <line x1="100" y1="100" x2="100" y2="24" stroke="#D96B43" stroke-width="2.5" stroke-linecap="round" />
                                        <line x1="100" y1="100" x2="100" y2="106" stroke="#26221E" stroke-width="2" stroke-linecap="round" />
                                        <circle cx="100" cy="100" r="5" fill="#26221E" stroke="#D96B43" stroke-width="1.5" />
                                        <circle cx="100" cy="100" r="2" fill="#FAF7F2" />
                                    </g>
                                </svg>
                            </div>

                            <!-- Live Digital Weight Reading & Manual Numeric Input -->
                            <div class="flex-1 flex flex-col items-center sm:items-end justify-center gap-1.5 text-center sm:text-right">
                                <div>
                                    <span class="text-2xl font-mono font-bold text-[#26221E] tabular-nums tracking-tight leading-none">
                                        <span x-text="Number(weight).toLocaleString()">{{ number_format($meatWeight) }}</span>
                                        <span class="text-xs font-sans font-semibold text-[#B84E26]">gram</span>
                                    </span>
                                    <span class="block text-[11px] font-mono text-[#5A534A] mt-0.5">
                                        (setara <span x-text="(weight / 1000).toFixed(2)">{{ number_format($meatWeight / 1000, 2) }}</span> kg daging)
                                    </span>
                                </div>

                                <!-- Input Angka Manual untuk Presisi Cepat -->
                                <div class="inline-flex items-center gap-1.5">
                                    <button
                                        type="button"
                                        @click="weight = Math.max(500, Number(weight) - 100); $wire.set('meatWeight', weight); playHaptic();"
                                        aria-label="Kurangi 100 gram"
                                        class="w-8 h-8 rounded-xl bg-[#FAF7F2] hover:bg-[#EAE3D5] border border-[#26221E]/15 text-[#26221E] font-mono font-bold text-sm cursor-pointer transition-colors"
                                    >
                                        &minus;
                                    </button>
                                    <div class="relative">
                                        <label for="meatWeightNumber" class="sr-only">Ketik berat daging dalam gram</label>
                                        <input
                                            type="number"
                                            id="meatWeightNumber"
                                            min="500"
                                            max="50000"
                                            step="50"
                                            wire:model.live.debounce.300ms="meatWeight"
                                            x-model.number="weight"
                                            class="w-24 text-center font-mono font-bold text-sm text-[#26221E] bg-[#FAF7F2] border border-[#26221E]/20 rounded-xl py-1 px-2 focus:outline-none focus:border-[#2D4235] tabular-nums"
                                        >
                                    </div>
                                    <button
                                        type="button"
                                        @click="weight = Math.min(50000, Number(weight) + 100); $wire.set('meatWeight', weight); playHaptic();"
                                        aria-label="Tambah 100 gram"
                                        class="w-8 h-8 rounded-xl bg-[#FAF7F2] hover:bg-[#EAE3D5] border border-[#26221E]/15 text-[#26221E] font-mono font-bold text-sm cursor-pointer transition-colors"
                                    >
                                        +
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Slider Bar with Haptic Tick On Input -->
                        <div class="space-y-1">
                            <div class="flex items-center justify-between text-[10.5px] font-mono text-[#5A534A]">
                                <span>Timbangan Rendah (500g)</span>
                                <span>Kapasitas Los (5 kg)</span>
                            </div>
                            <input
                                type="range"
                                id="meatWeightRange"
                                aria-label="Atur berat daging dalam gram"
                                min="500"
                                max="5000"
                                step="50"
                                wire:model.live.debounce.150ms="meatWeight"
                                x-model="weight"
                                @input="onSlide($event.target.value)"
                                class="w-full h-2 bg-[#E8ECE4] rounded-lg appearance-none cursor-pointer accent-[#D96B43] focus:outline-none"
                            >
                        </div>

                        <!-- Preset Buttons (Snap Weights) -->
                        <div class="grid grid-cols-4 gap-2 mt-3 pt-3 border-t border-[#26221E]/10">
                            @foreach ([500, 1000, 2000, 3000] as $preset)
                                <button
                                    type="button"
                                    wire:click="$set('meatWeight', {{ $preset }})"
                                    @click="weight = {{ $preset }}; playHaptic();"
                                    class="py-1.5 px-2 rounded-xl text-xs font-mono font-semibold transition-colors cursor-pointer {{ $meatWeight == $preset ? 'bg-[#2D4235] text-white' : 'bg-[#FAF7F2] text-[#26221E] hover:bg-black/5' }}"
                                >
                                    {{ $preset >= 1000 ? ($preset / 1000) . ' kg' : $preset . ' g' }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </fieldset>
            @else
                <fieldset>
                    <legend class="text-xs font-bold text-[#26221E] uppercase tracking-wider mb-2.5 flex items-center gap-2">
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-[#2D4235] text-white text-[11px] font-bold">3</span>
                        Batas Anggaran Belanja (Rp)
                    </legend>

                    <div class="bg-white/90 rounded-2xl border border-[#26221E]/10 p-4 shadow-xs">
                        <label for="budgetInput" class="block text-xs text-[#5A534A] mb-1.5">
                            Masukkan nominal uang belanja Anda:
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 font-bold text-sm text-[#26221E]/50">Rp</span>
                            <input
                                type="number"
                                id="budgetInput"
                                min="20000"
                                step="5000"
                                wire:model.live.debounce.300ms="budget"
                                class="w-full rounded-xl border-2 border-[#26221E]/15 bg-white pl-12 pr-4 py-2.5 text-base font-bold text-[#26221E] focus:outline-none focus:border-[#D96B43] focus:ring-2 focus:ring-[#D96B43]/20"
                            >
                        </div>

                        <!-- Budget Quick Presets -->
                        <div class="grid grid-cols-4 gap-2 mt-3 pt-3 border-t border-[#26221E]/10">
                            @foreach ([50000, 100000, 150000, 200000] as $presetBudget)
                                <button
                                    type="button"
                                    wire:click="$set('budget', {{ $presetBudget }})"
                                    class="py-1.5 px-2 rounded-xl text-xs font-mono font-semibold transition-colors cursor-pointer {{ $budget == $presetBudget ? 'bg-[#2D4235] text-white' : 'bg-[#FAF7F2] text-[#26221E] hover:bg-black/5' }}"
                                >
                                    {{ number_format($presetBudget / 1000) }}rb
                                </button>
                            @endforeach
                        </div>
                    </div>
                </fieldset>
            @endif
            <!-- ──────────────────────────────────── -->
            <!-- 4. Varian Tepung Pilihan (Racikan)   -->
            <!-- ──────────────────────────────────── -->
            <fieldset>
                <legend class="text-xs font-bold text-[#26221E] uppercase tracking-wider mb-2.5 flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-[#2D4235] text-white text-[11px] font-bold">4</span>
                    Pilihan Jenis Tepung
                </legend>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    <!-- Tapioka -->
                    <button
                        type="button"
                        wire:click="$set('flourType', 'tapioka')"
                        class="p-2.5 rounded-xl border text-left cursor-pointer transition-all duration-200 {{ $flourType === 'tapioka' ? 'border-[#D96B43] bg-[#FAF7F2] ring-1 ring-[#D96B43]/30 shadow-xs' : 'border-[#26221E]/10 bg-white hover:border-[#26221E]/25' }}"
                    >
                        <span class="block font-bold text-xs text-[#26221E]">Tapioka</span>
                        <span class="block text-[10px] text-[#5A534A] mt-0.5 leading-tight">{{ $catalogPrices['tapioka_unit'] ?? 'Pack' }} {{ number_format($catalogPrices['tapioka_weight'] ?? 500, 0, ',', '.') }}g (Rp {{ number_format($catalogPrices['tapioka_price'] ?? 9000, 0, ',', '.') }})</span>
                        <span class="block text-[9px] font-mono {{ ($catalogPrices['tapioka_available'] ?? true) ? 'text-[#B84E26]' : 'text-[#8B231A]' }} mt-1 font-semibold">{{ ($catalogPrices['tapioka_available'] ?? true) ? 'Kenyal Standar' : 'Stok Habis' }}</span>
                    </button>

                    <!-- ProBakso (Premix) -->
                    <button
                        type="button"
                        wire:click="$set('flourType', 'probakso')"
                        class="p-2.5 rounded-xl border text-left cursor-pointer transition-all duration-200 {{ $flourType === 'probakso' ? 'border-[#D96B43] bg-[#FAF7F2] ring-1 ring-[#D96B43]/30 shadow-xs' : 'border-[#26221E]/10 bg-white hover:border-[#26221E]/25' }}"
                    >
                        <span class="block font-bold text-xs text-[#26221E]">ProBakso</span>
                        <span class="block text-[10px] text-[#5A534A] mt-0.5 leading-tight">{{ $catalogPrices['probakso_unit'] ?? 'Pack' }} {{ number_format($catalogPrices['probakso_weight'] ?? 250, 0, ',', '.') }}g (Rp {{ number_format($catalogPrices['probakso_price'] ?? 10000, 0, ',', '.') }})</span>
                        <span class="block text-[9px] font-mono {{ ($catalogPrices['probakso_available'] ?? true) ? 'text-[#2D4235]' : 'text-[#8B231A]' }} mt-1 font-semibold">{{ ($catalogPrices['probakso_available'] ?? true) ? 'Premiks Pengenyal' : 'Stok Habis' }}</span>
                    </button>

                    <!-- Sagu -->
                    <button
                        type="button"
                        wire:click="$set('flourType', 'sagu')"
                        class="p-2.5 rounded-xl border text-left cursor-pointer transition-all duration-200 {{ $flourType === 'sagu' ? 'border-[#D96B43] bg-[#FAF7F2] ring-1 ring-[#D96B43]/30 shadow-xs' : 'border-[#26221E]/10 bg-white hover:border-[#26221E]/25' }}"
                    >
                        <span class="block font-bold text-xs text-[#26221E]">Sagu Aren</span>
                        <span class="block text-[10px] text-[#5A534A] mt-0.5 leading-tight">{{ $catalogPrices['sagu_unit'] ?? 'Pack' }} {{ number_format($catalogPrices['sagu_weight'] ?? 250, 0, ',', '.') }}g (Rp {{ number_format($catalogPrices['sagu_price'] ?? 5000, 0, ',', '.') }})</span>
                        <span class="block text-[9px] font-mono {{ ($catalogPrices['sagu_available'] ?? true) ? 'text-[#8B5E00]' : 'text-[#8B231A]' }} mt-1 font-semibold">{{ ($catalogPrices['sagu_available'] ?? true) ? 'Renyah Garing' : 'Stok Habis' }}</span>
                    </button>

                    <!-- Terigu -->
                    <button
                        type="button"
                        wire:click="$set('flourType', 'terigu')"
                        class="p-2.5 rounded-xl border text-left cursor-pointer transition-all duration-200 {{ $flourType === 'terigu' ? 'border-[#D96B43] bg-[#FAF7F2] ring-1 ring-[#D96B43]/30 shadow-xs' : 'border-[#26221E]/10 bg-white hover:border-[#26221E]/25' }}"
                    >
                        <span class="block font-bold text-xs text-[#26221E]">Terigu</span>
                        <span class="block text-[10px] text-[#5A534A] mt-0.5 leading-tight">{{ $catalogPrices['terigu_unit'] ?? 'Kg' }} / {{ number_format($catalogPrices['terigu_weight'] ?? 1000, 0, ',', '.') }}g (Rp {{ number_format($catalogPrices['terigu_price'] ?? 10000, 0, ',', '.') }})</span>
                        <span class="block text-[9px] font-mono {{ ($catalogPrices['terigu_available'] ?? true) ? 'text-[#5A534A]' : 'text-[#8B231A]' }} mt-1 font-semibold">{{ ($catalogPrices['terigu_available'] ?? true) ? 'Campuran Padat' : 'Stok Habis' }}</span>
                    </button>
                </div>
            </fieldset>

            <!-- ─────────────────────────────────────────────────────────── -->
            <!-- 5. Karakter Rasa & Komposisi (100% Live dari DB Admin!)     -->
            <!-- ─────────────────────────────────────────────────────────── -->
            @php
                $presets = $this->presetFormulas;
                $presetMeta = [
                    'enak' => ['label' => 'Enak', 'dot' => 'bg-[#B84E26]', 'pill' => 'bg-[#B84E26]/10 text-[#B84E26] border-[#B84E26]/20'],
                    'seimbang' => ['label' => 'Seimbang', 'dot' => 'bg-[#D97706]', 'pill' => 'bg-amber-500/10 text-amber-900 border-amber-500/25'],
                    'ekonomis' => ['label' => 'Ekonomis', 'dot' => 'bg-[#2D4235]', 'pill' => 'bg-[#2D4235]/10 text-[#2D4235] border-[#2D4235]/20'],
                ];
            @endphp
            <fieldset>
                <legend class="text-xs font-bold text-[#26221E] uppercase tracking-wider mb-2.5 flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-[#2D4235] text-white text-[11px] font-bold">5</span>
                    Karakter Rasa &amp; Komposisi
                </legend>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach ($presetMeta as $presetCode => $meta)
                        @php
                            $p = $presets[$presetCode];
                        @endphp
                        <button
                            type="button"
                            wire:click="$set('preference', '{{ $presetCode }}')"
                            class="p-4 rounded-2xl border text-left cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-[#D96B43] transition-all duration-200 flex flex-col justify-between {{ $preference === $presetCode ? 'border-[#D96B43] bg-[#FAF7F2] ring-1 ring-[#D96B43]/30 shadow-xs' : 'border-[#26221E]/10 bg-white/90 hover:border-[#26221E]/25 hover:bg-white' }}"
                        >
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-2">
                                    <span class="font-bold text-sm text-[#26221E]">{{ $meta['label'] }}</span>
                                    <span class="w-2.5 h-2.5 rounded-full shrink-0 {{ $meta['dot'] }}"></span>
                                </div>

                                <div class="flex flex-wrap items-center gap-1.5 mb-2">
                                    <span class="inline-block px-2 py-0.5 rounded-md border text-[10.5px] font-mono font-bold {{ $meta['pill'] }}">
                                        {{ $p['flour_pct'] }} Tepung
                                    </span>
                                    <span class="inline-block px-2 py-0.5 rounded-md border text-[10.5px] font-mono font-semibold bg-[#EAE3D5]/75 text-[#26221E] border-[#26221E]/15">
                                        {{ $p['explicit_ratio'] }}
                                    </span>
                                </div>

                                <div class="px-2.5 py-1.5 rounded-lg bg-[#E8ECE4]/70 border border-[#2D4235]/15 text-[11px] font-mono font-semibold text-[#2D4235] mb-2">
                                    {{ $p['per_kg_example'] }}
                                </div>

                                <p class="text-xs text-[#5A534A] leading-relaxed">
                                    {{ $p['description'] }}
                                </p>
                            </div>
                        </button>
                    @endforeach

                    <!-- Kustom Bebas -->
                    <button
                        type="button"
                        wire:click="$set('preference', 'kustom')"
                        class="p-4 rounded-2xl border text-left cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-[#D96B43] transition-all duration-200 flex flex-col justify-between {{ $preference === 'kustom' ? 'border-[#D96B43] bg-[#FAF7F2] ring-1 ring-[#D96B43]/30 shadow-xs' : 'border-[#26221E]/10 bg-white/90 hover:border-[#26221E]/25 hover:bg-white' }}"
                    >
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <span class="font-bold text-sm text-[#26221E]">Kustom Bebas</span>
                                <span class="w-2.5 h-2.5 rounded-full shrink-0 bg-[#2563EB]"></span>
                            </div>

                            <div class="flex flex-wrap items-center gap-1.5 mb-2">
                                <span class="inline-block px-2 py-0.5 rounded-md border text-[10.5px] font-mono font-bold bg-blue-50 text-blue-900 border-blue-200">
                                    10%–450%
                                </span>
                                <span class="inline-block px-2 py-0.5 rounded-md border text-[10.5px] font-mono font-semibold bg-[#EAE3D5]/75 text-[#26221E] border-[#26221E]/15">
                                    Atur Sendiri
                                </span>
                            </div>

                            <div class="px-2.5 py-1.5 rounded-lg bg-blue-50/80 border border-blue-200/80 text-[11px] font-mono font-semibold text-blue-900 mb-2">
                                1 kg daging + 100g s/d 4,5 kg tepung
                            </div>

                            <p class="text-xs text-[#5A534A] leading-relaxed">
                                Bebas atur rasio tepung sendiri — dari bakso super daging (10%) hingga bakso aci / cilok daging (350%–450%).
                            </p>
                        </div>
                    </button>
                </div>

                @if ($preference === 'kustom')
                    @php
                        $customRatioDec = $customFlourRatio / 100;
                        $customPerKgExample = \App\Models\RecipeFormula::formatPerKgExample($customRatioDec);
                        $customExplicitRatio = 'Daging ' . \App\Models\RecipeFormula::formatRatio(1.0, $customRatioDec) . ' Tepung';
                    @endphp
                    <div class="mt-3 p-4 rounded-2xl bg-[#FAF7F2] border border-[#D96B43]/35 space-y-3">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div class="flex items-center gap-2">
                                <label for="custom-flour-input" class="text-xs font-bold text-[#26221E]">
                                    Rasio Tepung Terhadap Daging:
                                </label>
                                <div class="inline-flex items-center rounded-lg border border-[#26221E]/20 bg-white overflow-hidden">
                                    <input
                                        type="number"
                                        id="custom-flour-input"
                                        min="10"
                                        max="450"
                                        step="5"
                                        wire:model.live.debounce.200ms="customFlourRatio"
                                        aria-label="Persentase rasio tepung kustom"
                                        class="w-16 px-2 py-1 text-center font-mono font-extrabold text-sm text-[#B84E26] focus:outline-none"
                                    />
                                    <span class="px-2 py-1 bg-[#EAE3D5]/70 text-xs font-mono font-bold text-[#26221E]">%</span>
                                </div>
                            </div>

                            <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-white border border-[#26221E]/10 text-[11px] text-[#26221E] font-semibold">
                                @if ($customFlourRatio <= 30)
                                    🥩 Super Daging (Bakso Padat Premium)
                                @elseif ($customFlourRatio <= 75)
                                    🥣 Bakso Daging Kenyal (Sedang)
                                @elseif ($customFlourRatio <= 130)
                                    ⚖️ Seimbang Warung (Sekitar 1 : 1)
                                @elseif ($customFlourRatio <= 220)
                                    🥟 Ekonomis Usaha (Sekitar 1 : 2)
                                @else
                                    🍡 Bakso Aci / Cilok Daging (1 : 2,5 s/d 1 : 4,5)
                                @endif
                            </span>
                        </div>

                        <!-- Live Per-1kg Conversion Strip -->
                        <div class="flex flex-wrap items-center justify-between gap-2 px-3 py-2 rounded-xl bg-[#E8ECE4]/75 border border-[#2D4235]/15 text-xs font-mono text-[#2D4235]">
                            <span class="font-semibold">{{ $customPerKgExample }}</span>
                            <span class="font-bold">{{ $customExplicitRatio }}</span>
                        </div>

                        <div>
                            <input
                                type="range"
                                id="custom-flour-slider"
                                aria-label="Geser rasio tepung kustom dari 10% sampai 450%"
                                wire:model.live="customFlourRatio"
                                min="10"
                                max="450"
                                step="5"
                                class="w-full accent-[#D96B43] cursor-pointer"
                            />
                            <div class="flex justify-between text-[10px] text-[#5A534A] mt-1 font-mono">
                                <span>10% (Padat)</span>
                                <span>100% (1:1)</span>
                                <span>200% (1:2)</span>
                                <span>350% (Cilok)</span>
                                <span>450% (1:4,5)</span>
                            </div>
                        </div>

                        <!-- Quick Custom Preset Chips -->
                        <div class="flex flex-wrap items-center gap-1.5 pt-2 border-t border-[#26221E]/10">
                            <span class="text-[10.5px] font-semibold text-[#5A534A] mr-1">Cepat:</span>
                            @foreach ([
                                20 => '20% (5:1)',
                                50 => '50% (2:1)',
                                100 => '100% (1:1)',
                                200 => '200% (1:2)',
                                350 => '350% (Cilok)',
                            ] as $quickPct => $quickLabel)
                                <button
                                    type="button"
                                    wire:click="$set('customFlourRatio', {{ $quickPct }})"
                                    class="px-2.5 py-1 rounded-lg text-[11px] font-mono font-semibold transition-colors cursor-pointer {{ (int) $customFlourRatio === $quickPct ? 'bg-[#2D4235] text-white' : 'bg-white text-[#26221E] border border-[#26221E]/15 hover:bg-[#EAE3D5]/60' }}"
                                >
                                    {{ $quickLabel }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif
            </fieldset>

            <!-- ──────────────────────────────────── -->
            <!-- 6. Jasa Selepan (Penggilingan Pasar) -->
            <!-- ──────────────────────────────────── -->
            <fieldset>
                <legend class="text-xs font-bold text-[#26221E] uppercase tracking-wider mb-2.5 flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-[#2D4235] text-white text-[11px] font-bold">6</span>
                    Jasa Selepan (Penggilingan Pasar)
                </legend>
                <div class="grid grid-cols-2 gap-2.5">
                    <!-- Pakai Jasa -->
                    <button
                        type="button"
                        wire:click="$set('useGrinding', true)"
                        class="p-3 rounded-2xl border text-center cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-[#D96B43] transition-all duration-200 {{ $useGrinding ? 'border-[#D96B43] bg-[#FAF7F2] ring-1 ring-[#D96B43]/30 shadow-xs' : 'border-[#26221E]/10 bg-white/90 hover:border-[#26221E]/25 hover:bg-white' }}"
                    >
                        <span class="block font-bold text-[13px] text-[#26221E]">Pakai Jasa</span>
                        <span class="block text-[11px] text-[#5A534A] mt-0.5">Digiling di mesin selepan pasar</span>
                    </button>

                    <!-- Tanpa Jasa -->
                    <button
                        type="button"
                        wire:click="$set('useGrinding', false)"
                        class="p-3 rounded-2xl border text-center cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-[#D96B43] transition-all duration-200 {{ !$useGrinding ? 'border-[#D96B43] bg-[#FAF7F2] ring-1 ring-[#D96B43]/30 shadow-xs' : 'border-[#26221E]/10 bg-white/90 hover:border-[#26221E]/25 hover:bg-white' }}"
                    >
                        <span class="block font-bold text-[13px] text-[#26221E]">Tanpa Jasa</span>
                        <span class="block text-[11px] text-[#5A534A] mt-0.5">Bahan utuh tanpa digiling</span>
                    </button>
                </div>
            </fieldset>
            @endif

            @if ($startType === 'ingredients_only')
                <!-- ────────────────────────────────────────────────────────────────── -->
                <!-- MODE BELI BAHAN SAJA: Pilih Bebas Bahan Kios (Tanpa Wajib Tepung!) -->
                <!-- ────────────────────────────────────────────────────────────────── -->
                <fieldset x-data="{ catFilter: 'all' }">
                    @php
                        $selectedLooseQty = array_sum($extraItems);
                    @endphp
                    <div class="flex flex-wrap items-center justify-between gap-2 mb-2.5">
                        <legend class="text-xs font-bold text-[#26221E] uppercase tracking-wider flex items-center gap-2">
                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-[#2D4235] text-white text-[11px] font-bold">2</span>
                            Pilih Bahan Kios Bebas (Tidak Wajib Tepung)
                        </legend>
                        @if ($selectedLooseQty > 0)
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded-full bg-[#2D4235] text-[#F4EFE6] text-[10px] font-mono font-bold">
                                    {{ $selectedLooseQty }} dipilih
                                </span>
                                <button
                                    type="button"
                                    wire:click="clearExtras"
                                    class="text-[11px] font-semibold text-[#8B231A] hover:underline cursor-pointer"
                                >
                                    Reset
                                </button>
                            </div>
                        @endif
                    </div>

                    <p class="text-xs text-[#5A534A] mb-3 leading-relaxed">
                        Tekan tombol <strong class="text-[#26221E]">+</strong> pada bahan apa saja yang ingin Anda beli — bisa bumbu/pelengkap saja, telur, atau campur tepung sesuai kebutuhan.
                    </p>

                    <!-- Filter Kategori Cepat -->
                    <div class="flex flex-wrap items-center gap-1.5 mb-3">
                        @foreach ([['all', 'Semua'], ['bumbu', 'Bumbu'], ['pelengkap', 'Telur & Pelengkap'], ['tepung', 'Tepung']] as [$catKey, $catLabel])
                            <button
                                type="button"
                                @click="catFilter = '{{ $catKey }}'"
                                class="px-3 py-1.5 rounded-full text-[11px] font-semibold border transition-colors cursor-pointer"
                                :class="catFilter === '{{ $catKey }}' ? 'bg-[#2D4235] text-[#F4EFE6] border-[#2D4235]' : 'bg-white text-[#5A534A] border-[#26221E]/12 hover:border-[#26221E]/30'"
                            >
                                {{ $catLabel }}
                            </button>
                        @endforeach
                    </div>

                    <!-- Grid Bebas Pilih Semua Bahan Non-Daging -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        @foreach ($this->availableExtraMaterials as $extraMat)
                            @php
                                $extraQty = $extraItems[$extraMat->id] ?? 0;
                                $pkgPrice = (float) ($extraMat->package_price ?? $extraMat->price);
                                $matCatGroup = match ($extraMat->category) {
                                    'tepung' => 'tepung',
                                    'bumbu' => 'bumbu',
                                    default => 'pelengkap',
                                };
                            @endphp
                            <div
                                wire:key="loose-mat-{{ $extraMat->id }}"
                                x-show="catFilter === 'all' || catFilter === '{{ $matCatGroup }}'"
                                class="p-3 rounded-2xl border transition-all flex items-center justify-between gap-2.5 {{ $extraQty > 0 ? 'border-[#2D4235] bg-[#FAF7F2] shadow-xs ring-1 ring-[#2D4235]/20' : 'border-[#26221E]/10 bg-white/90 hover:bg-white' }}"
                            >
                                <div class="grow min-w-0">
                                    <div class="font-bold text-xs text-[#26221E] truncate">{{ $extraMat->name }}</div>
                                    <div class="text-[11px] font-mono text-[#5A534A] mt-0.5">
                                        Rp {{ number_format($pkgPrice, 0, ',', '.') }} <span class="text-[10px]">/ {{ $extraMat->package_unit }} ({{ '@'.number_format($extraMat->package_weight) }}g)</span>
                                    </div>
                                </div>

                                <!-- Stepper Counter -->
                                <div class="flex items-center gap-1.5 shrink-0">
                                    @if ($extraQty > 0)
                                        <button
                                            type="button"
                                            wire:click="decrementExtra({{ $extraMat->id }})"
                                            class="w-7 h-7 rounded-full bg-[#EAE3D5] hover:bg-[#D8E0D3] text-[#26221E] font-bold text-xs flex items-center justify-center transition-colors shadow-2xs cursor-pointer"
                                            title="Kurangi"
                                        >
                                            -
                                        </button>
                                        <span class="font-mono font-bold text-xs text-[#2D4235] px-1 tabular-nums">
                                            {{ $extraQty }}
                                        </span>
                                    @endif
                                    <button
                                        type="button"
                                        wire:click="incrementExtra({{ $extraMat->id }})"
                                        class="w-7 h-7 rounded-full bg-[#2D4235] hover:bg-[#B84E26] text-white font-bold text-xs flex items-center justify-center transition-colors shadow-2xs cursor-pointer"
                                        title="Tambah"
                                    >
                                        +
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </fieldset>
            @else
                <!-- ─────────────────────────────────────────────────────── -->
                <!-- 7. Bahan Tambahan & Pelengkap Kios (Accordion 1-Klik)  -->
                <!-- ─────────────────────────────────────────────────────── -->
                <fieldset
                    x-data="{
                        openExtras: {{ count($extraItems) > 0 ? 'true' : 'false' }},
                        lastCount: {{ count($extraItems) }}
                    }"
                    x-init="$watch('$wire.extraItems', (val) => {
                        const c = val ? Object.keys(val).length : 0;
                        if (c > lastCount) { openExtras = true; }
                        lastCount = c;
                    })"
                    class="pt-4 border-t border-[#26221E]/10"
                >
                    <legend class="sr-only">Bahan Tambahan &amp; Pelengkap Kios (Opsional)</legend>

                    @php
                        $selectedExtraTotalQty = array_sum($extraItems);
                    @endphp

                    <!-- Header Toggle Accordion -->
                    <button
                        type="button"
                        @click="openExtras = !openExtras"
                        :aria-expanded="openExtras"
                        class="w-full flex items-center justify-between gap-3 p-3.5 rounded-2xl border transition-all duration-200 cursor-pointer text-left {{ $selectedExtraTotalQty > 0 ? 'border-[#2D4235]/40 bg-[#FAF7F2] shadow-xs' : 'border-[#26221E]/12 bg-[#FAF7F2]/80 hover:bg-[#FAF7F2] hover:border-[#D96B43]/50' }}"
                    >
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-[#B84E26] text-white text-[11px] font-bold shrink-0">7</span>
                            <div class="min-w-0">
                                <span class="block text-xs font-bold text-[#26221E] uppercase tracking-wider">
                                    Bahan Tambahan &amp; Pelengkap Kios (Opsional)
                                </span>
                                <span class="block text-[11px] text-[#5A534A] mt-0.5 truncate">
                                    Telur, Sobaso, Merica, Masako &mdash; klik untuk pilih pelengkap
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            @if ($selectedExtraTotalQty > 0)
                                <span class="px-2 py-0.5 rounded-full bg-[#2D4235] text-[#F4EFE6] text-[10px] font-mono font-bold">
                                    {{ $selectedExtraTotalQty }} dipilih
                                </span>
                            @else
                                <span class="hidden sm:inline text-[11px] font-semibold text-[#B84E26]" x-text="openExtras ? 'Tutup' : 'Buka'">
                                    Buka
                                </span>
                            @endif
                            <span class="w-7 h-7 rounded-xl bg-white border border-[#26221E]/10 flex items-center justify-center text-[#26221E] transition-transform duration-200" :class="openExtras ? 'rotate-180' : ''">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </span>
                        </div>
                    </button>

                    <!-- Grid Item Pelengkap (Tetap di DOM via x-show agar aman untuk Livewire) -->
                    <div
                        x-show="openExtras"
                        x-transition.opacity.duration.200ms
                        class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-2.5"
                        style="{{ count($extraItems) > 0 ? '' : 'display: none;' }}"
                    >
                        @foreach ($this->availableExtraMaterials as $extraMat)
                            @php
                                $extraQty = $extraItems[$extraMat->id] ?? 0;
                                $pkgPrice = (float) ($extraMat->package_price ?? $extraMat->price);
                            @endphp
                            <div wire:key="extra-mat-{{ $extraMat->id }}" class="p-3 rounded-2xl border transition-all flex items-center justify-between gap-2.5 {{ $extraQty > 0 ? 'border-[#2D4235] bg-[#FAF7F2] shadow-xs' : 'border-[#26221E]/10 bg-white/85 hover:bg-white' }}">
                                <div class="grow min-w-0">
                                    <div class="font-bold text-xs text-[#26221E] truncate">{{ $extraMat->name }}</div>
                                    <div class="text-[11px] font-mono text-[#5A534A] mt-0.5">
                                        Rp {{ number_format($pkgPrice, 0, ',', '.') }} <span class="text-[10px]">/ {{ $extraMat->package_unit }} ({{ '@'.number_format($extraMat->package_weight) }}g)</span>
                                    </div>
                                </div>

                                <!-- Stepper Counter -->
                                <div class="flex items-center gap-1.5 shrink-0">
                                    @if ($extraQty > 0)
                                        <button
                                            type="button"
                                            wire:click="decrementExtra({{ $extraMat->id }})"
                                            class="w-7 h-7 rounded-full bg-[#EAE3D5] hover:bg-[#D8E0D3] text-[#26221E] font-bold text-xs flex items-center justify-center transition-colors shadow-2xs cursor-pointer"
                                            title="Kurangi"
                                        >
                                            -
                                        </button>
                                        <span class="font-mono font-bold text-xs text-[#2D4235] px-1 tabular-nums">
                                            {{ $extraQty }}
                                        </span>
                                    @endif
                                    <button
                                        type="button"
                                        wire:click="incrementExtra({{ $extraMat->id }})"
                                        class="w-7 h-7 rounded-full bg-[#2D4235] hover:bg-[#D96B43] text-white font-bold text-xs flex items-center justify-center transition-colors shadow-2xs cursor-pointer"
                                        title="Tambah"
                                    >
                                        +
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </fieldset>
            @endif

            <!-- Mobile Sticky Bottom Summary Bar (Tampil hanya saat scroll form di HP/Tablet) -->
            @if (!$showCheckoutModal && !$showSaveModal && !$budgetError)
                <div class="lg:hidden sticky bottom-3 z-30 mr-16">
                    <div class="rounded-2xl bg-[#2D4235]/95 backdrop-blur-md text-[#F4EFE6] px-4 py-2.5 shadow-2xl border border-white/15 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <span class="block text-[9.5px] font-mono uppercase tracking-wider text-[#D8E0D3]/80">Uang Belanja Pasar</span>
                            <span class="block text-sm font-extrabold text-white tabular-nums truncate" style="font-family: 'Playfair Display', Georgia, serif;">
                                {{ \Illuminate\Support\Number::currency($totalMarketCost, 'IDR', 'id', precision: 0) }}
                            </span>
                        </div>
                        <a
                            href="#panel-struk-racikan"
                            class="px-3.5 py-2 rounded-xl bg-[#B84E26] hover:bg-[#9E3F1C] text-white text-xs font-bold whitespace-nowrap shadow-xs transition-colors"
                        >
                            Lihat Struk &darr;
                        </a>
                    </div>
                </div>
            @endif

        </div>

        <!-- ═════════════════════════════════════════════════════════ -->
        <!-- KOLOM KANAN: PANEL STRUK LIVE VIEWPORT-FITTED (5 Kolom)   -->
        <!-- ═════════════════════════════════════════════════════════ -->
        <div id="panel-struk-racikan" class="lg:col-span-5 lg:sticky lg:top-[6.75rem] scroll-mt-28">
            <div class="rounded-3xl bg-[#2D4235] text-[#F4EFE6] p-5 shadow-xl border border-[#2D4235]/30 relative flex flex-col lg:max-h-[calc(100vh-7.5rem)] overflow-hidden">

                <!-- ZONA 1 (FIXED TOP): Header Struk + Status + 4 Parameter -->
                <div class="shrink-0">
                    <!-- Header Struk -->
                    <div class="flex items-center justify-between pb-2.5 mb-2.5 border-b border-white/10">
                        <div>
                            <span class="text-[9.5px] font-mono tracking-widest text-[#D8E0D3] uppercase block">
                                RINGKASAN TAKARAN
                            </span>
                            <h2 class="text-lg sm:text-xl font-bold text-white tracking-tight leading-snug" style="font-family: 'Playfair Display', Georgia, serif;">
                                Rincian Belanja Resep
                            </h2>
                        </div>
                        <span class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center text-xs text-[#F4EFE6] shrink-0" aria-hidden="true">
                            🧾
                        </span>
                    </div>

                    <!-- Notice Pill (Required String by Tests) -->
                    @if ($isSimulation)
                        <p class="text-[10.5px] text-[#D8E0D3]/85 leading-snug mb-2.5 bg-white/5 px-2.5 py-1.5 rounded-xl border border-white/10">
                            SIMULASI: Formula resep dan takaran kemasan sedang menunggu konfirmasi toko.
                        </p>
                    @else
                        <div class="text-[10.5px] text-[#D8E0D3]/90 leading-snug mb-2.5 bg-emerald-950/35 px-2.5 py-1.5 rounded-xl border border-emerald-400/25 flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 shrink-0"></span>
                            <span>RESMI: Formula resep terverifikasi langsung oleh kios selepan BASOKU.</span>
                        </div>
                    @endif

                    <!-- Info Parameter Singkat -->
                    <div class="grid grid-cols-4 gap-1.5 py-2 px-2.5 rounded-xl bg-black/15 border border-white/10 text-xs mb-2.5 text-center">
                        <div class="min-w-0">
                            <span class="text-[#D8E0D3]/85 block text-[8.5px] uppercase font-mono">Daging</span>
                            <span class="font-semibold text-white truncate block text-[10.5px]">
                                @if ($startType === 'ingredients_only')
                                    Tanpa Daging
                                @else
                                    {{ ucfirst($meatType) }} ({{ number_format($meatWeight) }}g)
                                @endif
                            </span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[#D8E0D3]/85 block text-[8.5px] uppercase font-mono">
                                {{ $startType === 'ingredients_only' ? 'Belanja' : 'Tepung' }}
                            </span>
                            <span class="font-semibold text-white uppercase truncate block text-[10.5px]">
                                @if ($startType === 'ingredients_only')
                                    {{ count($breakdown) }} Jenis
                                @else
                                    {{ $flourType }}
                                @endif
                            </span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[#D8E0D3]/85 block text-[8.5px] uppercase font-mono">Karakter</span>
                            <span class="font-semibold text-white truncate block text-[10.5px]">
                                @if ($startType === 'ingredients_only')
                                    Bahan Saja
                                @elseif ($preference === 'kustom')
                                    {{ $customFlourRatio }}% Tepung
                                @else
                                    {{ ucfirst($preference) }}
                                @endif
                            </span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[#D8E0D3]/85 block text-[8.5px] uppercase font-mono">Selepan</span>
                            <span class="font-semibold text-white truncate block text-[10.5px]">{{ $useGrinding ? 'Pakai' : 'Tanpa' }}</span>
                        </div>
                    </div>

                    <!-- PERINGATAN JIKA BAHAN INTI SEDANG HABIS DI KIOS -->
                    @if ($availabilityError)
                        <div class="rounded-xl bg-[#8B231A]/35 border border-amber-400/50 p-3 mb-2.5 text-[11px] text-[#F4EFE6] leading-snug flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-amber-300 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                            <div>
                                <span class="font-bold text-amber-200 block">Stok Bahan Sedang Kosong</span>
                                <span>{{ $availabilityError }}</span>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- ZONA 2 (SCROLLABLE MIDDLE): Daftar Bahan & Takaran -->
                <div class="lg:flex-1 lg:min-h-0 lg:overflow-y-auto lg:pr-1 receipt-scrollbar">
                    @if ($budgetError)
                        <div class="rounded-2xl bg-[#8B231A]/30 border border-[#8B231A] p-4 text-sm text-[#F4EFE6] leading-relaxed">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-xl bg-[#8B231A] text-white flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-amber-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-amber-200 text-sm mb-1">Anggaran Belum Mencukupi</h3>
                                    <p class="text-xs text-[#F4EFE6]/80 leading-relaxed">
                                        {{ $budgetError }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @elseif ($startType === 'ingredients_only' && empty($breakdown))
                        <div class="rounded-2xl bg-white/5 border border-dashed border-white/20 p-5 text-center space-y-1.5 my-2">
                            <p class="text-xs font-bold text-white">Belum Ada Bahan Dipilih</p>
                            <p class="text-[11px] text-[#D8E0D3]/85 leading-relaxed">
                                Tekan tombol <strong class="text-white">+</strong> pada bahan kios di sebelah kiri (bumbu, telur, pelengkap, atau tepung). Tidak wajib membeli tepung.
                            </p>
                        </div>
                    @else
                        <!-- Header Tabel Struk Sticky Internal -->
                        <div class="sticky top-0 z-10 bg-[#2D4235] flex items-center justify-between pb-1.5 mb-1 border-b border-white/10 text-[10px] font-semibold uppercase tracking-wider text-[#D8E0D3] font-mono">
                            <span>Bahan &amp; Takaran</span>
                            <span>Subtotal Riil</span>
                        </div>
                        <div class="divide-y divide-white/5">
                            @foreach ($breakdown as $item)
                                <div class="py-1.5 flex items-start justify-between gap-2.5 text-xs">
                                    <div class="grow min-w-0">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="font-medium text-white text-[12px] leading-tight">{{ $item['name'] }}</span>
                                            @if (!empty($item['own_meat']))
                                                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-200 border border-emerald-500/30">Bawa Sendiri</span>
                                            @endif
                                            @if (!empty($item['is_extra']))
                                                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-200 border border-amber-500/30">Pelengkap Kios</span>
                                            @endif
                                        </div>
                                        <div class="text-[10.5px] text-[#D8E0D3]/85 flex flex-wrap items-center gap-1 mt-0.5 font-mono leading-snug">
                                            <span class="tabular-nums font-semibold text-white/95">{{ number_format($item['weight']) }} {{ $item['unit'] }}</span>
                                            @if (!empty($item['package_info']))
                                                <span>&bull;</span>
                                                <span class="text-amber-200 font-medium">{{ $item['package_info'] }}</span>
                                            @endif
                                            @if (isset($item['excess_grams']) && $item['excess_grams'] > 0)
                                                <span class="text-emerald-200 text-[9.5px]">(sisa {{ $item['excess_grams'] }}g dibawa pulang)</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span class="font-semibold text-[12px] tabular-nums font-mono {{ !empty($item['own_meat']) ? 'text-emerald-300' : 'text-white' }}">
                                            {{ \Illuminate\Support\Number::currency($item['market_cost'] ?? $item['cost'], 'IDR', 'id', precision: 0) }}
                                        </span>
                                        @if (isset($item['market_cost']) && isset($item['proportional_cost']) && $item['market_cost'] > $item['proportional_cost'])
                                            <span class="block text-[9.5px] text-[#D8E0D3]/85 tabular-nums font-mono leading-tight">
                                                Resep: {{ \Illuminate\Support\Number::currency($item['proportional_cost'], 'IDR', 'id', precision: 0) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- ZONA 3 (FIXED BOTTOM): 2 Total Sejajar + Tombol Aksi -->
                <div class="shrink-0 pt-3 mt-2 border-t border-dashed border-white/15">
                    @if (!$budgetError)
                        <!-- Dua Total Biaya Berdampingan (Kompak & Selalu Terlihat) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 mb-3">
                            <!-- 1. Biaya Bahan Terpakai (Proporsional Riil Adonan) -->
                            <div class="rounded-2xl bg-white/5 border border-white/10 p-3 flex flex-col justify-between">
                                <div class="flex items-center justify-between gap-1">
                                    <span class="text-[9.5px] font-semibold uppercase tracking-wide text-[#D8E0D3] leading-tight">Biaya Bahan Terpakai</span>
                                    <span class="text-[8.5px] font-mono px-1.5 py-0.5 rounded bg-white/15 text-white shrink-0">Proporsional</span>
                                </div>
                                <p class="text-lg sm:text-xl font-bold text-[#F4EFE6] tabular-nums leading-tight mt-1" style="font-family: 'Playfair Display', Georgia, serif;">
                                    {{ \Illuminate\Support\Number::currency($totalProportionalCost, 'IDR', 'id', precision: 0) }}
                                </p>
                                <p class="text-[10px] text-[#D8E0D3]/85 mt-0.5 leading-tight">Nilai murni masuk adonan</p>
                            </div>

                            <!-- 2. Uang Belanja Pasar (Kemasan Utuh di Toko) -->
                            <div class="rounded-2xl bg-[#D96B43]/25 border-2 border-[#D96B43]/60 p-3 shadow-md flex flex-col justify-between">
                                <div class="flex items-center justify-between gap-1">
                                    <span class="text-[9.5px] font-bold uppercase tracking-wide text-[#FCD7C8] leading-tight">Uang Belanja Pasar</span>
                                    <span class="text-[8.5px] font-mono font-bold px-1.5 py-0.5 rounded bg-[#B84E26] text-white shrink-0">Kemasan Utuh</span>
                                </div>
                                <p class="text-xl sm:text-2xl font-extrabold text-white tabular-nums tracking-tight leading-tight mt-1" style="font-family: 'Playfair Display', Georgia, serif;">
                                    {{ \Illuminate\Support\Number::currency($totalMarketCost, 'IDR', 'id', precision: 0) }}
                                </p>
                                <p class="text-[10px] text-white/90 mt-0.5 leading-tight">Tunai kemasan utuh di pasar</p>
                            </div>
                        </div>

                        @if ($calculationMode === 'budget' && $totalMarketCost > (float) $budget)
                            <p class="text-[10.5px] text-amber-200 bg-amber-500/10 border border-amber-400/25 rounded-xl px-3 py-2 mb-2.5 leading-snug">
                                Catatan anggaran: Nilai adonan terpakai sesuai batas Rp {{ number_format((float) $budget, 0, ',', '.') }}, namun pembelian kemasan utuh di pasar membutuhkan tunai Rp {{ number_format($totalMarketCost, 0, ',', '.') }} (sisa tepung &amp; bumbu dapat dibawa pulang).
                            </p>
                        @endif
                    @endif

                    <!-- Flash Message Berhasil Simpan Racikan -->
                    @if (session()->has('success_save'))
                        <div class="p-2.5 mb-2.5 bg-emerald-500/20 border border-emerald-500/40 text-emerald-200 rounded-xl text-xs font-semibold flex items-center justify-between">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                {{ session('success_save') }}
                            </span>
                            @if (Route::has('customer.recipes'))
                                <a href="{{ route('customer.recipes') }}" class="underline text-emerald-300 font-bold ml-2 shrink-0">Lihat &rarr;</a>
                            @endif
                        </div>
                    @endif

                    <!-- Tombol Aksi: Simpan Racikan & Pesan via WhatsApp -->
                    @php
                        $cannotOrder = empty($breakdown) || $budgetError || $availabilityError;
                    @endphp
                    <div class="flex flex-col sm:flex-row gap-2.5">
                        <!-- Tombol Simpan Racikan -->
                        <button
                            type="button"
                            wire:click="openSaveModal"
                            wire:loading.attr="disabled"
                            @disabled($cannotOrder)
                            class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-white/25 hover:border-white/50 bg-white/10 hover:bg-white/15 text-white font-medium text-xs sm:text-sm transition-all duration-200 active:scale-[0.99] cursor-pointer disabled:opacity-45 disabled:cursor-not-allowed"
                        >
                            <svg class="w-4 h-4 text-amber-300 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                            </svg>
                            <span>Simpan Racikan</span>
                        </button>

                        <!-- Tombol Pesan via WhatsApp -->
                        @if ($cannotOrder)
                            <button
                                type="button"
                                disabled
                                aria-disabled="true"
                                class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-[#25D366]/40 text-white font-medium text-xs sm:text-sm shadow-none cursor-not-allowed opacity-50"
                            >
                                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                                </svg>
                                <span>Pesan via WA</span>
                            </button>
                        @else
                            <button
                                type="button"
                                wire:click="openCheckout"
                                wire:loading.attr="disabled"
                                class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-[#15803D] hover:bg-[#166534] text-white font-semibold text-xs sm:text-sm shadow-md transition-all duration-200 active:scale-[0.99] cursor-pointer disabled:opacity-50"
                            >
                                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                                </svg>
                                <span>Pesan via WA</span>
                            </button>
                        @endif
                    </div>
                </div>

            </div>
        </div>

    </div>

    <!-- ═══════════════════════════════════════════ -->
    <!-- MODAL CHECKOUT & PENGAMBILAN PESANAN        -->
    <!-- ═══════════════════════════════════════════ -->
    @if ($showCheckoutModal)
        <div 
            class="fixed inset-0 z-50 bg-[#26221E]/80 backdrop-blur-md flex items-center justify-center p-4 transition-opacity duration-300"
            role="dialog"
            aria-modal="true"
            aria-labelledby="modal-checkout-title"
            @keydown.escape.window="$wire.closeCheckout()"
        >
            <div
                class="bg-[#FAF7F2] rounded-3xl w-full max-w-md p-6 sm:p-8 shadow-2xl relative border border-[#26221E]/10"
            >
                <!-- Tombol Tutup 'X' -->
                <button
                    type="button"
                    wire:click="closeCheckout"
                    class="absolute top-5 right-5 w-9 h-9 rounded-full bg-black/5 hover:bg-black/10 text-[#26221E]/60 hover:text-[#26221E] flex items-center justify-center transition-colors cursor-pointer"
                    aria-label="Tutup Modal"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Judul Modal -->
                <div class="mb-5">
                    <p class="text-[#D96B43] font-bold tracking-widest text-xs uppercase mb-1 font-mono">Konfirmasi Pesanan</p>
                    <h3 id="modal-checkout-title" class="text-[#26221E] text-2xl font-bold leading-tight" style="font-family: 'Playfair Display', Georgia, serif;">
                        Data Pengambilan
                    </h3>
                    <p class="text-xs text-[#5A534A] mt-1">
                        Lengkapi nama dan jadwal ke pasar sebelum mengirim pesanan ke WhatsApp penjual.
                    </p>
                </div>

                <!-- Ringkasan Singkat Pesanan & Info Titik Kios -->
                <div class="p-4 rounded-2xl bg-white border border-[#26221E]/10 mb-5 text-xs space-y-2 shadow-xs">
                    <div class="flex justify-between text-[#5A534A]">
                        <span>Racikan:</span>
                        <span class="font-bold text-[#26221E] text-right">
                            @if ($startType === 'ingredients_only')
                                Belanja Bahan Kios ({{ count($breakdown) }} jenis item)
                            @else
                                {{ ucfirst($meatType) }} ({{ number_format($meatWeight) }} g)
                            @endif
                            @if (!empty($extraItems))
                                <span class="text-[11px] text-[#D96B43] font-mono font-medium block">+ {{ count($extraItems) }} bahan pelengkap</span>
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between text-[#5A534A]">
                        <span>Total Belanja Kemasan Pasar:</span>
                        <span class="font-bold text-[#D96B43] text-sm tabular-nums">{{ \Illuminate\Support\Number::currency($totalMarketCost, 'IDR', 'id', precision: 0) }}</span>
                    </div>
                    <div class="flex justify-between text-[11px] text-[#5A534A]/80">
                        <span>Biaya Bahan Masuk Adonan:</span>
                        <span class="font-mono">{{ \Illuminate\Support\Number::currency($totalProportionalCost, 'IDR', 'id', precision: 0) }}</span>
                    </div>
                    <div class="pt-2 border-t border-[#26221E]/10 flex flex-col sm:flex-row sm:items-center justify-between gap-1 text-[11px] text-[#5A534A]">
                        <span>Lokasi Pengambilan: <strong class="text-[#26221E] font-mono">Titik Kios 7°59'31.5"S 113°14'58.9"E</strong></span>
                        <a href="https://www.google.com/maps/search/?api=1&query=-7.992083,113.249694" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 text-[#8B231A] hover:text-[#D96B43] font-bold underline underline-offset-2">
                            <span>Lihat Peta</span>
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                        </a>
                    </div>
                </div>

                <!-- Formulir Checkout -->
                <form wire:submit.prevent="processCheckout" class="space-y-4">
                    <!-- Field: Nama Lengkap -->
                    <div>
                        <label for="customerName" class="block text-xs font-bold uppercase tracking-wider text-[#26221E] mb-1.5">
                            Nama Lengkap <span class="text-[#D96B43]">*</span>
                        </label>
                        <input
                            type="text"
                            id="customerName"
                            wire:model="customerName"
                            placeholder="Contoh: Budi Santoso"
                            class="w-full rounded-2xl border-2 border-[#26221E]/15 bg-white px-4 py-3 text-sm font-medium text-[#26221E] placeholder:text-[#26221E]/30 focus:outline-none focus:border-[#D96B43] focus:ring-2 focus:ring-[#D96B43]/15 transition-all duration-200"
                        >
                        @error('customerName')
                            <p class="text-[11px] text-[#D96B43] mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Field: Nomor WhatsApp Pembeli -->
                    <div>
                        <label for="customerPhone" class="block text-xs font-bold uppercase tracking-wider text-[#26221E] mb-1.5">
                            Nomor WhatsApp Pembeli <span class="text-[#D96B43]">*</span>
                        </label>
                        <input
                            type="tel"
                            id="customerPhone"
                            wire:model="customerPhone"
                            placeholder="Contoh: 08123456789 atau 628123456789"
                            class="w-full rounded-2xl border-2 border-[#26221E]/15 bg-white px-4 py-3 text-sm font-medium text-[#26221E] placeholder:text-[#26221E]/30 focus:outline-none focus:border-[#D96B43] focus:ring-2 focus:ring-[#D96B43]/15 transition-all duration-200"
                        >
                        @error('customerPhone')
                            <p class="text-[11px] text-[#D96B43] mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                        <p class="text-[11px] text-[#5A534A] mt-1">
                            Pihak kios akan menghubungi nomor ini jika ada konfirmasi ketersediaan bahan.
                        </p>
                    </div>

                    <!-- Field: Jam Pengambilan -->
                    <div>
                        <label for="pickupTime" class="block text-xs font-bold uppercase tracking-wider text-[#26221E] mb-1.5">
                            Jam Pengambilan ke Pasar <span class="text-[#D96B43]">*</span>
                        </label>
                        <input
                            type="datetime-local"
                            id="pickupTime"
                            wire:model="pickupTime"
                            class="w-full rounded-2xl border-2 border-[#26221E]/15 bg-white px-4 py-3 text-sm font-medium text-[#26221E] focus:outline-none focus:border-[#D96B43] focus:ring-2 focus:ring-[#D96B43]/15 transition-all duration-200"
                        >
                        @error('pickupTime')
                            <p class="text-[11px] text-[#D96B43] mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                        <p class="text-[11px] text-[#5A534A] mt-1">
                            Kios buka pkl {{ sprintf('%02d.00–%02d.00 WIB', config('services.kiosk.open_hour', 4), config('services.kiosk.close_hour', 12)) }}. Pengambilan minimal 1 jam setelah pemesanan agar racikan siap giling saat Anda tiba.
                        </p>
                    </div>

                    <!-- Tombol Konfirmasi -->
                    <div class="pt-2">
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="processCheckout"
                            class="w-full py-4 px-6 rounded-2xl bg-[#25D366] hover:bg-[#1DA851] text-white font-bold text-sm shadow transition-all duration-200 active:scale-[0.99] cursor-pointer flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed"
                        >
                            <svg wire:loading.remove wire:target="processCheckout" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                            </svg>
                            <span wire:loading.remove wire:target="processCheckout">Konfirmasi & Kirim WA</span>
                            <span wire:loading wire:target="processCheckout" class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Menyimpan Pesanan...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ═══════════════════════════════════════════ -->
    <!-- MODAL SIMPAN RACIKAN RESEP SAYA             -->
    <!-- ═══════════════════════════════════════════ -->
    @if ($showSaveModal)
        <div 
            class="fixed inset-0 z-50 bg-[#26221E]/80 backdrop-blur-md flex items-center justify-center p-4 transition-opacity duration-300"
            role="dialog"
            aria-modal="true"
            aria-labelledby="modal-save-title"
            @keydown.escape.window="$wire.closeSaveModal()"
        >
            <div
                class="bg-[#FAF7F2] rounded-3xl w-full max-w-md p-6 sm:p-8 shadow-2xl relative border border-[#26221E]/10"
            >
                <!-- Tombol Tutup 'X' -->
                <button
                    type="button"
                    wire:click="closeSaveModal"
                    class="absolute top-5 right-5 w-9 h-9 rounded-full bg-black/5 hover:bg-black/10 text-[#26221E]/60 hover:text-[#26221E] flex items-center justify-center transition-colors cursor-pointer"
                    aria-label="Tutup Modal"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Judul Modal -->
                <div class="mb-5">
                    <p class="text-[#D96B43] font-bold tracking-widest text-xs uppercase mb-1 font-mono">Buku Resep Pribadi</p>
                    <h3 id="modal-save-title" class="text-[#26221E] text-2xl font-bold leading-tight" style="font-family: 'Playfair Display', Georgia, serif;">
                        Simpan Racikan
                    </h3>
                    <p class="text-xs text-[#5A534A] mt-1">
                        Beri nama racikan ini untuk disimpan ke koleksi resep pribadi Anda.
                    </p>
                </div>

                <!-- Formulir Simpan Racikan -->
                <form wire:submit.prevent="saveRecipe" class="space-y-4">
                    <div>
                        <label for="recipeName" class="block text-xs font-bold uppercase tracking-wider text-[#26221E] mb-1.5">
                            Nama Racikan <span class="text-[#D96B43]">*</span>
                        </label>
                        <input
                            type="text"
                            id="recipeName"
                            wire:model="recipeName"
                            placeholder="Contoh: Bakso Urat Spesial Lebaran"
                            class="w-full rounded-2xl border-2 border-[#26221E]/15 bg-white px-4 py-3 text-sm font-medium text-[#26221E] placeholder:text-[#26221E]/30 focus:outline-none focus:border-[#D96B43] focus:ring-2 focus:ring-[#D96B43]/15 transition-all duration-200"
                        >
                        @error('recipeName')
                            <p class="text-[11px] text-[#D96B43] mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Ringkasan Takaran -->
                    <div class="p-4 rounded-2xl bg-white border border-[#26221E]/10 text-xs space-y-1 shadow-xs">
                        <div class="flex justify-between text-[#5A534A]">
                            <span>Karakter & Bahan:</span>
                            <span class="font-bold text-[#26221E]">{{ ucfirst($meatType) }} &bull; {{ ucfirst($preference) }} ({{ number_format($meatWeight) }}g)</span>
                        </div>
                        <div class="flex justify-between text-[#5A534A]">
                            <span>Estimasi Nilai Bahan:</span>
                            <span class="font-bold text-[#D96B43] tabular-nums">{{ \Illuminate\Support\Number::currency($totalProportionalCost, 'IDR', 'id', precision: 0) }}</span>
                        </div>
                    </div>

                    <div class="pt-2 flex items-center justify-end gap-3">
                        <button
                            type="button"
                            wire:click="closeSaveModal"
                            class="px-5 py-2.5 rounded-xl border border-[#26221E]/20 hover:bg-black/5 text-[#26221E] font-semibold text-xs transition-colors cursor-pointer"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="saveRecipe"
                            class="px-6 py-2.5 rounded-xl bg-[#D96B43] hover:bg-[#C25932] text-white font-bold text-xs shadow transition-all active:scale-[0.99] cursor-pointer flex items-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed"
                        >
                            <span wire:loading.remove wire:target="saveRecipe">Simpan ke Resep Saya</span>
                            <span wire:loading wire:target="saveRecipe" class="flex items-center gap-1.5">
                                <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Menyimpan...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
