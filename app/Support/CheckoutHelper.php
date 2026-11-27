<?php

namespace App\Support;

use Carbon\Carbon;

class CheckoutHelper
{
    /**
     * Normalisasi nomor WhatsApp dari tanda hubung, spasi, titik, atau kurung sebelum validasi & penyimpanan.
     */
    public static function normalizePhone(?string $phone): string
    {
        $trimmed = trim((string) $phone);

        if ($trimmed === '') {
            return '';
        }

        return (string) preg_replace('/[\s\-\.\(\)]+/', '', $trimmed);
    }

    /**
     * Normalisasi nomor WhatsApp ke format digit kanonik 62xxx untuk perbandingan pelacakan pesanan.
     */
    public static function canonicalPhoneDigits(?string $phone): string
    {
        $digits = preg_replace('/[^0-9]/', '', (string) $phone) ?? '';
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

    /**
     * Format kode pesanan seragam (#BSK-00001).
     */
    public static function formatOrderCode(int $orderId): string
    {
        return '#BSK-'.str_pad((string) $orderId, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Hasilkan URL pelacakan pesanan dengan pre-fill kode pesanan.
     */
    public static function trackOrderUrl(int $orderId): string
    {
        return route('orders.track', [
            'code' => self::formatOrderCode($orderId),
        ]);
    }

    /**
     * Aturan validasi checkout terpusat untuk Kalkulator Racikan dan Katalog Bahan.
     *
     * @return array<string, array<int, mixed>>
     */
    public static function validationRules(): array
    {
        return [
            'customerName' => ['required', 'string', 'min:2', 'max:100'],
            'customerPhone' => ['required', 'string', 'regex:/^(\+62|62|08)[0-9]{8,13}$/'],
            'pickupTime' => [
                'bail',
                'required',
                'date',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    try {
                        $pickup = Carbon::parse((string) $value);
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

                    // Jam operasional kios: 04.00 s/d 12.00 WIB
                    if ($hour < $openHour || ($hour === $closeHour && $minute > 0) || $hour > $closeHour) {
                        $fail(sprintf('Jam pengambilan harus pada jam operasional kios (%02d.00 – %02d.00 WIB).', $openHour, $closeHour));
                    }
                },
            ],
        ];
    }

    /**
     * Pesan kesalahan validasi checkout terpusat.
     *
     * @return array<string, string>
     */
    public static function validationMessages(): array
    {
        return [
            'customerName.required' => 'Nama lengkap wajib diisi.',
            'customerName.min' => 'Nama minimal 2 karakter.',
            'customerPhone.required' => 'Nomor WhatsApp pembeli wajib diisi.',
            'customerPhone.regex' => 'Format nomor WhatsApp tidak valid (contoh: 0812-3456-7890 atau 628123456789).',
            'pickupTime.required' => 'Jam pengambilan wajib diisi.',
            'pickupTime.date' => 'Format jam pengambilan tidak valid.',
        ];
    }

    /**
     * Daftar tombol pintas jadwal pengambilan yang selalu valid terhadap jam operasional kios.
     *
     * @return array<int, array{label: string, value: string}>
     */
    public static function pickupShortcuts(): array
    {
        $openHour = (int) config('services.kiosk.open_hour', 4);
        $closeHour = (int) config('services.kiosk.close_hour', 12);
        $shortcuts = [];

        $todayEarliest = now()->addHour()->addMinutes(5);
        $todayOpen = now()->copy()->setTime($openHour, 0, 0);
        $todayClose = now()->copy()->setTime($closeHour, 0, 0);

        if ($todayEarliest->lt($todayOpen)) {
            $todayEarliest = $todayOpen;
        }

        if ($todayEarliest->lte($todayClose)) {
            $shortcuts[] = [
                'label' => 'Hari Ini '.$todayEarliest->format('H:i'),
                'value' => $todayEarliest->format('Y-m-d\TH:i'),
            ];
        }

        $morningHour = max($openHour, min($closeHour - 1, 7));
        $midMorningHour = max($openHour, min($closeHour - 1, 9));
        $defaultHour = max($openHour, min($closeHour - 1, 8));

        $tomorrowMorning = now()->addDay()->setTime($morningHour, 0);
        $shortcuts[] = [
            'label' => 'Besok '.$tomorrowMorning->format('H:i'),
            'value' => $tomorrowMorning->format('Y-m-d\TH:i'),
        ];

        if ($midMorningHour !== $morningHour) {
            $tomorrowMid = now()->addDay()->setTime($midMorningHour, 0);
            $shortcuts[] = [
                'label' => 'Besok '.$tomorrowMid->format('H:i'),
                'value' => $tomorrowMid->format('Y-m-d\TH:i'),
            ];
        }

        $dayAfter = now()->addDays(2)->setTime($defaultHour, 0);
        $shortcuts[] = [
            'label' => 'Lusa '.$dayAfter->format('H:i'),
            'value' => $dayAfter->format('Y-m-d\TH:i'),
        ];

        return $shortcuts;
    }
}
