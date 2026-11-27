<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    /**
     * Peta alias status kanonik Inggris & legacy Indonesia sebagai satu sumber kebenaran.
     *
     * @var array<string, list<string>>
     */
    public const STATUS_ALIASES = [
        'pending' => ['pending', 'menunggu'],
        'processing' => ['processing', 'diproses'],
        'completed' => ['completed', 'selesai'],
        'cancelled' => ['cancelled', 'batal', 'dibatalkan'],
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'orders';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'customer_name',
        'customer_phone',
        'pickup_at',
        'start_type',
        'meat_weight',
        'preference',
        'grinding_fee',
        'total_price',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'pickup_at' => 'datetime',
            'meat_weight' => 'decimal:3',
            'grinding_fee' => 'decimal:2',
            'total_price' => 'decimal:2',
        ];
    }

    /**
     * Get the user that placed the order (optional).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items in the order.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Cek apakah pesanan memakai mode bawa daging sendiri (mendukung 'own_meat' dan legacy 'bring_own_meat').
     */
    public function isOwnMeat(): bool
    {
        return in_array((string) $this->start_type, ['own_meat', 'bring_own_meat'], true);
    }

    /**
     * Normalisasi status pesanan ke nilai kanonik Inggris.
     */
    public static function normalizeStatus(?string $status): string
    {
        $clean = strtolower(trim((string) $status));

        foreach (self::STATUS_ALIASES as $canonical => $aliases) {
            if (in_array($clean, $aliases, true)) {
                return $canonical;
            }
        }

        return $clean !== '' ? $clean : 'pending';
    }

    /**
     * Ambil seluruh alias database untuk satu status kanonik.
     *
     * @return list<string>
     */
    public static function aliasesFor(string $canonical): array
    {
        $clean = strtolower(trim($canonical));

        return self::STATUS_ALIASES[$clean] ?? [$clean];
    }
}
