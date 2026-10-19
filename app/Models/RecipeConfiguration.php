<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RecipeConfiguration extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'recipe_configurations';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'meat_ratio',
        'flour_ratio',
        'seasoning_ratio',
        'grinding_fee',
        'is_active',
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
            'seasoning_ratio' => 'decimal:4',
            'grinding_fee' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the saved recipes using this configuration.
     */
    public function savedRecipes(): HasMany
    {
        return $this->hasMany(SavedRecipe::class);
    }

    /**
     * Get the orders using this configuration.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
