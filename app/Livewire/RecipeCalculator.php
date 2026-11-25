<?php

namespace App\Livewire;

use App\Models\Material;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\RecipeFormula;
use App\Models\SavedRecipe;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class RecipeCalculator extends Component
{
    /** Apakah komponen disematkan di beranda (mengatur heading H2 vs H1) */
    public bool $embedded = false;

    /** Mode kalkulasi: 'weight' (berdasarkan berat) atau 'budget' (berdasarkan anggaran) */
    public string $calculationMode = 'weight';

    /** Nilai anggaran dalam rupiah (default Rp 100.000) */
    public int|float $budget = 100000;

    /** Pesan kesalahan jika anggaran tidak mencukupi standar minimum 500g */
    public ?string $budgetError = null;

    /** Pesan peringatan jika bahan inti yang dipilih sedang habis di kios */
    public ?string $availabilityError = null;

    /** Daftar harga & status ketersediaan bahan aktif dari database untuk tampilan UI */
    public array $catalogPrices = [];

    /** Mode racikan: 'start_from_zero' (beli semua) atau 'own_meat' (bawa daging sendiri) */
    public string $startType = 'start_from_zero';

    /** Jenis daging utama: 'sapi' atau 'ayam' */
    public string $meatType = 'sapi';

    /** Berat daging dalam gram (minimal 500) */
    public int $meatWeight = 500;

    /** Preferensi karakter racikan: 'enak', 'seimbang', 'ekonomis', atau 'kustom' */
    public string $preference = 'seimbang';

    /** Persentase tepung kustom jika preference = 'kustom' (10% s/d 50%) */
    public int $customFlourRatio = 20;

    /** Jumlah pack tepung jika mode beli bahan saja */
    public int $ingredientFlourPacks = 1;

    /** Jumlah pack bawang putih jika mode beli bahan saja */
    public int $ingredientGarlicPacks = 0;

    /** Jumlah pack garam jika mode beli bahan saja */
    public int $ingredientSaltPacks = 0;

    /** Varian tepung yang dipilih: 'tapioka', 'probakso', 'sagu', 'terigu' */
    public string $flourType = 'tapioka';

    /** Status formula simulasi atau resmi dari kios */
    public bool $isSimulation = true;

    /** Nama formula aktif */
    public ?string $formulaName = null;

    /** Apakah menggunakan jasa selepan (penggilingan) */
    public bool $useGrinding = true;

    /** Rincian bahan terpakai dan estimasi biaya per bahan */
    public array $breakdown = [];

    /** Total biaya bahan terpakai (proporsional riil adonan) */
    public float $totalProportionalCost = 0;

    /** Total estimasi uang belanja kemasan di pasar */
    public float $totalMarketCost = 0;

    /** State modal checkout pesanan */
    public bool $showCheckoutModal = false;

    /** Form data checkout */
    public string $customerName = '';

    public string $customerPhone = '';

    public string $pickupTime = '';

    public string $sellerWaNumber = '6285122797969';

    /** State modal simpan racikan ke laboratorium saya */
    public bool $showSaveModal = false;

    public string $recipeName = '';

    /** Bahan tambahan & bumbu pelengkap [material_id => qty] */
    public array $extraItems = [];

    public function mount(): void
    {
        $this->sellerWaNumber = config('services.kiosk.wa_number', '6285122797969');

        if ($recipeId = request()->query('recipe_id')) {
            if (Auth::check()) {
                $saved = SavedRecipe::where('user_id', Auth::id())->find($recipeId);
                if ($saved) {
                    $validStartTypes = ['start_from_zero', 'own_meat', 'ingredients_only'];
                    $validPreferences = ['enak', 'seimbang', 'ekonomis', 'kustom', 'urat'];

                    $this->startType = in_array($saved->start_type, $validStartTypes, true)
                        ? $saved->start_type
                        : 'start_from_zero';
                    $this->preference = in_array($saved->preference, $validPreferences, true)
                        ? $saved->preference
                        : 'seimbang';
                    $this->meatWeight = $this->startType === 'ingredients_only'
                        ? 0
                        : max(500, (int) $saved->meat_weight);

                    $this->restoreDetailsFromSavedRecipe($saved);
                }
            }
        }

        if ($sessionExtras = session()->get('selected_materials')) {
            $this->extraItems = $sessionExtras;
            session()->forget('selected_materials');
        }

        if (Auth::check() && ($draft = session()->pull('calculator_draft')) && is_array($draft)) {
            $this->calculationMode = in_array($draft['calculationMode'] ?? '', ['weight', 'budget'], true) ? $draft['calculationMode'] : $this->calculationMode;
            $this->budget = isset($draft['budget']) ? (float) $draft['budget'] : $this->budget;
            $this->startType = in_array($draft['startType'] ?? '', ['start_from_zero', 'own_meat', 'ingredients_only'], true) ? $draft['startType'] : $this->startType;
            $this->meatType = in_array($draft['meatType'] ?? '', ['sapi', 'ayam'], true) ? $draft['meatType'] : $this->meatType;
            $this->meatWeight = (int) ($draft['meatWeight'] ?? $this->meatWeight);
            $this->flourType = in_array($draft['flourType'] ?? '', ['tapioka', 'probakso', 'sagu', 'terigu'], true) ? $draft['flourType'] : $this->flourType;
            $this->preference = in_array($draft['preference'] ?? '', ['enak', 'seimbang', 'ekonomis', 'kustom'], true) ? $draft['preference'] : $this->preference;
            $this->customFlourRatio = (int) ($draft['customFlourRatio'] ?? $this->customFlourRatio);
            $this->useGrinding = (bool) ($draft['useGrinding'] ?? $this->useGrinding);
            $this->ingredientFlourPacks = (int) ($draft['ingredientFlourPacks'] ?? $this->ingredientFlourPacks);
            $this->ingredientGarlicPacks = (int) ($draft['ingredientGarlicPacks'] ?? $this->ingredientGarlicPacks);
            $this->ingredientSaltPacks = (int) ($draft['ingredientSaltPacks'] ?? $this->ingredientSaltPacks);
            if (is_array($draft['extraItems'] ?? null)) {
                $this->extraItems = $draft['extraItems'];
            }
            $this->recipeName = 'Racikan '.ucfirst($this->meatType).' '.ucfirst($this->preference).' ('.$this->meatWeight.'g)';
            $this->showSaveModal = true;
        }

        $this->calculateEstimation();
    }

    /**
     * Pulihkan parameter racikan lengkap (jenis daging, tepung, rasio kustom, selepan, pelengkap)
     * dari rincian JSON resep yang tersimpan tanpa mengubah struktur data lama.
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
                $qty = isset($item['qty']) ? (int) $item['qty'] : 1;
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

            if ($this->preference === 'kustom' && $this->meatWeight > 0 && $isFlourItem) {
                $flourGrams = (float) ($item['weight'] ?? 0);
                if ($flourGrams > 0) {
                    $this->customFlourRatio = max(10, min(50, (int) round(($flourGrams / $this->meatWeight) * 100)));
                }
            }

            if ($this->startType === 'ingredients_only' && isset($item['packs'])) {
                if (str_contains($name, 'bawang')) {
                    $this->ingredientGarlicPacks = max(0, (int) $item['packs']);
                } elseif (str_contains($name, 'garam')) {
                    $this->ingredientSaltPacks = max(0, (int) $item['packs']);
                } elseif ($isFlourItem) {
                    $this->ingredientFlourPacks = max(1, (int) $item['packs']);
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
            if ($this->meatWeight < 500) {
                $this->meatWeight = 500;
            }
            $this->useGrinding = true;
        }
    }

    public function incrementExtra(int $materialId): void
    {
        $this->extraItems[$materialId] = ($this->extraItems[$materialId] ?? 0) + 1;
        $this->calculateEstimation();
    }

    public function decrementExtra(int $materialId): void
    {
        if (isset($this->extraItems[$materialId])) {
            $this->extraItems[$materialId]--;
            if ($this->extraItems[$materialId] <= 0) {
                unset($this->extraItems[$materialId]);
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
        return Material::where('is_available', true)
            ->whereNotIn('category', ['daging'])
            ->orderBy('category')
            ->orderBy('name')
            ->get();
    }

    public function updated($property = null): void
    {
        $this->calculateEstimation();
    }

    /**
     * Hitung estimasi racikan berdasarkan berat atau anggaran.
     * Menggunakan Formula Rasio Dinamis (Database-Driven Ratios) dengan fallback aman.
     */
    public function calculateEstimation(): void
    {
        $this->budgetError = null;
        $this->availabilityError = null;

        // 1. Ambil data formula rasio dari database (Model RecipeFormula) dengan fallback aman
        if ($this->preference === 'kustom') {
            $this->customFlourRatio = max(10, min(60, (int) $this->customFlourRatio));
            $meatRatio = 1.0;
            $flourRatio = (float) ($this->customFlourRatio / 100.0);
            $spiceRatio = 0.05;
            $this->isSimulation = true;
            $this->formulaName = 'Racikan Kustom ('.$this->customFlourRatio.'% Tepung)';
        } else {
            $defaultFormulas = RecipeFormula::defaultFormulas();
            $fallback = $defaultFormulas[$this->preference] ?? $defaultFormulas['seimbang'];

            try {
                $formula = RecipeFormula::where('code', $this->preference)->first();
            } catch (\Throwable $e) {
                $formula = null;
            }

            $meatRatio = (float) ($formula?->meat_ratio ?? $fallback['meat_ratio']);
            $flourRatio = (float) ($formula?->flour_ratio ?? $fallback['flour_ratio']);
            $spiceRatio = (float) ($formula?->spice_ratio ?? $fallback['spice_ratio']);
            $this->isSimulation = (bool) ($formula ? $formula->is_simulation : $fallback['is_simulation']);
            $this->formulaName = (string) ($formula?->name ?? $fallback['name']);
        }

        $garlicRatio = $spiceRatio * 0.6;
        $saltRatio = $spiceRatio * 0.4;

        // 2. Ambil seluruh master bahan dalam 1 query ringan (menghindari 4+ query berulang tiap geser slider)
        try {
            $allMaterials = Material::all();
        } catch (\Throwable $e) {
            $allMaterials = collect();
        }

        $findMaterial = fn (string $keyword): ?Material => $allMaterials->first(
            fn (Material $m) => str_contains(strtolower($m->name), strtolower($keyword))
        );

        $beefMaterial = $findMaterial('Daging Sapi');
        $chickenMaterial = $findMaterial('Daging Ayam');
        $tapiokaMaterial = $findMaterial('Tapioka');
        $probaksoMaterial = $findMaterial('ProBakso');
        $saguMaterial = $findMaterial('Sagu');
        $teriguMaterial = $findMaterial('Terigu');
        $garlicMaterial = $findMaterial('Bawang Putih');
        $saltMaterial = $findMaterial('Garam');

        $this->catalogPrices = [
            'beef_per_kg' => (float) ($beefMaterial?->price ?? 140.0) * 1000.0,
            'beef_available' => $beefMaterial ? (bool) $beefMaterial->is_available : true,
            'chicken_per_kg' => (float) ($chickenMaterial?->price ?? 40.0) * 1000.0,
            'chicken_available' => $chickenMaterial ? (bool) $chickenMaterial->is_available : true,
            'tapioka_weight' => (float) ($tapiokaMaterial?->package_weight ?? 500.0),
            'tapioka_price' => (float) ($tapiokaMaterial?->package_price ?? 9000.0),
            'tapioka_unit' => (string) ($tapiokaMaterial?->package_unit ?? 'Pack'),
            'tapioka_available' => $tapiokaMaterial ? (bool) $tapiokaMaterial->is_available : true,
            'probakso_weight' => (float) ($probaksoMaterial?->package_weight ?? 250.0),
            'probakso_price' => (float) ($probaksoMaterial?->package_price ?? 10000.0),
            'probakso_unit' => (string) ($probaksoMaterial?->package_unit ?? 'Pack'),
            'probakso_available' => $probaksoMaterial ? (bool) $probaksoMaterial->is_available : true,
            'sagu_weight' => (float) ($saguMaterial?->package_weight ?? 250.0),
            'sagu_price' => (float) ($saguMaterial?->package_price ?? 5000.0),
            'sagu_unit' => (string) ($saguMaterial?->package_unit ?? 'Pack'),
            'sagu_available' => $saguMaterial ? (bool) $saguMaterial->is_available : true,
            'terigu_weight' => (float) ($teriguMaterial?->package_weight ?? 1000.0),
            'terigu_price' => (float) ($teriguMaterial?->package_price ?? 10000.0),
            'terigu_unit' => (string) ($teriguMaterial?->package_unit ?? 'Kg'),
            'terigu_available' => $teriguMaterial ? (bool) $teriguMaterial->is_available : true,
            'garlic_weight' => (float) ($garlicMaterial?->package_weight ?? 100.0),
            'garlic_price' => (float) ($garlicMaterial?->package_price ?? 4000.0),
            'salt_weight' => (float) ($saltMaterial?->package_weight ?? 1000.0),
            'salt_price' => (float) ($saltMaterial?->package_price ?? 3000.0),
        ];

        $meatMaterial = $this->meatType === 'sapi' ? $beefMaterial : $chickenMaterial;
        $flourMaterial = match ($this->flourType) {
            'probakso' => $probaksoMaterial,
            'sagu' => $saguMaterial,
            'terigu' => $teriguMaterial,
            default => $tapiokaMaterial,
        } ?? $tapiokaMaterial;

        // Validasi ketersediaan stok bahan inti di kios
        if ($this->startType === 'start_from_zero' && $meatMaterial && ! $meatMaterial->is_available) {
            $this->availabilityError = "Stok {$meatMaterial->name} sedang habis di kios hari ini. Pilih jenis daging lain atau mode Bawa Daging Sendiri.";
        } elseif ($flourMaterial && ! $flourMaterial->is_available) {
            $this->availabilityError = "Stok {$flourMaterial->name} sedang habis di kios hari ini. Silakan pilih varian tepung lain.";
        }

        // Harga per gram dari database dengan fallback aman jika belum ada
        $meatPricePerGram = (float) ($meatMaterial?->price ?? ($this->meatType === 'sapi' ? 140.0 : 40.0));
        $flourPricePerGram = (float) ($flourMaterial?->price ?? 18.0);
        $garlicPricePerGram = (float) ($garlicMaterial?->price ?? 40.0);
        $saltPricePerGram = (float) ($saltMaterial?->price ?? 3.0);

        // Logika Mode Beli Bahan Saja (Tanpa Daging & Tanpa Jasa Giling)
        if ($this->startType === 'ingredients_only') {
            $this->useGrinding = false;
            $this->meatWeight = 0;

            $flourPkgWeight = (float) ($flourMaterial?->package_weight ?? 500.0);
            $flourPkgPrice = (float) ($flourMaterial?->package_price ?? 9000.0);
            $flourPkgUnit = (string) ($flourMaterial?->package_unit ?? 'pack');
            $flourPacks = max(1, (int) $this->ingredientFlourPacks);
            $this->ingredientFlourPacks = $flourPacks;
            $flourTotalGrams = $flourPacks * $flourPkgWeight;
            $flourMarketCost = $flourPacks * $flourPkgPrice;

            $garlicPkgWeight = (float) ($garlicMaterial?->package_weight ?? 100.0);
            $garlicPkgPrice = (float) ($garlicMaterial?->package_price ?? 4000.0);
            $garlicPacks = max(0, (int) $this->ingredientGarlicPacks);
            $this->ingredientGarlicPacks = $garlicPacks;
            $garlicTotalGrams = $garlicPacks * $garlicPkgWeight;
            $garlicMarketCost = $garlicPacks * $garlicPkgPrice;

            $saltPkgWeight = (float) ($saltMaterial?->package_weight ?? 1000.0);
            $saltPkgPrice = (float) ($saltMaterial?->package_price ?? 3000.0);
            $saltPacks = max(0, (int) $this->ingredientSaltPacks);
            $this->ingredientSaltPacks = $saltPacks;
            $saltTotalGrams = $saltPacks * $saltPkgWeight;
            $saltMarketCost = $saltPacks * $saltPkgPrice;

            $items = [];
            $items[] = [
                'material_id' => $flourMaterial?->id,
                'name' => $flourMaterial?->name ?? 'Tepung '.ucfirst($this->flourType),
                'weight' => $flourTotalGrams,
                'unit' => 'gram',
                'price_per_gram' => $flourPricePerGram,
                'cost' => $flourMarketCost,
                'proportional_cost' => $flourMarketCost,
                'market_cost' => $flourMarketCost,
                'package_info' => "{$flourPacks} {$flourPkgUnit} (@".number_format($flourPkgWeight).'g)',
                'packs' => $flourPacks,
                'excess_grams' => 0,
                'note' => "{$flourPacks} {$flourPkgUnit} utuh siap bawa pulang",
                'is_meat' => false,
                'own_meat' => false,
            ];

            if ($garlicPacks > 0) {
                $items[] = [
                    'material_id' => $garlicMaterial?->id,
                    'name' => $garlicMaterial?->name ?? 'Bawang Putih Kupas',
                    'weight' => $garlicTotalGrams,
                    'unit' => 'gram',
                    'price_per_gram' => $garlicPricePerGram,
                    'cost' => $garlicMarketCost,
                    'proportional_cost' => $garlicMarketCost,
                    'market_cost' => $garlicMarketCost,
                    'package_info' => "{$garlicPacks} pack (@".number_format($garlicPkgWeight).'g)',
                    'packs' => $garlicPacks,
                    'excess_grams' => 0,
                    'note' => "{$garlicPacks} pack bumbu pasar",
                    'is_meat' => false,
                    'own_meat' => false,
                ];
            }

            if ($saltPacks > 0) {
                $items[] = [
                    'material_id' => $saltMaterial?->id,
                    'name' => $saltMaterial?->name ?? 'Garam Kasar',
                    'weight' => $saltTotalGrams,
                    'unit' => 'gram',
                    'price_per_gram' => $saltPricePerGram,
                    'cost' => $saltMarketCost,
                    'proportional_cost' => $saltMarketCost,
                    'market_cost' => $saltMarketCost,
                    'package_info' => "{$saltPacks} pack (@".number_format($saltPkgWeight).'g)',
                    'packs' => $saltPacks,
                    'excess_grams' => 0,
                    'note' => "{$saltPacks} pack garam beryodium",
                    'is_meat' => false,
                    'own_meat' => false,
                ];
            }

            if (! empty($this->extraItems)) {
                $this->appendExtraItemsToBreakdown($items, $allMaterials);
            }

            $this->breakdown = $items;
            $this->totalProportionalCost = (float) array_sum(array_column($items, 'proportional_cost'));
            $this->totalMarketCost = (float) array_sum(array_column($items, 'market_cost'));

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

        // Logika Mode Anggaran (Calculate by Budget)
        if ($this->calculationMode === 'budget') {
            $budgetValue = max(0.0, (float) $this->budget);
            $availableForCore = $budgetValue - $extraItemsCost;
            $minBudget = ($costPerGram * 500.0) + $extraItemsCost;

            if ($budgetValue < $minBudget || $availableForCore <= 0.0) {
                $this->budgetError = 'Anggaran Anda belum mencukupi. Minimal butuh Rp '.number_format($minBudget, 0, ',', '.').' untuk racikan standar 500 gram.';
                $this->breakdown = [];
                $this->totalProportionalCost = 0.0;
                $this->totalMarketCost = 0.0;

                return;
            }

            // Hitung gramatur daging maksimal yang bisa diracik sesuai anggaran
            $this->meatWeight = (int) floor($availableForCore / $costPerGram);
        }

        $weight = max(500, min(50000, (int) $this->meatWeight));
        $this->meatWeight = $weight;

        // Kebutuhan takaran proporsional terpakai berdasarkan rasio dinamis
        $meatNeeded = $weight * $meatRatio;
        $flourNeeded = $weight * $flourRatio;
        $garlicNeeded = $weight * $garlicRatio;
        $saltNeeded = $weight * $saltRatio;

        // Biaya proporsional bahan terpakai
        $meatPropCost = $meatNeeded * $meatPricePerGram;
        $flourPropCost = $flourNeeded * $flourPricePerGram;
        $garlicPropCost = $garlicNeeded * $garlicPricePerGram;
        $saltPropCost = $saltNeeded * $saltPricePerGram;

        // Biaya kemasan pasar (uang belanja pasar minimum kemasan utuh)
        $meatMarketCost = $this->startType === 'own_meat' ? 0.0 : $meatPropCost;

        // Logika kemasan tepung pasar dinamis berdasarkan master bahan
        $flourPkgWeight = (float) ($flourMaterial?->package_weight ?? 500.0);
        $flourPkgPrice = (float) ($flourMaterial?->package_price ?? 9000.0);
        $flourPkgUnit = (string) ($flourMaterial?->package_unit ?? 'pack');
        $flourPacks = (int) max(1, ceil($flourNeeded / max(1.0, $flourPkgWeight)));
        $flourMarketCost = $flourPacks * $flourPkgPrice;
        $flourTotalGrams = $flourPacks * $flourPkgWeight;
        $flourExcessGrams = max(0, $flourTotalGrams - (int) round($flourNeeded));

        // Kemasan bawang putih & garam dari master bahan
        $garlicPkgWeight = (float) ($garlicMaterial?->package_weight ?? 100.0);
        $garlicPkgPrice = (float) ($garlicMaterial?->package_price ?? 4000.0);
        $garlicPacks = (int) max(1, ceil($garlicNeeded / max(1.0, $garlicPkgWeight)));
        $garlicMarketCost = $garlicPacks * $garlicPkgPrice;

        $saltPkgWeight = (float) ($saltMaterial?->package_weight ?? 1000.0);
        $saltPkgPrice = (float) ($saltMaterial?->package_price ?? 3000.0);
        $saltPacks = (int) max(1, ceil($saltNeeded / max(1.0, $saltPkgWeight)));
        $saltMarketCost = $saltPacks * $saltPkgPrice;

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

        // 2. Tepung Pilihan
        $flourName = $flourMaterial?->name ?? 'Tepung Tapioka';
        $flourNote = "Beli {$flourPacks} {$flourPkgUnit} @".number_format($flourPkgWeight).'g'.($flourExcessGrams > 0 ? " (sisa {$flourExcessGrams}g disimpan)" : '');
        $items[] = [
            'material_id' => $flourMaterial?->id,
            'name' => $flourName,
            'weight' => $flourNeeded,
            'unit' => 'gram',
            'price_per_gram' => $flourPricePerGram,
            'cost' => $flourPropCost,
            'proportional_cost' => $flourPropCost,
            'market_cost' => $flourMarketCost,
            'package_info' => "{$flourPacks} {$flourPkgUnit} (@".number_format($flourPkgWeight).'g)',
            'excess_grams' => $flourExcessGrams,
            'note' => $flourNote,
            'is_meat' => false,
            'own_meat' => false,
        ];

        // 3. Bawang Putih
        $garlicExcess = max(0, ($garlicPacks * $garlicPkgWeight) - (int) round($garlicNeeded));
        $items[] = [
            'material_id' => $garlicMaterial?->id,
            'name' => $garlicMaterial?->name ?? 'Bawang Putih Kupas',
            'weight' => $garlicNeeded,
            'unit' => 'gram',
            'price_per_gram' => $garlicPricePerGram,
            'cost' => $garlicPropCost,
            'proportional_cost' => $garlicPropCost,
            'market_cost' => $garlicMarketCost,
            'package_info' => "{$garlicPacks} pack (@".number_format($garlicPkgWeight).'g)',
            'excess_grams' => $garlicExcess,
            'note' => "Beli {$garlicPacks} pack @".number_format($garlicPkgWeight).'g',
            'is_meat' => false,
            'own_meat' => false,
        ];

        // 4. Garam
        $saltExcess = max(0, ($saltPacks * $saltPkgWeight) - (int) round($saltNeeded));
        $items[] = [
            'material_id' => $saltMaterial?->id,
            'name' => $saltMaterial?->name ?? 'Garam Kasar',
            'weight' => $saltNeeded,
            'unit' => 'gram',
            'price_per_gram' => $saltPricePerGram,
            'cost' => $saltPropCost,
            'proportional_cost' => $saltPropCost,
            'market_cost' => $saltMarketCost,
            'package_info' => "{$saltPacks} pack (@".number_format($saltPkgWeight).'g)',
            'excess_grams' => $saltExcess,
            'note' => "Beli {$saltPacks} pack @".number_format($saltPkgWeight).'g',
            'is_meat' => false,
            'own_meat' => false,
        ];

        // 5. Jasa Selepan (Penggilingan)
        if ($this->useGrinding) {
            $grindingRate = (float) config('services.kiosk.grinding_fee_per_kg', 15000.0);
            $grindingFee = ($weight / 1000.0) * $grindingRate;
            $items[] = [
                'material_id' => null,
                'name' => 'Jasa Selepan Pasar',
                'weight' => $weight,
                'unit' => 'gram',
                'price_per_gram' => $grindingRate / 1000.0,
                'cost' => $grindingFee,
                'proportional_cost' => $grindingFee,
                'market_cost' => $grindingFee,
                'note' => 'Rp '.number_format($grindingRate, 0, ',', '.').' / kg daging',
                'is_meat' => false,
                'own_meat' => false,
            ];
        }

        if (! empty($extraPreviewItems)) {
            $items = array_merge($items, $extraPreviewItems);
        }

        $this->breakdown = $items;
        $this->totalProportionalCost = (float) array_sum(array_column($items, 'proportional_cost'));
        $this->totalMarketCost = (float) array_sum(array_column($items, 'market_cost'));
    }

    /**
     * Tambahkan bahan pelengkap pilihan pengguna (hanya yang tersedia) ke breakdown kalkulator.
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
            ? $materialsPool->whereIn('id', $activeExtraIds)->where('is_available', true)->keyBy('id')
            : Material::whereIn('id', $activeExtraIds)->where('is_available', true)->get()->keyBy('id');

        foreach ($this->extraItems as $extraId => $qty) {
            $safeQty = min(100, max(0, (int) $qty));
            if ($safeQty > 0 && isset($extraMaterials[$extraId])) {
                $mat = $extraMaterials[$extraId];
                $pkgWeight = (float) ($mat->package_weight ?? 1.0);
                $pkgPrice = (float) ($mat->package_price ?? $mat->price ?? 0.0);
                $pkgUnit = (string) ($mat->package_unit ?? 'pack');
                $cost = $safeQty * $pkgPrice;

                $items[] = [
                    'material_id' => $mat->id,
                    'name' => $mat->name,
                    'weight' => $safeQty * $pkgWeight,
                    'unit' => $mat->unit ?? 'gram',
                    'price_per_gram' => (float) $mat->price,
                    'cost' => $cost,
                    'proportional_cost' => $cost,
                    'market_cost' => $cost,
                    'package_info' => "{$safeQty} {$pkgUnit} (@".number_format($pkgWeight).'g)',
                    'qty' => $safeQty,
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
        if ($this->totalProportionalCost > 0 && empty($this->budgetError) && empty($this->availabilityError)) {
            if (empty($this->pickupTime)) {
                $this->pickupTime = now()->addDay()->setTime(8, 0)->format('Y-m-d\TH:i');
            }
            $this->showCheckoutModal = true;
        }
    }

    /**
     * Tutup modal checkout.
     */
    public function closeCheckout(): void
    {
        $this->showCheckoutModal = false;
    }

    /**
     * Simpan pesanan ke database (Order & OrderItem) dan arahkan ke WhatsApp.
     */
    public function processCheckout()
    {
        $throttleKey = 'checkout|'.request()->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 10)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('customerPhone', "Terlalu banyak pesanan dibuat dari perangkat ini. Harap tunggu {$seconds} detik.");

            return;
        }

        $this->validate([
            'customerName' => ['required', 'string', 'min:2', 'max:100'],
            'customerPhone' => ['required', 'string', 'regex:/^(\+62|62|08)[0-9]{8,13}$/'],
            'pickupTime' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $pickup = Carbon::parse($value);
                    $minPickup = now()->addHour();
                    if ($pickup->lt($minPickup)) {
                        $fail('Waktu pengambilan minimal 1 jam dari pemesanan (paling awal pukul '.$minPickup->format('H:i').' WIB).');

                        return;
                    }

                    $openHour = (int) config('services.kiosk.open_hour', 4);
                    $closeHour = (int) config('services.kiosk.close_hour', 12);

                    $hour = (int) $pickup->format('H');
                    $minute = (int) $pickup->format('i');

                    // Jam operasional kios: 04.00 s/d 12.00 WIB
                    if ($hour < $openHour || ($hour == $closeHour && $minute > 0) || $hour > $closeHour) {
                        $fail(sprintf('Jam pengambilan harus pada jam operasional kios (%02d.00 – %02d.00 WIB).', $openHour, $closeHour));
                    }
                },
            ],
        ], [
            'customerName.required' => 'Nama lengkap wajib diisi.',
            'customerName.min' => 'Nama minimal 2 karakter.',
            'customerPhone.required' => 'Nomor WhatsApp pembeli wajib diisi.',
            'customerPhone.regex' => 'Format nomor WhatsApp tidak valid (contoh: 08123456789 atau 628123456789).',
            'pickupTime.required' => 'Jam pengambilan wajib diisi.',
            'pickupTime.date' => 'Format jam pengambilan tidak valid.',
        ]);

        // Hitung ulang secara server-authoritative agar manipulasi state client diabaikan
        RateLimiter::hit($throttleKey, 60);
        $this->calculateEstimation();

        if (! empty($this->availabilityError)) {
            $this->addError('calculation', $this->availabilityError);

            return;
        }

        if ($this->totalMarketCost <= 0 || ! empty($this->budgetError)) {
            $this->addError('calculation', 'Kalkulasi racikan tidak valid.');

            return;
        }

        $grindingRate = (float) config('services.kiosk.grinding_fee_per_kg', 15000.0);
        $grindingFee = $this->useGrinding ? (($this->meatWeight / 1000.0) * $grindingRate) : 0.0;
        $totalPrice = (float) $this->totalMarketCost;

        // 1 & 2. Simpan order dan order items secara atomik dalam transaksi DB
        $order = DB::transaction(function () use ($grindingFee, $totalPrice) {
            $order = Order::create([
                'user_id' => Auth::id(),
                'customer_name' => $this->customerName,
                'customer_phone' => $this->customerPhone,
                'pickup_at' => $this->pickupTime,
                'start_type' => $this->startType,
                'meat_weight' => $this->meatWeight,
                'preference' => $this->preference,
                'grinding_fee' => $grindingFee,
                'total_price' => $totalPrice,
                'status' => 'pending',
            ]);

            foreach ($this->breakdown as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'material_id' => $item['material_id'] ?? null,
                    'material_name' => $item['name'],
                    'quantity' => $item['weight'],
                    'unit' => $item['unit'] ?? 'gram',
                    'unit_price' => $item['price_per_gram'] ?? 0.0,
                    'subtotal' => $item['market_cost'] ?? $item['cost'] ?? $item['proportional_cost'] ?? 0.0,
                ]);
            }

            return $order;
        });

        // 3. Susun teks pesan WhatsApp terstruktur
        $startTypeLabel = match ($this->startType) {
            'own_meat' => 'Bawa Daging Sendiri',
            'ingredients_only' => 'Beli Bahan / Tepung Saja (Tanpa Daging)',
            default => 'Mulai dari Nol (Beli Semua)',
        };
        $pickupFormatted = date('d M Y, H:i', strtotime($this->pickupTime));

        $lines = [];
        $lines[] = '*PESANAN RACIKAN BAKSO - BASOKU*';
        $lines[] = '-----------------------------------';
        $lines[] = '*No. Pesanan:* #BSK-'.str_pad((string) $order->id, 5, '0', STR_PAD_LEFT);
        $lines[] = '*Nama Pemesan:* '.$this->customerName;
        $lines[] = '*No. WhatsApp:* '.$this->customerPhone;
        $lines[] = '*Jadwal Ambil:* '.$pickupFormatted.' WIB';
        $lines[] = '*Mode Racikan:* '.$startTypeLabel;
        if ($this->startType !== 'ingredients_only') {
            $lines[] = '*Daging Utama:* '.ucfirst($this->meatType).' ('.number_format($this->meatWeight).' gram)';
            $lines[] = '*Karakter:* '.ucfirst($this->preference);
        } else {
            $lines[] = '*Kategori:* Belanja Bahan & Bumbu Kios';
        }
        $lines[] = '';
        $lines[] = '*Rincian Belanja Kemasan Pasar:*';
        foreach ($this->breakdown as $item) {
            $costFormatted = 'Rp '.number_format($item['market_cost'] ?? $item['cost'] ?? 0, 0, ',', '.');
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

        $waText = urlencode(implode("\n", $lines));
        $this->closeCheckout();

        // 4. Redirect ke WhatsApp
        return $this->redirect('https://wa.me/'.$this->sellerWaNumber.'?text='.$waText, navigate: false);
    }

    /**
     * Buka modal penyimpanan racikan ke Laboratorium Resep.
     * Jika belum login, arahkan ke halaman masuk.
     */
    public function openSaveModal()
    {
        if (! Auth::check()) {
            session()->put('calculator_draft', [
                'calculationMode' => $this->calculationMode,
                'budget' => $this->budget,
                'startType' => $this->startType,
                'meatType' => $this->meatType,
                'meatWeight' => $this->meatWeight,
                'flourType' => $this->flourType,
                'preference' => $this->preference,
                'customFlourRatio' => $this->customFlourRatio,
                'useGrinding' => $this->useGrinding,
                'ingredientFlourPacks' => $this->ingredientFlourPacks,
                'ingredientGarlicPacks' => $this->ingredientGarlicPacks,
                'ingredientSaltPacks' => $this->ingredientSaltPacks,
                'extraItems' => $this->extraItems,
            ]);

            return $this->redirect('/masuk', navigate: true);
        }

        $this->recipeName = 'Racikan '.ucfirst($this->meatType).' '.ucfirst($this->preference).' ('.$this->meatWeight.'g)';
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
     * Simpan racikan ke basis data saved_recipes milik pengguna.
     */
    public function saveRecipe()
    {
        if (! Auth::check()) {
            return $this->redirect('/masuk', navigate: true);
        }

        $this->validate([
            'recipeName' => ['required', 'string', 'max:100'],
        ], [
            'recipeName.required' => 'Nama racikan wajib diisi.',
            'recipeName.max' => 'Nama racikan maksimal 100 karakter.',
        ]);

        SavedRecipe::create([
            'user_id' => Auth::id(),
            'name' => trim($this->recipeName),
            'meat_weight' => $this->meatWeight,
            'start_type' => $this->startType,
            'preference' => $this->preference,
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
