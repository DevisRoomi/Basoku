<?php

namespace App\Livewire;

use App\Models\Material;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        $this->resetErrorBag();
        $this->showDirectCheckoutModal = true;
    }

    /**
     * Tutup modal pemesanan langsung.
     */
    public function closeDirectCheckout(): void
    {
        $this->showDirectCheckoutModal = false;
        $this->resetErrorBag();
    }

    /**
     * Proses pesanan belanja bahan langsung ke WhatsApp & Database.
     */
    public function processDirectCheckout()
    {
        $throttleKey = 'catalog-checkout|'.request()->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 10)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('customerPhone', "Terlalu banyak pesanan dibuat dari perangkat ini. Harap tunggu {$seconds} detik.");

            return;
        }

        $this->validate([
            'customerName' => ['required', 'string', 'min:2', 'max:100'],
            'customerPhone' => ['required', 'string', 'regex:/^(\+62|62|08)[0-9]{8,13}$/'],
            'pickupTime' => [
                'bail',
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    try {
                        $pickup = Carbon::parse($value);
                    } catch (\Throwable) {
                        $fail('Format jam pengambilan tidak valid.');

                        return;
                    }

                    $minPickup = now()->addHour();
                    if ($pickup->lt($minPickup)) {
                        $fail('Waktu pengambilan minimal 1 jam dari pemesanan (paling awal pukul '.$minPickup->format('H:i').' WIB).');

                        return;
                    }

                    $openHour = (int) config('services.kiosk.open_hour', 4);
                    $closeHour = (int) config('services.kiosk.close_hour', 12);

                    $hour = (int) $pickup->format('H');
                    $minute = (int) $pickup->format('i');

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
        $pickupFormatted = Carbon::parse($this->pickupTime)->format('d M Y, H:i');
        $lines = [];
        $lines[] = '*PESANAN BELANJA BAHAN KIOS - BASOKU*';
        $lines[] = '-----------------------------------';
        $lines[] = '*No. Pesanan:* #BSK-'.str_pad((string) $order->id, 5, '0', STR_PAD_LEFT);
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

        $this->showDirectCheckoutModal = false;
        $this->clearCart();

        return $this->redirect('https://wa.me/'.$waNumber.'?text='.$waText, navigate: false);
    }

    public function render()
    {
        try {
            $query = Material::where('is_available', true);

            // Filter search
            if (! empty(trim($this->search))) {
                $query->where('name', 'like', '%'.trim($this->search).'%');
            }

            // Filter category
            if ($this->activeCategory !== 'all') {
                if ($this->activeCategory === 'pelengkap') {
                    $query->whereIn('category', ['pengikat', 'tambahan', 'penyedap']);
                } else {
                    $query->where('category', $this->activeCategory);
                }
            }

            $categoryOrder = [
                'daging' => 1,
                'tepung' => 2,
                'bumbu' => 3,
                'pelengkap' => 4,
            ];

            $materials = $query->orderBy('name')->get();
            $groupedMaterials = $materials
                ->groupBy(fn (Material $material) => in_array(strtolower($material->category), ['pengikat', 'tambahan', 'penyedap'], true)
                    ? 'pelengkap'
                    : strtolower($material->category))
                ->sortBy(fn ($items, string $cat) => $categoryOrder[$cat] ?? 99);

            // Hitung total belanja keranjang HANYA dari bahan yang masih aktif & tersedia
            $activeItems = array_filter($this->selectedItems, fn ($qty) => (int) $qty > 0);
            $totalSelectedPacks = 0;
            $totalSelectedCost = 0.0;

            if (! empty($activeItems)) {
                $allMaterials = Material::whereIn('id', array_keys($activeItems))
                    ->where('is_available', true)
                    ->get()
                    ->keyBy('id');
                foreach ($activeItems as $matId => $qty) {
                    $safeQty = min(100, max(0, (int) $qty));
                    if ($safeQty > 0 && isset($allMaterials[$matId])) {
                        $mat = $allMaterials[$matId];
                        $totalSelectedPacks += $safeQty;
                        $totalSelectedCost += ($safeQty * $mat->effectivePackagePrice());
                    }
                }
            }
        } catch (\Throwable) {
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
