<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavedRecipe extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'saved_recipes';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'recipe_configuration_id',
        'name',
        'meat_weight',
        'start_type',
        'preference',
        'recipe_details',
        'estimated_total',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'meat_weight' => 'decimal:3',
            'estimated_total' => 'decimal:2',
            'recipe_details' => 'array',
        ];
    }

    /**
     * Get the user that owns the saved recipe.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the recipe configuration referenced by the saved recipe.
     */
    public function recipeConfiguration(): BelongsTo
    {
        return $this->belongsTo(RecipeConfiguration::class);
    }
}
