@extends('shop.layouts.app', ['title' => 'Create account — Sutra Conscious'])

@section('content')
    <section class="container-narrow py-16 lg:py-24">
        <div class="max-w-md mx-auto" data-reveal>
            <p class="eyebrow">Account</p>
            <h1 class="mt-3 font-display text-display-md text-brand-black">Create your account</h1>
            <p class="mt-3 text-brand-black/65 text-sm">Save your details for faster checkout and see every order in one place. Past orders on the same email will be linked automatically.</p>

            <form action="{{ route('register.store') }}" method="POST" class="mt-10 space-y-5">
                @csrf
                <div>
                    <label class="field-label" for="name">Full name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus autocomplete="name" class="field-input">
                    @error('name') <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="field-label" for="email">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="email" class="field-input">
                    @error('email') <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="field-label" for="password">Password</label>
                    <input type="password" name="password" id="password" required autocomplete="new-password" class="field-input">
                    @error('password') <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="field-label" for="password_confirmation">Confirm password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password" class="field-input">
                </div>
                <button type="submit" class="btn-primary w-full">Create account</button>
            </form>

            <p class="mt-8 text-center text-sm text-brand-black/60">
                Already have an account?
                <a href="{{ route('login') }}" class="text-brand-blue hover:underline">Sign in</a>
            </p>
        </div>
    </section>
@endsection
