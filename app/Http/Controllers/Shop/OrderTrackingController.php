<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class OrderTrackingController extends Controller
{
    public function show()
    {
        return view('shop.orders.track');
    }

    public function lookup(Request $request)
    {
        $data = $request->validate([
            'number' => ['required', 'string', 'max:32'],
            'email' => ['required', 'email', 'max:255'],
        ]);

        $order = Order::query()
            ->where('number', strtoupper(trim($data['number'])))
            ->whereRaw('LOWER(customer_email) = ?', [strtolower($data['email'])])
            ->first();

        if (! $order) {
            return back()
                ->withInput()
                ->withErrors(['number' => 'We could not find an order with that number and email.']);
        }

        return redirect()->to(
            URL::temporarySignedRoute('orders.guest.show', now()->addHours(2), ['order' => $order->number])
        );
    }

    public function guestShow(Request $request, Order $order)
    {
        if (! $request->hasValidSignature()) {
            abort(403);
        }

        $order->load('items');

        return view('shop.account.order', [
            'order' => $order,
            'backUrl' => route('orders.track'),
            'backLabel' => 'Track another order',
            'isGuestView' => true,
        ]);
    }
}
