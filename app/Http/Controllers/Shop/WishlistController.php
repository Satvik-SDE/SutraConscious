<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\WishlistService;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function __construct(protected WishlistService $wishlist) {}

    public function index()
    {
        $items = $this->wishlist->items();

        return view('shop.wishlist', [
            'items' => $items,
        ]);
    }

    public function toggle(Request $request, Product $product)
    {
        abort_unless($product->is_active, 404);

        $added = $this->wishlist->toggle($product);

        $message = $added
            ? 'Saved to your wishlist.'
            : 'Removed from your wishlist.';

        if ($request->wantsJson()) {
            return response()->json([
                'saved' => $added,
                'message' => $message,
                'count' => $this->wishlist->count(),
            ]);
        }

        return back()->with('status', $message);
    }

    public function destroy(Product $product)
    {
        abort_unless($product->is_active, 404);

        $this->wishlist->remove($product);

        return redirect()->route('wishlist.show')->with('status', 'Removed from your wishlist.');
    }
}
