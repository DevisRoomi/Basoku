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
     * Hitung format rasio Daging : Tepung yang disederhanakan (mis. "5 : 1", "1 : 1", "1 : 2").
     */
    public function formattedMeatFlourRatio(): string
    {
        return self::formatRatio((float) $this->meat_ratio, (float) $this->flour_ratio);
    }

    /**
     * Format persentase tepung terhadap daging secara ringkas (mis. "20%", "100%", "12,5%").
     */
    public function flourPercentageLabel(): string
    {
        return self::formatPercentage((float) $this->flour_ratio);
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
    public static function formatPerKgExample(float $flourRatio): string
    {
        $flourGrams = (int) round(1000 * max(0.0, $flourRatio));

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
            $dbFormulas = self::whereIn('code', $order)->get()->keyBy('code');
        } catch (\Throwable) {
            $dbFormulas = collect();
        }

        $presets = [];
        foreach ($order as $code) {
            $db = $dbFormulas->get($code);
            $fallback = $defaults[$code];

            $meatRatio = (float) ($db?->meat_ratio ?? $fallback['meat_ratio']);
            $flourRatio = (float) ($db?->flour_ratio ?? $fallback['flour_ratio']);
            $spiceRatio = (float) ($db?->spice_ratio ?? $fallback['spice_ratio']);
            $ratioLabel = self::formatRatio($meatRatio, $flourRatio);

            $presets[$code] = [
                'code' => $code,
                'name' => (string) ($db?->name ?? $fallback['name']),
                'description' => (string) ($db?->description ?: $fallback['description']),
                'meat_ratio' => $meatRatio,
                'flour_ratio' => $flourRatio,
                'spice_ratio' => $spiceRatio,
                'flour_pct' => self::formatPercentage($flourRatio),
                'ratio_label' => $ratioLabel,
                'explicit_ratio' => "Daging {$ratioLabel} Tepung",
                'per_kg_example' => self::formatPerKgExample($flourRatio),
                'is_simulation' => (bool) ($db ? $db->is_simulation : $fallback['is_simulation']),
            ];
        }

        return $presets;
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
