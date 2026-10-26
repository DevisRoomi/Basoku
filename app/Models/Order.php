<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'orders';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'customer_name',
        'pickup_at',
        'start_type',
        'meat_weight',
        'preference',
        'grinding_fee',
        'total_price',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'pickup_at' => 'datetime',
            'meat_weight' => 'decimal:3',
            'grinding_fee' => 'decimal:2',
            'total_price' => 'decimal:2',
        ];
    }

    /**
     * Get the user that placed the order (optional).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the recipe configuration used for the order (optional).
     */
    public function recipeConfiguration(): BelongsTo
    {
        return $this->belongsTo(RecipeConfiguration::class);
    }

    /**
     * Get the items in the order.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
