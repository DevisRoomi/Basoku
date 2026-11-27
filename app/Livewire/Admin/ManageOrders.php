<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
#[Title('Manajemen Pesanan — BASOKU Admin')]
class ManageOrders extends Component
{
    use WithPagination;

    public bool $showDetailModal = false;

    public ?Order $selectedOrder = null;

    // Filter status tab: 'all', 'pending', 'processing', 'completed', 'cancelled'
    public string $statusFilter = 'all';

    /**
     * Normalisasi status pesanan (mendukung nilai lama bahasa Indonesia dan standar Inggris).
     */
    public function normalizeStatus(?string $status): string
    {
        return Order::normalizeStatus($status);
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Pastikan pengguna memiliki peran admin pada setiap lifecycle request.
     */
    public function boot(): void
    {
        if (! Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Akses khusus administrator BASOKU.');
        }
    }

    /**
     * Pastikan hanya admin yang dapat mengakses.
     */
    public function mount()
    {
        if (! Auth::check() || Auth::user()->role !== 'admin') {
            return $this->redirect('/masuk', navigate: true);
        }
    }

    /**
     * Buka modal rincian pesanan.
     */
    public function viewOrder(int $id)
    {
        $this->selectedOrder = Order::with('orderItems')->findOrFail($id);
        $this->showDetailModal = true;
    }

    /**
     * Tutup modal rincian pesanan.
     */
    public function closeModal()
    {
        $this->showDetailModal = false;
        $this->selectedOrder = null;
    }

    /**
     * Perbarui status pesanan (pesanan yang dibatalkan pembeli terkunci permanen).
     */
    public function updateStatus(int $id, string $newStatus)
    {
        $allowedStatuses = ['pending', 'processing', 'completed', 'cancelled'];

        if (! in_array($newStatus, $allowedStatuses, true)) {
            return;
        }

        $order = Order::findOrFail($id);
        $orderCode = '#BSK-'.str_pad((string) $order->id, 5, '0', STR_PAD_LEFT);

        if ($order->isCancelledByCustomer()) {
            session()->flash('error', "Pesanan {$orderCode} ({$order->customer_name}) telah dibatalkan oleh pembeli sehingga statusnya terkunci permanen dan tidak dapat diubah kembali.");

            return;
        }

        $order->status = $newStatus;
        if ($newStatus === 'cancelled') {
            $order->cancelled_by = 'admin';
            $order->cancelled_at = now();
        } else {
            $order->cancelled_by = null;
            $order->cancelled_at = null;
        }
        $order->save();

        // Refresh selectedOrder jika sedang terbuka di modal
        if ($this->selectedOrder && $this->selectedOrder->id === $id) {
            $this->selectedOrder->refresh();
        }

        $statusLabels = [
            'pending' => 'Menunggu Konfirmasi',
            'processing' => 'Sedang Diproses',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan oleh Kios',
        ];

        $label = $statusLabels[$newStatus] ?? $newStatus;
        session()->flash('success', "Status pesanan {$orderCode} ({$order->customer_name}) berhasil diubah menjadi: {$label}.");
    }

    /**
     * Netralkan sel CSV dari potensi Excel/Calc Formula Injection (=, +, -, @, tab, CR).
     */
    protected function sanitizeCsvCell(mixed $value): string
    {
        $text = (string) ($value ?? '');
        if ($text !== '' && preg_match('/^[=+\-@\t\r]/', $text) === 1) {
            return "'".$text;
        }

        return $text;
    }

    /**
     * Ekspor data pesanan ke file CSV native stream secara hemat memori (lazy chunking & anti-formula-injection).
     */
    public function exportCsv()
    {
        $fileName = 'rekap-pesanan-basoku-'.now()->format('Y-m-d-His').'.csv';
        $statusFilter = $this->statusFilter;

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($statusFilter) {
            $handle = fopen('php://output', 'w');

            // BOM UTF-8 agar Excel mengenali encoding secara otomatis
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header kolom
            fputcsv($handle, [
                'ID Pesanan',
                'Tanggal Pesan',
                'Nama Pelanggan',
                'No WhatsApp',
                'Mode Racikan',
                'Preferensi Resep',
                'Berat Daging (gram)',
                'Berat Tepung (gram)',
                'Biaya Selepan (Rp)',
                'Total Bayar (Rp)',
                'Status',
                'Jadwal Pickup (WIB)',
            ]);

            $query = Order::with('orderItems.material')->orderBy('created_at', 'desc');
            if ($statusFilter !== 'all') {
                $aliases = Order::aliasesFor($statusFilter);
                $placeholders = implode(', ', array_fill(0, count($aliases), '?'));
                $query->whereRaw("LOWER(TRIM(status)) IN ({$placeholders})", $aliases);
            }

            foreach ($query->lazy(200) as $order) {
                $flourWeight = (int) round($order->orderItems
                    ->filter(fn ($item) => ($item->material?->category === 'tepung') || str_contains(strtolower((string) $item->material_name), 'tepung'))
                    ->sum(function ($item) {
                        $unit = strtolower((string) $item->unit);
                        if ($unit === 'gram' || $unit === 'g') {
                            return (float) $item->quantity;
                        }
                        $pkgWeight = $item->material ? $item->material->effectivePackageWeight(500.0) : 500.0;

                        return (float) $item->quantity * $pkgWeight;
                    }));

                $phoneCell = $order->customer_phone ? $this->sanitizeCsvCell($order->customer_phone) : '-';

                fputcsv($handle, [
                    '#BSK-'.str_pad((string) $order->id, 5, '0', STR_PAD_LEFT),
                    $order->created_at ? $order->created_at->format('Y-m-d H:i') : '-',
                    $this->sanitizeCsvCell($order->customer_name),
                    $phoneCell,
                    match ($order->start_type) {
                        'start_from_zero' => 'Bahan Lengkap Toko',
                        'ingredients_only' => 'Beli Bahan Saja',
                        default => 'Bawa Daging Sendiri',
                    },
                    $order->start_type === 'ingredients_only' ? '-' : $this->sanitizeCsvCell(ucfirst(str_replace('_', ' ', (string) $order->preference))),
                    $order->start_type === 'ingredients_only' ? '0' : (int) $order->meat_weight,
                    $flourWeight,
                    (int) $order->grinding_fee,
                    (int) $order->total_price,
                    match ($this->normalizeStatus($order->status)) {
                        'pending' => 'Menunggu Konfirmasi',
                        'processing' => 'Sedang Diproses',
                        'completed' => 'Selesai',
                        'cancelled' => match ($order->cancelled_by) {
                            'customer' => 'Dibatalkan (Pembeli)',
                            'admin' => 'Dibatalkan (Kios)',
                            default => 'Dibatalkan',
                        },
                        default => $this->sanitizeCsvCell($order->status),
                    },
                    $order->pickup_at ? Carbon::parse($order->pickup_at)->format('Y-m-d H:i') : '-',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Susun tautan WhatsApp ternormalisasi (format 62...) beserta template notifikasi status pesanan.
     */
    public function getCustomerWaUrl(Order $order): ?string
    {
        $digits = preg_replace('/[^0-9]/', '', (string) ($order->customer_phone ?? ''));
        if (empty($digits)) {
            return null;
        }

        if (str_starts_with($digits, '0')) {
            $digits = '62'.substr($digits, 1);
        } elseif (! str_starts_with($digits, '62')) {
            $digits = '62'.$digits;
        }

        $orderCode = '#BSK-'.str_pad((string) $order->id, 5, '0', STR_PAD_LEFT);
        $totalFormatted = 'Rp '.number_format((float) $order->total_price, 0, ',', '.');
        $pickupText = $order->pickup_at
            ? Carbon::parse($order->pickup_at)->format('d M Y, H:i').' WIB'
            : 'sesuai jadwal';

        $message = match ($this->normalizeStatus($order->status)) {
            'processing' => "Halo {$order->customer_name}, pesanan BASOKU Anda ({$orderCode}) sedang kami siapkan dan proses giling di los selepan. Total estimasi: {$totalFormatted}. Jadwal ambil: {$pickupText}.",
            'completed' => "Halo {$order->customer_name}, pesanan BASOKU Anda ({$orderCode}) sudah SELESAI dan siap diambil di Kios BASOKU Pasar Klakah! Total pembayaran: {$totalFormatted}. Ditunggu kedatangannya ya.",
            'cancelled' => $order->isCancelledByCustomer()
                ? "Halo {$order->customer_name}, kami telah menerima pembatalan pesanan BASOKU Anda ({$orderCode}) dari sisi pembeli. Jika ingin memesan ulang kapan saja, silakan hubungi kami kembali."
                : "Halo {$order->customer_name}, mohon maaf pesanan BASOKU Anda ({$orderCode}) dibatalkan oleh pihak kios. Jika ada kekeliruan atau ingin penjadwalan ulang, silakan balas pesan ini.",
            default => "Halo {$order->customer_name}, terima kasih telah memesan di BASOKU ({$orderCode}). Mohon konfirmasi pesanan Anda (Total: {$totalFormatted}, Jadwal ambil: {$pickupText}) agar segera kami proses.",
        };

        return 'https://wa.me/'.$digits.'?text='.urlencode($message);
    }

    /**
     * Logout admin.
     */
    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return $this->redirect('/masuk', navigate: true);
    }

    public function render()
    {
        $query = Order::query()->with('user:id')->orderBy('created_at', 'desc');

        if ($this->statusFilter !== 'all') {
            $aliases = Order::aliasesFor($this->statusFilter);
            $placeholders = implode(', ', array_fill(0, count($aliases), '?'));
            $query->whereRaw("LOWER(TRIM(status)) IN ({$placeholders})", $aliases);
        }

        $orders = (clone $query)->paginate(15);
        if ($orders->currentPage() > $orders->lastPage() && $orders->lastPage() >= 1) {
            $this->setPage($orders->lastPage());
            $orders = (clone $query)->paginate(15, ['*'], 'page', $orders->lastPage());
        }

        // Hitung total pesanan berdasarkan status untuk badge filter tab secara teragregasi (1 query, tahan kapital/spasi)
        $statusCounts = Order::query()
            ->selectRaw('LOWER(TRIM(status)) as status_key, count(*) as count')
            ->groupBy('status_key')
            ->pluck('count', 'status_key');

        $countAliases = function (string $canonical) use ($statusCounts): int {
            $total = 0;
            foreach (Order::aliasesFor($canonical) as $alias) {
                $total += (int) ($statusCounts[$alias] ?? 0);
            }

            return $total;
        };

        $counts = [
            'all' => (int) $statusCounts->sum(),
            'pending' => $countAliases('pending'),
            'processing' => $countAliases('processing'),
            'completed' => $countAliases('completed'),
            'cancelled' => $countAliases('cancelled'),
        ];

        return view('livewire.admin.manage-orders', [
            'orders' => $orders,
            'counts' => $counts,
        ]);
    }
}
