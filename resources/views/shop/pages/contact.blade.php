@extends('shop.layouts.app', ['title' => 'Contact — Sutra Conscious'])

@section('content')
    <section class="container-wide py-16 lg:py-24">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-20">
            <div class="lg:col-span-5" data-reveal>
                <p class="eyebrow">Get in touch</p>
                <h1 class="mt-5 font-display text-display-lg text-brand-black">Say hello.</h1>
                <p class="mt-6 text-brand-black/70 text-lg leading-relaxed max-w-md">
                    We're a small, hands-on team. Send us a note below — we read everything.
                </p>
            </div>

            <div class="lg:col-span-7 space-y-10" data-reveal data-reveal-delay="200">
                <div class="bg-surface-cream border border-surface-line p-7 lg:p-9">
                    <h2 class="font-display text-2xl text-brand-black">Write to us</h2>
                    <p class="mt-2 text-sm text-brand-black/60">We usually reply within 1–2 business days.</p>

                    @if(session('status'))
                        <p class="mt-6 text-sm text-brand-blue">{{ session('status') }}</p>
                    @endif

                    <form action="{{ route('contact.submit') }}" method="POST" class="mt-8 space-y-5" novalidate>
                        @csrf

                        {{-- Honeypot for bots --}}
                        <div class="hidden" aria-hidden="true">
                            <label for="website">Website</label>
                            <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
                        </div>

                        <div>
                            <label for="contact-name" class="field-label">Name</label>
                            <input type="text" name="name" id="contact-name" value="{{ $defaults['name'] ?? '' }}" required class="field-input">
                            @error('name') <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label for="contact-email" class="field-label">Email</label>
                                <input type="email" name="email" id="contact-email" value="{{ $defaults['email'] ?? '' }}" required class="field-input">
                                @error('email') <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="contact-phone" class="field-label">Phone <span class="text-brand-black/30 normal-case tracking-normal">(optional)</span></label>
                                <input type="tel" name="phone" id="contact-phone" value="{{ $defaults['phone'] ?? '' }}" class="field-input">
                                @error('phone') <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label for="contact-message" class="field-label">Your message</label>
                            <textarea name="message" id="contact-message" rows="5" required class="field-input resize-y min-h-[140px]">{{ old('message') }}</textarea>
                            @error('message') <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p> @enderror
                        </div>

                        <button type="submit" class="btn-primary">Send message</button>
                    </form>
                </div>

                <div class="space-y-1">
                    @php
                        $rows = [
                            ['label' => 'Email', 'value' => 'support@sutraconscious.com', 'href' => 'mailto:support@sutraconscious.com'],
                            ['label' => 'Phone', 'value' => '+91 93215 39748', 'href' => 'tel:+919321539748'],
                            ['label' => 'Instagram', 'value' => '@sutraconscious', 'href' => 'https://www.instagram.com/sutraconscious/'],
                            ['label' => 'Founders', 'value' => 'Shuchi & Adit', 'href' => null],
                            ['label' => 'Based in', 'value' => 'Bharat', 'href' => null],
                        ];
                    @endphp

                    @foreach($rows as $row)
                        <div class="grid grid-cols-[120px_1fr] gap-6 py-6 border-b border-surface-line items-center">
                            <div class="text-[0.7rem] uppercase tracking-[0.25em] text-brand-black/50">{{ $row['label'] }}</div>
                            <div>
                                @if($row['href'])
                                    <a href="{{ $row['href'] }}" target="{{ Str::startsWith($row['href'], 'http') ? '_blank' : '_self' }}" rel="noopener" class="text-xl text-brand-black hover:text-brand-blue transition-colors link-underline">{{ $row['value'] }}</a>
                                @else
                                    <div class="text-xl text-brand-black">{{ $row['value'] }}</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endsection
