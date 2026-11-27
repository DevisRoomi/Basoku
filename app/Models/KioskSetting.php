<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class KioskSetting extends Model
{
    public const CACHE_KEY = 'basoku.kiosk_settings';

    /**
     * Daftar kunci pengaturan kios yang diizinkan disimpan di database.
     *
     * @var list<string>
     */
    public const ALLOWED_KEYS = [
        'wa_number',
        'grinding_fee_per_kg',
        'open_hour',
        'close_hour',
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Ambil seluruh nilai aktif pengaturan kios (override DB dipadukan dengan fallback config/env).
     *
     * @return array{wa_number: string, grinding_fee_per_kg: float, open_hour: int, close_hour: int}
     */
    public static function currentValues(): array
    {
        $overrides = self::loadOverrides();

        return [
            'wa_number' => (string) ($overrides['wa_number'] ?? config('services.kiosk.wa_number', '6285122797969')),
            'grinding_fee_per_kg' => (float) ($overrides['grinding_fee_per_kg'] ?? config('services.kiosk.grinding_fee_per_kg', 15000.0)),
            'open_hour' => (int) ($overrides['open_hour'] ?? config('services.kiosk.open_hour', 4)),
            'close_hour' => (int) ($overrides['close_hour'] ?? config('services.kiosk.close_hour', 12)),
        ];
    }

    /**
     * Terapkan pengaturan kios dari database ke runtime config('services.kiosk.*').
     */
    public static function applyToConfig(): void
    {
        $overrides = self::loadOverrides();
        if (empty($overrides)) {
            return;
        }

        if (isset($overrides['wa_number']) && trim((string) $overrides['wa_number']) !== '') {
            config(['services.kiosk.wa_number' => (string) $overrides['wa_number']]);
        }

        if (isset($overrides['grinding_fee_per_kg']) && is_numeric($overrides['grinding_fee_per_kg'])) {
            config(['services.kiosk.grinding_fee_per_kg' => (float) $overrides['grinding_fee_per_kg']]);
        }

        if (isset($overrides['open_hour']) && is_numeric($overrides['open_hour'])) {
            config(['services.kiosk.open_hour' => (int) $overrides['open_hour']]);
        }

        if (isset($overrides['close_hour']) && is_numeric($overrides['close_hour'])) {
            config(['services.kiosk.close_hour' => (int) $overrides['close_hour']]);
        }
    }

    /**
     * Simpan pengaturan kios ke database, bersihkan cache, dan langsung terapkan ke runtime config.
     *
     * @param  array<string, scalar>  $values
     */
    public static function saveValues(array $values): void
    {
        foreach ($values as $key => $value) {
            if (! in_array($key, self::ALLOWED_KEYS, true)) {
                continue;
            }

            self::updateOrCreate(
                ['key' => $key],
                ['value' => (string) $value]
            );
        }

        self::clearCache();
        self::applyToConfig();
    }

    /**
     * Bersihkan cache pengaturan kios.
     */
    public static function clearCache(): void
    {
        try {
            Cache::forget(self::CACHE_KEY);
        } catch (\Throwable) {
            // Abaikan bila store cache belum siap
        }
    }

    /**
     * Baca override dari cache/DB secara aman (tidak pernah gagal sebelum migrasi dijalankan).
     *
     * @return array<string, string|null>
     */
    protected static function loadOverrides(): array
    {
        try {
            $cached = Cache::get(self::CACHE_KEY);
            if (is_array($cached)) {
                return $cached;
            }

            $rows = self::query()
                ->whereIn('key', self::ALLOWED_KEYS)
                ->pluck('value', 'key')
                ->all();

            Cache::put(self::CACHE_KEY, $rows, now()->addHour());

            return $rows;
        } catch (\Throwable) {
            return [];
        }
    }
}
