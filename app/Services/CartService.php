<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class CartService
{
    public function current(): Cart
    {
        if (Auth::check()) {
            return Cart::firstOrCreate(
                ['user_id' => Auth::id()],
                ['currency' => 'INR']
            );
        }

        $sessionId = Session::getId();

        return Cart::firstOrCreate(
            ['session_id' => $sessionId, 'user_id' => null],
            ['currency' => 'INR']
        );
    }

    public function quantityInCart(ProductVariant $variant, ?int $ignoreItemId = null): int
    {
        $cart = $this->current();

        return (int) CartItem::query()
            ->where('cart_id', $cart->id)
            ->where('product_variant_id', $variant->id)
            ->when($ignoreItemId, fn ($query) => $query->where('id', '!=', $ignoreItemId))
            ->sum('quantity');
    }

    public function availableQuantity(ProductVariant $variant, ?int $ignoreItemId = null): int
    {
        return max(0, $variant->stock - $this->quantityInCart($variant, $ignoreItemId));
    }

    public function add(ProductVariant $variant, int $quantity = 1): CartItem
    {
        $variant->refresh();

        $available = $this->availableQuantity($variant);

        if ($available <= 0) {
            throw ValidationException::withMessages([
                'cart' => 'This size is out of stock.',
            ]);
        }

        if ($quantity > $available) {
            throw ValidationException::withMessages([
                'cart' => $available === 1
                    ? 'Only 1 piece left in stock.'
                    : "Only {$available} pieces left in stock.",
            ]);
        }

        $cart = $this->current();

        $item = CartItem::firstOrNew([
            'cart_id' => $cart->id,
            'product_variant_id' => $variant->id,
        ]);

        $item->quantity = ($item->exists ? $item->quantity : 0) + $quantity;
        $item->unit_price = $variant->price();
        $item->save();

        return $item;
    }

    public function update(CartItem $item, int $quantity): void
    {
        if ($quantity <= 0) {
            $item->delete();

            return;
        }

        $variant = $item->variant()->firstOrFail();
        $available = $this->availableQuantity($variant, $item->id);

        if ($quantity > $available) {
            throw ValidationException::withMessages([
                'cart' => $available === 0
                    ? 'This size is out of stock.'
                    : ($available === 1
                        ? 'Only 1 piece left in stock.'
                        : "Only {$available} pieces left in stock."),
            ]);
        }

        $item->quantity = $quantity;
        $item->save();
    }

    public function remove(CartItem $item): void
    {
        $item->delete();
    }

    public function clear(): void
    {
        $cart = $this->current();
        $cart->items()->delete();
    }

    public function itemCount(): int
    {
        return $this->current()->loadMissing('items')->itemCount();
    }
}
