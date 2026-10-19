<div class="w-full">

    <!-- Atmospheric Editorial Header Card (Kinfolk / Architectural Style) -->
    <div class="rounded-[2.5rem] bg-[#FAF7F2] text-[#26221E] p-8 sm:p-12 mb-10 shadow-sm border border-[#26221E]/10 relative overflow-hidden">
        <!-- Subtle Giant Watermark -->
        <div class="absolute -right-6 -bottom-6 editorial-watermark select-none pointer-events-none opacity-25 tracking-tighter" aria-hidden="true">
            SELEPAN
        </div>
        <div class="relative z-10 max-w-2xl">
            <div class="flex items-center gap-2 mb-3">
                <span class="w-8 h-[2px] bg-[#D96B43]"></span>
                <span class="text-xs font-mono font-bold tracking-[0.2em] uppercase text-[#D96B43]">
                    Kalkulator Racikan &bull; Tradisi Pasar
                </span>
            </div>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-[#26221E] leading-tight" style="font-family: 'Playfair Display', Georgia, serif;">
                Rancang Racikanmu
            </h2>
            <p class="mt-3 text-[#5A534A] text-sm sm:text-base leading-relaxed font-normal">
                Pilih cara kalkulasi yang Anda inginkan: atur berat daging yang ingin diolah, atau tentukan anggaran belanja Anda untuk hasil porsi maksimal.
            </p>
        </div>
    </div>

    <!-- Grid Utama: Form Input (Kiri) + Panel Estimasi Struk (Kanan) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-start">

        <!-- ═══════════════════════════════════════════ -->
        <!-- KOLOM KIRI: FORMULIR INPUT (7 Kolom)       -->
        <!-- ═══════════════════════════════════════════ -->
        <div class="lg:col-span-7 space-y-8">

            <!-- ──────────────────────────────────── -->
            <!-- Tab Mode Perhitungan                 -->
            <!-- ──────────────────────────────────── -->
            <div class="p-1.5 rounded-full bg-[#FAF7F2] border border-[#26221E]/10 flex items-center gap-2 shadow-xs">
                <button
                    type="button"
                    wire:click="$set('calculationMode', 'weight')"
                    class="flex-1 py-3 px-5 rounded-full text-xs sm:text-sm font-semibold transition-all duration-200 cursor-pointer flex items-center justify-center gap-2 focus:outline-none {{ $calculationMode === 'weight' ? 'bg-[#2D4235] text-white shadow-sm' : 'text-[#26221E]/70 hover:text-[#26221E] hover:bg-black/5' }}"
                >
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75M18.75 4.97A48.416 48.416 0 0012 4.5c-2.291 0-4.545.16-6.75.47m13.5 0c1.01.143 2.01.317 3 .52m-16.5-.52c-1.01.143-2.01.317-3 .52m16.5 0a48.72 48.72 0 01-16.5 0" />
                    </svg>
                    <span>Hitung Berdasarkan Berat</span>
                </button>
                <button
                    type="button"
                    wire:click="$set('calculationMode', 'budget')"
                    class="flex-1 py-3 px-5 rounded-full text-xs sm:text-sm font-semibold transition-all duration-200 cursor-pointer flex items-center justify-center gap-2 focus:outline-none {{ $calculationMode === 'budget' ? 'bg-[#2D4235] text-white shadow-sm' : 'text-[#26221E]/70 hover:text-[#26221E] hover:bg-black/5' }}"
                >
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6H2.25m0 0v1.5m0-1.5h1.5m0 0h16.5m-16.5 0a.75.75 0 01-.75-.75V3m0 3h1.5m16.5 0a.75.75 0 00.75-.75V3m0 3v1.5m0-1.5h-1.5m0 0H3.75" />
                    </svg>
                    <span>Sesuaikan Anggaran</span>
                </button>
            </div>

            <!-- ──────────────────────────────────── -->
            <!-- 1. Mulai dari Mana?                  -->
            <!-- ──────────────────────────────────── -->
            <fieldset>
                <legend class="text-xs font-bold text-[#26221E] uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-[#2D4235] text-white text-[11px] font-bold">1</span>
                    Mulai dari Mana?
                </legend>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Opsi: Mulai dari Nol -->
                    <button
                        type="button"
                        wire:click="$set('startType', 'start_from_zero')"
                        class="text-left w-full rounded-3xl border-2 p-5 cursor-pointer focus:outline-none transition-all duration-200 {{ $startType === 'start_from_zero' ? 'border-[#D96B43] bg-[#FAF7F2] shadow-sm ring-1 ring-[#D96B43]/20' : 'border-[#26221E]/10 bg-white/90 hover:border-[#26221E]/25 hover:bg-white' }}"
                    >
                        <div class="flex items-start gap-3.5">
                            <div class="w-10 h-10 rounded-2xl flex items-center justify-center shrink-0 transition-colors {{ $startType === 'start_from_zero' ? 'bg-[#D96B43] text-white' : 'bg-[#26221E]/5 text-[#26221E]/60' }}">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                                </svg>
                            </div>
                            <div>
                                <span class="block text-sm font-bold text-[#26221E]">Mulai dari Nol</span>
                                <span class="block text-xs text-[#5A534A] mt-1 leading-relaxed">Belum punya bahan apa-apa. Hitung semua kebutuhan belanja daging dan bumbu dari awal.</span>
                            </div>
                        </div>
                    </button>

                    <!-- Opsi: Bawa Daging Sendiri -->
                    <button
                        type="button"
                        wire:click="$set('startType', 'own_meat')"
                        class="text-left w-full rounded-3xl border-2 p-5 cursor-pointer focus:outline-none transition-all duration-200 {{ $startType === 'own_meat' ? 'border-[#D96B43] bg-[#FAF7F2] shadow-sm ring-1 ring-[#D96B43]/20' : 'border-[#26221E]/10 bg-white/90 hover:border-[#26221E]/25 hover:bg-white' }}"
                    >
                        <div class="flex items-start gap-3.5">
                            <div class="w-10 h-10 rounded-2xl flex items-center justify-center shrink-0 transition-colors {{ $startType === 'own_meat' ? 'bg-[#D96B43] text-white' : 'bg-[#26221E]/5 text-[#26221E]/60' }}">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                </svg>
                            </div>
                            <div>
                                <span class="block text-sm font-bold text-[#26221E]">Bawa Daging Sendiri</span>
                                <span class="block text-xs text-[#5A534A] mt-1 leading-relaxed">Sudah punya daging di rumah. Hitung kebutuhan bumbu tambahan dan selepan saja.</span>
                            </div>
                        </div>
                    </button>
                </div>
            </fieldset>

            <!-- ──────────────────────────────────── -->
            <!-- 2. Pilih Daging                      -->
            <!-- ──────────────────────────────────── -->
            <fieldset>
                <legend class="text-xs font-bold text-[#26221E] uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-[#2D4235] text-white text-[11px] font-bold">2</span>
                    Pilih Daging
                </legend>
                <div class="grid grid-cols-2 gap-4">
                    <!-- Daging Sapi -->
                    <button
                        type="button"
                        wire:click="$set('meatType', 'sapi')"
                        class="p-5 rounded-3xl border-2 text-center cursor-pointer transition-all duration-200 {{ $meatType === 'sapi' ? 'border-[#D96B43] bg-[#FAF7F2] shadow-sm ring-1 ring-[#D96B43]/20' : 'border-[#26221E]/10 bg-white/90 hover:border-[#26221E]/25 hover:bg-white' }}"
                    >
                        <div class="w-10 h-10 mx-auto mb-2 rounded-xl bg-[#2D4235] text-[#F4EFE6] flex items-center justify-center font-mono font-bold text-xs">
                            SP
                        </div>
                        <span class="block font-bold text-sm text-[#26221E]">Daging Sapi</span>
                        <span class="block text-[11px] text-[#5A534A] mt-0.5">Sapi segar paha / gandik</span>
                    </button>

                    <!-- Daging Ayam -->
                    <button
                        type="button"
                        wire:click="$set('meatType', 'ayam')"
                        class="p-5 rounded-3xl border-2 text-center cursor-pointer transition-all duration-200 {{ $meatType === 'ayam' ? 'border-[#D96B43] bg-[#FAF7F2] shadow-sm ring-1 ring-[#D96B43]/20' : 'border-[#26221E]/10 bg-white/90 hover:border-[#26221E]/25 hover:bg-white' }}"
                    >
                        <div class="w-10 h-10 mx-auto mb-2 rounded-xl bg-[#2D4235] text-[#F4EFE6] flex items-center justify-center font-mono font-bold text-xs">
                            AY
                        </div>
                        <span class="block font-bold text-sm text-[#26221E]">Daging Ayam</span>
                        <span class="block text-[11px] text-[#5A534A] mt-0.5">Dada ayam fillet bersih</span>
                    </button>
                </div>
            </fieldset>

            <!-- ──────────────────────────────────── -->
            <!-- 3. Atur Berat / Anggaran             -->
            <!-- ──────────────────────────────────── -->
            @if ($calculationMode === 'weight')
                <fieldset class="space-y-4">
                    <legend class="text-xs font-bold text-[#26221E] uppercase tracking-wider mb-2 flex items-center gap-2">
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-[#2D4235] text-white text-[11px] font-bold">3</span>
                        Berat Daging (Gram)
                    </legend>
                    
                    <div class="bg-white/90 rounded-3xl border border-[#26221E]/10 p-5 shadow-xs">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-xs text-[#5A534A]">Geser atau pilih takaran cepat:</span>
                            <span class="text-xl font-mono font-bold text-[#D96B43] tabular-nums">
                                {{ number_format($meatWeight) }} g
                            </span>
                        </div>

                        <!-- Slider -->
                        <input
                            type="range"
                            min="500"
                            max="5000"
                            step="50"
                            wire:model.live.debounce.150ms="meatWeight"
                            class="w-full h-2 bg-[#E8ECE4] rounded-lg appearance-none cursor-pointer accent-[#D96B43]"
                        >

                        <!-- Preset Buttons -->
                        <div class="grid grid-cols-4 gap-2 mt-4 pt-4 border-t border-[#26221E]/10">
                            @foreach ([500, 1000, 2000, 3000] as $preset)
                                <button
                                    type="button"
                                    wire:click="$set('meatWeight', {{ $preset }})"
                                    class="py-1.5 px-2 rounded-xl text-xs font-mono font-semibold transition-colors cursor-pointer {{ $meatWeight == $preset ? 'bg-[#2D4235] text-white' : 'bg-[#FAF7F2] text-[#26221E] hover:bg-black/5' }}"
                                >
                                    {{ $preset >= 1000 ? ($preset / 1000) . ' kg' : $preset . ' g' }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </fieldset>
            @else
                <fieldset class="space-y-4">
                    <legend class="text-xs font-bold text-[#26221E] uppercase tracking-wider mb-2 flex items-center gap-2">
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-[#2D4235] text-white text-[11px] font-bold">3</span>
                        Batas Anggaran Belanja (Rp)
                    </legend>

                    <div class="bg-white/90 rounded-3xl border border-[#26221E]/10 p-5 shadow-xs">
                        <label for="budgetInput" class="block text-xs text-[#5A534A] mb-2">
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
                                class="w-full rounded-2xl border-2 border-[#26221E]/15 bg-white pl-12 pr-4 py-3 text-base font-bold text-[#26221E] focus:outline-none focus:border-[#D96B43] focus:ring-2 focus:ring-[#D96B43]/20"
                            >
                        </div>

                        <!-- Budget Quick Presets -->
                        <div class="grid grid-cols-4 gap-2 mt-4 pt-4 border-t border-[#26221E]/10">
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
            <!-- 4. Karakter Rasa & Komposisi         -->
            <!-- ──────────────────────────────────── -->
            <fieldset>
                <legend class="text-xs font-bold text-[#26221E] uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-[#2D4235] text-white text-[11px] font-bold">4</span>
                    Karakter Rasa & Komposisi
                </legend>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    
                    <!-- Enak -->
                    <button
                        type="button"
                        wire:click="$set('preference', 'enak')"
                        class="p-4 rounded-3xl border-2 text-left cursor-pointer transition-all duration-200 {{ $preference === 'enak' ? 'border-[#D96B43] bg-[#FAF7F2] shadow-sm ring-1 ring-[#D96B43]/20' : 'border-[#26221E]/10 bg-white/90 hover:border-[#26221E]/25 hover:bg-white' }}"
                    >
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-sm text-[#26221E]">Enak</span>
                            <span class="w-2.5 h-2.5 rounded-full bg-[#D96B43]"></span>
                        </div>
                        <p class="text-[11px] text-[#5A534A] leading-relaxed">
                            Daging dominan, tekstur urat mantap, gurih kaldu sapi pekat.
                        </p>
                    </button>

                    <!-- Seimbang -->
                    <button
                        type="button"
                        wire:click="$set('preference', 'seimbang')"
                        class="p-4 rounded-3xl border-2 text-left cursor-pointer transition-all duration-200 {{ $preference === 'seimbang' ? 'border-[#D96B43] bg-[#FAF7F2] shadow-sm ring-1 ring-[#D96B43]/20' : 'border-[#26221E]/10 bg-white/90 hover:border-[#26221E]/25 hover:bg-white' }}"
                    >
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-sm text-[#26221E]">Seimbang</span>
                            <span class="w-2.5 h-2.5 rounded-full bg-[#E5A93C]"></span>
                        </div>
                        <p class="text-[11px] text-[#5A534A] leading-relaxed">
                            Kenyal pas, renyah gurih favorit hidangan keluarga.
                        </p>
                    </button>

                    <!-- Ekonomis -->
                    <button
                        type="button"
                        wire:click="$set('preference', 'ekonomis')"
                        class="p-4 rounded-3xl border-2 text-left cursor-pointer transition-all duration-200 {{ $preference === 'ekonomis' ? 'border-[#D96B43] bg-[#FAF7F2] shadow-sm ring-1 ring-[#D96B43]/20' : 'border-[#26221E]/10 bg-white/90 hover:border-[#26221E]/25 hover:bg-white' }}"
                    >
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-sm text-[#26221E]">Ekonomis</span>
                            <span class="w-2.5 h-2.5 rounded-full bg-[#2D4235]"></span>
                        </div>
                        <p class="text-[11px] text-[#5A534A] leading-relaxed">
                            Porsi melimpah untuk hajatan besar & modal usaha.
                        </p>
                    </button>

                </div>
            </fieldset>

            <!-- ──────────────────────────────────── -->
            <!-- 5. Jasa Selepan (Penggilingan Pasar) -->
            <!-- ──────────────────────────────────── -->
            <fieldset>
                <legend class="text-xs font-bold text-[#26221E] uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-[#2D4235] text-white text-[11px] font-bold">5</span>
                    Jasa Selepan (Penggilingan Pasar)
                </legend>
                <div class="grid grid-cols-2 gap-4">
                    <!-- Pakai Jasa -->
                    <button
                        type="button"
                        wire:click="$set('useGrinding', true)"
                        class="p-4 rounded-3xl border-2 text-center cursor-pointer transition-all duration-200 {{ $useGrinding ? 'border-[#D96B43] bg-[#FAF7F2] shadow-sm ring-1 ring-[#D96B43]/20' : 'border-[#26221E]/10 bg-white/90 hover:border-[#26221E]/25 hover:bg-white' }}"
                    >
                        <span class="block font-bold text-sm text-[#26221E]">Pakai Jasa</span>
                        <span class="block text-[11px] text-[#5A534A] mt-0.5">Digiling di mesin selepan pasar</span>
                    </button>

                    <!-- Tanpa Jasa -->
                    <button
                        type="button"
                        wire:click="$set('useGrinding', false)"
                        class="p-4 rounded-3xl border-2 text-center cursor-pointer transition-all duration-200 {{ !$useGrinding ? 'border-[#D96B43] bg-[#FAF7F2] shadow-sm ring-1 ring-[#D96B43]/20' : 'border-[#26221E]/10 bg-white/90 hover:border-[#26221E]/25 hover:bg-white' }}"
                    >
                        <span class="block font-bold text-sm text-[#26221E]">Tanpa Jasa</span>
                        <span class="block text-[11px] text-[#5A534A] mt-0.5">Bahan utuh tanpa digiling</span>
                    </button>
                </div>
            </fieldset>

        </div>

        <!-- ═══════════════════════════════════════════ -->
        <!-- KOLOM KANAN: PANEL STRUK ESTIMASI (5 Kolom) -->
        <!-- ═══════════════════════════════════════════ -->
        <div class="lg:col-span-5 sticky top-28">
            <div class="rounded-[2.5rem] bg-[#2D4235] text-[#F4EFE6] p-6 sm:p-8 shadow-xl border border-[#2D4235]/30 relative overflow-hidden">
                
                <div class="relative z-10">
                    
                    <!-- Header Struk -->
                    <div class="flex items-center justify-between pb-4 mb-4 border-b border-white/10">
                        <div>
                            <span class="text-[10px] font-mono tracking-widest text-[#D8E0D3] uppercase block">
                                RINGKASAN TAKARAN
                            </span>
                            <h3 class="text-xl sm:text-2xl font-bold text-white tracking-tight" style="font-family: 'Playfair Display', Georgia, serif;">
                                Rincian Belanja Resep
                            </h3>
                        </div>
                        <span class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center text-xs text-[#F4EFE6]">
                            🧾
                        </span>
                    </div>

                    <!-- Notice Pill (Required String by Tests) -->
                    @if ($isSimulation)
                        <p class="text-[11px] text-[#D8E0D3]/80 leading-relaxed mb-5 bg-white/5 p-3 rounded-2xl border border-white/10">
                            SIMULASI: Formula resep dan takaran kemasan sedang menunggu konfirmasi toko.
                        </p>
                    @else
                        <div class="text-[11px] text-[#D8E0D3]/90 leading-relaxed mb-5 bg-[#2D4235]/40 p-3 rounded-2xl border border-[#2D4235] flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                            <span>RESMI: Formula resep terverifikasi langsung oleh kios selepan BASOKU.</span>
                        </div>
                    @endif

                    <!-- Info Parameter Singkat -->
                    <div class="grid grid-cols-3 gap-2 p-3 rounded-2xl bg-black/15 border border-white/10 text-xs mb-6 text-center">
                        <div>
                            <span class="text-[#D8E0D3]/60 block text-[10px] uppercase font-mono">Daging</span>
                            <span class="font-semibold text-white truncate block">
                                {{ ucfirst($meatType) }} ({{ number_format($meatWeight) }} g)
                            </span>
                        </div>
                        <div>
                            <span class="text-[#D8E0D3]/60 block text-[10px] uppercase font-mono">Karakter</span>
                            <span class="font-semibold text-white">{{ ucfirst($preference) }}</span>
                        </div>
                        <div>
                            <span class="text-[#D8E0D3]/60 block text-[10px] uppercase font-mono">Jasa Selepan</span>
                            <span class="font-semibold text-white">{{ $useGrinding ? 'Pakai Jasa' : 'Tanpa Jasa' }}</span>
                        </div>
                    </div>

                    <!-- KONTEN: JIKA ADA BUDGET ERROR TAMPILKAN KOTAK ERROR HANGAT -->
                    @if ($budgetError)
                        <div class="rounded-2xl bg-[#8B231A]/30 border border-[#8B231A] p-5 mb-6 text-sm text-[#F4EFE6] leading-relaxed">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-xl bg-[#8B231A] text-white flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-amber-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-amber-200 text-sm mb-1">Anggaran Belum Mencukupi</h4>
                                    <p class="text-xs text-[#F4EFE6]/80 leading-relaxed">
                                        {{ $budgetError }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Rincian Bahan Gaya Struk Rapi ($breakdown) -->
                        <div class="mb-6">
                            <div class="flex items-center justify-between pb-2 mb-2 border-b border-white/10 text-[11px] font-semibold uppercase tracking-wider text-[#D8E0D3]/70 font-mono">
                                <span>Bahan & Takaran</span>
                                <span>Subtotal Riil</span>
                            </div>
                            <div class="divide-y divide-white/5 space-y-2">
                                @foreach ($breakdown as $item)
                                    <div class="pt-2 first:pt-0 flex items-start justify-between gap-3 text-xs">
                                        <div class="grow">
                                            <div class="flex items-center gap-2">
                                                <span class="font-medium text-white">{{ $item['name'] }}</span>
                                                @if (!empty($item['own_meat']))
                                                    <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Bawa Sendiri</span>
                                                @endif
                                            </div>
                                            <div class="text-[11px] text-[#D8E0D3]/60 flex items-center gap-1.5 mt-0.5 font-mono">
                                                <span class="tabular-nums">{{ number_format($item['weight']) }} {{ $item['unit'] }}</span>
                                                <span>&bull;</span>
                                                <span class="text-[10px] text-[#D8E0D3]/50">{{ $item['note'] }}</span>
                                            </div>
                                        </div>
                                        <div class="text-right shrink-0">
                                            <span class="font-semibold text-xs tabular-nums font-mono {{ !empty($item['own_meat']) ? 'text-emerald-400' : 'text-white' }}">
                                                {{ \Illuminate\Support\Number::currency($item['cost'] ?? $item['proportional_cost'], 'IDR', 'id', precision: 0) }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Dua Total Biaya Berbeda Sesuai Aturan Bisnis -->
                        <div class="space-y-3.5 mb-6 pt-4 border-t border-dashed border-white/15">
                            
                            <!-- 1. Biaya Bahan Terpakai (Proporsional Riil Adonan) -->
                            <div class="rounded-2xl bg-white/5 border border-white/10 p-4 transition-all duration-200">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-semibold uppercase tracking-wider text-[#D8E0D3]">Biaya Bahan Terpakai</span>
                                    <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-white/10 text-white">Proporsional</span>
                                </div>
                                <p class="text-2xl sm:text-3xl font-bold text-[#D8E0D3] tabular-nums" style="font-family: 'Playfair Display', Georgia, serif;">
                                    {{ \Illuminate\Support\Number::currency($totalProportionalCost, 'IDR', 'id', precision: 0) }}
                                </p>
                                <p class="text-[11px] text-[#D8E0D3]/60 mt-1">Nilai murni bahan yang benar-benar tercampur ke dalam adonan</p>
                            </div>

                            <!-- 2. Uang Belanja Pasar (Kemasan Utuh di Toko) -->
                            <div class="rounded-2xl bg-[#D96B43]/20 border-2 border-[#D96B43]/50 p-5 shadow-lg transition-all duration-200">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-bold uppercase tracking-wider text-[#FCD7C8]">Uang Belanja Pasar</span>
                                    <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded bg-[#D96B43] text-white">Kemasan Utuh</span>
                                </div>
                                <p class="text-3xl sm:text-4xl font-extrabold text-white tabular-nums tracking-tight" style="font-family: 'Playfair Display', Georgia, serif;">
                                    {{ \Illuminate\Support\Number::currency($totalMarketCost, 'IDR', 'id', precision: 0) }}
                                </p>
                                <p class="text-[11px] text-white/80 mt-1">Estimasi uang tunai yang harus disiapkan untuk beli kemasan utuh di pasar</p>
                            </div>

                        </div>
                    @endif

                    <!-- Flash Message Berhasil Simpan Racikan -->
                    @if (session()->has('success_save'))
                        <div class="p-3.5 mb-5 bg-emerald-500/20 border border-emerald-500/40 text-emerald-200 rounded-2xl text-xs font-semibold flex items-center justify-between">
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
                    <div class="flex flex-col sm:flex-row gap-3">
                        <!-- Tombol Simpan Racikan -->
                        <button
                            type="button"
                            wire:click="openSaveModal"
                            class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-full border border-white/25 hover:border-white/50 bg-white/10 hover:bg-white/15 text-white font-medium text-xs sm:text-sm transition-all duration-200 active:scale-[0.99] cursor-pointer"
                        >
                            <svg class="w-4 h-4 text-amber-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                            </svg>
                            <span>Simpan Racikan</span>
                        </button>

                        <!-- Tombol Pesan via WhatsApp -->
                        @if ($budgetError)
                            <button
                                type="button"
                                disabled
                                aria-disabled="true"
                                class="flex-1 flex items-center justify-center gap-2 px-5 py-3.5 rounded-full bg-[#25D366]/40 text-white font-medium text-xs sm:text-sm shadow-none cursor-not-allowed opacity-50"
                            >
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                                </svg>
                                <span>Pesan via WA</span>
                            </button>
                        @else
                            <button
                                type="button"
                                wire:click="openCheckout"
                                class="flex-1 flex items-center justify-center gap-2 px-5 py-3.5 rounded-full bg-[#25D366] hover:bg-[#1DA851] text-white font-semibold text-xs sm:text-sm shadow-md hover:shadow-lg transition-all duration-200 active:scale-[0.99] cursor-pointer"
                            >
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
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
        <div class="fixed inset-0 z-50 bg-[#26221E]/80 backdrop-blur-md flex items-center justify-center p-4 transition-opacity duration-300">
            <div
                class="bg-[#FAF7F2] rounded-[2.5rem] w-full max-w-md p-6 sm:p-8 shadow-2xl relative border border-[#26221E]/10"
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
                    <h3 class="text-[#26221E] text-2xl font-bold leading-tight" style="font-family: 'Playfair Display', Georgia, serif;">
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
                        <span class="font-bold text-[#26221E]">{{ ucfirst($meatType) }} ({{ number_format($meatWeight) }} g)</span>
                    </div>
                    <div class="flex justify-between text-[#5A534A]">
                        <span>Estimasi Nilai Bahan:</span>
                        <span class="font-bold text-[#D96B43] tabular-nums">{{ \Illuminate\Support\Number::currency($totalProportionalCost, 'IDR', 'id', precision: 0) }}</span>
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
                            Pilih perkiraan waktu Anda akan tiba di pasar / los selepan.
                        </p>
                    </div>

                    <!-- Tombol Konfirmasi -->
                    <div class="pt-2">
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            class="w-full py-4 px-6 rounded-full bg-[#25D366] hover:bg-[#1DA851] text-white font-bold text-sm shadow-md hover:shadow-lg transition-all duration-200 active:scale-[0.99] cursor-pointer flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed"
                        >
                            <svg wire:loading.remove class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                            </svg>
                            <span wire:loading.remove>Konfirmasi & Kirim WA</span>
                            <span wire:loading class="flex items-center gap-2">
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
        <div class="fixed inset-0 z-50 bg-[#26221E]/80 backdrop-blur-md flex items-center justify-center p-4 transition-opacity duration-300">
            <div
                class="bg-[#FAF7F2] rounded-[2.5rem] w-full max-w-md p-6 sm:p-8 shadow-2xl relative border border-[#26221E]/10"
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
                    <p class="text-[#D96B43] font-bold tracking-widest text-xs uppercase mb-1 font-mono">Laboratorium Resep</p>
                    <h3 class="text-[#26221E] text-2xl font-bold leading-tight" style="font-family: 'Playfair Display', Georgia, serif;">
                        Simpan Racikan Saya
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
                            class="px-5 py-2.5 rounded-full border border-[#26221E]/20 hover:bg-black/5 text-[#26221E] font-semibold text-xs transition-colors cursor-pointer"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            class="px-6 py-2.5 rounded-full bg-[#D96B43] hover:bg-[#C25932] text-white font-bold text-xs shadow-md transition-all active:scale-[0.99] cursor-pointer flex items-center gap-2"
                        >
                            <span wire:loading.remove>Simpan ke Resep Saya</span>
                            <span wire:loading>Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
