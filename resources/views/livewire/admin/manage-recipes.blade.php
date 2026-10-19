<main class="flex-1 flex flex-col min-w-0 overflow-y-auto bg-[#F4EFE6]">
    <!-- Top Bar & Header Halaman -->
    <header class="bg-[#FAF7F2]/90 backdrop-blur-md border-b border-[#26221E]/10 px-6 sm:px-8 py-5 shrink-0 sticky top-0 z-20">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-[#C83B2B]">
                    Laboratorium & Formulasi Adonan
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-[#26221E] mt-0.5" style="font-family: 'Playfair Display', Georgia, serif;">
                    Konfigurasi Formula Resep
                </h1>
                <p class="text-xs text-[#5A534A] mt-1">
                    Atur proporsi takaran bahan baku (daging, tepung, bumbu) per preferensi pasar dan status validasi resmi kios selepan.
                </p>
            </div>

            <!-- Tombol Aksi Cepat -->
            <div class="flex items-center gap-2.5 shrink-0">
                <button
                    type="button"
                    wire:click="resetToDefaults"
                    wire:confirm="Kembalikan semua rasio ke nilai formula acuan standar?"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-white hover:bg-[#FAF7F2] text-[#26221E] border border-[#26221E]/15 font-bold text-xs shadow-sm transition-all duration-150 cursor-pointer"
                    title="Pulihkan rasio bawaan (Enak, Seimbang, Ekonomis)"
                >
                    <svg class="w-4 h-4 text-[#5A534A]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span>Pulihkan Standar</span>
                </button>
                <a
                    href="{{ route('calculator') }}"
                    target="_blank"
                    class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl bg-[#2D4235] hover:bg-[#1E2D24] text-[#FAF7F2] font-bold text-xs shadow-sm transition-all duration-150 cursor-pointer"
                >
                    <span>Cek Kalkulator</span>
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                </a>
            </div>
        </div>
    </header>

    <!-- Konten Utama -->
    <div class="p-6 sm:p-8 space-y-6 flex-1">

        <!-- Flash Message Sukses -->
        @if (session()->has('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl p-4 text-xs font-semibold flex items-center justify-between shadow-sm animate-fade-in">
                <div class="flex items-center gap-2.5">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" wire:click="$refresh" class="text-emerald-700 hover:text-emerald-900 cursor-pointer">
                    &times;
                </button>
            </div>
        @endif

        <!-- Card Banner Edukasi Formula Dinamis -->
        <div class="bg-[#FAF7F2] rounded-3xl p-6 sm:p-7 border border-[#26221E]/10 shadow-sm relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-[#D96B43]/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="max-w-3xl relative z-10">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider text-[#8B231A] bg-[#8B231A]/10 border border-[#8B231A]/20 mb-2">
                    Kaidah Proporsi Bahan Kios Selepan
                </span>
                <h2 class="text-xl sm:text-2xl font-bold text-[#26221E]" style="font-family: 'Playfair Display', Georgia, serif;">
                    Sistem Rasio Adonan Terpusat
                </h2>
                <p class="text-xs sm:text-sm text-[#5A534A] mt-2 leading-relaxed font-sans">
                    Setiap preferensi di bawah ini terhubung langsung ke algoritma kalkulator publik. 
                    Perubahan rasio tepung dan bumbu akan otomatis memperbarui kalkulasi kebutuhan gramatur, rincian biaya proporsional, serta takaran belanja pasar pelanggan tanpa memerlukan perubahan kode.
                </p>
            </div>
        </div>

        <!-- Grid Kartu Formula Resep -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            @forelse ($formulas as $formula)
                @php
                    $isEnak = $formula->code === 'enak';
                    $isSeimbang = $formula->code === 'seimbang';
                    $isEkonomis = $formula->code === 'ekonomis';

                    $badgeTheme = $isEnak 
                        ? 'bg-[#8B231A]/10 text-[#8B231A] border-[#8B231A]/20'
                        : ($isSeimbang ? 'bg-[#2D4235]/10 text-[#2D4235] border-[#2D4235]/20' : 'bg-[#D96B43]/10 text-[#D96B43] border-[#D96B43]/20');

                    $flourPct = round($formula->flour_ratio * 100, 1);
                    $spicePct = round($formula->spice_ratio * 100, 1);

                    // Simulasi kebutuhan per 500g daging
                    $simTapiocaGram = 500 * $formula->flour_ratio;
                    $simGarlicGram = 500 * ($formula->spice_ratio * 0.6);
                    $simSaltGram = 500 * ($formula->spice_ratio * 0.4);
                @endphp

                <div class="bg-[#FAF7F2] rounded-3xl p-6 sm:p-7 border border-[#26221E]/10 shadow-sm flex flex-col justify-between hover:border-[#8B231A]/30 transition-all duration-200">
                    <div>
                        <!-- Header Kartu -->
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <span class="inline-block px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider border {{ $badgeTheme }}">
                                    Kode: {{ $formula->code }}
                                </span>
                                <h3 class="text-lg font-bold text-[#26221E] mt-2" style="font-family: 'Playfair Display', Georgia, serif;">
                                    {{ $formula->name }}
                                </h3>
                            </div>

                            <!-- Status Badge -->
                            @if ($formula->is_simulation)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    Simulasi
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                    Resmi Toko
                                </span>
                            @endif
                        </div>

                        <!-- Deskripsi Karakter -->
                        <p class="text-xs text-[#5A534A] mt-3 leading-relaxed font-sans min-h-[3rem]">
                            {{ $formula->description ?: 'Formula racikan adonan bakso khas BASOKU.' }}
                        </p>

                        <!-- Panel Rincian Rasio -->
                        <div class="mt-5 p-4 rounded-2xl bg-white/70 border border-[#26221E]/10 space-y-3.5 text-xs">
                            <!-- Rasio Daging -->
                            <div>
                                <div class="flex justify-between items-center text-[11px] mb-1">
                                    <span class="font-bold text-[#26221E]">Rasio Daging (Acuan)</span>
                                    <span class="font-mono font-bold text-[#8B231A]">{{ number_format($formula->meat_ratio, 4) }} (100%)</span>
                                </div>
                                <div class="w-full bg-[#E5DFD5] h-2 rounded-full overflow-hidden">
                                    <div class="bg-[#8B231A] h-2 rounded-full" style="width: 100%"></div>
                                </div>
                            </div>

                            <!-- Rasio Tepung -->
                            <div>
                                <div class="flex justify-between items-center text-[11px] mb-1">
                                    <span class="font-bold text-[#26221E]">Rasio Tepung Tapioka</span>
                                    <span class="font-mono font-bold text-[#D96B43]">{{ number_format($formula->flour_ratio, 4) }} ({{ $flourPct }}%)</span>
                                </div>
                                <div class="w-full bg-[#E5DFD5] h-2 rounded-full overflow-hidden">
                                    <div class="bg-[#D96B43] h-2 rounded-full" style="width: {{ min(100, $flourPct * 2) }}%"></div>
                                </div>
                                <span class="text-[10px] text-[#5A534A]/80 mt-0.5 block">
                                    Acuan 500g daging: ~{{ number_format($simTapiocaGram, 0) }}g tepung
                                </span>
                            </div>

                            <!-- Rasio Bumbu -->
                            <div>
                                <div class="flex justify-between items-center text-[11px] mb-1">
                                    <span class="font-bold text-[#26221E]">Rasio Bumbu (Bawang & Garam)</span>
                                    <span class="font-mono font-bold text-[#2D4235]">{{ number_format($formula->spice_ratio, 4) }} ({{ $spicePct }}%)</span>
                                </div>
                                <div class="w-full bg-[#E5DFD5] h-2 rounded-full overflow-hidden">
                                    <div class="bg-[#2D4235] h-2 rounded-full" style="width: {{ min(100, $spicePct * 10) }}%"></div>
                                </div>
                                <span class="text-[10px] text-[#5A534A]/80 mt-0.5 block">
                                    Bawang putih: ~{{ number_format($simGarlicGram, 1) }}g, Garam: ~{{ number_format($simSaltGram, 1) }}g
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Aksi Bawah -->
                    <div class="mt-6 pt-4 border-t border-[#26221E]/10 flex items-center justify-between gap-2">
                        <button
                            type="button"
                            wire:click="toggleSimulation({{ $formula->id }})"
                            class="text-xs font-semibold px-3 py-2 rounded-xl transition-colors cursor-pointer {{ $formula->is_simulation ? 'bg-amber-100 text-amber-900 hover:bg-amber-200' : 'bg-emerald-100 text-emerald-900 hover:bg-emerald-200' }}"
                        >
                            {{ $formula->is_simulation ? 'Jadikan Resmi' : 'Kembalikan Simulasi' }}
                        </button>

                        <button
                            type="button"
                            wire:click="openEditModal({{ $formula->id }})"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#8B231A] hover:bg-[#701A13] text-[#FAF7F2] text-xs font-bold shadow-sm transition-colors cursor-pointer"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            <span>Ubah Rasio</span>
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-3xl p-12 text-center border border-[#26221E]/10">
                    <p class="text-sm text-[#5A534A]">Belum ada formula racikan terdaftar di database.</p>
                    <button
                        type="button"
                        wire:click="resetToDefaults"
                        class="mt-4 px-4 py-2 rounded-xl bg-[#8B231A] text-white text-xs font-bold"
                    >
                        Buat Formula Acuan Standar
                    </button>
                </div>
            @endforelse
        </div>
    </div>

    <!-- MODAL EDIT FORMULA RASIO -->
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-fade-in">
            <div class="bg-[#FAF7F2] rounded-3xl max-w-lg w-full p-6 sm:p-8 border border-[#26221E]/20 shadow-2xl relative">
                <!-- Header Modal -->
                <div class="flex items-center justify-between pb-4 border-b border-[#26221E]/10">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-[#8B231A]">
                            Penyesuaian Parameter Racikan
                        </span>
                        <h3 class="text-xl font-bold text-[#26221E] mt-0.5" style="font-family: 'Playfair Display', Georgia, serif;">
                            Ubah Formula: {{ $name }}
                        </h3>
                    </div>
                    <button
                        type="button"
                        wire:click="closeModal"
                        class="w-8 h-8 rounded-full bg-black/5 hover:bg-black/10 text-[#26221E] flex items-center justify-center text-lg transition-colors cursor-pointer"
                    >
                        &times;
                    </button>
                </div>

                <!-- Form Input -->
                <form wire:submit="save" class="mt-5 space-y-4 text-xs">
                    <!-- Nama Formula -->
                    <div>
                        <label for="formula-name" class="block font-bold text-[#26221E] mb-1">
                            Nama Formula
                        </label>
                        <input
                            type="text"
                            id="formula-name"
                            wire:model="name"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-[#26221E]/15 text-[#26221E] focus:outline-none focus:border-[#8B231A] focus:ring-1 focus:ring-[#8B231A] transition-all font-medium"
                            placeholder="Contoh: Racikan Seimbang (Standar Pasar)"
                        />
                        @error('name') <span class="text-red-600 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Input Grid Rasio -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <!-- Rasio Daging -->
                        <div>
                            <label for="meat-ratio" class="block font-bold text-[#26221E] mb-1">
                                Rasio Daging (Acuan)
                            </label>
                            <input
                                type="number"
                                step="0.0001"
                                id="meat-ratio"
                                wire:model.live="meat_ratio"
                                class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-[#26221E]/15 text-[#26221E] focus:outline-none focus:border-[#8B231A] font-mono"
                            />
                            <span class="text-[10px] text-[#5A534A] mt-0.5 block">Default: 1.0000 (100%)</span>
                            @error('meat_ratio') <span class="text-red-600 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Rasio Tepung -->
                        <div>
                            <label for="flour-ratio" class="block font-bold text-[#26221E] mb-1">
                                Rasio Tepung Tapioka
                            </label>
                            <input
                                type="number"
                                step="0.0001"
                                id="flour-ratio"
                                wire:model.live="flour_ratio"
                                class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-[#26221E]/15 text-[#26221E] focus:outline-none focus:border-[#8B231A] font-mono"
                            />
                            <span class="text-[10px] text-[#5A534A] mt-0.5 block">0.2000 = 20% daging</span>
                            @error('flour_ratio') <span class="text-red-600 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Rasio Bumbu -->
                        <div>
                            <label for="spice-ratio" class="block font-bold text-[#26221E] mb-1">
                                Rasio Bumbu Total
                            </label>
                            <input
                                type="number"
                                step="0.0001"
                                id="spice-ratio"
                                wire:model.live="spice_ratio"
                                class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-[#26221E]/15 text-[#26221E] focus:outline-none focus:border-[#8B231A] font-mono"
                            />
                            <span class="text-[10px] text-[#5A534A] mt-0.5 block">0.0500 = 5% daging</span>
                            @error('spice_ratio') <span class="text-red-600 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Toggle Simulasi -->
                        <div>
                            <label class="block font-bold text-[#26221E] mb-1">
                                Status Validitas
                            </label>
                            <label class="flex items-center gap-2 p-2.5 rounded-xl bg-white border border-[#26221E]/15 cursor-pointer">
                                <input
                                    type="checkbox"
                                    wire:model="is_simulation"
                                    class="w-4 h-4 rounded text-[#8B231A] focus:ring-[#8B231A]"
                                />
                                <span class="text-xs font-semibold text-[#26221E]">
                                    Mode Simulasi
                                </span>
                            </label>
                            <span class="text-[10px] text-[#5A534A] mt-0.5 block">Hilangkan centang jika sudah diverifikasi kios.</span>
                        </div>
                    </div>

                    <!-- Live Estimation Box -->
                    @php
                        $flourVal = (float) ($flour_ratio ?: 0);
                        $spiceVal = (float) ($spice_ratio ?: 0);
                        $tapiocaEst = 500 * $flourVal;
                        $garlicEst = 500 * ($spiceVal * 0.6);
                        $saltEst = 500 * ($spiceVal * 0.4);
                    @endphp
                    <div class="p-3.5 rounded-2xl bg-black/5 border border-black/5 space-y-1 text-[11px]">
                        <span class="font-bold text-[#26221E] block">Simulasi Takaran 500g Daging:</span>
                        <div class="text-[#5A534A] flex justify-between">
                            <span>Tepung Tapioka:</span>
                            <span class="font-mono font-bold text-[#26221E]">{{ number_format($tapiocaEst, 1) }} gram ({{ round($flourVal * 100, 1) }}%)</span>
                        </div>
                        <div class="text-[#5A534A] flex justify-between">
                            <span>Bawang Putih (60% bumbu):</span>
                            <span class="font-mono font-bold text-[#26221E]">{{ number_format($garlicEst, 1) }} gram</span>
                        </div>
                        <div class="text-[#5A534A] flex justify-between">
                            <span>Garam Kasar (40% bumbu):</span>
                            <span class="font-mono font-bold text-[#26221E]">{{ number_format($saltEst, 1) }} gram</span>
                        </div>
                    </div>

                    <!-- Deskripsi Formula -->
                    <div>
                        <label for="formula-desc" class="block font-bold text-[#26221E] mb-1">
                            Deskripsi / Catatan Adonan
                        </label>
                        <textarea
                            id="formula-desc"
                            rows="2"
                            wire:model="description"
                            class="w-full px-3.5 py-2 rounded-xl bg-white border border-[#26221E]/15 text-[#26221E] focus:outline-none focus:border-[#8B231A]"
                            placeholder="Catatan keunggulan tekstur atau target pasar..."
                        ></textarea>
                        @error('description') <span class="text-red-600 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Tombol Aksi Modal -->
                    <div class="pt-4 border-t border-[#26221E]/10 flex items-center justify-end gap-2.5">
                        <button
                            type="button"
                            wire:click="closeModal"
                            class="px-4 py-2.5 rounded-xl bg-black/5 hover:bg-black/10 text-[#26221E] font-bold text-xs transition-colors cursor-pointer"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="px-5 py-2.5 rounded-xl bg-[#8B231A] hover:bg-[#701A13] text-[#FAF7F2] font-bold text-xs shadow-md transition-all active:scale-[0.99] cursor-pointer"
                        >
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</main>
