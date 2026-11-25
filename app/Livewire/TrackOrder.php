<?php

namespace App\Livewire;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Lacak Status Pesanan — BASOKU')]
class TrackOrder extends Component
{
    public string $orderCode = '';

    public string $phoneNumber = '';

    public ?int $trackedOrderId = null;

    public ?string $lookupError = null;

    public function mount(): void
    {
        if ($code = request()->query('code')) {
            $this->orderCode = trim((string) $code);
        }
    }

    /**
     * Cari pesanan tamu secara aman dengan mewajibkan kecocokan Nomor Pesanan DAN Nomor WhatsApp.
     */
    public function searchOrder(): void
    {
        $this->lookupError = null;

        $throttleKey = 'track-order|'.request()->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 15)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->lookupError = "Terlalu banyak percobaan pencarian. Harap tunggu {$seconds} detik.";

            return;
        }

        $this->validate([
            'orderCode' => ['required', 'string', 'max:30'],
            'phoneNumber' => ['required', 'string', 'min:8', 'max:20'],
        ], [
            'orderCode.required' => 'Masukkan nomor pesanan (contoh: #BSK-00001).',
            'phoneNumber.required' => 'Masukkan nomor WhatsApp yang digunakan saat memesan.',
            'phoneNumber.min' => 'Nomor WhatsApp minimal 8 digit.',
        ]);

        RateLimiter::hit($throttleKey, 60);

        $digitsOnlyId = preg_replace('/[^0-9]/', '', $this->orderCode);
        $orderId = (int) ltrim((string) $digitsOnlyId, '0');
        $normalizedInputPhone = $this->normalizePhone($this->phoneNumber);

        if ($orderId <= 0 || $normalizedInputPhone === '') {
            $this->trackedOrderId = null;
            $this->lookupError = 'Pesanan tidak ditemukan. Pastikan nomor pesanan (#BSK-...) dan nomor WhatsApp sesuai.';

            return;
        }

        $order = Order::with('orderItems')->find($orderId);

        if (! $order || $this->normalizePhone((string) $order->customer_phone) !== $normalizedInputPhone) {
            $this->trackedOrderId = null;
            $this->lookupError = 'Pesanan tidak ditemukan. Pastikan nomor pesanan dan nomor WhatsApp cocok dengan data pemesanan.';

            return;
        }

        $this->trackedOrderId = $order->id;
    }

    public function resetSearch(): void
    {
        $this->trackedOrderId = null;
        $this->lookupError = null;
        $this->orderCode = '';
        $this->phoneNumber = '';
        $this->resetErrorBag();
    }

    protected function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/[^0-9]/', '', $phone) ?? '';
        if ($digits === '') {
            return '';
        }

        if (str_starts_with($digits, '0')) {
            return '62'.substr($digits, 1);
        }

        if (! str_starts_with($digits, '62')) {
            return '62'.$digits;
        }

        return $digits;
    }

    public function render(): View
    {
        $order = $this->trackedOrderId
            ? Order::with('orderItems')->find($this->trackedOrderId)
            : null;

        return view('livewire.track-order', [
            'order' => $order,
        ]);
    }
}
