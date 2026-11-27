<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Material extends Model
{
    use HasFactory;

    public const CACHE_KEY = 'basoku.materials.all';

    /**
     * Slug kanonik untuk 8 bahan inti yang dirujuk langsung oleh Kalkulator Racikan.
     *
     * @var list<string>
     */
    public const CORE_SLUGS = [
        'daging-sapi',
        'daging-ayam',
        'tepung-tapioka',
        'tepung-probakso',
        'tepung-terigu',
        'tepung-sagu',
        'bawang-putih-kupas',
        'garam-kasar',
    ];

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
        'slug',
        'category',
        'selling_mode',
        'unit',
        'package_unit',
        'package_weight',
        'package_price',
        'price',
        'is_available',
    ];

    protected static function booted(): void
    {
        static::creating(function (Material $material): void {
            if (empty($material->slug)) {
                $material->slug = self::generateUniqueSlug((string) $material->name);
            }
        });

        static::saved(fn (): null => self::clearCache());
        static::deleted(fn (): null => self::clearCache());
    }

    /**
     * Ambil seluruh data bahan dengan cache ringan (otomatis terinvalidasi saat bahan ditambah/diubah/dihapus).
     *
     * @return EloquentCollection<int, Material>
     */
    public static function cachedAll(): EloquentCollection
    {
        $cached = Cache::get(self::CACHE_KEY);

        if (is_array($cached)) {
            return self::hydrate($cached);
        }

        if ($cached !== null) {
            self::clearCache();
        }

        $materials = self::query()->orderBy('name')->get();

        Cache::put(
            self::CACHE_KEY,
            $materials->map(fn (self $material): array => $material->getAttributes())->all(),
            now()->addMinutes(10)
        );

        return $materials;
    }

    /**
     * Bersihkan cache daftar bahan baku.
     */
    public static function clearCache(): null
    {
        try {
            Cache::forget(self::CACHE_KEY);
        } catch (\Throwable) {
            // Abaikan bila cache store belum siap
        }

        return null;
    }

    /**
     * Apakah bahan ini termasuk 8 bahan inti kalkulator yang sebaiknya dinonaktifkan (bukan dihapus permanen).
     */
    public function isCoreMaterial(): bool
    {
        return in_array((string) $this->slug, self::CORE_SLUGS, true);
    }

    /**
     * Hasilkan slug unik berbasis nama bahan baku.
     */
    public static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug(trim($name)) ?: 'bahan-kios';
        $candidate = $base;
        $suffix = 2;

        while (
            self::query()
                ->where('slug', $candidate)
                ->when($ignoreId !== null, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $candidate = $base.'-'.$suffix;
            $suffix++;
        }

        return $candidate;
    }

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
