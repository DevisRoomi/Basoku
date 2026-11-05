<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Manajemen Pesanan — BASOKU Admin')]
class ManageOrders extends Component
{
    public bool $showDetailModal = false;

    public ?Order $selectedOrder = null;

    // Filter status tab: 'all', 'pending', 'processing', 'completed', 'cancelled'
    public string $statusFilter = 'all';

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
        $fileName = 'rekap-pesanan-basoku-' . now()->format('Y-m-d-His') . '.csv';

        $query = Order::with('orderItems')->orderBy('created_at', 'desc');
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
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

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
                fputcsv($handle, [
                    '#BSK-' . str_pad($order->id, 5, '0', STR_PAD_LEFT),
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
                    (int) $order->flour_weight,
                    (int) $order->grinding_fee,
                    (int) $order->total_price,
                    match ($order->status) {
                        'pending' => 'Menunggu Konfirmasi',
                        'processing' => 'Sedang Diproses',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                        default => $order->status,
                    },
                    $order->pickup_at ? \Carbon\Carbon::parse($order->pickup_at)->format('Y-m-d H:i') : '-',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
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

        $orders = $query->get();

        // Hitung total pesanan berdasarkan status untuk badge filter tab
        $counts = [
            'all' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'completed' => Order::where('status', 'completed')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
        ];

        return view('livewire.admin.manage-orders', [
            'orders' => $orders,
            'counts' => $counts,
        ]);
    }
}
