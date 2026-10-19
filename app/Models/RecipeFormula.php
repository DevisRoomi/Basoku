<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecipeFormula extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'recipe_formulas';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'name',
        'meat_ratio',
        'flour_ratio',
        'spice_ratio',
        'is_simulation',
        'description',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'meat_ratio' => 'decimal:4',
            'flour_ratio' => 'decimal:4',
            'spice_ratio' => 'decimal:4',
            'is_simulation' => 'boolean',
        ];
    }

    /**
     * Nilai rasio bawaan (fallback) jika database kosong.
     *
     * @return array<string, array{name: string, meat_ratio: float, flour_ratio: float, spice_ratio: float, is_simulation: bool, description: string}>
     */
    public static function defaultFormulas(): array
    {
        return [
            'seimbang' => [
                'name' => 'Racikan Seimbang',
                'meat_ratio' => 1.0000,
                'flour_ratio' => 0.2000,
                'spice_ratio' => 0.0500,
                'is_simulation' => true,
                'description' => 'Rasio seimbang standar pasar (tepung 20%, bumbu 5%). Paling disukai pelanggan harian.',
            ],
            'enak' => [
                'name' => 'Racikan Enak (Spesial Daging)',
                'meat_ratio' => 1.0000,
                'flour_ratio' => 0.1000,
                'spice_ratio' => 0.0500,
                'is_simulation' => true,
                'description' => 'Dominan serat daging asli dengan sedikit tepung (10%). Tekstur kenyal alami dan cita rasa gurih premium.',
            ],
            'ekonomis' => [
                'name' => 'Racikan Ekonomis',
                'meat_ratio' => 1.0000,
                'flour_ratio' => 0.4000,
                'spice_ratio' => 0.0500,
                'is_simulation' => true,
                'description' => 'Rasio tepung lebih tinggi (40%) untuk kuantitas butir bakso lebih banyak. Cocok untuk hajatan atau jualan keliling.',
            ],
        ];
    }
}
