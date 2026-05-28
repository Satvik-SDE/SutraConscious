<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function view(User $user, Order $order): bool
    {
        if ($order->user_id !== null && (int) $order->user_id === (int) $user->id) {
            return true;
        }

        return strcasecmp((string) $order->customer_email, (string) $user->email) === 0;
    }
}
