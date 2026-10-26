<div class="min-h-screen flex flex-col justify-between text-[#26221E] bg-motif-kawung">
    <!-- Navbar Publik -->
    <x-navbar />

    <!-- Rich Culinary Vault Header Banner (Pure Editorial Deep Sage) -->
    <div class="relative bg-[#2D4235] text-[#F4EFE6] pt-12 sm:pt-16 pb-16 px-4 sm:px-6 lg:px-12 border-b border-[#2D4235]/30 overflow-hidden">
        <div class="max-w-7xl mx-auto relative z-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div class="max-w-2xl">
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-8 h-[2px] bg-[#D96B43]"></span>
                    <span class="text-xs font-mono font-bold tracking-[0.2em] uppercase text-[#D8E0D3]">
                        Laboratorium Resep Pribadi
                    </span>
                </div>
                <h1 class="text-3xl sm:text-5xl font-bold text-white tracking-tight leading-tight" style="font-family: 'Playfair Display', Georgia, serif;">
                    Racikan Saya
                </h1>
                <p class="text-sm text-[#D8E0D3]/85 mt-3 leading-relaxed font-normal">
                    Koleksi resep bakso favorit Anda. Buka takaran bahan yang pernah Anda hitung dan rencanakan belanja pasar berikutnya dengan presisi.
                </p>
            </div>

            <!-- Tombol Tambah Racikan Baru -->
            <a
                href="{{ url('/') }}#racikan-saya"
                class="inline-flex items-center justify-center gap-2.5 px-6 py-3.5 rounded-full bg-[#D96B43] hover:bg-[#C25932] text-white font-semibold text-xs shadow-md transition-all duration-200 active:scale-[0.99] shrink-0"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Buat Racikan Baru</span>
            </a>
        </div>
    </div>

    <!-- Area Konten Utama Bertekstur Oatmeal Kertas -->
    <main class="flex-1 py-12 px-4 sm:px-6 lg:px-12 max-w-7xl mx-auto w-full">

        <!-- Flash Message Notifikasi -->
        @if (session()->has('success'))
            <div class="mb-8 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl p-4 text-xs font-semibold flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 cursor-pointer text-base">
                    &times;
                </button>
            </div>
        @endif

        <!-- Grid Kartu Racikan Tersimpan -->
        <div>
            @if ($recipes->isEmpty())
                <!-- State Kosong (Empty State) -->
                <div class="bg-[#FAF7F2] rounded-3xl p-10 sm:p-16 border border-[#26221E]/10 text-center max-w-lg mx-auto shadow-sm">
                    <div class="w-16 h-16 rounded-full bg-[#2D4235] text-[#F4EFE6] font-serif font-bold text-2xl mx-auto flex items-center justify-center mb-4">
                        B.
                    </div>
                    <h2 class="text-2xl font-bold text-[#26221E]" style="font-family: 'Playfair Display', Georgia, serif;">
                        Belum Ada Racikan Tersimpan
                    </h2>
                    <p class="text-xs text-[#5A534A] mt-2 leading-relaxed">
                        Anda belum menyimpan racikan apapun. Gunakan kalkulator interaktif untuk menghitung kebutuhan bahan bakso Anda dan simpan ke laboratorium ini.
                    </p>
                    <a
                        href="{{ url('/') }}#racikan-saya"
                        class="mt-6 inline-flex items-center gap-2 px-6 py-3 rounded-full bg-[#D96B43] hover:bg-[#C25932] text-white font-bold text-xs shadow-md transition-all active:scale-[0.99]"
                    >
                        <span>Mulai Meracik Sekarang</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($recipes as $recipe)
                        <div class="bg-[#FAF7F2] rounded-3xl border border-[#26221E]/10 p-6 shadow-xs hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                            <div>
                                <!-- Header Kartu Resep -->
                                <div class="flex items-start justify-between gap-2 pb-3 border-b border-[#26221E]/10">
                                    <div class="overflow-hidden">
                                        <h3 class="text-lg font-bold text-[#26221E] truncate" style="font-family: 'Playfair Display', Georgia, serif;">
                                            {{ $recipe->name }}
                                        </h3>
                                        <span class="text-[10px] text-[#5A534A] block font-mono mt-0.5">
                                            Disimpan {{ $recipe->created_at->format('d M Y, H:i') }}
                                        </span>
                                    </div>
                                    <button
                                        type="button"
                                        wire:click="deleteRecipe({{ $recipe->id }})"
                                        wire:confirm="Yakin ingin menghapus racikan '{{ $recipe->name }}'?"
                                        title="Hapus racikan ini"
                                        class="text-[#26221E]/40 hover:text-[#8B231A] p-1.5 rounded-lg transition-colors cursor-pointer shrink-0"
                                    >
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Spesifikasi Utama (Required String: {weight}g Daging) -->
                                <div class="grid grid-cols-2 gap-2 mt-4 text-xs font-mono">
                                    <div class="p-2.5 rounded-xl bg-white border border-[#26221E]/5 shadow-xs">
                                        <span class="text-[10px] uppercase text-[#5A534A] block">Daging Utama</span>
                                        <span class="font-bold text-[#26221E] mt-0.5 block">
                                            {{ (int) $recipe->meat_weight }}g Daging
                                        </span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-white border border-[#26221E]/5 shadow-xs">
                                        <span class="text-[10px] uppercase text-[#5A534A] block">Preferensi</span>
                                        <span class="font-bold text-[#26221E] capitalize mt-0.5 block">
                                            {{ $recipe->preference }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Mini Breakdown Bahan -->
                                <div class="mt-4">
                                    <span class="text-[10px] uppercase font-bold text-[#5A534A] tracking-wider block mb-2 font-mono">
                                        Komposisi Takaran:
                                    </span>
                                    <div class="space-y-1.5 text-xs">
                                        @if (is_array($recipe->recipe_details))
                                            @foreach (array_slice($recipe->recipe_details, 0, 4) as $item)
                                                 <div class="flex justify-between items-center text-[#26221E]/90">
                                                    <span class="truncate pr-2 font-medium">• {{ $item['name'] ?? 'Bahan' }}</span>
                                                    <span class="font-mono text-[#26221E] whitespace-nowrap text-[11px]">
                                                        {{ (float) ($item['weight'] ?? 0) }} {{ $item['unit'] ?? 'g' }}
                                                    </span>
                                                </div>
                                            @endforeach
                                            @if (count($recipe->recipe_details) > 4)
                                                <p class="text-[10px] text-[#D96B43] italic pt-0.5">
                                                    + {{ count($recipe->recipe_details) - 4 }} bahan racikan lainnya
                                                </p>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Footer Kartu: Nilai Bahan & Aksi -->
                            <div class="mt-6 pt-4 border-t border-[#26221E]/10 flex items-end justify-between">
                                <div>
                                    <span class="text-[9px] font-mono uppercase text-[#5A534A] block">ESTIMASI BAHAN</span>
                                    <span class="text-base font-bold text-[#D96B43] tabular-nums" style="font-family: 'Playfair Display', Georgia, serif;">
                                        {{ \Illuminate\Support\Number::currency($recipe->estimated_total, 'IDR', 'id', precision: 0) }}
                                    </span>
                                </div>
                                <a
                                    href="{{ url('/') }}#racikan-saya"
                                    class="inline-flex items-center gap-1 text-xs font-semibold text-[#2D4235] hover:text-[#D96B43] transition-colors"
                                >
                                    <span>Hitung Ulang</span>
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>

                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </main>

    <!-- Footer Publik -->
    <x-footer />
</div>
