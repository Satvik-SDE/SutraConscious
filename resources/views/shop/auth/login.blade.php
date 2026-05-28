@extends('shop.layouts.app', ['title' => 'Sign in — Sutra Conscious'])

@section('content')
    <section class="container-narrow py-16 lg:py-24">
        <div class="max-w-md mx-auto" data-reveal>
            <p class="eyebrow">Account</p>
            <h1 class="mt-3 font-display text-display-md text-brand-black">Welcome back</h1>
            <p class="mt-3 text-brand-black/65 text-sm">Sign in to view orders, track shipments, and check out faster.</p>

            @if(session('status'))
                <p class="mt-6 text-sm text-brand-blue">{{ session('status') }}</p>
            @endif

            <form action="{{ route('login.store') }}" method="POST" class="mt-10 space-y-5">
                @csrf
                <div>
                    <label class="field-label" for="email">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus autocomplete="email" class="field-input">
                    @error('email') <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="field-label" for="password">Password</label>
                    <input type="password" name="password" id="password" required autocomplete="current-password" class="field-input">
                    @error('password') <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p> @enderror
                </div>
                <label class="flex items-center gap-2 text-sm text-brand-black/70 cursor-pointer">
                    <input type="checkbox" name="remember" value="1" class="rounded border-surface-line text-brand-blue focus:ring-brand-blue/30">
                    Remember me
                </label>
                <button type="submit" class="btn-primary w-full">Sign in</button>
            </form>

            <p class="mt-8 text-center text-sm text-brand-black/60">
                New here?
                <a href="{{ route('register') }}" class="text-brand-blue hover:underline">Create an account</a>
            </p>
            <p class="mt-4 text-center text-sm text-brand-black/60">
                Guest order?
                <a href="{{ route('orders.track') }}" class="text-brand-blue hover:underline">Track without signing in</a>
            </p>
        </div>
    </section>
@endsection
