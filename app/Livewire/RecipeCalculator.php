<?php

namespace App\Livewire;

use App\Models\Material;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\RecipeFormula;
use App\Models\SavedRecipe;
use App\Support\CheckoutHelper;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Locked;
use Livewire\Component;

class RecipeCalculator extends Component
{
    /** Apakah komponen disematkan di beranda (mengatur heading H2 vs H1) */
    public bool $embedded = false;

    /** Mode kalkulasi: 'weight' (berdasarkan berat) atau 'budget' (berdasarkan anggaran) */
    public string $calculationMode = 'weight';

    /** Nilai anggaran dalam rupiah (default Rp 100.000; toleran input string/kosong agar tidak crash 500) */
    public int|float|string|null $budget = 100000;

    /** Pesan kesalahan jika anggaran tidak mencukupi standar minimum 500g */
    public ?string $budgetError = null;

    /** Pesan peringatan jika bahan inti yang dipilih sedang habis di kios */
    public ?string $availabilityError = null;

    /** Daftar harga & status ketersediaan bahan aktif dari database untuk tampilan UI */
    #[Locked]
    public array $catalogPrices = [];

    /** Mode racikan: 'start_from_zero', 'own_meat', atau 'ingredients_only' */
    public string $startType = 'start_from_zero';

    /** Jenis daging utama: 'sapi' atau 'ayam' */
    public string $meatType = 'sapi';

    /** Berat daging dalam gram (minimal 500; toleran input string/kosong agar tidak crash 500) */
    public int|string|null $meatWeight = 500;

    /** Preferensi karakter racikan: 'enak', 'seimbang', 'ekonomis', atau 'kustom' */
    public string $preference = 'kustom';

    /** Persentase tepung kustom jika preference = 'kustom' (10% s/d 450%) */
    public int|string|null $customFlourRatio = 20;

    /** Varian tepung yang dipilih: 'tapioka', 'probakso', 'sagu', 'terigu' */
    public string $flourType = 'tapioka';

    /** Status formula simulasi atau resmi dari kios */
    public bool $isSimulation = true;

    /** Nama formula aktif */
    public ?string $formulaName = null;

    /** Apakah menggunakan jasa selepan (penggilingan) */
    public bool $useGrinding = true;

    /** Rincian bahan terpakai dan estimasi biaya per bahan */
    #[Locked]
    public array $breakdown = [];

    /** Total biaya bahan terpakai (proporsional riil adonan) */
    #[Locked]
    public float $totalProportionalCost = 0;

    /** Total estimasi uang belanja kemasan di pasar */
    #[Locked]
    public float $totalMarketCost = 0;

    /** State modal checkout pesanan */
    public bool $showCheckoutModal = false;

    /** Form data checkout */
    public string $customerName = '';

    public string $customerPhone = '';

    public string $pickupTime = '';

    #[Locked]
    public string $sellerWaNumber = '6285122797969';

    /** State layar konfirmasi pasca-checkout (anti-hilang kode & anti-double-order) */
    #[Locked]
    public ?string $confirmedOrderCode = null;

    #[Locked]
    public float $confirmedOrderTotal = 0.0;

    #[Locked]
    public ?string $confirmedPickupLabel = null;

    #[Locked]
    public ?string $confirmedWaUrl = null;

    #[Locked]
    public ?string $confirmedTrackUrl = null;

    /** State modal simpan racikan ke laboratorium saya */
    public bool $showSaveModal = false;

    public string $recipeName = '';

    /** Bahan tambahan & bumbu pelengkap [material_id => qty] */
    public array $extraItems = [];

    public function mount(): void
    {
        $this->sellerWaNumber = (string) config('services.kiosk.wa_number', '6285122797969');

        $preset = request()->query('preset');
        if (is_string($preset) && in_array($preset, ['enak', 'seimbang', 'ekonomis', 'kustom'], true)) {
            $this->preference = $preset;
        }

        $recipeId = request()->integer('recipe_id');
        if ($recipeId > 0 && Auth::check()) {
            $saved = SavedRecipe::where('user_id', Auth::id())->find($recipeId);
            if ($saved) {
                $validStartTypes = ['start_from_zero', 'own_meat', 'ingredients_only'];
                $validPreferences = ['enak', 'seimbang', 'ekonomis', 'kustom', 'urat', 'bahan_saja', 'belanja_katalog'];

                $this->startType = in_array($saved->start_type, $validStartTypes, true)
                    ? $saved->start_type
                    : 'start_from_zero';
                $this->preference = in_array($saved->preference, $validPreferences, true)
                    ? (in_array($saved->preference, ['bahan_saja', 'belanja_katalog'], true) ? 'kustom' : $saved->preference)
                    : 'seimbang';
                $this->meatWeight = $this->startType === 'ingredients_only'
                    ? 0
                    : max(500, min(50000, (int) $saved->meat_weight));

                $this->restoreDetailsFromSavedRecipe($saved);
            }
        }

        $sessionExtras = session()->pull('selected_materials');
        if (is_array($sessionExtras) && ! empty($sessionExtras)) {
            $this->importTransferredCatalogMaterials($sessionExtras);
        }

        if (Auth::check() && ($draft = session()->pull('calculator_draft')) && is_array($draft)) {
            $this->calculationMode = in_array($draft['calculationMode'] ?? '', ['weight', 'budget'], true) ? $draft['calculationMode'] : $this->calculationMode;
            $this->budget = isset($draft['budget']) && is_numeric($draft['budget']) ? (float) $draft['budget'] : $this->budget;
            $this->startType = in_array($draft['startType'] ?? '', ['start_from_zero', 'own_meat', 'ingredients_only'], true) ? $draft['startType'] : $this->startType;
            $this->meatType = in_array($draft['meatType'] ?? '', ['sapi', 'ayam'], true) ? $draft['meatType'] : $this->meatType;
            $this->meatWeight = isset($draft['meatWeight']) && is_numeric($draft['meatWeight']) ? (int) $draft['meatWeight'] : $this->meatWeight;
            $this->flourType = in_array($draft['flourType'] ?? '', ['tapioka', 'probakso', 'sagu', 'terigu'], true) ? $draft['flourType'] : $this->flourType;
            $this->preference = in_array($draft['preference'] ?? '', ['enak', 'seimbang', 'ekonomis', 'kustom'], true) ? $draft['preference'] : $this->preference;
            $this->customFlourRatio = isset($draft['customFlourRatio']) && is_numeric($draft['customFlourRatio']) ? max(10, min(450, (int) $draft['customFlourRatio'])) : $this->customFlourRatio;
            $this->useGrinding = (bool) ($draft['useGrinding'] ?? $this->useGrinding);
            if (is_array($draft['extraItems'] ?? null)) {
                $this->extraItems = $draft['extraItems'];
            }
            $this->calculateEstimation();
            $this->recipeName = $this->defaultRecipeName();
            $this->showSaveModal = true;

            return;
        }

        $this->calculateEstimation();
    }

    /**
     * Impor item yang ditransfer dari Katalog Bahan secara cerdas:
     * - Item 'daging' dipetakan ke daging utama kalkulator agar tidak terhitung ganda.
     * - Jika membawa daging + tepung, tepung pertama dipetakan ke varian tepung utama.
     * - Jika hanya membawa bahan non-daging, otomatis aktifkan mode 'ingredients_only' agar tidak dipaksa 500g daging & tepung dobel.
     *
     * @param  array<int|string, mixed>  $selectedMaterials
     */
    protected function importTransferredCatalogMaterials(array $selectedMaterials): void
    {
        $ids = array_values(array_filter(array_map('intval', array_keys($selectedMaterials)), fn (int $id) => $id > 0));
        if (empty($ids)) {
            return;
        }

        $materials = Material::whereIn('id', $ids)->where('is_available', true)->get()->keyBy('id');
        $nonMeatExtras = [];
        $transferredMeatGrams = 0;
        $transferredFlours = [];

        foreach ($selectedMaterials as $matId => $rawQty) {
            $id = (int) $matId;
            $qty = min(100, max(0, (int) $rawQty));
            if ($qty <= 0 || ! isset($materials[$id])) {
                continue;
            }

            $mat = $materials[$id];
            $category = strtolower((string) $mat->category);

            if ($category === 'daging') {
                $this->startType = 'start_from_zero';
                $this->meatType = str_contains(strtolower((string) $mat->name), 'ayam') ? 'ayam' : 'sapi';
                $transferredMeatGrams += (int) round($qty * $mat->effectivePackageWeight(1000.0));
            } elseif ($category === 'tepung') {
                $transferredFlours[$id] = ['material' => $mat, 'qty' => $qty];
            } else {
                $nonMeatExtras[$id] = $qty;
            }
        }

        if ($transferredMeatGrams > 0) {
            $this->meatWeight = max(500, min(50000, $transferredMeatGrams));
            $this->useGrinding = true;

            $primaryFlourMapped = false;
            foreach ($transferredFlours as $id => $entry) {
                $flourKey = $this->detectFlourTypeFromName((string) $entry['material']->name);
                if (! $primaryFlourMapped && $flourKey !== null) {
                    $this->flourType = $flourKey;
                    $primaryFlourMapped = true;
                } else {
                    $nonMeatExtras[$id] = $entry['qty'];
                }
            }
        } else {
            foreach ($transferredFlours as $id => $entry) {
                $nonMeatExtras[$id] = $entry['qty'];
            }

            if (! empty($nonMeatExtras)) {
                $this->startType = 'ingredients_only';
                $this->meatWeight = 0;
                $this->useGrinding = false;
            }
        }

        if (! empty($nonMeatExtras)) {
            $this->extraItems = $nonMeatExtras;
        }
    }

    /**
     * Deteksi kunci varian tepung utama kalkulator dari nama bahan katalog.
     */
    protected function detectFlourTypeFromName(string $name): ?string
    {
        $lower = strtolower($name);

        return match (true) {
            str_contains($lower, 'probakso') => 'probakso',
            str_contains($lower, 'sagu') => 'sagu',
            str_contains($lower, 'terigu') => 'terigu',
            str_contains($lower, 'tapioka') => 'tapioka',
            default => null,
        };
    }

    /**
     * Pulihkan parameter racikan lengkap (jenis daging, tepung, rasio kustom, selepan, pelengkap)
     * dari rincian JSON resep yang tersimpan, termasuk migrasi resep 'ingredients_only' versi lama.
     */
    protected function restoreDetailsFromSavedRecipe(SavedRecipe $saved): void
    {
        $details = $saved->recipe_details;
        if (! is_array($details) || empty($details)) {
            return;
        }

        $hasGrinding = false;
        $restoredExtras = [];

        foreach ($details as $item) {
            if (! is_array($item)) {
                continue;
            }

            $name = strtolower((string) ($item['name'] ?? ''));

            if (! empty($item['is_extra']) && ! empty($item['material_id'])) {
                $matId = (int) $item['material_id'];
                $qty = min(100, max(0, isset($item['qty']) ? (int) $item['qty'] : 1));
                if ($qty > 0) {
                    $restoredExtras[$matId] = $qty;
                }

                continue;
            }

            if (! empty($item['is_meat']) || str_contains($name, 'daging')) {
                $this->meatType = str_contains($name, 'ayam') ? 'ayam' : 'sapi';

                continue;
            }

            if (str_contains($name, 'probakso')) {
                $this->flourType = 'probakso';
            } elseif (str_contains($name, 'sagu')) {
                $this->flourType = 'sagu';
            } elseif (str_contains($name, 'terigu')) {
                $this->flourType = 'terigu';
            } elseif (str_contains($name, 'tapioka')) {
                $this->flourType = 'tapioka';
            }

            $isFlourItem = str_contains($name, 'tepung')
                || str_contains($name, 'tapioka')
                || str_contains($name, 'probakso')
                || str_contains($name, 'sagu')
                || str_contains($name, 'terigu');

            if ($this->preference === 'kustom' && (int) $this->meatWeight > 0 && $isFlourItem) {
                $flourGrams = (float) ($item['weight'] ?? 0);
                if ($flourGrams > 0) {
                    $this->customFlourRatio = max(10, min(450, (int) round(($flourGrams / (int) $this->meatWeight) * 100)));
                }
            }

            // Migrasi otomatis item ingredients_only lama (berbasis 'packs') ke extraItems agar dapat diedit/dihapus di UI
            if ($this->startType === 'ingredients_only' && isset($item['packs'])) {
                $packs = min(100, max(0, (int) $item['packs']));
                if ($packs > 0 && ! empty($item['material_id'])) {
                    $restoredExtras[(int) $item['material_id']] = $packs;
                } elseif ($packs > 0) {
                    $legacySlug = match (true) {
                        str_contains($name, 'bawang') => 'bawang-putih-kupas',
                        str_contains($name, 'garam') => 'garam-kasar',
                        str_contains($name, 'probakso') => 'tepung-probakso',
                        str_contains($name, 'sagu') => 'tepung-sagu',
                        str_contains($name, 'terigu') => 'tepung-terigu',
                        $isFlourItem => 'tepung-tapioka',
                        default => null,
                    };
                    $legacyMaterial = $legacySlug ? Material::cachedAll()->firstWhere('slug', $legacySlug) : null;
                    if ($legacyMaterial) {
                        $restoredExtras[$legacyMaterial->id] = $packs;
                    }
                }
            }

            if (str_contains($name, 'selepan')) {
                $hasGrinding = true;
            }
        }

        if ($this->startType !== 'ingredients_only') {
            $this->useGrinding = $hasGrinding;
        }

        if (! empty($restoredExtras)) {
            $this->extraItems = $restoredExtras;
        }
    }

    public function updatingStartType(string $value): void
    {
        if ($this->startType === 'ingredients_only' && $value !== 'ingredients_only') {
            if (! is_numeric($this->meatWeight) || (int) $this->meatWeight < 500) {
                $this->meatWeight = 500;
            }
            $this->useGrinding = true;
        }
    }

    /**
     * Daftar preset formula aktif (enak, seimbang, ekonomis) langsung dari database admin.
     *
     * @return array<string, array{code: string, name: string, description: string, meat_ratio: float, flour_ratio: float, spice_ratio: float, flour_pct: string, ratio_label: string, explicit_ratio: string, per_kg_example: string, is_simulation: bool}>
     */
    public function getPresetFormulasProperty(): array
    {
        return RecipeFormula::orderedPresets();
    }

    public function incrementExtra(int $materialId): void
    {
        $current = (int) ($this->extraItems[$materialId] ?? 0);
        $this->extraItems[$materialId] = min(100, max(0, $current) + 1);
        $this->calculateEstimation();
    }

    public function decrementExtra(int $materialId): void
    {
        if (isset($this->extraItems[$materialId])) {
            $next = ((int) $this->extraItems[$materialId]) - 1;
            if ($next <= 0) {
                unset($this->extraItems[$materialId]);
            } else {
                $this->extraItems[$materialId] = min(100, $next);
            }
            $this->calculateEstimation();
        }
    }

    public function removeExtra(int $materialId): void
    {
        unset($this->extraItems[$materialId]);
        $this->calculateEstimation();
    }

    public function clearExtras(): void
    {
        $this->extraItems = [];
        $this->calculateEstimation();
    }

    public function getAvailableExtraMaterialsProperty()
    {
        return Material::cachedAll()
            ->where('is_available', true)
            ->filter(fn (Material $material) => strtolower((string) $material->category) !== 'daging')
            ->sortBy(fn (Material $material) => strtolower((string) $material->category).'|'.strtolower((string) $material->name))
            ->values();
    }

    public function updated($property = null): void
    {
        $this->calculateEstimation();
    }

    /**
     * Normalisasi input numerik mentah (mendukung kosong/null/koma tanpa TypeError Livewire).
     */
    protected function parseNumericInput(mixed $value, float $default): float
    {
        if ($value === null || $value === '') {
            return $default;
        }

        if (is_int($value) || is_float($value)) {
            return is_finite((float) $value) ? (float) $value : $default;
        }

        $cleaned = str_replace([' ', ','], ['', '.'], trim((string) $value));
        if ($cleaned === '' || ! is_numeric($cleaned)) {
            return $default;
        }

        $parsed = (float) $cleaned;

        return is_finite($parsed) ? $parsed : $default;
    }

    /**
     * Hitung rasio bumbu dinamis untuk mode Kustom Bebas mengikuti kurva resmi kios:
     * 10%-20% tepung -> 5% bumbu; 100% tepung -> 8% bumbu; 200% -> 12%; 350% -> 18%; 450% -> 22%.
     */
    protected function computeCustomSpiceRatio(float $flourRatio): float
    {
        return max(0.05, round(0.04 + (0.04 * max(0.0, $flourRatio)), 4));
    }

    /**
     * Nama bawaan racikan untuk modal Simpan Racikan dan pemulihan draft pasca-login.
     */
    protected function defaultRecipeName(): string
    {
        if ($this->startType === 'ingredients_only') {
            return 'Belanja Bahan Kios ('.count($this->breakdown).' Item)';
        }

        $effectiveCustomRatio = max(10, min(450, (int) round($this->parseNumericInput($this->customFlourRatio, 20.0))));
        $effectiveWeight = max(500, min(50000, (int) round($this->parseNumericInput($this->meatWeight, 500.0))));
        $prefLabel = $this->preference === 'kustom'
            ? 'Kustom '.$effectiveCustomRatio.'%'
            : ucfirst($this->preference);

        return 'Racikan '.ucfirst($this->meatType).' '.$prefLabel.' ('.$effectiveWeight.'g)';
    }

    /**
     * Hitung estimasi racikan berdasarkan berat atau anggaran.
     * Menggunakan Formula Rasio Dinamis (Database-Driven Ratios) dengan fallback aman.
     */
    public function calculateEstimation(): void
    {
        $this->budgetError = null;
        $this->availabilityError = null;

        // 0. Normalisasi & whitelist seluruh properti mode/enum agar kebal manipulasi state client
        if (! in_array($this->calculationMode, ['weight', 'budget'], true)) {
            $this->calculationMode = 'weight';
        }
        if (! in_array($this->startType, ['start_from_zero', 'own_meat', 'ingredients_only'], true)) {
            $this->startType = 'start_from_zero';
        }
        if (! in_array($this->meatType, ['sapi', 'ayam'], true)) {
            $this->meatType = 'sapi';
        }
        if (! in_array($this->flourType, ['tapioka', 'probakso', 'sagu', 'terigu'], true)) {
            $this->flourType = 'tapioka';
        }
        if (! in_array($this->preference, ['enak', 'seimbang', 'ekonomis', 'kustom'], true)) {
            $this->preference = 'kustom';
        }

        // 1. Ambil data formula rasio dari database (Model RecipeFormula) dengan normalisasi per-1 bagian daging
        if ($this->preference === 'kustom') {
            $effectiveCustomRatio = max(10, min(450, (int) round($this->parseNumericInput($this->customFlourRatio, 20.0))));
            if ($this->customFlourRatio !== null && $this->customFlourRatio !== '') {
                $this->customFlourRatio = $effectiveCustomRatio;
            }
            $meatRatio = 1.0;
            $flourRatio = (float) ($effectiveCustomRatio / 100.0);
            $spiceRatio = $this->computeCustomSpiceRatio($flourRatio);
            $this->isSimulation = true;
            $this->formulaName = 'Racikan Kustom ('.$effectiveCustomRatio.'% Tepung)';
        } else {
            $defaultFormulas = RecipeFormula::defaultFormulas();
            $fallback = $defaultFormulas[$this->preference] ?? $defaultFormulas['seimbang'];

            try {
                $formula = RecipeFormula::cachedAll()->firstWhere('code', $this->preference);
            } catch (\Throwable $e) {
                Log::error('RecipeCalculator gagal memuat formula rasio dari database.', [
                    'preference' => $this->preference,
                    'error' => $e->getMessage(),
                ]);
                $formula = null;
            }

            $rawMeatRatio = (float) ($formula?->meat_ratio ?? $fallback['meat_ratio']);
            $rawFlourRatio = (float) ($formula?->flour_ratio ?? $fallback['flour_ratio']);
            $rawSpiceRatio = (float) ($formula?->spice_ratio ?? $fallback['spice_ratio']);

            // Normalisasi terhadap acuan 1 bagian daging agar berat pilihan user, header struk, dan ongkos selepan selalu sinkron
            $meatRatio = 1.0;
            $flourRatio = RecipeFormula::normalizedFlourRatio($rawMeatRatio, $rawFlourRatio);
            $spiceRatio = RecipeFormula::normalizedSpiceRatio($rawMeatRatio, $rawSpiceRatio);
            $this->isSimulation = (bool) ($formula ? $formula->is_simulation : $fallback['is_simulation']);
            $this->formulaName = (string) ($formula?->name ?? $fallback['name']);
        }

        $garlicRatio = round($spiceRatio * 0.6, 6);
        $saltRatio = round($spiceRatio * 0.4, 6);

        // 2. Ambil seluruh master bahan dalam 1 query ringan (menghindari query berulang tiap geser slider)
        try {
            $allMaterials = Material::cachedAll();
        } catch (\Throwable $e) {
            Log::error('RecipeCalculator gagal memuat daftar bahan dari database.', [
                'error' => $e->getMessage(),
            ]);
            $allMaterials = collect();
        }

        $hasCatalogRecords = $allMaterials->isNotEmpty();

        $findMaterial = fn (string $slug, string $keyword): ?Material => $allMaterials->firstWhere('slug', $slug)
            ?? $allMaterials->first(
                fn (Material $m) => str_contains(strtolower($m->name), strtolower($keyword))
            );

        $beefMaterial = $findMaterial('daging-sapi', 'Daging Sapi');
        $chickenMaterial = $findMaterial('daging-ayam', 'Daging Ayam');
        $tapiokaMaterial = $findMaterial('tepung-tapioka', 'Tapioka');
        $probaksoMaterial = $findMaterial('tepung-probakso', 'ProBakso');
        $saguMaterial = $findMaterial('tepung-sagu', 'Sagu');
        $teriguMaterial = $findMaterial('tepung-terigu', 'Terigu');
        $garlicMaterial = $findMaterial('bawang-putih-kupas', 'Bawang Putih');
        $saltMaterial = $findMaterial('garam-kasar', 'Garam');

        // Bersihkan extraItems dari material tidak tersedia, sudah dihapus, atau kategori daging agar badge selalu akurat
        if (! empty($this->extraItems) && $hasCatalogRecords) {
            $validExtraIds = $allMaterials
                ->where('is_available', true)
                ->filter(fn (Material $m) => strtolower((string) $m->category) !== 'daging')
                ->pluck('id')
                ->all();
            $validLookup = array_flip($validExtraIds);
            $cleanedExtras = [];
            foreach ($this->extraItems as $extraId => $qty) {
                $id = (int) $extraId;
                $safeQty = min(100, max(0, (int) $qty));
                if ($safeQty > 0 && isset($validLookup[$id])) {
                    $cleanedExtras[$id] = $safeQty;
                }
            }
            $this->extraItems = $cleanedExtras;
        }

        $isAvailable = fn (?Material $mat): bool => $mat ? (bool) $mat->is_available : ! $hasCatalogRecords;

        $this->catalogPrices = [
            'beef_per_kg' => $beefMaterial ? round($beefMaterial->effectivePricePerGram() * 1000.0, 2) : 140000.0,
            'beef_available' => $isAvailable($beefMaterial),
            'chicken_per_kg' => $chickenMaterial ? round($chickenMaterial->effectivePricePerGram() * 1000.0, 2) : 40000.0,
            'chicken_available' => $isAvailable($chickenMaterial),
            'tapioka_weight' => $tapiokaMaterial ? $tapiokaMaterial->effectivePackageWeight(500.0) : 500.0,
            'tapioka_price' => $tapiokaMaterial ? $tapiokaMaterial->effectivePackagePrice() : 9000.0,
            'tapioka_unit' => $tapiokaMaterial ? $tapiokaMaterial->effectivePackageUnit() : 'Pack',
            'tapioka_available' => $isAvailable($tapiokaMaterial),
            'probakso_weight' => $probaksoMaterial ? $probaksoMaterial->effectivePackageWeight(250.0) : 250.0,
            'probakso_price' => $probaksoMaterial ? $probaksoMaterial->effectivePackagePrice() : 10000.0,
            'probakso_unit' => $probaksoMaterial ? $probaksoMaterial->effectivePackageUnit() : 'Pack',
            'probakso_available' => $isAvailable($probaksoMaterial),
            'sagu_weight' => $saguMaterial ? $saguMaterial->effectivePackageWeight(250.0) : 250.0,
            'sagu_price' => $saguMaterial ? $saguMaterial->effectivePackagePrice() : 6000.0,
            'sagu_unit' => $saguMaterial ? $saguMaterial->effectivePackageUnit() : 'Pack',
            'sagu_available' => $isAvailable($saguMaterial),
            'terigu_weight' => $teriguMaterial ? $teriguMaterial->effectivePackageWeight(500.0) : 500.0,
            'terigu_price' => $teriguMaterial ? $teriguMaterial->effectivePackagePrice() : 6000.0,
            'terigu_unit' => $teriguMaterial ? $teriguMaterial->effectivePackageUnit() : 'Pack',
            'terigu_available' => $isAvailable($teriguMaterial),
            'garlic_weight' => $garlicMaterial ? $garlicMaterial->effectivePackageWeight(100.0) : 100.0,
            'garlic_price' => $garlicMaterial ? $garlicMaterial->effectivePackagePrice() : 4000.0,
            'garlic_available' => $isAvailable($garlicMaterial),
            'salt_weight' => $saltMaterial ? $saltMaterial->effectivePackageWeight(250.0) : 250.0,
            'salt_price' => $saltMaterial ? $saltMaterial->effectivePackagePrice() : 1500.0,
            'salt_available' => $isAvailable($saltMaterial),
        ];

        $meatMaterial = $this->meatType === 'sapi' ? $beefMaterial : $chickenMaterial;
        $flourMaterial = match ($this->flourType) {
            'probakso' => $probaksoMaterial,
            'sagu' => $saguMaterial,
            'terigu' => $teriguMaterial,
            default => $tapiokaMaterial,
        };

        // Validasi ketersediaan stok bahan inti di kios (daging, tepung, bawang putih, garam)
        $needsMeat = $this->startType === 'start_from_zero';
        $needsSelectedFlour = $this->startType !== 'ingredients_only' && $flourRatio > 0;
        $needsGarlic = $this->startType !== 'ingredients_only' && $garlicRatio > 0;
        $needsSalt = $this->startType !== 'ingredients_only' && $saltRatio > 0;

        if ($needsMeat && $hasCatalogRecords && (! $meatMaterial || ! $meatMaterial->is_available)) {
            $meatLabel = $meatMaterial?->name ?? ($this->meatType === 'sapi' ? 'Daging Sapi' : 'Daging Ayam');
            $this->availabilityError = "Stok {$meatLabel} sedang habis di kios hari ini. Pilih jenis daging lain atau mode Bawa Daging Sendiri.";
        } elseif ($needsSelectedFlour && $hasCatalogRecords && (! $flourMaterial || ! $flourMaterial->is_available)) {
            $flourLabel = $flourMaterial?->name ?? ('Tepung '.ucfirst($this->flourType));
            $this->availabilityError = "Stok {$flourLabel} sedang habis di kios hari ini. Silakan pilih varian tepung lain.";
        } elseif ($needsGarlic && $hasCatalogRecords && $garlicMaterial && ! $garlicMaterial->is_available) {
            $this->availabilityError = "Stok {$garlicMaterial->name} sedang habis di kios hari ini. Hubungi admin kios untuk ketersediaan bumbu.";
        } elseif ($needsSalt && $hasCatalogRecords && $saltMaterial && ! $saltMaterial->is_available) {
            $this->availabilityError = "Stok {$saltMaterial->name} sedang habis di kios hari ini. Hubungi admin kios untuk ketersediaan bumbu.";
        }

        // Harga per gram kanonik dari database dengan fallback aman jika tabel kosong
        $meatPricePerGram = $meatMaterial ? $meatMaterial->effectivePricePerGram() : ($this->meatType === 'sapi' ? 140.0 : 40.0);
        $flourPricePerGram = $flourMaterial ? $flourMaterial->effectivePricePerGram() : 18.0;
        $garlicPricePerGram = $garlicMaterial ? $garlicMaterial->effectivePricePerGram() : 40.0;
        $saltPricePerGram = $saltMaterial ? $saltMaterial->effectivePricePerGram() : 6.0;

        // Logika Mode Beli Bahan Saja (Bebas pilih bahan kios via extraItems tanpa wajib tepung & tanpa daging/jasa giling)
        if ($this->startType === 'ingredients_only') {
            $this->useGrinding = false;
            $this->meatWeight = 0;

            $items = [];
            if (! empty($this->extraItems)) {
                $this->appendExtraItemsToBreakdown($items, $allMaterials);
            }

            $this->breakdown = $items;
            $this->totalProportionalCost = round((float) array_sum(array_column($items, 'proportional_cost')), 2);
            $this->totalMarketCost = round((float) array_sum(array_column($items, 'market_cost')), 2);

            return;
        }

        // Hitung biaya bahan pelengkap tambahan terlebih dulu agar diperhitungkan dalam mode anggaran
        $extraPreviewItems = [];
        if (! empty($this->extraItems)) {
            $this->appendExtraItemsToBreakdown($extraPreviewItems, $allMaterials);
        }
        $extraItemsCost = (float) array_sum(array_column($extraPreviewItems, 'market_cost'));

        // Biaya proporsional per 1 gram daging acuan (menggunakan konfigurasi ongkos selepan resmi)
        $grindingRate = (float) config('services.kiosk.grinding_fee_per_kg', 15000.0);
        $costPerGram = ($this->startType === 'start_from_zero' ? ($meatPricePerGram * $meatRatio) : 0.0)
            + ($flourRatio * $flourPricePerGram)
            + ($garlicRatio * $garlicPricePerGram)
            + ($saltRatio * $saltPricePerGram)
            + ($this->useGrinding ? ($grindingRate / 1000.0) : 0.0);

        // Logika Mode Anggaran (Calculate by Budget) — aman terhadap DivisionByZero & overflow float besar
        if ($this->calculationMode === 'budget') {
            if ($costPerGram <= 0.0) {
                $this->budgetError = 'Konfigurasi harga atau rasio bahan bernilai nol sehingga target anggaran tidak dapat dihitung.';
                $this->breakdown = [];
                $this->totalProportionalCost = 0.0;
                $this->totalMarketCost = 0.0;

                return;
            }

            $budgetValue = max(0.0, min(1000000000.0, $this->parseNumericInput($this->budget, 0.0)));
            $availableForCore = $budgetValue - $extraItemsCost;
            $minBudget = ($costPerGram * 500.0) + $extraItemsCost;

            if ($budgetValue < $minBudget || $availableForCore <= 0.0) {
                $this->budgetError = 'Anggaran Anda belum mencukupi. Minimal butuh Rp '.number_format($minBudget, 0, ',', '.').' untuk racikan standar 500 gram.';
                $this->breakdown = [];
                $this->totalProportionalCost = 0.0;
                $this->totalMarketCost = 0.0;

                return;
            }

            // Clamp dalam float SEBELUM konversi ke int agar angka besar tidak memicu ErrorException PHP 8.4
            $computedWeight = floor($availableForCore / $costPerGram);
            $this->meatWeight = (int) min(50000.0, max(500.0, $computedWeight));
        }

        $parsedWeight = $this->parseNumericInput($this->meatWeight, 500.0);
        $weight = (int) min(50000.0, max(500.0, round($parsedWeight)));
        if ($this->meatWeight !== null && $this->meatWeight !== '') {
            $this->meatWeight = $weight;
        }

        // Kebutuhan takaran proporsional terpakai (dibulatkan 4 desimal untuk mencegah glitch IEEE-754 saat ceil)
        $meatNeeded = round($weight * $meatRatio, 4);
        $flourNeeded = round($weight * $flourRatio, 4);
        $garlicNeeded = round($weight * $garlicRatio, 4);
        $saltNeeded = round($weight * $saltRatio, 4);

        // Biaya proporsional bahan terpakai
        $meatPropCost = round($meatNeeded * $meatPricePerGram, 2);
        $flourPropCost = round($flourNeeded * $flourPricePerGram, 2);
        $garlicPropCost = round($garlicNeeded * $garlicPricePerGram, 2);
        $saltPropCost = round($saltNeeded * $saltPricePerGram, 2);

        // Biaya kemasan pasar (uang belanja pasar minimum kemasan utuh)
        $meatMarketCost = $this->startType === 'own_meat' ? 0.0 : $meatPropCost;

        // Logika harga pasar berdasarkan selling_mode tiap bahan (curah vs kemasan utuh)
        $flourIsLoose = $flourMaterial ? $flourMaterial->isLoose() : false;
        $flourPkgWeight = $flourMaterial ? $flourMaterial->effectivePackageWeight(500.0) : 500.0;
        $flourPkgPrice = $flourMaterial ? $flourMaterial->effectivePackagePrice() : 9000.0;
        $flourPkgUnit = $flourMaterial ? $flourMaterial->effectivePackageUnit() : 'Pack';

        if ($flourNeeded <= 0.0) {
            $flourMarketCost = 0.0;
            $flourExcessGrams = 0;
            $flourPackageInfo = 'Tanpa tepung (0g)';
            $flourNote = 'Formula tanpa campuran tepung';
        } elseif ($flourIsLoose) {
            $flourMarketCost = $this->roundMarketCash($flourPropCost);
            $flourExcessGrams = 0;
            $flourPackageInfo = 'Timbang pas los';
            $flourNote = 'Timbang pas los pasar (bulat Rp 500)';
        } else {
            $flourPacks = (int) max(1, ceil(round($flourNeeded / $flourPkgWeight, 6)));
            $flourMarketCost = $flourPacks * $flourPkgPrice;
            $flourTotalGrams = $flourPacks * $flourPkgWeight;
            $flourExcessGrams = max(0, (int) round($flourTotalGrams - $flourNeeded));
            $flourPackageInfo = "{$flourPacks} {$flourPkgUnit} (@".number_format($flourPkgWeight).'g)';
            $flourNote = "Beli {$flourPacks} {$flourPkgUnit} @".number_format($flourPkgWeight).'g'.($flourExcessGrams > 0 ? " (sisa {$flourExcessGrams}g disimpan)" : '');
        }

        // Bawang Putih Kupas: curah (timbang pas bulat Rp 500) atau kemasan utuh jika admin ubah mode
        $garlicIsLoose = $garlicMaterial ? $garlicMaterial->isLoose() : true;
        $garlicPkgWeight = $garlicMaterial ? $garlicMaterial->effectivePackageWeight(100.0) : 100.0;
        $garlicPkgPrice = $garlicMaterial ? $garlicMaterial->effectivePackagePrice() : 4000.0;
        $garlicPkgUnit = $garlicMaterial ? $garlicMaterial->effectivePackageUnit() : 'Pack';

        if ($garlicNeeded <= 0.0) {
            $garlicMarketCost = 0.0;
            $garlicExcess = 0;
            $garlicPackageInfo = '0g';
            $garlicNote = 'Tidak digunakan';
        } elseif ($garlicIsLoose) {
            $garlicMarketCost = $this->roundMarketCash($garlicPropCost);
            $garlicExcess = 0;
            $garlicPackageInfo = 'Timbang pas los';
            $garlicNote = 'Timbang pas los pasar (bulat Rp 500)';
        } else {
            $garlicPacks = (int) max(1, ceil(round($garlicNeeded / $garlicPkgWeight, 6)));
            $garlicMarketCost = $garlicPacks * $garlicPkgPrice;
            $garlicExcess = max(0, (int) round(($garlicPacks * $garlicPkgWeight) - $garlicNeeded));
            $garlicPackageInfo = "{$garlicPacks} {$garlicPkgUnit} (@".number_format($garlicPkgWeight).'g)';
            $garlicNote = "Beli {$garlicPacks} {$garlicPkgUnit} @".number_format($garlicPkgWeight).'g';
        }

        // Garam Kasar: curah (ambil pas los bulat Rp 500) atau kemasan utuh jika admin ubah mode
        $saltIsLoose = $saltMaterial ? $saltMaterial->isLoose() : true;
        $saltPkgWeight = $saltMaterial ? $saltMaterial->effectivePackageWeight(250.0) : 250.0;
        $saltPkgPrice = $saltMaterial ? $saltMaterial->effectivePackagePrice() : 1500.0;
        $saltPkgUnit = $saltMaterial ? $saltMaterial->effectivePackageUnit() : 'Pack';

        if ($saltNeeded <= 0.0) {
            $saltMarketCost = 0.0;
            $saltExcess = 0;
            $saltPackageInfo = '0g';
            $saltNote = 'Tidak digunakan';
        } elseif ($saltIsLoose) {
            $saltMarketCost = $this->roundMarketCash($saltPropCost);
            $saltExcess = 0;
            $saltPackageInfo = 'Timbang pas los';
            $saltNote = 'Ambil pas los pasar (bulat Rp 500)';
        } else {
            $saltPacks = (int) max(1, ceil(round($saltNeeded / $saltPkgWeight, 6)));
            $saltMarketCost = $saltPacks * $saltPkgPrice;
            $saltExcess = max(0, (int) round(($saltPacks * $saltPkgWeight) - $saltNeeded));
            $saltPackageInfo = "{$saltPacks} {$saltPkgUnit} (@".number_format($saltPkgWeight).'g)';
            $saltNote = "Beli {$saltPacks} {$saltPkgUnit} @".number_format($saltPkgWeight).'g';
        }

        $items = [];

        // 1. Daging Utama
        $isOwnMeat = ($this->startType === 'own_meat');
        $items[] = [
            'material_id' => $meatMaterial?->id,
            'name' => $this->meatType === 'sapi' ? 'Daging Sapi' : 'Daging Ayam',
            'weight' => $meatNeeded,
            'unit' => 'gram',
            'price_per_gram' => $meatPricePerGram,
            'cost' => $isOwnMeat ? 0.0 : $meatPropCost,
            'proportional_cost' => $isOwnMeat ? 0.0 : $meatPropCost,
            'market_cost' => $meatMarketCost,
            'package_info' => $isOwnMeat ? 'Bawa Sendiri' : number_format($meatNeeded).' gram daging pasar',
            'note' => $isOwnMeat ? 'Bawa sendiri (Rp 0)' : 'Daging segar pasar',
            'is_meat' => true,
            'own_meat' => $isOwnMeat,
        ];

        // 2. Tepung Pilihan (hanya dimasukkan bila takaran > 0g)
        if ($flourNeeded > 0.0) {
            $flourName = $flourMaterial?->name ?? 'Tepung '.ucfirst($this->flourType);
            $items[] = [
                'material_id' => $flourMaterial?->id,
                'name' => $flourName,
                'weight' => $flourNeeded,
                'unit' => 'gram',
                'price_per_gram' => $flourPricePerGram,
                'cost' => $flourPropCost,
                'proportional_cost' => $flourPropCost,
                'market_cost' => $flourMarketCost,
                'package_info' => $flourPackageInfo,
                'excess_grams' => $flourExcessGrams,
                'note' => $flourNote,
                'is_meat' => false,
                'own_meat' => false,
            ];
        }

        // 3. Bawang Putih
        if ($garlicNeeded > 0.0) {
            $items[] = [
                'material_id' => $garlicMaterial?->id,
                'name' => $garlicMaterial?->name ?? 'Bawang Putih Kupas',
                'weight' => $garlicNeeded,
                'unit' => 'gram',
                'price_per_gram' => $garlicPricePerGram,
                'cost' => $garlicPropCost,
                'proportional_cost' => $garlicPropCost,
                'market_cost' => $garlicMarketCost,
                'package_info' => $garlicPackageInfo,
                'excess_grams' => $garlicExcess,
                'note' => $garlicNote,
                'is_meat' => false,
                'own_meat' => false,
            ];
        }

        // 4. Garam
        if ($saltNeeded > 0.0) {
            $items[] = [
                'material_id' => $saltMaterial?->id,
                'name' => $saltMaterial?->name ?? 'Garam Kasar',
                'weight' => $saltNeeded,
                'unit' => 'gram',
                'price_per_gram' => $saltPricePerGram,
                'cost' => $saltPropCost,
                'proportional_cost' => $saltPropCost,
                'market_cost' => $saltMarketCost,
                'package_info' => $saltPackageInfo,
                'excess_grams' => $saltExcess,
                'note' => $saltNote,
                'is_meat' => false,
                'own_meat' => false,
            ];
        }

        // 5. Jasa Selepan (Penggilingan)
        if ($this->useGrinding) {
            $grindingFee = round(($weight / 1000.0) * $grindingRate, 2);
            $items[] = [
                'material_id' => null,
                'name' => 'Jasa Selepan Pasar',
                'weight' => $weight,
                'unit' => 'gram',
                'price_per_gram' => $grindingRate / 1000.0,
                'cost' => $grindingFee,
                'proportional_cost' => $grindingFee,
                'market_cost' => $grindingFee,
                'package_info' => 'Termasuk es batu penggilingan',
                'note' => 'Rp '.number_format($grindingRate, 0, ',', '.').' / kg daging (termasuk es batu giling)',
                'is_meat' => false,
                'own_meat' => false,
                'is_service' => true,
            ];
        }

        if (! empty($extraPreviewItems)) {
            $items = array_merge($items, $extraPreviewItems);
        }

        $this->breakdown = $items;
        $this->totalProportionalCost = round((float) array_sum(array_column($items, 'proportional_cost')), 2);
        $this->totalMarketCost = round((float) array_sum(array_column($items, 'market_cost')), 2);
    }

    /**
     * Pembulatan tunai pasar ramah untuk bahan curah (kelipatan Rp 500, minimal Rp 500, bebas glitch IEEE-754).
     */
    protected function roundMarketCash(float $amount): float
    {
        $rounded = round($amount, 4);
        if ($rounded <= 0.0) {
            return 0.0;
        }

        return (float) max(500.0, ceil(round($rounded / 500.0, 6)) * 500.0);
    }

    /**
     * Tambahkan bahan pelengkap pilihan pengguna (hanya yang tersedia & bukan daging) ke breakdown kalkulator.
     *
     * @param  Collection<int, Material>|null  $materialsPool
     */
    protected function appendExtraItemsToBreakdown(array &$items, $materialsPool = null): void
    {
        $activeExtraIds = array_keys(array_filter($this->extraItems, fn ($q) => (int) $q > 0));
        if (empty($activeExtraIds)) {
            return;
        }

        $extraMaterials = $materialsPool
            ? $materialsPool->whereIn('id', $activeExtraIds)->where('is_available', true)->filter(fn (Material $m) => strtolower((string) $m->category) !== 'daging')->keyBy('id')
            : Material::whereIn('id', $activeExtraIds)->where('is_available', true)->where('category', '!=', 'daging')->get()->keyBy('id');

        foreach ($this->extraItems as $extraId => $qty) {
            $safeQty = min(100, max(0, (int) $qty));
            if ($safeQty > 0 && isset($extraMaterials[$extraId])) {
                $mat = $extraMaterials[$extraId];
                $pkgWeight = $mat->effectivePackageWeight(1.0);
                $pkgPrice = $mat->effectivePackagePrice();
                $pkgUnit = $mat->effectivePackageUnit();
                $cost = round($safeQty * $pkgPrice, 2);

                $items[] = [
                    'material_id' => $mat->id,
                    'name' => $mat->name,
                    'weight' => round($safeQty * $pkgWeight, 2),
                    'qty' => $safeQty,
                    'unit' => $pkgUnit,
                    'unit_price' => $pkgPrice,
                    'price_per_gram' => $mat->effectivePricePerGram(),
                    'cost' => $cost,
                    'proportional_cost' => $cost,
                    'market_cost' => $cost,
                    'package_info' => "{$safeQty} {$pkgUnit} (@".number_format($pkgWeight).'g)',
                    'excess_grams' => 0,
                    'note' => "Bahan Pelengkap: {$safeQty} {$pkgUnit}",
                    'is_meat' => false,
                    'own_meat' => false,
                    'is_extra' => true,
                ];
            }
        }
    }

    /**
     * Buka modal checkout jika tidak ada error dan kalkulasi valid.
     */
    public function openCheckout(): void
    {
        $this->calculateEstimation();
        if (! empty($this->breakdown) && $this->totalMarketCost > 0 && empty($this->budgetError) && empty($this->availabilityError)) {
            if (empty($this->pickupTime)) {
                $this->pickupTime = now()->addDay()->setTime(8, 0)->format('Y-m-d\TH:i');
            }
            $this->resetConfirmedOrder();
            $this->resetErrorBag();
            $this->showCheckoutModal = true;
        }
    }

    /**
     * Tutup modal checkout dan bersihkan error bag serta state konfirmasi lama.
     */
    public function closeCheckout(): void
    {
        $this->showCheckoutModal = false;
        $this->resetConfirmedOrder();
        $this->resetErrorBag();
    }

    /**
     * Bersihkan state layar konfirmasi pesanan sebelumnya.
     */
    protected function resetConfirmedOrder(): void
    {
        $this->confirmedOrderCode = null;
        $this->confirmedOrderTotal = 0.0;
        $this->confirmedPickupLabel = null;
        $this->confirmedWaUrl = null;
        $this->confirmedTrackUrl = null;
    }

    /**
     * Simpan pesanan ke database (Order & OrderItem) dan tampilkan layar konfirmasi + tautan WhatsApp.
     */
    public function processCheckout(): void
    {
        if ($this->confirmedOrderCode !== null) {
            return;
        }

        $throttleKey = 'checkout|'.request()->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 10)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('customerPhone', "Terlalu banyak pesanan dibuat dari perangkat ini. Harap tunggu {$seconds} detik.");

            return;
        }

        $this->customerPhone = CheckoutHelper::normalizePhone($this->customerPhone);

        $this->validate(
            CheckoutHelper::validationRules(),
            CheckoutHelper::validationMessages()
        );

        // Hitung ulang secara server-authoritative agar manipulasi state client diabaikan
        RateLimiter::hit($throttleKey, 60);
        $this->calculateEstimation();

        if (! empty($this->availabilityError)) {
            $this->addError('calculation', $this->availabilityError);

            return;
        }

        if (empty($this->breakdown) || $this->totalMarketCost <= 0 || ! empty($this->budgetError)) {
            $this->addError('calculation', $this->budgetError ?: 'Kalkulasi racikan tidak valid.');

            return;
        }

        $effectiveWeight = $this->startType === 'ingredients_only'
            ? 0
            : max(500, min(50000, (int) round($this->parseNumericInput($this->meatWeight, 500.0))));
        $grindingRate = (float) config('services.kiosk.grinding_fee_per_kg', 15000.0);
        $grindingFee = $this->useGrinding ? round(($effectiveWeight / 1000.0) * $grindingRate, 2) : 0.0;
        $totalPrice = (float) $this->totalMarketCost;
        $persistedPreference = $this->startType === 'ingredients_only' ? 'bahan_saja' : $this->preference;

        // 1 & 2. Simpan order dan order items secara atomik dalam transaksi DB
        $order = DB::transaction(function () use ($effectiveWeight, $grindingFee, $totalPrice, $persistedPreference) {
            $order = Order::create([
                'user_id' => Auth::id(),
                'customer_name' => trim($this->customerName),
                'customer_phone' => trim($this->customerPhone),
                'pickup_at' => $this->pickupTime,
                'start_type' => $this->startType,
                'meat_weight' => $effectiveWeight,
                'preference' => $persistedPreference,
                'grinding_fee' => $grindingFee,
                'total_price' => $totalPrice,
                'status' => 'pending',
            ]);

            foreach ($this->breakdown as $item) {
                $isPackItem = ! empty($item['is_extra']) || isset($item['qty']);
                $isOwnMeatItem = ! empty($item['own_meat']);

                OrderItem::create([
                    'order_id' => $order->id,
                    'material_id' => $item['material_id'] ?? null,
                    'material_name' => $item['name'],
                    'quantity' => $isPackItem ? (float) $item['qty'] : (float) $item['weight'],
                    'unit' => $isPackItem ? (string) ($item['unit'] ?? 'Pack') : (string) ($item['unit'] ?? 'gram'),
                    'unit_price' => $isOwnMeatItem
                        ? 0.0
                        : ($isPackItem
                            ? (float) ($item['unit_price'] ?? $item['market_cost'] ?? 0.0)
                            : (float) ($item['price_per_gram'] ?? 0.0)),
                    'subtotal' => $isOwnMeatItem
                        ? 0.0
                        : (float) ($item['market_cost'] ?? $item['cost'] ?? $item['proportional_cost'] ?? 0.0),
                ]);
            }

            return $order;
        });

        // 3. Susun teks pesan WhatsApp terstruktur (tanpa duplikasi baris Jasa Selepan)
        $startTypeLabel = match ($this->startType) {
            'own_meat' => 'Bawa Daging Sendiri',
            'ingredients_only' => 'Beli Bahan / Tepung Saja (Tanpa Daging)',
            default => 'Mulai dari Nol (Beli Semua)',
        };
        $pickupFormatted = Carbon::parse($this->pickupTime)->format('d M Y, H:i');
        $effectiveCustomRatio = max(10, min(450, (int) round($this->parseNumericInput($this->customFlourRatio, 20.0))));

        $orderCode = CheckoutHelper::formatOrderCode($order->id);
        $lines = [];
        $lines[] = '*PESANAN RACIKAN BAKSO - BASOKU*';
        $lines[] = '-----------------------------------';
        $lines[] = '*No. Pesanan:* '.$orderCode;
        $lines[] = '*Nama Pemesan:* '.trim($this->customerName);
        $lines[] = '*No. WhatsApp:* '.trim($this->customerPhone);
        $lines[] = '*Jadwal Ambil:* '.$pickupFormatted.' WIB';
        $lines[] = '*Mode Racikan:* '.$startTypeLabel;
        if ($this->startType !== 'ingredients_only') {
            $lines[] = '*Daging Utama:* '.ucfirst($this->meatType).' ('.number_format($effectiveWeight).' gram)';
            $prefLabel = $this->preference === 'kustom'
                ? 'Kustom ('.$effectiveCustomRatio.'% Tepung)'
                : ucfirst($this->preference);
            $lines[] = '*Karakter:* '.$prefLabel;
        } else {
            $lines[] = '*Kategori:* Belanja Bahan & Bumbu Kios';
        }
        $lines[] = '';
        $lines[] = '*Rincian Belanja Kemasan Pasar:*';
        foreach ($this->breakdown as $item) {
            if (! empty($item['is_service'])) {
                continue;
            }
            $costFormatted = 'Rp '.number_format((float) ($item['market_cost'] ?? $item['cost'] ?? 0), 0, ',', '.');
            $pkgInfo = ! empty($item['package_info']) ? ' ['.$item['package_info'].']' : '';
            $excessInfo = ! empty($item['excess_grams']) ? " (sisa {$item['excess_grams']}g dibawa pulang)" : '';
            $lines[] = '• '.$item['name'].$pkgInfo.': '.$costFormatted.$excessInfo;
        }

        $lines[] = '';
        $lines[] = '*Jasa Selepan:* '.($this->useGrinding ? 'Rp '.number_format($grindingFee, 0, ',', '.') : 'Tanpa Selepan');
        $lines[] = '-----------------------------------';
        $lines[] = '*TOTAL BAYAR BELANJA PASAR:* Rp '.number_format($this->totalMarketCost, 0, ',', '.');
        $lines[] = '*Nilai Bahan Terpakai:* Rp '.number_format($this->totalProportionalCost, 0, ',', '.');
        $lines[] = '';
        $lines[] = 'Mohon konfirmasi pesanan ini agar bahan dapat disiapkan sebelum waktu pengambilan. Terima kasih!';

        $waNumber = (string) config('services.kiosk.wa_number', '6285122797969');
        $waText = urlencode(implode("\n", $lines));

        // 4. Tampilkan modal konfirmasi berisi kode #BSK-XXXXX, tombol WA, dan tautan lacak pesanan
        $this->confirmedOrderCode = $orderCode;
        $this->confirmedOrderTotal = $totalPrice;
        $this->confirmedPickupLabel = $pickupFormatted.' WIB';
        $this->confirmedWaUrl = 'https://wa.me/'.$waNumber.'?text='.$waText;
        $this->confirmedTrackUrl = CheckoutHelper::trackOrderUrl($order->id);
        $this->showCheckoutModal = true;
    }

    /**
     * Buka modal penyimpanan racikan ke Laboratorium Resep.
     * Jika belum login, arahkan ke halaman masuk.
     */
    public function openSaveModal()
    {
        $this->calculateEstimation();
        if (empty($this->breakdown) || $this->totalMarketCost <= 0 || ! empty($this->availabilityError) || ! empty($this->budgetError)) {
            return;
        }

        if (! Auth::check()) {
            session()->put('calculator_draft', [
                'calculationMode' => $this->calculationMode,
                'budget' => $this->parseNumericInput($this->budget, 100000.0),
                'startType' => $this->startType,
                'meatType' => $this->meatType,
                'meatWeight' => $this->startType === 'ingredients_only' ? 0 : (int) round($this->parseNumericInput($this->meatWeight, 500.0)),
                'flourType' => $this->flourType,
                'preference' => $this->preference,
                'customFlourRatio' => (int) round($this->parseNumericInput($this->customFlourRatio, 20.0)),
                'useGrinding' => $this->useGrinding,
                'extraItems' => $this->extraItems,
            ]);

            return $this->redirect('/masuk', navigate: true);
        }

        $this->recipeName = $this->defaultRecipeName();
        $this->resetErrorBag();
        $this->showSaveModal = true;
    }

    /**
     * Tutup modal simpan racikan.
     */
    public function closeSaveModal()
    {
        $this->showSaveModal = false;
        $this->recipeName = '';
        $this->resetErrorBag();
    }

    /**
     * Simpan racikan ke basis data saved_recipes milik pengguna (tervalidasi, dihitung ulang, & rate-limited).
     */
    public function saveRecipe()
    {
        if (! Auth::check()) {
            return $this->redirect('/masuk', navigate: true);
        }

        $throttleKey = 'save-recipe|'.Auth::id();
        if (RateLimiter::tooManyAttempts($throttleKey, 20)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('recipeName', "Terlalu banyak percobaan simpan racikan. Harap tunggu {$seconds} detik.");

            return;
        }

        $this->validate([
            'recipeName' => ['required', 'string', 'max:100'],
        ], [
            'recipeName.required' => 'Nama racikan wajib diisi.',
            'recipeName.max' => 'Nama racikan maksimal 100 karakter.',
        ]);

        // Hitung ulang secara server-authoritative sebelum menyimpan
        $this->calculateEstimation();

        if (empty($this->breakdown) || $this->totalMarketCost <= 0 || ! empty($this->availabilityError) || ! empty($this->budgetError)) {
            $this->addError('recipeName', $this->availabilityError ?: ($this->budgetError ?: 'Racikan belum valid untuk disimpan.'));

            return;
        }

        RateLimiter::hit($throttleKey, 60);

        $effectiveWeight = $this->startType === 'ingredients_only'
            ? 0
            : max(500, min(50000, (int) round($this->parseNumericInput($this->meatWeight, 500.0))));
        $persistedPreference = $this->startType === 'ingredients_only' ? 'bahan_saja' : $this->preference;

        SavedRecipe::create([
            'user_id' => Auth::id(),
            'name' => trim($this->recipeName),
            'meat_weight' => $effectiveWeight,
            'start_type' => $this->startType,
            'preference' => $persistedPreference,
            'recipe_details' => $this->breakdown,
            'estimated_total' => $this->totalProportionalCost,
        ]);

        $this->showSaveModal = false;
        $this->recipeName = '';

        session()->flash('success_save', 'Racikan berhasil disimpan ke Laboratorium Resep Anda!');
    }

    public function render()
    {
        return view('livewire.recipe-calculator');
    }
}
