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
     * Perbarui status pesanan.
     */
    public function updateStatus(int $id, string $newStatus)
    {
        $allowedStatuses = ['pending', 'processing', 'completed', 'cancelled'];

        if (! in_array($newStatus, $allowedStatuses)) {
            return;
        }

        $order = Order::findOrFail($id);
        $order->status = $newStatus;
        $order->save();

        // Refresh selectedOrder jika sedang terbuka di modal
        if ($this->selectedOrder && $this->selectedOrder->id === $id) {
            $this->selectedOrder->refresh();
        }

        $statusLabels = [
            'pending' => 'Menunggu Konfirmasi',
            'processing' => 'Sedang Diproses',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];

        $label = $statusLabels[$newStatus] ?? $newStatus;
        session()->flash('success', "Status pesanan #{$order->id} ({$order->customer_name}) berhasil diubah menjadi: {$label}.");
    }

    /**
     * Ekspor data pesanan ke file CSV native stream untuk pembukuan kas.
     */
    public function exportCsv()
    {
        $fileName = 'rekap-pesanan-basoku-'.now()->format('Y-m-d-His').'.csv';

        $query = Order::with('orderItems.material')->orderBy('created_at', 'desc');
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }
        $orders = $query->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($orders) {
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

            foreach ($orders as $order) {
                $flourWeight = (int) round($order->orderItems
                    ->filter(fn ($item) => ($item->material?->category === 'tepung') || str_contains(strtolower((string) $item->material_name), 'tepung'))
                    ->sum('quantity'));

                fputcsv($handle, [
                    '#BSK-'.str_pad((string) $order->id, 5, '0', STR_PAD_LEFT),
                    $order->created_at ? $order->created_at->format('Y-m-d H:i') : '-',
                    $order->customer_name,
                    $order->customer_phone ?? '-',
                    match ($order->start_type) {
                        'start_from_zero' => 'Bahan Lengkap Toko',
                        'ingredients_only' => 'Beli Bahan Saja',
                        default => 'Bawa Daging Sendiri',
                    },
                    $order->start_type === 'ingredients_only' ? '-' : ucfirst(str_replace('_', ' ', $order->preference)),
                    $order->start_type === 'ingredients_only' ? '0' : (int) $order->meat_weight,
                    $flourWeight,
                    (int) $order->grinding_fee,
                    (int) $order->total_price,
                    match ($order->status) {
                        'pending' => 'Menunggu Konfirmasi',
                        'processing' => 'Sedang Diproses',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                        default => $order->status,
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

        $message = match ($order->status) {
            'processing' => "Halo {$order->customer_name}, pesanan BASOKU Anda ({$orderCode}) sedang kami siapkan dan proses giling di los selepan. Total estimasi: {$totalFormatted}. Jadwal ambil: {$pickupText}.",
            'completed' => "Halo {$order->customer_name}, pesanan BASOKU Anda ({$orderCode}) sudah SELESAI dan siap diambil di Kios BASOKU Pasar Klakah! Total pembayaran: {$totalFormatted}. Ditunggu kedatangannya ya.",
            'cancelled' => "Halo {$order->customer_name}, menginformasikan bahwa pesanan BASOKU Anda ({$orderCode}) tercatat dibatalkan. Jika ada kekeliruan atau ingin memesan ulang, silakan balas pesan ini.",
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
        $query = Order::with('orderItems')->orderBy('created_at', 'desc');

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        $orders = $query->paginate(15);

        // Hitung total pesanan berdasarkan status untuk badge filter tab secara teragregasi (1 query)
        $statusCounts = Order::query()
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $counts = [
            'all' => (int) $statusCounts->sum(),
            'pending' => (int) ($statusCounts['pending'] ?? 0),
            'processing' => (int) ($statusCounts['processing'] ?? 0),
            'completed' => (int) ($statusCounts['completed'] ?? 0),
            'cancelled' => (int) ($statusCounts['cancelled'] ?? 0),
        ];

        return view('livewire.admin.manage-orders', [
            'orders' => $orders,
            'counts' => $counts,
        ]);
    }
}
