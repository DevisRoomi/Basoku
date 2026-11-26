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
     * Harga jual kanonik per 1 kemasan/satuan/kg acuan untuk tampilan katalog dan checkout.
     */
    public function effectivePackagePrice(): float
    {
        $pkgPrice = (float) ($this->package_price ?? 0);
        if ($pkgPrice > 0) {
            return $pkgPrice;
        }

        $basePrice = max(0.0, (float) ($this->price ?? 0));
        if ($this->isUnitBased()) {
            return $basePrice;
        }

        $pkgWeight = (float) ($this->package_weight ?? 0);
        if ($pkgWeight > 0) {
            return round($basePrice * $pkgWeight, 2);
        }

        if (strtolower((string) $this->unit) === 'kg') {
            return round($basePrice * 1000.0, 2);
        }

        return $basePrice;
    }

    /**
     * Harga dasar kanonik per 1 gram bahan (aman untuk mode curah, kemasan, maupun satuan).
     */
    public function effectivePricePerGram(): float
    {
        $pkgWeight = (float) ($this->package_weight ?? 0);

        if ($this->isUnitBased()) {
            $unitPrice = $this->effectivePackagePrice();

            return $pkgWeight > 0 ? round($unitPrice / $pkgWeight, 4) : $unitPrice;
        }

        $basePrice = (float) ($this->price ?? 0);
        if ($basePrice > 0) {
            return $basePrice;
        }

        $pkgPrice = (float) ($this->package_price ?? 0);
        if ($pkgPrice > 0 && $pkgWeight > 0) {
            return round($pkgPrice / $pkgWeight, 4);
        }

        return 0.0;
    }

    /**
     * Berat kemasan efektif dalam gram dengan fallback positif aman.
     */
    public function effectivePackageWeight(float $fallback = 1.0): float
    {
        $weight = (float) ($this->package_weight ?? 0);

        return $weight > 0 ? $weight : max(1.0, $fallback);
    }

    /**
     * Label satuan kemasan efektif (tidak pernah kosong).
     */
    public function effectivePackageUnit(): string
    {
        $unit = trim((string) ($this->package_unit ?: $this->unit ?: 'Pack'));

        return $unit !== '' ? $unit : 'Pack';
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
