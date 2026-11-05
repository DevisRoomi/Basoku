<?php

namespace App\Livewire;

use App\Models\Material;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\RecipeFormula;
use App\Models\SavedRecipe;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class RecipeCalculator extends Component
{
    /** Mode kalkulasi: 'weight' (berdasarkan berat) atau 'budget' (berdasarkan anggaran) */
    public string $calculationMode = 'weight';

    /** Nilai anggaran dalam rupiah (default Rp 100.000) */
    public int|float $budget = 100000;

    /** Pesan kesalahan jika anggaran tidak mencukupi standar minimum 500g */
    public ?string $budgetError = null;

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

    public function mount(): void
    {
        $this->sellerWaNumber = config('services.kiosk.wa_number', '6285122797969');

        if ($recipeId = request()->query('recipe_id')) {
            $saved = \App\Models\SavedRecipe::find($recipeId);
            if ($saved) {
                $this->meatWeight = (int) $saved->meat_weight;
                $this->startType = $saved->start_type ?? 'weight';
                $this->preference = $saved->preference ?? 'standar';
            }
        }

        $this->calculateEstimation();
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

        // 1. Ambil data formula rasio dari database (Model RecipeFormula) dengan fallback aman
        if ($this->preference === 'kustom') {
            $meatRatio = 1.0;
            $flourRatio = max(0.10, min(0.60, (float) ($this->customFlourRatio / 100.0)));
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

        // 2. Ambil data harga riil dari database (Model Material)
        try {
            $meatMaterial = $this->meatType === 'sapi'
                ? Material::where('name', 'like', '%Daging Sapi%')->first()
                : Material::where('name', 'like', '%Daging Ayam%')->first();

            $flourQuery = match ($this->flourType) {
                'probakso' => Material::where('name', 'like', '%ProBakso%'),
                'sagu' => Material::where('name', 'like', '%Sagu%'),
                'terigu' => Material::where('name', 'like', '%Terigu%'),
                default => Material::where('name', 'like', '%Tapioka%'),
            };
            $flourMaterial = $flourQuery->first() ?? Material::where('name', 'like', '%Tapioka%')->first();

            $garlicMaterial = Material::where('name', 'like', '%Bawang Putih%')->first();
            $saltMaterial = Material::where('name', 'like', '%Garam%')->first();
        } catch (\Throwable $e) {
            $meatMaterial = null;
            $flourMaterial = null;
            $garlicMaterial = null;
            $saltMaterial = null;
        }

        // Harga per gram dari database dengan fallback aman jika belum ada
        $meatPricePerGram = (float) ($meatMaterial?->price ?? ($this->meatType === 'sapi' ? 120.0 : 45.0));
        $flourPricePerGram = (float) ($flourMaterial?->price ?? 18.0);
        $garlicPricePerGram = (float) ($garlicMaterial?->price ?? 40.0);
        $saltPricePerGram = (float) ($saltMaterial?->price ?? 14.0);

        // Logika Mode Beli Bahan Saja (Tanpa Daging & Tanpa Jasa Giling)
        if ($this->startType === 'ingredients_only') {
            $this->useGrinding = false;
            $this->meatWeight = 0;

            $flourPkgWeight = (float) ($flourMaterial?->package_weight ?? 500.0);
            $flourPkgPrice = (float) ($flourMaterial?->package_price ?? 9000.0);
            $flourPkgUnit = (string) ($flourMaterial?->package_unit ?? 'pack');
            $flourPacks = max(1, (int) $this->ingredientFlourPacks);
            $flourTotalGrams = $flourPacks * $flourPkgWeight;
            $flourMarketCost = $flourPacks * $flourPkgPrice;

            $garlicPkgWeight = (float) ($garlicMaterial?->package_weight ?? 100.0);
            $garlicPkgPrice = (float) ($garlicMaterial?->package_price ?? 4000.0);
            $garlicPacks = max(0, (int) $this->ingredientGarlicPacks);
            $garlicTotalGrams = $garlicPacks * $garlicPkgWeight;
            $garlicMarketCost = $garlicPacks * $garlicPkgPrice;

            $saltPkgWeight = (float) ($saltMaterial?->package_weight ?? 250.0);
            $saltPkgPrice = (float) ($saltMaterial?->package_price ?? 3500.0);
            $saltPacks = max(0, (int) $this->ingredientSaltPacks);
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
                'package_info' => "{$flourPacks} {$flourPkgUnit} (@".number_format($flourPkgWeight)."g)",
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
                    'package_info' => "{$garlicPacks} pack (@".number_format($garlicPkgWeight)."g)",
                    'excess_grams' => 0,
                    'note' => "{$garlicPacks} pack bumbu pasar",
                    'is_meat' => false,
                    'own_meat' => false,
                ];
            }

            if ($saltPacks > 0) {
                $items[] = [
                    'material_id' => $saltMaterial?->id,
                    'name' => $saltMaterial?->name ?? 'Garam Halus',
                    'weight' => $saltTotalGrams,
                    'unit' => 'gram',
                    'price_per_gram' => $saltPricePerGram,
                    'cost' => $saltMarketCost,
                    'proportional_cost' => $saltMarketCost,
                    'market_cost' => $saltMarketCost,
                    'package_info' => "{$saltPacks} pack (@".number_format($saltPkgWeight)."g)",
                    'excess_grams' => 0,
                    'note' => "{$saltPacks} pack garam beryodium",
                    'is_meat' => false,
                    'own_meat' => false,
                ];
            }

            $this->breakdown = $items;
            $this->totalProportionalCost = $flourMarketCost + $garlicMarketCost + $saltMarketCost;
            $this->totalMarketCost = $this->totalProportionalCost;

            return;
        }

        // Biaya proporsional per 1 gram daging acuan
        $costPerGram = ($this->startType === 'start_from_zero' ? ($meatPricePerGram * $meatRatio) : 0.0)
            + ($flourRatio * $flourPricePerGram)
            + ($garlicRatio * $garlicPricePerGram)
            + ($saltRatio * $saltPricePerGram)
            + ($this->useGrinding ? 15.0 : 0.0);

        // Logika Mode Anggaran (Calculate by Budget)
        if ($this->calculationMode === 'budget') {
            $budgetValue = (float) $this->budget;
            $minBudget = $costPerGram * 500.0;

            if ($budgetValue < $minBudget) {
                $this->budgetError = 'Anggaran Anda belum mencukupi. Minimal butuh Rp '.number_format($minBudget, 0, ',', '.').' untuk racikan standar 500 gram.';
                $this->breakdown = [];
                $this->totalProportionalCost = 0.0;
                $this->totalMarketCost = 0.0;

                return;
            }

            // Hitung gramatur daging maksimal yang bisa diracik sesuai anggaran
            $this->meatWeight = (int) floor($budgetValue / $costPerGram);
        }

        $weight = max(500, (int) $this->meatWeight);

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

        $saltPkgWeight = (float) ($saltMaterial?->package_weight ?? 250.0);
        $saltPkgPrice = (float) ($saltMaterial?->package_price ?? 3500.0);
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
        $flourNote = "Beli {$flourPacks} {$flourPkgUnit} @".number_format($flourPkgWeight)."g" . ($flourExcessGrams > 0 ? " (sisa {$flourExcessGrams}g disimpan)" : '');
        $items[] = [
            'material_id' => $flourMaterial?->id,
            'name' => $flourName,
            'weight' => $flourNeeded,
            'unit' => 'gram',
            'price_per_gram' => $flourPricePerGram,
            'cost' => $flourPropCost,
            'proportional_cost' => $flourPropCost,
            'market_cost' => $flourMarketCost,
            'package_info' => "{$flourPacks} {$flourPkgUnit} (@".number_format($flourPkgWeight)."g)",
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
            'package_info' => "{$garlicPacks} pack (@".number_format($garlicPkgWeight)."g)",
            'excess_grams' => $garlicExcess,
            'note' => "Beli {$garlicPacks} pack @".number_format($garlicPkgWeight).'g',
            'is_meat' => false,
            'own_meat' => false,
        ];

        // 4. Garam
        $saltExcess = max(0, ($saltPacks * $saltPkgWeight) - (int) round($saltNeeded));
        $items[] = [
            'material_id' => $saltMaterial?->id,
            'name' => $saltMaterial?->name ?? 'Garam Halus',
            'weight' => $saltNeeded,
            'unit' => 'gram',
            'price_per_gram' => $saltPricePerGram,
            'cost' => $saltPropCost,
            'proportional_cost' => $saltPropCost,
            'market_cost' => $saltMarketCost,
            'package_info' => "{$saltPacks} pack (@".number_format($saltPkgWeight)."g)",
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

        $this->breakdown = $items;
        $this->totalProportionalCost = (float) array_sum(array_column($items, 'proportional_cost'));
        $this->totalMarketCost = (float) array_sum(array_column($items, 'market_cost'));
    }

    /**
     * Buka modal checkout jika tidak ada error dan kalkulasi valid.
     */
    public function openCheckout(): void
    {
        if ($this->totalProportionalCost > 0 && empty($this->budgetError)) {
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
        $this->validate([
            'customerName' => ['required', 'string', 'min:2', 'max:100'],
            'customerPhone' => ['required', 'string', 'regex:/^(\+62|62|08)[0-9]{8,13}$/'],
            'pickupTime' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $pickup = \Carbon\Carbon::parse($value);
                    $minPickup = now()->addHour();
                    if ($pickup->lt($minPickup)) {
                        $fail('Waktu pengambilan minimal 1 jam dari pemesanan (paling awal pukul '.$minPickup->format('H:i').' WIB).');
                        return;
                    }

                    $openHour = (int) config('services.kiosk.open_hour', 5);
                    $closeHour = (int) config('services.kiosk.close_hour', 12);

                    $hour = (int) $pickup->format('H');
                    $minute = (int) $pickup->format('i');

                    // Jam operasional kios: 05.00 s/d 12.00 WIB
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
        $this->calculateEstimation();

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
        $lines[] = '*No. Pesanan:* #ORD-'.str_pad((string) $order->id, 5, '0', STR_PAD_LEFT);
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
            $pkgInfo = ! empty($item['package_info']) ? ' ['. $item['package_info'] .']' : '';
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

    /**
     * Hitung kombinasi kemasan pasar tepung (250g, 500g, 1000g).
     *
     * @return array{packs: array{250: int, 500: int, 1000: int}, total_grams: int, note: string}
     */
    private function calculateFlourPackaging(float $neededGrams): array
    {
        if ($neededGrams <= 0) {
            return ['packs' => [250 => 0, 500 => 0, 1000 => 0], 'total_grams' => 0, 'note' => ''];
        }

        $kgPacks = (int) floor($neededGrams / 1000.0);
        $remainder = fmod($neededGrams, 1000.0);

        $pack500 = 0;
        $pack250 = 0;

        if ($remainder > 0 && $remainder <= 250.0) {
            $pack250 = 1;
        } elseif ($remainder > 250.0 && $remainder <= 500.0) {
            $pack500 = 1;
        } elseif ($remainder > 500.0 && $remainder <= 750.0) {
            $pack500 = 1;
            $pack250 = 1;
        } elseif ($remainder > 750.0) {
            $kgPacks += 1;
        }

        $totalGrams = ($kgPacks * 1000) + ($pack500 * 500) + ($pack250 * 250);
        $excess = $totalGrams - (int) round($neededGrams);

        $parts = [];
        if ($kgPacks > 0) {
            $parts[] = "{$kgPacks}x 1 kg";
        }
        if ($pack500 > 0) {
            $parts[] = "{$pack500}x 500g";
        }
        if ($pack250 > 0) {
            $parts[] = "{$pack250}x 250g";
        }

        $packDesc = implode(' + ', $parts);
        $note = "Beli {$packDesc}" . ($excess > 0 ? " (sisa {$excess}g)" : '');

        return [
            'packs' => [250 => $pack250, 500 => $pack500, 1000 => $kgPacks],
            'total_grams' => $totalGrams,
            'note' => $note,
        ];
    }

    public function render()
    {
        return view('livewire.recipe-calculator');
    }
}
