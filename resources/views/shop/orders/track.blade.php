@extends('shop.layouts.app', ['title' => 'Track order — Sutra Conscious'])

@section('content')
    <section class="container-narrow py-16 lg:py-24">
        <div class="max-w-md mx-auto" data-reveal>
            <p class="eyebrow">Orders</p>
            <h1 class="mt-3 font-display text-display-md text-brand-black">Track your order</h1>
            <p class="mt-3 text-brand-black/65 text-sm">Enter your order number and the email used at checkout. No account required.</p>

            <form action="{{ route('orders.track.lookup') }}" method="POST" class="mt-10 space-y-5">
                @csrf
                <div>
                    <label class="field-label" for="number">Order number</label>
                    <input type="text" name="number" id="number" value="{{ old('number') }}" required placeholder="e.g. SC260528ABC123" class="field-input uppercase">
                    @error('number') <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="field-label" for="email">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="email" class="field-input">
                    @error('email') <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="btn-primary w-full">View order</button>
            </form>

            <p class="mt-8 text-center text-sm text-brand-black/60">
                Want order history in one place?
                <a href="{{ route('register') }}" class="text-brand-blue hover:underline">Create an account</a>
                or <a href="{{ route('login') }}" class="text-brand-blue hover:underline">sign in</a>
            </p>
        </div>
    </section>
@endsection
