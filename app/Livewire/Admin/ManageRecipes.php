<?php

namespace App\Livewire\Admin;

use App\Models\RecipeFormula;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Konfigurasi Formula Resep — BASOKU Admin')]
class ManageRecipes extends Component
{
    // State Modal Edit
    public bool $showModal = false;

    public ?int $recipeId = null;

    public string $code = '';

    public string $name = '';

    public int|float|string $meat_ratio = 1.0;

    public int|float|string $flour_ratio = 0.2;

    public int|float|string $spice_ratio = 0.05;

    public bool $is_simulation = true;

    public ?string $description = '';

    /**
     * Otorisasi role admin.
     */
    public function mount(): mixed
    {
        if (! Auth::check() || Auth::user()->role !== 'admin') {
            return $this->redirect('/masuk', navigate: true);
        }

        // Pastikan formula default tersedia jika tabel kosong
        $this->ensureDefaultFormulasExist();

        return null;
    }

    /**
     * Pastikan tabel terisi setidaknya formula dasar jika belum pernah di-seed.
     */
    protected function ensureDefaultFormulasExist(): void
    {
        if (RecipeFormula::count() === 0) {
            foreach (RecipeFormula::defaultFormulas() as $code => $data) {
                RecipeFormula::create(array_merge($data, ['code' => $code]));
            }
        }
    }

    /**
     * Buka modal untuk mengubah rasio formula.
     */
    public function openEditModal(int $id): void
    {
        $formula = RecipeFormula::findOrFail($id);

        $this->resetErrorBag();
        $this->resetValidation();

        $this->recipeId = $formula->id;
        $this->code = $formula->code;
        $this->name = $formula->name;
        $this->meat_ratio = (float) $formula->meat_ratio;
        $this->flour_ratio = (float) $formula->flour_ratio;
        $this->spice_ratio = (float) $formula->spice_ratio;
        $this->is_simulation = (bool) $formula->is_simulation;
        $this->description = $formula->description;

        $this->showModal = true;
    }

    /**
     * Tutup modal pengeditan.
     */
    public function closeModal(): void
    {
        $this->showModal = false;
        $this->recipeId = null;
        $this->resetErrorBag();
        $this->resetValidation();
    }

    /**
     * Toggle status simulasi/resmi langsung dari baris tabel.
     */
    public function toggleSimulation(int $id): void
    {
        $formula = RecipeFormula::findOrFail($id);
        $formula->is_simulation = ! $formula->is_simulation;
        $formula->save();

        $statusLabel = $formula->is_simulation ? 'Simulasi (Menunggu Konfirmasi Toko)' : 'Resmi (Terverifikasi Toko)';
        session()->flash('success', "Status formula '{$formula->name}' diubah menjadi: {$statusLabel}.");
    }

    /**
     * Simpan perubahan rasio formula ke database.
     */
    public function save(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:100'],
            'meat_ratio' => ['required', 'numeric', 'min:0.1', 'max:5'],
            'flour_ratio' => ['required', 'numeric', 'min:0', 'max:2'],
            'spice_ratio' => ['required', 'numeric', 'min:0', 'max:1'],
            'is_simulation' => ['boolean'],
            'description' => ['nullable', 'string', 'max:500'],
        ], [
            'name.required' => 'Nama formula wajib diisi.',
            'meat_ratio.required' => 'Rasio daging wajib diisi.',
            'meat_ratio.numeric' => 'Rasio daging harus berupa angka.',
            'flour_ratio.required' => 'Rasio tepung wajib diisi.',
            'flour_ratio.numeric' => 'Rasio tepung harus berupa angka.',
            'spice_ratio.required' => 'Rasio bumbu wajib diisi.',
            'spice_ratio.numeric' => 'Rasio bumbu harus berupa angka.',
        ]);

        if ($this->recipeId) {
            $formula = RecipeFormula::findOrFail($this->recipeId);
            $formula->update([
                'name' => trim($this->name),
                'meat_ratio' => (float) $this->meat_ratio,
                'flour_ratio' => (float) $this->flour_ratio,
                'spice_ratio' => (float) $this->spice_ratio,
                'is_simulation' => $this->is_simulation,
                'description' => $this->description ? trim($this->description) : null,
            ]);

            session()->flash('success', "Konfigurasi formula '{$formula->name}' berhasil diperbarui.");
        }

        $this->closeModal();
    }

    /**
     * Pulihkan semua rasio ke standar formula acuan awal.
     */
    public function resetToDefaults(): void
    {
        foreach (RecipeFormula::defaultFormulas() as $code => $data) {
            RecipeFormula::updateOrCreate(
                ['code' => $code],
                array_merge($data, ['code' => $code])
            );
        }

        session()->flash('success', 'Semua formula racikan berhasil dipulihkan ke nilai acuan standar.');
    }

    public function render(): View
    {
        $formulas = RecipeFormula::orderBy('id')->get();

        return view('livewire.admin.manage-recipes', [
            'formulas' => $formulas,
        ]);
    }
}
