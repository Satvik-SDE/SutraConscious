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
                <div x-data="{ showPassword: false }">
                    <label class="field-label" for="password">Password</label>
                    <div class="relative">
                        <input
                            :type="showPassword ? 'text' : 'password'"
                            name="password"
                            id="password"
                            required
                            autocomplete="current-password"
                            class="field-input pr-12"
                        >
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 px-3 flex items-center text-brand-black/45 hover:text-brand-blue transition-colors"
                            :aria-label="showPassword ? 'Hide password' : 'Show password'"
                        >
                            <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                            </svg>
                            <svg x-show="showPassword" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/>
                            </svg>
                        </button>
                    </div>
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
