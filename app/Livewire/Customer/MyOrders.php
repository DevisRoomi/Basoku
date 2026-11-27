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

    /**
     * Batalkan pesanan milik pelanggan sendiri selama status masih 'pending' (menunggu konfirmasi kios).
     */
    public function cancelOrder(int $orderId): void
    {
        $order = Order::query()
            ->where('user_id', Auth::id())
            ->find($orderId);

        if (! $order) {
            return;
        }

        if (Order::normalizeStatus((string) $order->status) !== 'pending') {
            session()->flash('order_error', 'Pesanan yang sudah diproses, selesai, atau dibatalkan tidak dapat dibatalkan secara mandiri.');

            return;
        }

        $order->update(['status' => 'cancelled']);
        $code = '#BSK-'.str_pad((string) $order->id, 5, '0', STR_PAD_LEFT);
        session()->flash('order_notice', "Pesanan {$code} berhasil dibatalkan.");
    }

    public function render(): View
    {
        $userId = Auth::id();

        $query = Order::query()
            ->with('orderItems')
            ->where('user_id', $userId);

        if ($this->statusFilter !== 'all') {
            $aliases = Order::aliasesFor($this->statusFilter);
            $placeholders = implode(', ', array_fill(0, count($aliases), '?'));
            $query->whereRaw("LOWER(TRIM(status)) IN ({$placeholders})", $aliases);
        }

        $orders = $query->latest()->paginate(8);

        $statusCounts = Order::query()
            ->where('user_id', $userId)
            ->selectRaw('LOWER(TRIM(status)) as status_key, count(*) as total')
            ->groupBy('status_key')
            ->pluck('total', 'status_key');

        $sumAliases = fn (string $canonical): int => (int) collect(Order::aliasesFor($canonical))
            ->sum(fn (string $alias): int => (int) ($statusCounts[$alias] ?? 0));

        $counts = [
            'all' => (int) $statusCounts->sum(),
            'pending' => $sumAliases('pending'),
            'processing' => $sumAliases('processing'),
            'completed' => $sumAliases('completed'),
            'cancelled' => $sumAliases('cancelled'),
        ];

        return view('livewire.customer.my-orders', [
            'orders' => $orders,
            'counts' => $counts,
        ]);
    }
}
