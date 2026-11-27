<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RecipeFormula extends Model
{
    use HasFactory;

    public const CACHE_KEY = 'basoku.recipe_formulas.all';

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

    protected static function booted(): void
    {
        static::saved(fn (): null => self::clearCache());
        static::deleted(fn (): null => self::clearCache());
    }

    /**
     * Ambil seluruh formula rasio dengan cache ringan (otomatis terinvalidasi saat admin mengubah formula).
     *
     * @return EloquentCollection<int, RecipeFormula>
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

        $formulas = self::query()->orderBy('id')->get();

        Cache::put(
            self::CACHE_KEY,
            $formulas->map(fn (self $formula): array => $formula->getAttributes())->all(),
            now()->addMinutes(10)
        );

        return $formulas;
    }

    /**
     * Bersihkan cache formula rasio.
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
     * Hitung format rasio Daging : Tepung yang disederhanakan (mis. "5 : 1", "1 : 1", "1 : 2").
     */
    public function formattedMeatFlourRatio(): string
    {
        return self::formatRatio((float) $this->meat_ratio, (float) $this->flour_ratio);
    }

    /**
     * Normalisasi rasio tepung terhadap acuan 1 bagian daging agar selalu konsisten meski meat_ratio != 1.0.
     */
    public static function normalizedFlourRatio(float $meatRatio, float $flourRatio): float
    {
        return $meatRatio > 0 ? max(0.0, $flourRatio / $meatRatio) : max(0.0, $flourRatio);
    }

    /**
     * Normalisasi rasio bumbu terhadap acuan 1 bagian daging.
     */
    public static function normalizedSpiceRatio(float $meatRatio, float $spiceRatio): float
    {
        return $meatRatio > 0 ? max(0.0, $spiceRatio / $meatRatio) : max(0.0, $spiceRatio);
    }

    /**
     * Format persentase tepung terhadap daging secara ringkas (mis. "20%", "100%", "12,5%").
     */
    public function flourPercentageLabel(): string
    {
        return self::formatPercentage(self::normalizedFlourRatio((float) $this->meat_ratio, (float) $this->flour_ratio));
    }

    /**
     * Sederhanakan pasangan rasio daging dan tepung dengan FPB/GCD.
     */
    public static function formatRatio(float $meatRatio, float $flourRatio): string
    {
        $meatUnits = max(1, (int) round($meatRatio * 1000));
        $flourUnits = max(0, (int) round($flourRatio * 1000));

        if ($flourUnits === 0) {
            return '1 : 0';
        }

        $a = $meatUnits;
        $b = $flourUnits;
        while ($b !== 0) {
            $tmp = $a % $b;
            $a = $b;
            $b = $tmp;
        }
        $gcd = max(1, $a);

        return ($meatUnits / $gcd).' : '.($flourUnits / $gcd);
    }

    /**
     * Format angka rasio desimal menjadi teks persentase bersih.
     */
    public static function formatPercentage(float $ratio): string
    {
        $pct = round($ratio * 100, 1);

        return ((float) ((int) $pct) === $pct
            ? (string) ((int) $pct)
            : str_replace('.', ',', (string) $pct)).'%';
    }

    /**
     * Format contoh takaran tepung per acuan 1 kg daging (mis. "1 kg daging + 200g tepung" atau "1 kg daging + 2 kg tepung").
     */
    public static function formatPerKgExample(float $flourRatio, float $meatRatio = 1.0): string
    {
        $normalizedFlour = self::normalizedFlourRatio($meatRatio, $flourRatio);
        $flourGrams = (int) round(1000 * max(0.0, $normalizedFlour));

        if ($flourGrams >= 1000) {
            $kg = $flourGrams / 1000;
            $kgText = ((float) ((int) $kg) === $kg)
                ? (string) ((int) $kg)
                : str_replace('.', ',', rtrim(rtrim(number_format($kg, 2, '.', ''), '0'), '.'));

            return "1 kg daging + {$kgText} kg tepung";
        }

        return '1 kg daging + '.number_format($flourGrams, 0, ',', '.').'g tepung';
    }

    /**
     * Ambil urutan preset formula aktif (enak, seimbang, ekonomis) dari database dengan fallback aman.
     *
     * @return array<string, array{code: string, name: string, description: string, meat_ratio: float, flour_ratio: float, spice_ratio: float, flour_pct: string, ratio_label: string, explicit_ratio: string, per_kg_example: string, is_simulation: bool}>
     */
    public static function orderedPresets(): array
    {
        $defaults = self::defaultFormulas();
        $order = ['enak', 'seimbang', 'ekonomis'];

        try {
            $dbFormulas = self::cachedAll()
                ->whereIn('code', $order)
                ->keyBy('code');
        } catch (\Throwable $e) {
            Log::error('RecipeFormula::orderedPresets gagal memuat formula dari database.', [
                'error' => $e->getMessage(),
            ]);
            $dbFormulas = collect();
        }

        $presets = [];
        foreach ($order as $code) {
            $db = $dbFormulas->get($code);
            $fallback = $defaults[$code];

            $meatRatio = (float) ($db?->meat_ratio ?? $fallback['meat_ratio']);
            $flourRatio = (float) ($db?->flour_ratio ?? $fallback['flour_ratio']);
            $spiceRatio = (float) ($db?->spice_ratio ?? $fallback['spice_ratio']);
            $normalizedFlour = self::normalizedFlourRatio($meatRatio, $flourRatio);
            $ratioLabel = self::formatRatio($meatRatio, $flourRatio);

            $presets[$code] = [
                'code' => $code,
                'name' => (string) ($db?->name ?? $fallback['name']),
                'description' => (string) ($db?->description ?: $fallback['description']),
                'meat_ratio' => $meatRatio,
                'flour_ratio' => $flourRatio,
                'spice_ratio' => $spiceRatio,
                'flour_pct' => self::formatPercentage($normalizedFlour),
                'ratio_label' => $ratioLabel,
                'explicit_ratio' => "Daging {$ratioLabel} Tepung",
                'per_kg_example' => self::formatPerKgExample($flourRatio, $meatRatio),
                'is_simulation' => (bool) ($db ? $db->is_simulation : $fallback['is_simulation']),
            ];
        }

        return $presets;
    }

    /**
     * Nilai rasio bawaan resmi kios BASOKU (acuan per 1 kg daging) jika database kosong atau dipulihkan ke standar.
     *
     * @return array<string, array{name: string, meat_ratio: float, flour_ratio: float, spice_ratio: float, is_simulation: bool, description: string}>
     */
    public static function defaultFormulas(): array
    {
        return [
            'seimbang' => [
                'name' => 'Racikan Seimbang',
                'meat_ratio' => 1.0000,
                'flour_ratio' => 1.0000,
                'spice_ratio' => 0.0800,
                'is_simulation' => false,
                'description' => 'Rasio seimbang 1:1 antara daging dan tepung (100%) dengan takaran bumbu 8%. Rasa daging tetap terasa, tekstur kenyal pas, dan gurihnya seimbang—paling populer untuk jualan harian maupun stok rumahan.',
            ],
            'enak' => [
                'name' => 'Racikan Enak (Spesial Daging)',
                'meat_ratio' => 1.0000,
                'flour_ratio' => 0.2000,
                'spice_ratio' => 0.0500,
                'is_simulation' => false,
                'description' => 'Dominan serat daging asli dengan sedikit tepung (20%) dan bumbu 5%. Tekstur padat, kenyal alami, dengan cita rasa gurih daging premium.',
            ],
            'ekonomis' => [
                'name' => 'Racikan Ekonomis',
                'meat_ratio' => 1.0000,
                'flour_ratio' => 2.0000,
                'spice_ratio' => 0.1200,
                'is_simulation' => false,
                'description' => 'Rasio ekonomis 1:2 antara daging dan tepung (200%) dengan takaran bumbu 12% agar tetap gurih. Menghasilkan butiran bakso paling banyak dengan modal hemat—cocok untuk jualan keliling maupun hajatan. Catatan: ini batas maksimal tepung agar tekstur tetap bakso, bukan cilok.',
            ],
        ];
    }
}
