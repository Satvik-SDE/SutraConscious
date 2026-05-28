<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;

class CustomerAccountService
{
    public function attachGuestOrders(User $user): int
    {
        return Order::query()
            ->whereNull('user_id')
            ->whereRaw('LOWER(customer_email) = ?', [strtolower($user->email)])
            ->update(['user_id' => $user->id]);
    }
}
