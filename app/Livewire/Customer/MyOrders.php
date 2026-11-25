<?php

namespace App\Livewire\Customer;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Pesanan & Antrean Saya — BASOKU')]
class MyOrders extends Component
{
    use WithPagination;

    #[Url]
    public string $statusFilter = 'all';

    public ?int $selectedOrderId = null;

    public function filterByStatus(string $status): void
    {
        $this->statusFilter = $status;
        $this->resetPage();
    }

    public function selectOrder(int $orderId): void
    {
        $this->selectedOrderId = ($this->selectedOrderId === $orderId) ? null : $orderId;
    }

    public function render(): View
    {
        $userId = Auth::id();

        $query = Order::query()
            ->with('orderItems')
            ->where('user_id', $userId);

        if ($this->statusFilter !== 'all') {
            $statusMap = [
                'pending' => ['pending', 'menunggu'],
                'processing' => ['processing', 'diproses'],
                'completed' => ['completed', 'selesai'],
                'cancelled' => ['cancelled', 'batal'],
            ];
            $query->whereIn('status', $statusMap[$this->statusFilter] ?? [$this->statusFilter]);
        }

        $orders = $query->latest()->paginate(8);

        $statusCounts = Order::query()
            ->where('user_id', $userId)
            ->selectRaw('LOWER(status) as status_key, count(*) as total')
            ->groupBy('status_key')
            ->pluck('total', 'status_key');

        $counts = [
            'all' => (int) $statusCounts->sum(),
            'pending' => (int) (($statusCounts['pending'] ?? 0) + ($statusCounts['menunggu'] ?? 0)),
            'processing' => (int) (($statusCounts['processing'] ?? 0) + ($statusCounts['diproses'] ?? 0)),
            'completed' => (int) (($statusCounts['completed'] ?? 0) + ($statusCounts['selesai'] ?? 0)),
        ];

        return view('livewire.customer.my-orders', [
            'orders' => $orders,
            'counts' => $counts,
        ]);
    }
}
