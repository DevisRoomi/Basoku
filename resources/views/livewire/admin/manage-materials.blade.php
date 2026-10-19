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
                    Atur harga acuan bahan baku dan ketersediaan stok pasar secara *real-time*.
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
                Semua Kategori ({{ \App\Models\Material::count() }})
            </button>
            @foreach ($allCategories as $cat)
                <button
                    type="button"
                    wire:click="$set('selectedCategory', '{{ $cat }}')"
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
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl p-4 text-xs font-semibold flex items-center justify-between shadow-sm animate-fade-in">
                <div class="flex items-center gap-2.5">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800">
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
                            <th scope="col" class="py-4 px-6">Nama Bahan</th>
                            <th scope="col" class="py-4 px-6">Kategori</th>
                            <th scope="col" class="py-4 px-6">Satuan</th>
                            <th scope="col" class="py-4 px-6 text-right">Harga Satuan</th>
                            <th scope="col" class="py-4 px-6 text-center">Status</th>
                            <th scope="col" class="py-4 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#161210]/5 text-xs font-medium text-[#161210]">
                        @forelse ($materials as $material)
                            <tr class="hover:bg-[#FAF6F0]/40 transition-colors">
                                <!-- Kolom 1: Nama Bahan -->
                                <td class="py-4 px-6 font-bold text-sm">
                                    {{ $material->name }}
                                </td>

                                <!-- Kolom 2: Kategori -->
                                <td class="py-4 px-6">
                                    <span class="inline-block px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider {{ match($material->category) {
                                        'daging' => 'bg-rose-100 text-rose-800 border border-rose-200',
                                        'tepung' => 'bg-amber-100 text-amber-800 border border-amber-200',
                                        'bumbu' => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
                                        default => 'bg-[#FAF6F0] text-[#161210]/80 border border-[#161210]/10'
                                    } }}">
                                        {{ $material->category }}
                                    </span>
                                </td>

                                <!-- Kolom 3: Satuan -->
                                <td class="py-4 px-6 text-[#161210]/70">
                                    {{ $material->unit }}
                                </td>

                                <!-- Kolom 4: Harga Satuan -->
                                <td class="py-4 px-6 text-right font-extrabold text-[#C83B2B] text-sm">
                                    {{ \Illuminate\Support\Number::currency($material->price, 'IDR', 'id', precision: 0) }}
                                    <span class="text-[10px] font-normal text-[#161210]/50">/ {{ $material->unit }}</span>
                                </td>

                                <!-- Kolom 5: Status Ketersediaan (Clickable) -->
                                <td class="py-4 px-6 text-center">
                                    <button
                                        type="button"
                                        wire:click="toggleAvailability({{ $material->id }})"
                                        title="Klik untuk ubah status ketersediaan"
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold cursor-pointer transition-transform active:scale-95 {{ $material->is_available ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100' : 'bg-red-50 text-red-700 border border-red-200 hover:bg-red-100' }}"
                                    >
                                        <span class="w-1.5 h-1.5 rounded-full {{ $material->is_available ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                                        {{ $material->is_available ? 'Tersedia' : 'Habis' }}
                                    </button>
                                </td>

                                <!-- Kolom 6: Aksi -->
                                <td class="py-4 px-6 text-right space-x-2">
                                    <button
                                        type="button"
                                        wire:click="openEditModal({{ $material->id }})"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-[#FAF6F0] hover:bg-[#161210] hover:text-white text-[#161210] font-semibold text-xs border border-[#161210]/10 transition-colors cursor-pointer"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        <span>Edit</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-xs text-[#161210]/50">
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
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto bg-[#161210]/80 backdrop-blur-sm animate-fade-in">
            <div
                class="bg-[#FAF6F0] rounded-2xl w-full max-w-lg p-6 sm:p-8 shadow-2xl border border-[#161210]/10 relative z-10"
                @click.away="$wire.closeModal()"
            >
                <!-- Header Modal -->
                <div class="flex items-start justify-between pb-4 border-b border-[#161210]/10">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-[#C83B2B]">
                            {{ $isEditing ? 'Perbarui Data Bahan' : 'Bahan Baku Baru' }}
                        </span>
                        <h2 class="text-xl sm:text-2xl font-bold text-[#161210] mt-0.5" style="font-family: 'Playfair Display', Georgia, serif;">
                            {{ $isEditing ? 'Edit Bahan: ' . $name : 'Tambah Bahan Baku' }}
                        </h2>
                    </div>
                    <button
                        type="button"
                        wire:click="closeModal"
                        class="text-[#161210]/40 hover:text-[#161210] p-1.5 rounded-lg transition-colors cursor-pointer"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Isi Formulir Modal -->
                <form wire:submit.prevent="save" class="mt-6 space-y-4">
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

                    <!-- Field 2: Kategori & Satuan (2 Kolom) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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

                        <!-- Satuan -->
                        <div>
                            <label for="material-unit" class="block text-xs font-bold uppercase tracking-wider text-[#161210] mb-1.5">
                                Satuan Acuan <span class="text-[#C83B2B]">*</span>
                            </label>
                            <select
                                id="material-unit"
                                wire:model="unit"
                                class="w-full rounded-xl border-2 border-[#161210]/10 bg-white px-3 py-2.5 text-xs sm:text-sm font-medium text-[#161210] focus:outline-none focus:border-[#C83B2B] focus:ring-2 focus:ring-[#C83B2B]/20 transition-all cursor-pointer"
                            >
                                <option value="gram">gram</option>
                                <option value="kg">kg</option>
                                <option value="butir">butir</option>
                                <option value="pack">pack</option>
                            </select>
                            @error('unit')
                                <p class="text-[11px] text-[#C83B2B] mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Field 3: Harga Normalisasi -->
                    <div>
                        <label for="material-price" class="block text-xs font-bold uppercase tracking-wider text-[#161210] mb-1.5">
                            Harga Satuan (Rp) <span class="text-[#C83B2B]">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-xs font-bold text-[#161210]/40">
                                Rp
                            </span>
                            <input
                                type="number"
                                step="any"
                                id="material-price"
                                wire:model="price"
                                placeholder="Contoh: 140 (per gram) atau 40000"
                                class="w-full rounded-xl border-2 border-[#161210]/10 bg-white pl-11 pr-4 py-2.5 text-xs sm:text-sm font-semibold text-[#161210] placeholder:text-[#161210]/30 focus:outline-none focus:border-[#C83B2B] focus:ring-2 focus:ring-[#C83B2B]/20 transition-all"
                            >
                        </div>
                        <p class="text-[10px] text-[#161210]/50 mt-1">
                            *Jika satuan <code>gram</code>, masukkan harga per 1 gram (misal Rp140.000/kg diisi <code>140</code>).
                        </p>
                        @error('price')
                            <p class="text-[11px] text-[#C83B2B] mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Field 4: Status Ketersediaan (Checkbox) -->
                    <div class="pt-2">
                        <label class="flex items-center gap-3 p-3 rounded-xl border border-[#161210]/10 bg-white cursor-pointer hover:bg-[#FAF6F0]/50 transition-colors">
                            <input
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
                            class="px-6 py-2.5 rounded-xl bg-[#C83B2B] hover:bg-[#A92F21] text-white font-bold text-xs shadow-md shadow-[#C83B2B]/20 transition-all duration-200 active:scale-[0.99] cursor-pointer flex items-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed"
                        >
                            <span wire:loading.remove>{{ $isEditing ? 'Simpan Perubahan' : 'Tambah Bahan' }}</span>
                            <span wire:loading class="flex items-center gap-1.5">
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
