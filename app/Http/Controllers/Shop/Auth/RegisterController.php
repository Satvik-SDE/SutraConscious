<?php

namespace App\Http\Controllers\Shop\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\CustomerAccountService;
use App\Services\WishlistService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function __construct(
        protected CustomerAccountService $accounts,
        protected WishlistService $wishlist,
    ) {}

    public function show()
    {
        return view('shop.auth.register');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'is_admin' => false,
        ]);

        event(new Registered($user));

        Auth::login($user);
        $request->session()->regenerate();

        $attached = $this->accounts->attachGuestOrders($user);
        $this->wishlist->mergeSessionIntoUser($user);

        $redirect = redirect()->route('account.orders');

        if ($attached > 0) {
            $redirect->with('status', "Welcome! We linked {$attached} previous " . str('order')->plural($attached) . ' to your account.');
        }

        return $redirect;
    }
}
