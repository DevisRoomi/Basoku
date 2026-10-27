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

    /** Preferensi karakter racikan: 'enak', 'seimbang', atau 'ekonomis' */
    public string $preference = 'seimbang';

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

        $garlicRatio = $spiceRatio * 0.6;
        $saltRatio = $spiceRatio * 0.4;

        // 2. Ambil data harga riil dari database (Model Material)
        try {
            $meatMaterial = $this->meatType === 'sapi'
                ? Material::where('name', 'like', '%Daging Sapi%')->first()
                : Material::where('name', 'like', '%Daging Ayam%')->first();

            $tapiocaMaterial = Material::where('name', 'like', '%Tapioka%')->first();
            $garlicMaterial = Material::where('name', 'like', '%Bawang Putih%')->first();
            $saltMaterial = Material::where('name', 'like', '%Garam%')->first();
        } catch (\Throwable $e) {
            $meatMaterial = null;
            $tapiocaMaterial = null;
            $garlicMaterial = null;
            $saltMaterial = null;
        }

        // Harga per gram dari database dengan fallback aman jika belum ada
        $meatPricePerGram = (float) ($meatMaterial?->price ?? ($this->meatType === 'sapi' ? 140.0 : 40.0));
        $tapiocaPricePerGram = (float) ($tapiocaMaterial?->price ?? 18.0);
        $garlicPricePerGram = (float) ($garlicMaterial?->price ?? 40.0);
        $saltPricePerGram = (float) ($saltMaterial?->price ?? 3.0);

        // Biaya proporsional per 1 gram daging acuan
        $costPerGram = ($this->startType === 'start_from_zero' ? ($meatPricePerGram * $meatRatio) : 0.0)
            + ($flourRatio * $tapiocaPricePerGram)
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
        $tapiocaNeeded = $weight * $flourRatio;
        $garlicNeeded = $weight * $garlicRatio;
        $saltNeeded = $weight * $saltRatio;

        // Biaya proporsional bahan terpakai
        $meatPropCost = $meatNeeded * $meatPricePerGram;
        $tapiocaPropCost = $tapiocaNeeded * $tapiocaPricePerGram;
        $garlicPropCost = $garlicNeeded * $garlicPricePerGram;
        $saltPropCost = $saltNeeded * $saltPricePerGram;

        // Biaya kemasan pasar (uang belanja pasar minimum kemasan utuh)
        $meatMarketCost = $this->startType === 'own_meat' ? 0.0 : $meatPropCost;

        // Logika kemasan tepung pasar (250g, 500g, 1 kg)
        $flourPackaging = $this->calculateFlourPackaging($tapiocaNeeded);
        $tapiocaMarketCost = $flourPackaging['total_grams'] * $tapiocaPricePerGram;

        $garlicPacks = (int) ceil($garlicNeeded / 100.0);
        $garlicMarketCost = $garlicPacks * 4000.0;
        $saltPacks = (int) ceil($saltNeeded / 1000.0);
        $saltMarketCost = $saltPacks * 3000.0;

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
            'note' => $isOwnMeat ? 'Bawa sendiri (Rp 0)' : 'Daging segar pasar',
            'is_meat' => true,
            'own_meat' => $isOwnMeat,
        ];

        // 2. Tepung Tapioka
        $items[] = [
            'material_id' => $tapiocaMaterial?->id,
            'name' => $tapiocaMaterial?->name ?? 'Tepung Tapioka',
            'weight' => $tapiocaNeeded,
            'unit' => 'gram',
            'price_per_gram' => $tapiocaPricePerGram,
            'cost' => $tapiocaPropCost,
            'proportional_cost' => $tapiocaPropCost,
            'market_cost' => $tapiocaMarketCost,
            'note' => $flourPackaging['note'],
            'is_meat' => false,
            'own_meat' => false,
        ];

        // 3. Bawang Putih
        $items[] = [
            'material_id' => $garlicMaterial?->id,
            'name' => $garlicMaterial?->name ?? 'Bawang Putih Kupas',
            'weight' => $garlicNeeded,
            'unit' => 'gram',
            'price_per_gram' => $garlicPricePerGram,
            'cost' => $garlicPropCost,
            'proportional_cost' => $garlicPropCost,
            'market_cost' => $garlicMarketCost,
            'note' => 'Acuan kemasan 100g',
            'is_meat' => false,
            'own_meat' => false,
        ];

        // 4. Garam
        $items[] = [
            'material_id' => $saltMaterial?->id,
            'name' => $saltMaterial?->name ?? 'Garam Kasar',
            'weight' => $saltNeeded,
            'unit' => 'gram',
            'price_per_gram' => $saltPricePerGram,
            'cost' => $saltPropCost,
            'proportional_cost' => $saltPropCost,
            'market_cost' => $saltMarketCost,
            'note' => 'Acuan kemasan 1 kg',
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
        $startTypeLabel = $this->startType === 'start_from_zero' ? 'Mulai dari Nol (Beli Semua)' : 'Bawa Daging Sendiri';
        $pickupFormatted = date('d M Y, H:i', strtotime($this->pickupTime));

        $lines = [];
        $lines[] = '*PESANAN RACIKAN BAKSO - BASOKU*';
        $lines[] = '-----------------------------------';
        $lines[] = '*No. Pesanan:* #ORD-'.str_pad((string) $order->id, 5, '0', STR_PAD_LEFT);
        $lines[] = '*Nama Pemesan:* '.$this->customerName;
        $lines[] = '*Jadwal Ambil:* '.$pickupFormatted.' WIB';
        $lines[] = '*Mode Racikan:* '.$startTypeLabel;
        $lines[] = '*Daging Utama:* '.ucfirst($this->meatType).' ('.number_format($this->meatWeight).' gram)';
        $lines[] = '*Karakter:* '.ucfirst($this->preference);
        $lines[] = '';
        $lines[] = '*Rincian Bahan & Takaran:*';
        foreach ($this->breakdown as $item) {
            $costFormatted = 'Rp '.number_format($item['cost'] ?? $item['proportional_cost'] ?? 0, 0, ',', '.');
            $note = ! empty($item['own_meat']) ? ' (Bawa Sendiri)' : '';
            $lines[] = '• '.$item['name'].': '.number_format($item['weight']).' '.($item['unit'] ?? 'gram').$note.' - '.$costFormatted;
        }

        $lines[] = '';
        $lines[] = '*Jasa Selepan:* '.($this->useGrinding ? 'Rp '.number_format($grindingFee, 0, ',', '.') : 'Tanpa Selepan');
        $lines[] = '-----------------------------------';
        $lines[] = '*TOTAL NILAI BAHAN:* Rp '.number_format($totalPrice, 0, ',', '.');
        $lines[] = '*ESTIMASI BELANJA PASAR:* Rp '.number_format($this->totalMarketCost, 0, ',', '.');
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
