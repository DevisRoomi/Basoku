<?php

namespace App\Livewire;

use App\Models\Order;
use App\Support\CheckoutHelper;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Lacak Status Pesanan — BASOKU')]
class TrackOrder extends Component
{
    public string $orderCode = '';

    public string $phoneNumber = '';

    #[Locked]
    public ?int $trackedOrderId = null;

    #[Locked]
    public ?string $verifiedPhone = null;

    public ?string $lookupError = null;

    public ?string $actionNotice = null;

    public function mount(): void
    {
        $code = request()->query('code');
        if (is_string($code) && trim($code) !== '') {
            $this->orderCode = trim($code);
        }
    }

    /**
     * Cari pesanan tamu secara aman dengan mewajibkan kecocokan Nomor Pesanan DAN Nomor WhatsApp.
     */
    public function searchOrder(): void
    {
        $this->lookupError = null;
        $this->actionNotice = null;

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
            $this->verifiedPhone = null;
            $this->lookupError = 'Pesanan tidak ditemukan. Pastikan nomor pesanan (#BSK-...) dan nomor WhatsApp sesuai.';

            return;
        }

        $order = Order::with('orderItems')->find($orderId);

        if (! $order || $this->normalizePhone((string) $order->customer_phone) !== $normalizedInputPhone) {
            $this->trackedOrderId = null;
            $this->verifiedPhone = null;
            $this->lookupError = 'Pesanan tidak ditemukan. Pastikan nomor pesanan dan nomor WhatsApp cocok dengan data pemesanan.';

            return;
        }

        $this->trackedOrderId = $order->id;
        $this->verifiedPhone = $normalizedInputPhone;
    }

    public function resetSearch(): void
    {
        $this->trackedOrderId = null;
        $this->verifiedPhone = null;
        $this->lookupError = null;
        $this->actionNotice = null;
        $this->orderCode = '';
        $this->phoneNumber = '';
        $this->resetErrorBag();
    }

    /**
     * Izinkan tamu membatalkan pesanan terverifikasi secara mandiri khusus saat status masih 'pending'.
     */
    public function cancelTrackedOrder(): void
    {
        $this->lookupError = null;
        $this->actionNotice = null;

        if ($this->trackedOrderId === null || $this->verifiedPhone === null) {
            return;
        }

        $order = Order::find($this->trackedOrderId);
        if (! $order || $this->normalizePhone((string) $order->customer_phone) !== $this->verifiedPhone) {
            $this->trackedOrderId = null;
            $this->verifiedPhone = null;

            return;
        }

        if (Order::normalizeStatus((string) $order->status) !== 'pending') {
            $this->lookupError = 'Pesanan yang sudah diproses, selesai, atau dibatalkan tidak dapat dibatalkan secara mandiri.';

            return;
        }

        $order->update(['status' => 'cancelled']);
        $code = CheckoutHelper::formatOrderCode($order->id);
        $this->actionNotice = "Pesanan {$code} berhasil dibatalkan.";
    }

    protected function normalizePhone(string $phone): string
    {
        return CheckoutHelper::canonicalPhoneDigits($phone);
    }

    public function render(): View
    {
        $order = null;
        if ($this->trackedOrderId !== null && $this->verifiedPhone !== null) {
            $candidate = Order::with('orderItems')->find($this->trackedOrderId);
            if ($candidate && $this->normalizePhone((string) $candidate->customer_phone) === $this->verifiedPhone) {
                $order = $candidate;
            } else {
                $this->trackedOrderId = null;
                $this->verifiedPhone = null;
            }
        }

        return view('livewire.track-order', [
            'order' => $order,
        ]);
    }
}
