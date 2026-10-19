<?php

namespace App\Livewire;

use App\Models\Material;
use Livewire\Component;

class MaterialCatalog extends Component
{
    public function render()
    {
        try {
            $materials = Material::where('is_available', true)
                ->orderBy('category')
                ->orderBy('name')
                ->get();

            $groupedMaterials = $materials->groupBy('category');
        } catch (\Throwable $e) {
            $groupedMaterials = collect();
        }

        return view('livewire.material-catalog', [
            'groupedMaterials' => $groupedMaterials,
        ]);
    }
}
