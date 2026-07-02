<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PromoCode extends Model
{
    public const TYPE_PERCENT = 'percent';

    public const TYPE_FIXED = 'fixed';

    public const TYPE_FREE_SHIPPING = 'free_shipping';

    protected $fillable = [
        'code',
        'type',
        'value',
        'min_order_amount',
        'max_uses',
        'used_count',
        'starts_at',
        'expires_at',
        'is_active',
        'description',
    ];

    protected $casts = [
        'value' => 'integer',
        'min_order_amount' => 'integer',
        'max_uses' => 'integer',
        'used_count' => 'integer',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /** @return array<string, string> */
    public static function types(): array
    {
        return [
            self::TYPE_PERCENT => 'Percentage off subtotal',
            self::TYPE_FIXED => 'Fixed amount off subtotal',
            self::TYPE_FREE_SHIPPING => 'Free shipping',
        ];
    }

    public function typeLabel(): string
    {
        return self::types()[$this->type] ?? ucfirst(str_replace('_', ' ', $this->type));
    }

    public function valueLabel(): string
    {
        return match ($this->type) {
            self::TYPE_PERCENT => $this->value.'%',
            self::TYPE_FIXED => '₹'.number_format($this->value),
            self::TYPE_FREE_SHIPPING => '—',
            default => '—',
        };
    }
}
