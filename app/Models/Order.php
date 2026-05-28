<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_CANCELLED = 'cancelled';

    public const PAYMENT_UNPAID = 'unpaid';
    public const PAYMENT_PAID = 'paid';
    public const PAYMENT_FAILED = 'failed';
    public const PAYMENT_REFUNDED = 'refunded';

    protected $fillable = [
        'number',
        'user_id',
        'status',
        'payment_status',
        'payment_provider',
        'razorpay_order_id',
        'razorpay_payment_id',
        'razorpay_signature',
        'currency',
        'subtotal',
        'shipping_total',
        'discount_total',
        'total',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_line1',
        'shipping_line2',
        'shipping_city',
        'shipping_state',
        'shipping_country',
        'shipping_postal_code',
        'notes',
        'tracking_carrier',
        'tracking_number',
        'tracking_url',
        'shipped_at',
        'paid_at',
    ];

    protected $casts = [
        'shipped_at' => 'datetime',
        'paid_at' => 'datetime',
        'subtotal' => 'integer',
        'shipping_total' => 'integer',
        'discount_total' => 'integer',
        'total' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getRouteKeyName(): string
    {
        return 'number';
    }

    public static function generateNumber(): string
    {
        return 'SC' . now()->format('ymd') . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Pending',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_SHIPPED => 'Shipped',
            self::STATUS_DELIVERED => 'Delivered',
            self::STATUS_CANCELLED => 'Cancelled',
            default => ucfirst($this->status),
        };
    }

    public function paymentStatusLabel(): string
    {
        return match ($this->payment_status) {
            self::PAYMENT_UNPAID => 'Awaiting payment',
            self::PAYMENT_PAID => 'Paid',
            self::PAYMENT_FAILED => 'Payment failed',
            self::PAYMENT_REFUNDED => 'Refunded',
            default => ucfirst($this->payment_status),
        };
    }

    public function hasTracking(): bool
    {
        return filled($this->tracking_number) || filled($this->tracking_url);
    }

    public function trackingLink(): ?string
    {
        if (filled($this->tracking_url)) {
            return $this->tracking_url;
        }

        if (filled($this->tracking_number)) {
            $query = trim(($this->tracking_carrier ?? '') . ' ' . $this->tracking_number . ' tracking');

            return 'https://www.google.com/search?q=' . urlencode($query);
        }

        return null;
    }

    /** @return list<array{key: string, label: string, done: bool, current: bool, at: ?\Illuminate\Support\Carbon}> */
    public function fulfillmentTimeline(): array
    {
        if ($this->status === self::STATUS_CANCELLED) {
            return [
                [
                    'key' => 'cancelled',
                    'label' => 'Order cancelled',
                    'done' => true,
                    'current' => true,
                    'at' => $this->updated_at,
                ],
            ];
        }

        $paid = $this->payment_status === self::PAYMENT_PAID;
        $processing = in_array($this->status, [self::STATUS_PROCESSING, self::STATUS_SHIPPED, self::STATUS_DELIVERED], true);
        $shipped = in_array($this->status, [self::STATUS_SHIPPED, self::STATUS_DELIVERED], true);
        $delivered = $this->status === self::STATUS_DELIVERED;

        return [
            [
                'key' => 'placed',
                'label' => 'Order placed',
                'done' => true,
                'current' => ! $paid && $this->status === self::STATUS_PENDING,
                'at' => $this->created_at,
            ],
            [
                'key' => 'paid',
                'label' => 'Payment confirmed',
                'done' => $paid,
                'current' => $paid && ! $processing,
                'at' => $this->paid_at,
            ],
            [
                'key' => 'processing',
                'label' => 'Preparing your order',
                'done' => $processing,
                'current' => $processing && ! $shipped,
                'at' => $paid ? ($this->paid_at ?? $this->updated_at) : null,
            ],
            [
                'key' => 'shipped',
                'label' => 'Shipped',
                'done' => $shipped,
                'current' => $shipped && ! $delivered,
                'at' => $this->shipped_at,
            ],
            [
                'key' => 'delivered',
                'label' => 'Delivered',
                'done' => $delivered,
                'current' => $delivered,
                'at' => $delivered ? $this->updated_at : null,
            ],
        ];
    }
}
