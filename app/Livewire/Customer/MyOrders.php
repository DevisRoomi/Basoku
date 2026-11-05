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

#[Layout('layouts.app')]
#[Title('Pesanan Saya | BASOKU')]
class MyOrders extends Component
{
    use WithPagination;

    #[Url]
    public string $statusFilter = 'all';

    public string $searchQuery = '';

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

        $query = Order::query()->with('orderItems');

        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            // Guest lookup mode: must search by phone or order ID
            if (! empty($this->searchQuery)) {
                $trimmed = trim($this->searchQuery);
                $cleanPhone = preg_replace('/[^0-9]/', '', $trimmed);

                $query->where(function ($q) use ($trimmed, $cleanPhone) {
                    $q->where('id', (int) str_replace('#BSK-', '', $trimmed))
                      ->orWhere('customer_phone', 'like', "%{$cleanPhone}%")
                      ->orWhere('customer_name', 'like', "%{$trimmed}%");
                });
            } else {
                $query->whereRaw('1 = 0'); // Empty until search
            }
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        $orders = $query->latest()->paginate(8);

        $counts = [
            'all' => $userId ? Order::where('user_id', $userId)->count() : 0,
            'pending' => $userId ? Order::where('user_id', $userId)->whereIn('status', ['pending', 'menunggu'])->count() : 0,
            'processing' => $userId ? Order::where('user_id', $userId)->whereIn('status', ['processing', 'diproses'])->count() : 0,
            'completed' => $userId ? Order::where('user_id', $userId)->whereIn('status', ['completed', 'selesai'])->count() : 0,
        ];

        return view('livewire.customer.my-orders', [
            'orders' => $orders,
            'counts' => $counts,
        ]);
    }
}
