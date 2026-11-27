<?php

namespace App\Livewire;

use App\Models\Material;
use App\Models\Order;
use App\Models\OrderItem;
use App\Support\CheckoutHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Locked;
use Livewire\Component;

class MaterialCatalog extends Component
{
    /** State keranjang belanja item bahan [material_id => quantity] */
    public array $selectedItems = [];

    /** Filter pencarian nama bahan */
    public string $search = '';

    /** Filter kategori bahan aktif ('all', 'daging', 'tepung', 'bumbu', 'pelengkap') */
    public string $activeCategory = 'all';

    /** Modal checkout pesanan langsung dari katalog */
    public bool $showDirectCheckoutModal = false;

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

    public function mount(): void
    {
        $this->sellerWaNumber = (string) config('services.kiosk.wa_number', '6285122797969');
        $this->pickupTime = now()->addDay()->setTime(8, 0)->format('Y-m-d\TH:i');
    }

    /**
     * Tambah jumlah item bahan pilihan (+1, maksimal 100 per bahan).
     */
    public function incrementItem(int $materialId): void
    {
        $current = (int) ($this->selectedItems[$materialId] ?? 0);
        $this->selectedItems[$materialId] = min(100, max(0, $current) + 1);
    }

    /**
     * Kurangi jumlah item bahan pilihan (-1).
     */
    public function decrementItem(int $materialId): void
    {
        if (isset($this->selectedItems[$materialId])) {
            $next = ((int) $this->selectedItems[$materialId]) - 1;
            if ($next <= 0) {
                unset($this->selectedItems[$materialId]);
            } else {
                $this->selectedItems[$materialId] = min(100, $next);
            }
        }
    }

    /**
     * Hapus item bahan dari pilihan.
     */
    public function removeItem(int $materialId): void
    {
        unset($this->selectedItems[$materialId]);
    }

    /**
     * Kosongkan seluruh item yang dipilih.
     */
    public function clearCart(): void
    {
        $this->selectedItems = [];
    }

    /**
     * Transfer item yang dipilih ke Kalkulator Racikan tanpa hilang.
     */
    public function transferToCalculator()
    {
        $active = [];
        foreach ($this->selectedItems as $id => $qty) {
            $safeQty = min(100, max(0, (int) $qty));
            if ($safeQty > 0) {
                $active[(int) $id] = $safeQty;
            }
        }

        if (! empty($active)) {
            session()->put('selected_materials', $active);
        }

        return redirect()->route('calculator');
    }

    /**
     * Buka modal pemesanan langsung dari katalog hanya jika ada item tersedia.
     */
    public function openDirectCheckout(): void
    {
        $active = array_filter($this->selectedItems, fn ($qty) => (int) $qty > 0);
        if (empty($active)) {
            return;
        }

        $availableIds = Material::whereIn('id', array_keys($active))
            ->where('is_available', true)
            ->pluck('id')
            ->all();

        $this->selectedItems = array_intersect_key($active, array_flip($availableIds));
        if (empty($this->selectedItems)) {
            return;
        }

        if (empty($this->pickupTime)) {
            $this->pickupTime = now()->addDay()->setTime(8, 0)->format('Y-m-d\TH:i');
        }

        $this->resetConfirmedOrder();
        $this->resetErrorBag();
        $this->showDirectCheckoutModal = true;
    }

    /**
     * Tutup modal pemesanan langsung dan bersihkan state konfirmasi lama.
     */
    public function closeDirectCheckout(): void
    {
        $this->showDirectCheckoutModal = false;
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
     * Proses pesanan belanja bahan langsung ke Database lalu tampilkan layar konfirmasi & tombol WhatsApp.
     */
    public function processDirectCheckout(): void
    {
        if ($this->confirmedOrderCode !== null) {
            return;
        }

        $throttleKey = 'catalog-checkout|'.request()->ip();
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

        $activeItems = array_filter($this->selectedItems, fn ($qty) => (int) $qty > 0);
        if (empty($activeItems)) {
            $this->addError('cart', 'Belum ada bahan yang dipilih.');

            return;
        }

        $materials = Material::whereIn('id', array_keys($activeItems))
            ->where('is_available', true)
            ->get()
            ->keyBy('id');

        $orderItemsData = [];
        $totalPrice = 0.0;

        foreach ($activeItems as $materialId => $qty) {
            $safeQty = min(100, max(0, (int) $qty));
            if ($safeQty > 0 && isset($materials[$materialId])) {
                $mat = $materials[$materialId];
                $pkgPrice = $mat->effectivePackagePrice();
                $pkgWeight = $mat->effectivePackageWeight(1.0);
                $pkgUnit = $mat->effectivePackageUnit();
                $subtotal = round($safeQty * $pkgPrice, 2);
                $totalPrice += $subtotal;

                $orderItemsData[] = [
                    'material_id' => $mat->id,
                    'material_name' => $mat->name,
                    'quantity' => $safeQty,
                    'unit' => $pkgUnit,
                    'unit_price' => $pkgPrice,
                    'subtotal' => $subtotal,
                    'package_info' => "{$safeQty} {$pkgUnit} (@".number_format($pkgWeight).'g)',
                ];
            }
        }

        if (empty($orderItemsData) || $totalPrice <= 0) {
            $this->addError('cart', 'Bahan yang dipilih sedang tidak tersedia di kios.');

            return;
        }

        RateLimiter::hit($throttleKey, 60);

        // Simpan order belanja bahan ke DB
        $order = DB::transaction(function () use ($totalPrice, $orderItemsData) {
            $order = Order::create([
                'user_id' => Auth::id(),
                'customer_name' => trim($this->customerName),
                'customer_phone' => trim($this->customerPhone),
                'pickup_at' => $this->pickupTime,
                'start_type' => 'ingredients_only',
                'meat_weight' => 0,
                'preference' => 'belanja_katalog',
                'grinding_fee' => 0.0,
                'total_price' => round($totalPrice, 2),
                'status' => 'pending',
            ]);

            foreach ($orderItemsData as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'material_id' => $item['material_id'],
                    'material_name' => $item['material_name'],
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            return $order;
        });

        // Susun pesan WhatsApp
        $orderCode = CheckoutHelper::formatOrderCode($order->id);
        $pickupFormatted = Carbon::parse($this->pickupTime)->format('d M Y, H:i');
        $lines = [];
        $lines[] = '*PESANAN BELANJA BAHAN KIOS - BASOKU*';
        $lines[] = '-----------------------------------';
        $lines[] = '*No. Pesanan:* '.$orderCode;
        $lines[] = '*Nama Pemesan:* '.trim($this->customerName);
        $lines[] = '*No. WhatsApp:* '.trim($this->customerPhone);
        $lines[] = '*Jadwal Ambil:* '.$pickupFormatted.' WIB';
        $lines[] = '*Kategori:* Belanja Bahan Baku Langsung';
        $lines[] = '';
        $lines[] = '*Daftar Bahan Yang Dipesan:*';

        $num = 1;
        foreach ($orderItemsData as $item) {
            $lines[] = $num.'. '.$item['material_name'].' ['.$item['package_info'].']: Rp '.number_format($item['subtotal'], 0, ',', '.');
            $num++;
        }

        $lines[] = '-----------------------------------';
        $lines[] = '*TOTAL BAYAR:* Rp '.number_format($totalPrice, 0, ',', '.');
        $lines[] = '';
        $lines[] = 'Mohon disiapkan sebelum jadwal pickup. Terima kasih!';

        $waNumber = (string) config('services.kiosk.wa_number', '6285122797969');
        $waText = urlencode(implode("\n", $lines));

        $this->clearCart();
        $this->confirmedOrderCode = $orderCode;
        $this->confirmedOrderTotal = round($totalPrice, 2);
        $this->confirmedPickupLabel = $pickupFormatted.' WIB';
        $this->confirmedWaUrl = 'https://wa.me/'.$waNumber.'?text='.$waText;
        $this->confirmedTrackUrl = CheckoutHelper::trackOrderUrl($order->id);
        $this->showDirectCheckoutModal = true;
    }

    public function render()
    {
        try {
            // Muat seluruh bahan tersedia 1x dari cache ringan agar pencarian, filter kategori, dan hitungan keranjang tidak menjalankan query ganda
            $availableMaterials = Material::cachedAll()
                ->where('is_available', true)
                ->values();

            $filtered = $availableMaterials;
            $trimmedSearch = trim($this->search);

            if ($trimmedSearch !== '') {
                $needle = mb_strtolower($trimmedSearch);
                $filtered = $filtered->filter(
                    fn (Material $material) => str_contains(mb_strtolower((string) $material->name), $needle)
                );
            }

            if ($this->activeCategory !== 'all') {
                if ($this->activeCategory === 'pelengkap') {
                    $filtered = $filtered->filter(
                        fn (Material $material) => in_array(strtolower((string) $material->category), ['pengikat', 'tambahan', 'penyedap'], true)
                    );
                } else {
                    $filtered = $filtered->filter(
                        fn (Material $material) => strtolower((string) $material->category) === strtolower($this->activeCategory)
                    );
                }
            }

            $categoryOrder = [
                'daging' => 1,
                'tepung' => 2,
                'bumbu' => 3,
                'pelengkap' => 4,
            ];

            $groupedMaterials = $filtered
                ->groupBy(fn (Material $material) => in_array(strtolower((string) $material->category), ['pengikat', 'tambahan', 'penyedap'], true)
                    ? 'pelengkap'
                    : strtolower((string) $material->category))
                ->sortBy(fn ($items, string $cat) => $categoryOrder[$cat] ?? 99);

            // Hitung total belanja keranjang dari koleksi bahan tersedia yang sama (0 query tambahan)
            $activeItems = array_filter($this->selectedItems, fn ($qty) => (int) $qty > 0);
            $totalSelectedPacks = 0;
            $totalSelectedCost = 0.0;

            if (! empty($activeItems)) {
                $materialsById = $availableMaterials->keyBy('id');
                foreach ($activeItems as $matId => $qty) {
                    $safeQty = min(100, max(0, (int) $qty));
                    if ($safeQty > 0 && isset($materialsById[$matId])) {
                        $mat = $materialsById[$matId];
                        $totalSelectedPacks += $safeQty;
                        $totalSelectedCost += ($safeQty * $mat->effectivePackagePrice());
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error('MaterialCatalog::render gagal memuat daftar bahan baku.', [
                'error' => $e->getMessage(),
            ]);
            $groupedMaterials = collect();
            $totalSelectedPacks = 0;
            $totalSelectedCost = 0.0;
        }

        return view('livewire.material-catalog', [
            'groupedMaterials' => $groupedMaterials,
            'totalSelectedPacks' => $totalSelectedPacks,
            'totalSelectedCost' => $totalSelectedCost,
        ]);
    }
}
