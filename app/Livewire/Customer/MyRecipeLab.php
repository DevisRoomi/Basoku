<?php

namespace App\Livewire\Customer;

use App\Models\SavedRecipe;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Laboratorium Racikan Saya — BASOKU')]
class MyRecipeLab extends Component
{
    /**
     * Pastikan hanya pengguna terautentikasi yang dapat mengakses.
     */
    public function mount()
    {
        if (! Auth::check()) {
            return $this->redirect('/masuk', navigate: true);
        }
    }

    /**
     * Hapus racikan yang tersimpan.
     */
    public function deleteRecipe(int $id)
    {
        $recipe = SavedRecipe::where('user_id', Auth::id())->findOrFail($id);
        $name = $recipe->name;
        $recipe->delete();

        session()->flash('success', "Racikan '{$name}' telah dihapus dari koleksi Anda.");
    }

    public function render()
    {
        $recipes = SavedRecipe::where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('livewire.customer.my-recipe-lab', [
            'recipes' => $recipes,
        ]);
    }
}
