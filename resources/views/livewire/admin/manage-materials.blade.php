<main class="flex-1 flex flex-col min-w-0 overflow-y-auto">
    <!-- Top Bar & Header Halaman -->
    <header class="bg-white/80 backdrop-blur-md border-b border-[#161210]/10 px-6 sm:px-8 py-5 shrink-0">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-[#C83B2B]">
                    Katalog & Penentuan Harga
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-[#161210] mt-0.5" style="font-family: 'Playfair Display', Georgia, serif;">
                    Kelola Harga Bahan
                </h1>
                <p class="text-xs text-[#161210]/60 mt-1">
                    Atur harga acuan bahan baku dan ketersediaan stok pasar secara <em>real-time</em>.
                </p>
            </div>

            <!-- Tombol Tambah Bahan -->
            <button
                type="button"
                wire:click="openAddModal"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-[#C83B2B] hover:bg-[#A92F21] text-white font-bold text-xs shadow-md shadow-[#C83B2B]/20 transition-all duration-200 active:scale-[0.99] cursor-pointer shrink-0"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Tambah Bahan</span>
            </button>
        </div>

        <!-- Filter Tab Kategori Cepat -->
        <div class="flex items-center gap-2 mt-5 overflow-x-auto pb-1 text-xs">
            <button
                type="button"
                wire:click="$set('selectedCategory', 'semua')"
                class="px-3 py-1.5 rounded-lg font-semibold transition-colors cursor-pointer shrink-0 {{ $selectedCategory === 'semua' ? 'bg-[#161210] text-[#FAF6F0]' : 'bg-[#FAF6F0] text-[#161210]/70 hover:bg-white hover:text-[#161210] border border-[#161210]/10' }}"
            >
                Semua Kategori ({{ $totalMaterialsCount }})
            </button>
            @foreach ($allCategories as $cat)
                <button
                    wire:key="mat-cat-tab-{{ $cat }}"
                    type="button"
                    wire:click="$set('selectedCategory', @js($cat))"
                    class="px-3 py-1.5 rounded-lg font-semibold uppercase tracking-wider transition-colors cursor-pointer shrink-0 {{ $selectedCategory === $cat ? 'bg-[#161210] text-[#FAF6F0]' : 'bg-[#FAF6F0] text-[#161210]/70 hover:bg-white hover:text-[#161210] border border-[#161210]/10' }}"
                >
                    {{ $cat }}
                </button>
            @endforeach
        </div>
    </header>

    <!-- Konten Tabel Bahan Baku -->
    <div class="p-6 sm:p-8 space-y-6 flex-1">

        <!-- Flash Message Sukses -->
        @if (session()->has('success'))
            <div x-data="{ show: true }" x-show="show" class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl p-4 text-xs font-semibold flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-2.5">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" @click="show = false" aria-label="Tutup notifikasi" class="text-emerald-700 hover:text-emerald-900 cursor-pointer px-1">
                    &times;
                </button>
            </div>
        @endif

        <!-- Flash Message Error -->
        @if (session()->has('error'))
            <div x-data="{ show: true }" x-show="show" role="alert" class="bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl p-4 text-xs font-semibold flex items-center justify-between shadow-sm">
                <span>{{ session('error') }}</span>
                <button type="button" @click="show = false" aria-label="Tutup peringatan" class="text-rose-700 hover:text-rose-900 cursor-pointer px-1">
                    &times;
                </button>
            </div>
        @endif

        <!-- Card Tabel Data -->
        <div class="bg-white rounded-3xl border border-[#161210]/10 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-[#161210]/10 bg-[#FAF6F0]/60 text-[11px] font-bold uppercase tracking-wider text-[#161210]/70">
                            <th scope="col" class="py-4 px-5">Nama Bahan</th>
                            <th scope="col" class="py-4 px-4">Kategori</th>
                            <th scope="col" class="py-4 px-4">Mode Jual</th>
                            <th scope="col" class="py-4 px-4">Satuan</th>
                            <th scope="col" class="py-4 px-4">Berat Acuan</th>
                            <th scope="col" class="py-4 px-5 text-right">Harga Jual</th>
                            <th scope="col" class="py-4 px-4 text-center">Status</th>
                            <th scope="col" class="py-4 px-5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#161210]/5 text-xs font-medium text-[#161210]">
                        @forelse ($materials as $material)
                            <tr wire:key="material-row-{{ $material->id }}" class="hover:bg-[#FAF6F0]/40 transition-colors">
                                <!-- Kolom 1: Nama Bahan -->
                                <td class="py-4 px-5 font-bold text-sm">
                                    {{ $material->name }}
                                </td>

                                <!-- Kolom 2: Kategori -->
                                <td class="py-4 px-4">
                                    <span class="inline-block px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider whitespace-nowrap {{ match($material->category) {
                                        'daging' => 'bg-rose-100 text-rose-800 border border-rose-200',
                                        'tepung' => 'bg-amber-100 text-amber-800 border border-amber-200',
                                        'bumbu' => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
                                        default => 'bg-[#FAF6F0] text-[#161210]/80 border border-[#161210]/10'
                                    } }}">
                                        {{ $material->category }}
                                    </span>
                                </td>

                                <!-- Kolom 3: Mode Jual Pasar -->
                                <td class="py-4 px-4">
                                    <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold whitespace-nowrap {{ match($material->selling_mode ?? 'kemasan') {
                                        'curah' => 'bg-sky-50 text-sky-800 border border-sky-200',
                                        'satuan' => 'bg-violet-50 text-violet-800 border border-violet-200',
                                        default => 'bg-orange-50 text-orange-800 border border-orange-200'
                                    } }}">
                                        {{ $material->sellingModeLabel() }}
                                    </span>
                                </td>

                                <!-- Kolom 4: Satuan (Pack/Kg/Sachet/Butir) -->
                                <td class="py-4 px-4 text-[#161210]/70 font-semibold whitespace-nowrap">
                                    {{ $material->package_unit ?? $material->unit }}
                                </td>

                                <!-- Kolom 5: Berat per Kemasan / Acuan -->
                                <td class="py-4 px-4 font-mono text-[#161210]/80 whitespace-nowrap">
                                    @if ($material->package_weight)
                                        {{ number_format($material->package_weight, 0, ',', '.') }} g
                                    @else
                                        <span class="text-xs text-[#161210]/30">-</span>
                                    @endif
                                </td>

                                <!-- Kolom 6: Harga Jual & Harga Dasar yang Akurat Sesuai Mode -->
                                <td class="py-4 px-5 text-right font-extrabold text-[#C83B2B] text-sm whitespace-nowrap">
                                    @if ($material->isUnitBased())
                                        {{ \Illuminate\Support\Number::currency($material->package_price ?? $material->price, 'IDR', 'id', precision: 0) }}
                                        <span class="text-[10px] font-normal text-[#161210]/55 block">/ {{ $material->package_unit ?? $material->unit }}</span>
                                    @elseif ($material->package_price)
                                        {{ \Illuminate\Support\Number::currency($material->package_price, 'IDR', 'id', precision: 0) }}
                                        <span class="text-[10px] font-normal text-[#161210]/55 block">({{ \Illuminate\Support\Number::currency($material->price, 'IDR', 'id', precision: 0) }} / g)</span>
                                    @else
                                        {{ \Illuminate\Support\Number::currency($material->price, 'IDR', 'id', precision: 0) }}
                                        <span class="text-[10px] font-normal text-[#161210]/55">/ {{ $material->unit }}</span>
                                    @endif
                                </td>

                                <!-- Kolom 7: Status Ketersediaan (Clickable) -->
                                <td class="py-4 px-4 text-center">
                                    <button
                                        type="button"
                                        wire:click="toggleAvailability({{ $material->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="toggleAvailability({{ $material->id }})"
                                        title="Klik untuk ubah status ketersediaan"
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold cursor-pointer transition-transform active:scale-95 whitespace-nowrap disabled:opacity-50 {{ $material->is_available ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100' : 'bg-red-50 text-red-700 border border-red-200 hover:bg-red-100' }}"
                                    >
                                        <span class="w-1.5 h-1.5 rounded-full {{ $material->is_available ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                                        {{ $material->is_available ? 'Tersedia' : 'Habis' }}
                                    </button>
                                </td>

                                <!-- Kolom 8: Aksi -->
                                <td class="py-4 px-5 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center justify-end gap-1.5">
                                        <button
                                            type="button"
                                            wire:click="openEditModal({{ $material->id }})"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-[#FAF6F0] hover:bg-[#161210] hover:text-white text-[#161210] font-semibold text-xs border border-[#161210]/10 transition-colors cursor-pointer"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            <span>Edit</span>
                                        </button>

                                        @if ($material->isCoreMaterial())
                                            <span
                                                title="Bahan inti kalkulator tidak dapat dihapus permanen. Ubah status menjadi Habis bila kosong."
                                                class="inline-flex items-center px-2 py-1.5 rounded-lg bg-[#FAF6F0] text-[#161210]/45 font-mono text-[10px] font-bold border border-[#161210]/10 select-none"
                                            >
                                                Inti
                                            </span>
                                        @else
                                            <button
                                                type="button"
                                                wire:click="deleteMaterial({{ $material->id }})"
                                                wire:confirm="Yakin ingin menghapus bahan '{{ $material->name }}' secara permanen dari katalog?"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-700 hover:text-white text-rose-700 font-semibold text-xs border border-rose-200 transition-colors cursor-pointer"
                                                aria-label="Hapus bahan {{ $material->name }}"
                                            >
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                <span>Hapus</span>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-xs text-[#161210]/50">
                                    Belum ada data bahan baku pada kategori ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer Tabel Informatif -->
            <div class="px-6 py-4 bg-[#FAF6F0]/40 border-t border-[#161210]/10 flex flex-col sm:flex-row items-center justify-between text-xs text-[#161210]/60 gap-2">
                <span>Total <strong>{{ $materials->count() }}</strong> bahan baku tercatat.</span>
                <span class="italic text-[11px]">*Perubahan harga langsung berpengaruh pada kalkulator racikan publik.</span>
            </div>
        </div>

    </div>

    <!-- MODAL FORM (TAMBAH / EDIT BAHAN) -->
    @if ($showModal)
        <div
            class="fixed inset-0 z-50 flex items-start sm:items-center justify-center p-4 overflow-y-auto bg-[#161210]/80 backdrop-blur-sm"
            role="dialog"
            aria-modal="true"
            aria-labelledby="material-modal-title"
            x-data
            x-init="window.basokuFocusFirst($el)"
            @keydown.tab="window.basokuTrapFocus($el, $event)"
            @keydown.escape.window="$wire.closeModal()"
        >
            <div
                class="bg-[#FAF6F0] rounded-2xl w-full max-w-lg my-auto max-h-[90dvh] overflow-y-auto p-6 sm:p-8 shadow-2xl border border-[#161210]/10 relative z-10"
            >
                <!-- Header Modal -->
                <div class="flex items-start justify-between pb-4 border-b border-[#161210]/10">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-[#C83B2B]">
                            {{ $isEditing ? 'Perbarui Data Bahan' : 'Bahan Baku Baru' }}
                        </span>
                        <h2 id="material-modal-title" class="text-xl sm:text-2xl font-bold text-[#161210] mt-0.5" style="font-family: 'Playfair Display', Georgia, serif;">
                            {{ $isEditing ? 'Edit Bahan: ' . $name : 'Tambah Bahan Baku' }}
                        </h2>
                    </div>
                    <button
                        type="button"
                        wire:click="closeModal"
                        aria-label="Tutup modal bahan"
                        class="text-[#161210]/60 hover:text-[#161210] p-1.5 rounded-lg transition-colors cursor-pointer"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Isi Formulir Modal -->
                <form
                    wire:submit.prevent="save"
                    x-data="{
                        sellingMode: @entangle('selling_mode'),
                        packageUnit: @entangle('package_unit'),
                        packageWeight: @entangle('package_weight'),
                        packagePrice: @entangle('package_price'),
                        basePrice: @entangle('price'),
                        manualOverride: @entangle('manualPriceOverride'),
                        parseNum(val) {
                            if (val === null || val === undefined) return NaN;
                            let cleaned = String(val).trim().replace(/\s+/g, '');
                            if (cleaned === '') return NaN;
                            if (cleaned.includes('.') && cleaned.includes(',')) {
                                cleaned = cleaned.replace(/\./g, '').replace(',', '.');
                            } else if (/^-?\d{1,3}(\.\d{3})+$/.test(cleaned)) {
                                cleaned = cleaned.replace(/\./g, '');
                            } else {
                                cleaned = cleaned.replace(',', '.');
                            }
                            return parseFloat(cleaned);
                        },
                        recalcBasePrice() {
                            if (this.manualOverride) return;
                            const pkgP = this.parseNum(this.packagePrice);
                            const pkgW = this.parseNum(this.packageWeight);
                            if (isNaN(pkgP) || pkgP <= 0) return;
                            if (this.sellingMode === 'satuan') {
                                this.basePrice = Math.round(pkgP * 100) / 100;
                            } else if (!isNaN(pkgW) && pkgW > 0) {
                                this.basePrice = Math.round((pkgP / pkgW) * 100) / 100;
                            }
                        },
                        formatIdr(val) {
                            const n = this.parseNum(val);
                            if (isNaN(n)) return '0';
                            return new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 }).format(n);
                        }
                    }"
                    class="mt-6 space-y-4"
                >
                    <!-- Field 1: Nama Bahan -->
                    <div>
                        <label for="material-name" class="block text-xs font-bold uppercase tracking-wider text-[#161210] mb-1.5">
                            Nama Bahan Baku <span class="text-[#C83B2B]">*</span>
                        </label>
                        <input
                            type="text"
                            id="material-name"
                            wire:model="name"
                            placeholder="Contoh: Daging Sapi Paha Dalam"
                            class="w-full rounded-xl border-2 border-[#161210]/10 bg-white px-4 py-2.5 text-xs sm:text-sm font-medium text-[#161210] placeholder:text-[#161210]/30 focus:outline-none focus:border-[#C83B2B] focus:ring-2 focus:ring-[#C83B2B]/20 transition-all"
                        >
                        @error('name')
                            <p class="text-[11px] text-[#C83B2B] mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Field 2: Kategori, Bentuk Kemasan & Satuan Acuan -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                        <!-- Kategori -->
                        <div>
                            <label for="material-category" class="block text-xs font-bold uppercase tracking-wider text-[#161210] mb-1.5">
                                Kategori <span class="text-[#C83B2B]">*</span>
                            </label>
                            <select
                                id="material-category"
                                wire:model="category"
                                class="w-full rounded-xl border-2 border-[#161210]/10 bg-white px-3 py-2.5 text-xs sm:text-sm font-medium text-[#161210] focus:outline-none focus:border-[#C83B2B] focus:ring-2 focus:ring-[#C83B2B]/20 transition-all cursor-pointer"
                            >
                                <option value="daging">Daging</option>
                                <option value="tepung">Tepung</option>
                                <option value="bumbu">Bumbu</option>
                                <option value="penyedap">Penyedap</option>
                                <option value="pengikat">Pengikat</option>
                                <option value="tambahan">Tambahan</option>
                            </select>
                            @error('category')
                                <p class="text-[11px] text-[#C83B2B] mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Bentuk Kemasan Pasar -->
                        <div>
                            <label for="material-package-unit" class="block text-xs font-bold uppercase tracking-wider text-[#161210] mb-1.5">
                                Kemasan Jual
                            </label>
                            <input
                                type="text"
                                id="material-package-unit"
                                wire:model.live.debounce.250ms="package_unit"
                                x-model="packageUnit"
                                placeholder="Pack, Kg, Butir, Ikat"
                                class="w-full rounded-xl border-2 border-[#161210]/10 bg-white px-3 py-2.5 text-xs sm:text-sm font-medium text-[#161210] focus:outline-none focus:border-[#C83B2B] focus:ring-2 focus:ring-[#C83B2B]/20 transition-all"
                            >
                            @error('package_unit')
                                <p class="text-[11px] text-[#C83B2B] mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Satuan Acuan (unit) -->
                        <div>
                            <label for="material-unit" class="block text-xs font-bold uppercase tracking-wider text-[#161210] mb-1.5">
                                Satuan Acuan <span class="text-[#C83B2B]">*</span>
                            </label>
                            <input
                                type="text"
                                id="material-unit"
                                list="material-unit-options"
                                wire:model="unit"
                                placeholder="gram, Kg, Pack, Ikat"
                                class="w-full rounded-xl border-2 border-[#161210]/10 bg-white px-3 py-2.5 text-xs sm:text-sm font-medium text-[#161210] focus:outline-none focus:border-[#C83B2B] focus:ring-2 focus:ring-[#C83B2B]/20 transition-all"
                            >
                            <datalist id="material-unit-options">
                                <option value="gram"></option>
                                <option value="Kg"></option>
                                <option value="Pack"></option>
                                <option value="Butir"></option>
                                <option value="Sachet"></option>
                                <option value="Ikat"></option>
                                <option value="Bungkus"></option>
                                <option value="Botol"></option>
                            </datalist>
                            @error('unit')
                                <p class="text-[11px] text-[#C83B2B] mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Field 2b: Mode Jual di Pasar -->
                    <div>
                        <label for="material-selling-mode" class="block text-xs font-bold uppercase tracking-wider text-[#161210] mb-1.5">
                            Mode Jual Pasar <span class="text-[#C83B2B]">*</span>
                        </label>
                        <select
                            id="material-selling-mode"
                            wire:model.live="selling_mode"
                            x-model="sellingMode"
                            @change="recalcBasePrice()"
                            class="w-full rounded-xl border-2 border-[#161210]/10 bg-white px-3 py-2.5 text-xs sm:text-sm font-medium text-[#161210] focus:outline-none focus:border-[#C83B2B] focus:ring-2 focus:ring-[#C83B2B]/20 transition-all cursor-pointer"
                        >
                            <option value="curah">Timbang Bebas / Curah (bisa beli pas takaran resep)</option>
                            <option value="kemasan">Kemasan Utuh / Wajib Pack (kelipatan 1 pack utuh)</option>
                            <option value="satuan">Eceran Satuan (per butir / per sachet)</option>
                        </select>
                        @error('selling_mode')
                            <p class="text-[11px] text-[#C83B2B] mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Field 3: Berat Kemasan & Harga Kemasan Pasar Utuh (2 Kolom) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Berat per Kemasan (Gram) -->
                        <div>
                            <label for="material-package-weight" class="block text-xs font-bold uppercase tracking-wider text-[#161210] mb-1.5">
                                Berat per Kemasan (Gram)
                            </label>
                            <div class="relative">
                                <input
                                    type="text"
                                    inputmode="decimal"
                                    id="material-package-weight"
                                    wire:model.live.debounce.250ms="package_weight"
                                    x-model="packageWeight"
                                    @input="recalcBasePrice()"
                                    placeholder="250, 500, atau 1000"
                                    class="w-full rounded-xl border-2 border-[#161210]/10 bg-white px-3 py-2.5 pr-8 text-xs sm:text-sm font-semibold text-[#161210] focus:outline-none focus:border-[#C83B2B] focus:ring-2 focus:ring-[#C83B2B]/20 transition-all"
                                >
                                <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs font-bold text-[#161210]/40">
                                    g
                                </span>
                            </div>
                            @error('package_weight')
                                <p class="text-[11px] text-[#C83B2B] mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Harga Kemasan Pasar Utuh (Rp) -->
                        <div>
                            <label for="material-package-price" class="block text-xs font-bold uppercase tracking-wider text-[#161210] mb-1.5">
                                Harga Kemasan Utuh (Rp)
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-bold text-[#161210]/40">
                                    Rp
                                </span>
                                <input
                                    type="text"
                                    inputmode="decimal"
                                    id="material-package-price"
                                    wire:model.live.debounce.250ms="package_price"
                                    x-model="packagePrice"
                                    @input="recalcBasePrice()"
                                    placeholder="Contoh: 10000 atau 9000"
                                    class="w-full rounded-xl border-2 border-[#161210]/10 bg-white pl-9 pr-3 py-2.5 text-xs sm:text-sm font-semibold text-[#161210] focus:outline-none focus:border-[#C83B2B] focus:ring-2 focus:ring-[#C83B2B]/20 transition-all"
                                >
                            </div>
                            @error('package_price')
                                <p class="text-[11px] text-[#C83B2B] mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Field 4: Harga Dasar Acuan (Otomatis dari Kemasan, Bisa Override Manual) -->
                    <div class="rounded-xl border border-[#161210]/10 bg-white p-3.5 space-y-2.5">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <label for="material-price" class="block text-xs font-bold uppercase tracking-wider text-[#161210]">
                                Harga Dasar Acuan (Rp) <span class="text-[#C83B2B]">*</span>
                            </label>
                            <label class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-[#161210]/70 cursor-pointer select-none">
                                <input
                                    id="material-manual-price"
                                    name="manual_price_override"
                                    type="checkbox"
                                    wire:model.live="manualPriceOverride"
                                    x-model="manualOverride"
                                    @change="if (!manualOverride) recalcBasePrice()"
                                    class="w-3.5 h-3.5 rounded text-[#C83B2B] focus:ring-[#C83B2B]/30 border-[#161210]/25"
                                >
                                <span>Atur manual</span>
                            </label>
                        </div>

                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-xs font-bold text-[#161210]/40">
                                Rp
                            </span>
                            <input
                                type="text"
                                inputmode="decimal"
                                id="material-price"
                                wire:model="price"
                                x-model="basePrice"
                                :readonly="!manualOverride"
                                placeholder="Otomatis dari Harga ÷ Berat"
                                :class="manualOverride ? 'bg-white border-[#C83B2B]/40 text-[#161210]' : 'bg-[#FAF6F0] border-[#161210]/10 text-[#161210]/80 cursor-not-allowed'"
                                class="w-full rounded-xl border-2 pl-11 pr-20 py-2.5 text-xs sm:text-sm font-mono font-bold placeholder:text-[#161210]/30 focus:outline-none focus:border-[#C83B2B] focus:ring-2 focus:ring-[#C83B2B]/20 transition-all"
                            >
                            <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-[11px] font-mono text-[#161210]/60">
                                / <span x-text="sellingMode === 'satuan' ? String(packageUnit || 'satuan').toLowerCase() : 'gram'"></span>
                            </span>
                        </div>

                        <div x-show="manualOverride" class="flex items-start gap-2 text-[11px] text-amber-900 bg-amber-50 rounded-lg px-2.5 py-2 border border-amber-200">
                            <span class="font-bold shrink-0">Mode Manual Aktif:</span>
                            <span>Harga dasar tidak mengikuti rumus kemasan otomatis. Pastikan sesuai dengan harga jual pasar.</span>
                        </div>

                        <div x-show="!manualOverride" class="flex items-start gap-2 text-[11px] text-[#2D4235] bg-[#E8ECE4]/70 rounded-lg px-2.5 py-2 border border-[#2D4235]/15">
                            <span class="font-bold shrink-0">⚡ Otomatis:</span>
                            <span x-show="sellingMode === 'satuan'">
                                Mode eceran satuan memakai harga kemasan penuh (<strong class="font-mono">Rp <span x-text="formatIdr(basePrice)"></span></strong> / satuan).
                            </span>
                            <span x-show="sellingMode !== 'satuan'">
                                <strong class="font-mono">Rp <span x-text="formatIdr(packagePrice)"></span></strong> &divide;
                                <strong class="font-mono"><span x-text="formatIdr(packageWeight)"></span>g</strong> =
                                <strong class="font-mono text-[#B84E26]">Rp <span x-text="formatIdr(basePrice)"></span> / gram</strong>.
                            </span>
                        </div>
                        @error('price')
                            <p class="text-[11px] text-[#C83B2B] mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Field 4: Status Ketersediaan (Checkbox) -->
                    <div class="pt-2">
                        <label class="flex items-center gap-3 p-3 rounded-xl border border-[#161210]/10 bg-white cursor-pointer hover:bg-[#FAF6F0]/50 transition-colors">
                            <input
                                id="material-is-available"
                                name="is_available"
                                type="checkbox"
                                wire:model="is_available"
                                class="w-4 h-4 rounded text-[#C83B2B] focus:ring-[#C83B2B]/30 border-[#161210]/20"
                            >
                            <div>
                                <span class="text-xs font-bold text-[#161210] block">
                                    Bahan Tersedia & Aktif
                                </span>
                                <span class="text-[10px] text-[#161210]/60 block">
                                    Bahan ini dapat dipilih pelanggan dalam kalkulator dan tampil di katalog.
                                </span>
                            </div>
                        </label>
                    </div>

                    <!-- Footer Tombol Aksi -->
                    <div class="pt-4 flex items-center justify-end gap-3 border-t border-[#161210]/10">
                        <button
                            type="button"
                            wire:click="closeModal"
                            class="px-5 py-2.5 rounded-xl border border-[#161210]/15 hover:bg-[#161210]/5 text-[#161210] font-semibold text-xs transition-colors cursor-pointer"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="save"
                            class="px-6 py-2.5 rounded-xl bg-[#C83B2B] hover:bg-[#A92F21] text-white font-bold text-xs shadow-md shadow-[#C83B2B]/20 transition-all duration-200 active:scale-[0.99] cursor-pointer flex items-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed"
                        >
                            <span wire:loading.remove wire:target="save">{{ $isEditing ? 'Simpan Perubahan' : 'Tambah Bahan' }}</span>
                            <span wire:loading wire:target="save" class="flex items-center gap-1.5">
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
</main>
