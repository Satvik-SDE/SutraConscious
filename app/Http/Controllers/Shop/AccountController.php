<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function orders(Request $request)
    {
        $orders = $request->user()
            ->orders()
            ->withCount('items')
            ->latest()
            ->paginate(10);

        return view('shop.account.orders', compact('orders'));
    }

    public function order(Order $order)
    {
        $this->authorize('view', $order);

        $order->load('items');

        return view('shop.account.order', [
            'order' => $order,
            'backUrl' => route('account.orders'),
            'backLabel' => 'All orders',
        ]);
    }
}
