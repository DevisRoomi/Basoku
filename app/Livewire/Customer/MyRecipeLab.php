<?php

namespace App\Livewire\Customer;

use App\Models\SavedRecipe;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Laboratorium Racikan Saya — BASOKU')]
class MyRecipeLab extends Component
{
    use WithPagination;

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
        $recipe = SavedRecipe::where('user_id', Auth::id())->find($id);
        if (! $recipe) {
            return;
        }
        $name = $recipe->name;
        $recipe->delete();

        session()->flash('success', "Racikan '{$name}' telah dihapus dari koleksi Anda.");
    }

    public function render()
    {
        $query = SavedRecipe::where('user_id', Auth::id())->latest();
        $recipes = $query->paginate(12);

        if ($recipes->isEmpty() && $recipes->currentPage() > 1) {
            $targetPage = max(1, $recipes->lastPage());
            $this->setPage($targetPage);
            $recipes = $query->paginate(12, ['*'], 'page', $targetPage);
        }

        return view('livewire.customer.my-recipe-lab', [
            'recipes' => $recipes,
        ]);
    }
}
