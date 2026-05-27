<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'size',
        'color',
        'sku',
        'price_override',
        'stock',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price_override' => 'integer',
        'stock' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function price(): int
    {
        return $this->price_override ?? $this->product->base_price;
    }

    public function label(): string
    {
        $parts = [$this->size];
        if ($this->color) {
            $parts[] = $this->color;
        }
        return implode(' / ', $parts);
    }
}
