<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'currency',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function subtotal(): int
    {
        return $this->items->sum(fn (CartItem $i) => $i->unit_price * $i->quantity);
    }

    public function itemCount(): int
    {
        return $this->items->sum('quantity');
    }

    public function isEmpty(): bool
    {
        return $this->items->isEmpty();
    }
}
