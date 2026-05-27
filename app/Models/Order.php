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
        'paid_at',
    ];

    protected $casts = [
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
}
