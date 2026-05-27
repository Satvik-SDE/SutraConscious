<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(protected CartService $cart) {}

    public function show()
    {
        $cart = $this->cart->current()->load([
            'items.variant.product.images',
        ]);

        return view('shop.cart', [
            'cart' => $cart,
        ]);
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'variant_id' => ['required', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:10'],
        ]);

        $variant = ProductVariant::with('product')->findOrFail($data['variant_id']);

        if (! $variant->is_active || ! $variant->product->is_active) {
            return back()->withErrors(['cart' => 'Sorry, this item is not available.']);
        }

        $this->cart->add($variant, (int) $data['quantity']);

        return redirect()->route('cart.show')->with('status', 'Added to cart.');
    }

    public function update(Request $request, CartItem $item)
    {
        $this->authorizeItem($item);

        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:10'],
        ]);

        $this->cart->update($item, (int) $data['quantity']);

        return redirect()->route('cart.show');
    }

    public function remove(CartItem $item)
    {
        $this->authorizeItem($item);
        $this->cart->remove($item);

        return redirect()->route('cart.show');
    }

    protected function authorizeItem(CartItem $item): void
    {
        $cart = $this->cart->current();
        abort_unless($item->cart_id === $cart->id, 403);
    }
}
