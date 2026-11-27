<?php

namespace App\Livewire\Admin;

use App\Models\Material;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Kelola Harga Bahan — BASOKU Admin')]
class ManageMaterials extends Component
{
    // State Modal
    public bool $showModal = false;

    public bool $isEditing = false;

    #[Locked]
    public ?int $materialId = null;

    // Form Fields
    public string $name = '';

    public string $category = 'daging';

    public string $selling_mode = 'kemasan';

    public string $unit = 'gram';

    public string $package_unit = 'Pack';

    public $package_weight = null;

    public $package_price = null;

    public $price = '';

    public bool $manualPriceOverride = false;

    public bool $is_available = true;

    // Filter Kategori (opsional di tabel)
    public string $selectedCategory = 'semua';

    /**
     * Pastikan pengguna memiliki peran admin pada setiap lifecycle request.
     */
    public function boot(): void
    {
        if (! Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Akses khusus administrator BASOKU.');
        }
    }

    /**
     * Pastikan hanya admin yang dapat mengakses.
     */
    public function mount()
    {
        if (! Auth::check() || Auth::user()->role !== 'admin') {
            return $this->redirect('/masuk', navigate: true);
        }
    }

    /**
     * Buka modal untuk menambah bahan baru.
     */
    public function openAddModal()
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->showModal = true;
    }

    /**
     * Buka modal untuk mengedit bahan yang sudah ada.
     */
    public function openEditModal(int $id)
    {
        $material = Material::findOrFail($id);

        $this->resetErrorBag();
        $this->resetValidation();

        $this->materialId = $material->id;
        $this->name = $material->name;
        $this->category = $material->category;
        $this->selling_mode = $material->selling_mode ?? 'kemasan';
        $this->unit = $material->unit;
        $this->package_unit = $material->package_unit ?? 'Pack';
        $this->package_weight = $material->package_weight ? (float) $material->package_weight : null;
        $this->package_price = $material->package_price ? (float) $material->package_price : null;
        // Format harga tanpa trailing desimal jika bulat
        $this->price = (float) $material->price;
        $this->manualPriceOverride = false;
        $this->is_available = (bool) $material->is_available;

        $this->isEditing = true;
        $this->showModal = true;
    }

    /**
     * Normalisasi string angka Indonesia:
     * - Mendukung format ribuan bertitik ("10.000", "140.000", "140.000,50")
     * - Mendukung desimal koma ("12,5", "2750,5") maupun desimal titik ("12.5", "0.25")
     */
    protected function normalizeDecimal(mixed $value): ?float
    {
        if ($value === null) {
            return null;
        }

        if (is_int($value) || is_float($value)) {
            return is_finite((float) $value) ? (float) $value : null;
        }

        $cleaned = str_replace(' ', '', trim((string) $value));
        if ($cleaned === '') {
            return null;
        }

        if (str_contains($cleaned, '.') && str_contains($cleaned, ',')) {
            // Format Indonesia penuh: "140.000,50" -> "140000.50"
            $cleaned = str_replace(['.', ','], ['', '.'], $cleaned);
        } elseif (preg_match('/^-?\d{1,3}(\.\d{3})+$/', $cleaned)) {
            // Format ribuan Indonesia tanpa koma: "10.000" -> "10000"
            $cleaned = str_replace('.', '', $cleaned);
        } else {
            $cleaned = str_replace(',', '.', $cleaned);
        }

        if (! is_numeric($cleaned)) {
            return null;
        }

        return (float) $cleaned;
    }

    /**
     * Hitung ulang harga dasar acuan (per gram / per unit) otomatis dari harga & berat kemasan.
     */
    protected function syncBasePriceFromPackage(): void
    {
        if ($this->manualPriceOverride) {
            return;
        }

        $pkgPrice = $this->normalizeDecimal($this->package_price);
        $pkgWeight = $this->normalizeDecimal($this->package_weight);

        if ($pkgPrice === null || $pkgPrice <= 0) {
            return;
        }

        if ($this->selling_mode === 'satuan') {
            $this->price = round($pkgPrice, 2);
        } elseif ($pkgWeight !== null && $pkgWeight > 0) {
            $this->price = round($pkgPrice / $pkgWeight, 2);
        }
    }

    public function updatedPackagePrice(): void
    {
        $this->syncBasePriceFromPackage();
    }

    public function updatedPackageWeight(): void
    {
        $this->syncBasePriceFromPackage();
    }

    public function updatedSellingMode(): void
    {
        if ($this->selling_mode === 'satuan' && trim($this->package_unit) !== '') {
            $this->unit = trim($this->package_unit);
        } elseif ($this->selling_mode !== 'satuan' && trim($this->unit) === '') {
            $this->unit = 'gram';
        }

        $this->syncBasePriceFromPackage();
    }

    public function updatedPackageUnit(): void
    {
        if ($this->selling_mode === 'satuan' && trim($this->package_unit) !== '') {
            $this->unit = trim($this->package_unit);
        }
    }

    public function updatedManualPriceOverride(bool $value): void
    {
        if (! $value) {
            $this->syncBasePriceFromPackage();
        }
    }

    /**
     * Tutup modal dan bersihkan form input.
     */
    public function closeModal()
    {
        $this->resetForm();
        $this->showModal = false;
    }

    /**
     * Toggle ketersediaan bahan baku secara instan.
     */
    public function toggleAvailability(int $id)
    {
        $material = Material::findOrFail($id);
        $material->is_available = ! $material->is_available;
        $material->save();

        $statusText = $material->is_available ? 'Tersedia' : 'Habis / Nonaktif';
        session()->flash('success', "Status bahan '{$material->name}' diubah menjadi: {$statusText}.");
    }

    /**
     * Simpan data bahan baku (tambah baru atau perbarui).
     */
    public function save()
    {
        // Normalisasi input desimal & ribuan Indonesia sebelum validasi
        if (is_string($this->package_weight)) {
            $trimmedWeight = trim($this->package_weight);
            $normalizedWeight = $this->normalizeDecimal($trimmedWeight);
            $this->package_weight = $trimmedWeight === '' ? null : ($normalizedWeight ?? $trimmedWeight);
        }
        if (is_string($this->package_price)) {
            $trimmedPkgPrice = trim($this->package_price);
            $normalizedPkgPrice = $this->normalizeDecimal($trimmedPkgPrice);
            $this->package_price = $trimmedPkgPrice === '' ? null : ($normalizedPkgPrice ?? $trimmedPkgPrice);
        }
        if (is_string($this->price)) {
            $trimmedPrice = trim($this->price);
            $normalizedPrice = $this->normalizeDecimal($trimmedPrice);
            $this->price = $trimmedPrice === '' ? '' : ($normalizedPrice ?? $trimmedPrice);
        }

        // Jika harga dasar belum diisi saat menambah bahan baru, coba hitung otomatis dari kemasan
        if (($this->price === '' || $this->price === null) && ! $this->manualPriceOverride) {
            $this->syncBasePriceFromPackage();
        }

        $this->name = trim($this->name);
        $this->package_unit = trim($this->package_unit);
        $this->unit = trim($this->unit);

        // Sinkronisasi otomatis unit jika kosong atau bahan baru mode satuan masih memakai default 'gram'
        if ($this->unit === '' || (! $this->isEditing && $this->selling_mode === 'satuan' && strtolower($this->unit) === 'gram' && $this->package_unit !== '')) {
            $this->unit = $this->package_unit !== '' ? $this->package_unit : 'gram';
        }

        $this->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('materials', 'name')->ignore($this->materialId),
            ],
            'category' => ['required', 'string', 'in:daging,tepung,bumbu,penyedap,pengikat,tambahan'],
            'selling_mode' => ['required', 'in:curah,kemasan,satuan'],
            'unit' => ['required', 'string', 'max:20'],
            'package_unit' => ['nullable', 'string', 'max:30'],
            'package_weight' => ['nullable', 'numeric', 'gt:0', 'max:99999999'],
            'package_price' => ['nullable', 'numeric', 'gt:0', 'max:9999999999'],
            'price' => ['required', 'numeric', 'min:0', 'max:9999999999'],
            'is_available' => ['boolean'],
        ], [
            'name.required' => 'Nama bahan baku wajib diisi.',
            'name.unique' => 'Nama bahan baku ini sudah terdaftar di katalog.',
            'category.required' => 'Kategori bahan wajib dipilih.',
            'category.in' => 'Kategori bahan tidak valid.',
            'selling_mode.required' => 'Mode jual bahan wajib dipilih.',
            'unit.required' => 'Satuan acuan wajib diisi.',
            'package_weight.gt' => 'Berat kemasan harus lebih dari 0 gram.',
            'package_price.gt' => 'Harga kemasan harus lebih dari Rp 0.',
            'price.required' => 'Harga bahan wajib diisi.',
            'price.numeric' => 'Harga harus berupa angka.',
            'price.min' => 'Harga tidak boleh bernilai negatif.',
        ]);

        $finalWeight = $this->package_weight !== null && $this->package_weight !== '' ? (float) $this->package_weight : null;
        $finalPkgPrice = $this->package_price !== null && $this->package_price !== '' ? (float) $this->package_price : null;
        $finalPrice = (float) $this->price;

        // Jaga konsistensi harga katalog (package_price) vs kalkulator (price) bila admin langsung mengubah harga dasar
        if (! $this->manualPriceOverride && $finalWeight !== null && $finalWeight > 0) {
            if ($this->selling_mode === 'satuan' && ($finalPkgPrice === null || round($finalPkgPrice, 2) !== round($finalPrice, 2))) {
                $finalPkgPrice = round($finalPrice, 2);
            } elseif ($this->selling_mode !== 'satuan' && ($finalPkgPrice === null || round($finalPkgPrice / $finalWeight, 2) !== round($finalPrice, 2))) {
                $finalPkgPrice = round($finalPrice * $finalWeight, 2);
            }
        }

        $payload = [
            'name' => $this->name,
            'category' => strtolower(trim($this->category)),
            'selling_mode' => $this->selling_mode,
            'unit' => $this->unit,
            'package_unit' => $this->package_unit !== '' ? $this->package_unit : null,
            'package_weight' => $finalWeight,
            'package_price' => $finalPkgPrice,
            'price' => $finalPrice,
            'is_available' => $this->is_available,
        ];

        if ($this->isEditing && $this->materialId) {
            $material = Material::findOrFail($this->materialId);
            // Pertahankan slug kanonik lama saat nama bahan diedit agar referensi kalkulator tetap stabil
            $material->update($payload);

            session()->flash('success', "Harga dan data bahan '{$material->name}' berhasil diperbarui.");
        } else {
            $payload['slug'] = Material::generateUniqueSlug($this->name);
            $material = Material::create($payload);

            session()->flash('success', "Bahan baru '{$material->name}' berhasil ditambahkan ke katalog.");
        }

        $this->closeModal();
    }

    /**
     * Hapus bahan baku non-inti dari katalog (bahan inti kalkulator diproteksi agar kalkulasi tetap stabil).
     */
    public function deleteMaterial(int $id): void
    {
        $material = Material::findOrFail($id);

        if ($material->isCoreMaterial()) {
            session()->flash('error', "Bahan inti '{$material->name}' digunakan langsung oleh Kalkulator Racikan sehingga tidak dapat dihapus permanen. Ubah statusnya menjadi 'Habis' bila stok kosong.");

            return;
        }

        $name = $material->name;
        $material->delete();

        session()->flash('success', "Bahan '{$name}' telah dihapus dari sistem.");
    }

    /**
     * Reset field form.
     */
    protected function resetForm()
    {
        $this->materialId = null;
        $this->name = '';
        $this->category = 'daging';
        $this->selling_mode = 'kemasan';
        $this->unit = 'gram';
        $this->package_unit = 'Pack';
        $this->package_weight = null;
        $this->package_price = null;
        $this->price = '';
        $this->manualPriceOverride = false;
        $this->is_available = true;
        $this->isEditing = false;
        $this->resetErrorBag();
        $this->resetValidation();
    }

    /**
     * Logout sesi admin.
     */
    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return $this->redirect('/masuk', navigate: true);
    }

    public function render()
    {
        $query = Material::query();

        if ($this->selectedCategory !== 'semua') {
            $query->where('category', $this->selectedCategory);
        }

        $materials = $query->orderBy('category')->orderBy('name')->get();

        // Ambil daftar kategori unik dan jumlah total untuk filter tab (tanpa query di dalam Blade)
        $allCategories = Material::select('category')->distinct()->pluck('category')->toArray();
        $totalMaterialsCount = $this->selectedCategory === 'semua'
            ? $materials->count()
            : Material::count();

        return view('livewire.admin.manage-materials', [
            'materials' => $materials,
            'allCategories' => $allCategories,
            'totalMaterialsCount' => $totalMaterialsCount,
        ]);
    }
}
