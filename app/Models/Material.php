<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Material extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'materials';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'category',
        'selling_mode',
        'unit',
        'package_unit',
        'package_weight',
        'package_price',
        'price',
        'is_available',
    ];

    /**
     * Apakah bahan dijual timbang bebas / curah (bisa beli pas sesuai takaran resep).
     */
    public function isLoose(): bool
    {
        return ($this->selling_mode ?? 'kemasan') === 'curah';
    }

    /**
     * Apakah bahan wajib dibeli dalam kelipatan kemasan utuh (pack/bungkus segel).
     */
    public function isPackaged(): bool
    {
        return ($this->selling_mode ?? 'kemasan') === 'kemasan';
    }

    /**
     * Apakah bahan dijual per satuan bijian/sachet (telur, merica sachet, sobaso, masako).
     */
    public function isUnitBased(): bool
    {
        return ($this->selling_mode ?? 'kemasan') === 'satuan';
    }

    /**
     * Label ramah bahasa Indonesia untuk mode jual bahan di pasar.
     */
    public function sellingModeLabel(): string
    {
        return match ($this->selling_mode ?? 'kemasan') {
            'curah' => 'Timbang Bebas',
            'satuan' => 'Eceran Satuan',
            default => 'Kemasan Utuh',
        };
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'package_weight' => 'decimal:2',
            'package_price' => 'decimal:2',
            'price' => 'decimal:2',
            'is_available' => 'boolean',
        ];
    }

    /**
     * Get the order items associated with this material.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
