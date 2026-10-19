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
