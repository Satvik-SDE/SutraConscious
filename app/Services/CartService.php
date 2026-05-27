<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

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

    public function add(ProductVariant $variant, int $quantity = 1): CartItem
    {
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
