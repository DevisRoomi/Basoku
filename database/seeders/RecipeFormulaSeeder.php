<?php

namespace Database\Seeders;

use App\Models\RecipeFormula;
use Illuminate\Database\Seeder;

class RecipeFormulaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $formulas = RecipeFormula::defaultFormulas();

        foreach ($formulas as $code => $data) {
            RecipeFormula::updateOrCreate(
                ['code' => $code],
                array_merge($data, ['code' => $code])
            );
        }
    }
}
