<?php

namespace App\Livewire\Admin;

use App\Models\Material;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Kelola Harga Bahan — BASOKU Admin')]
class ManageMaterials extends Component
{
    // State Modal
    public bool $showModal = false;

    public bool $isEditing = false;

    public ?int $materialId = null;

    // Form Fields
    public string $name = '';

    public string $category = 'daging';

    public string $unit = 'gram';

    public $price = '';

    public bool $is_available = true;

    // Filter Kategori (opsional di tabel)
    public string $selectedCategory = 'semua';

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
        $this->unit = $material->unit;
        // Format harga tanpa trailing desimal jika bulat
        $this->price = (float) $material->price;
        $this->is_available = (bool) $material->is_available;

        $this->isEditing = true;
        $this->showModal = true;
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
        $this->validate([
            'name' => ['required', 'string', 'max:100'],
            'category' => ['required', 'string', 'max:50'],
            'unit' => ['required', 'string', 'max:20'],
            'price' => ['required', 'numeric', 'min:0'],
            'is_available' => ['boolean'],
        ], [
            'name.required' => 'Nama bahan baku wajib diisi.',
            'category.required' => 'Kategori bahan wajib dipilih.',
            'unit.required' => 'Satuan acuan wajib dipilih.',
            'price.required' => 'Harga bahan wajib diisi.',
            'price.numeric' => 'Harga harus berupa angka.',
            'price.min' => 'Harga tidak boleh bernilai negatif.',
        ]);

        if ($this->isEditing && $this->materialId) {
            $material = Material::findOrFail($this->materialId);
            $material->update([
                'name' => trim($this->name),
                'category' => strtolower(trim($this->category)),
                'unit' => trim($this->unit),
                'price' => $this->price,
                'is_available' => $this->is_available,
            ]);

            session()->flash('success', "Harga dan data bahan '{$material->name}' berhasil diperbarui.");
        } else {
            $material = Material::create([
                'name' => trim($this->name),
                'category' => strtolower(trim($this->category)),
                'unit' => trim($this->unit),
                'price' => $this->price,
                'is_available' => $this->is_available,
            ]);

            session()->flash('success', "Bahan baru '{$material->name}' berhasil ditambahkan ke katalog.");
        }

        $this->closeModal();
    }

    /**
     * Hapus bahan baku dari katalog.
     */
    public function deleteMaterial(int $id)
    {
        $material = Material::findOrFail($id);
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
        $this->unit = 'gram';
        $this->price = '';
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

        // Ambil daftar kategori unik untuk filter tab
        $allCategories = Material::select('category')->distinct()->pluck('category')->toArray();

        return view('livewire.admin.manage-materials', [
            'materials' => $materials,
            'allCategories' => $allCategories,
        ]);
    }
}
