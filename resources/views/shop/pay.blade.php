@extends('shop.layouts.app', ['title' => 'Pay — Sutra Conscious'])

@push('head')
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
@endpush

@section('content')
    <section class="container-narrow py-16 lg:py-24 text-center">
        <div data-reveal>
            <div class="mb-6 flex items-center justify-center gap-3 text-xs uppercase tracking-[0.18em] text-brand-black/60">
                <span>Bag</span>
                <span class="w-6 h-px bg-brand-black/20"></span>
                <span>Details</span>
                <span class="w-6 h-px bg-brand-black/20"></span>
                <span class="text-brand-blue">Payment</span>
            </div>
            <p class="eyebrow">Almost there</p>
            <h1 class="mt-4 font-display text-display-md text-brand-black">Complete your payment</h1>
            <p class="mt-4 text-brand-black/70">Order <span class="text-brand-blue font-medium">{{ $order->number }}</span></p>
            <div class="mt-3 font-display text-5xl text-brand-blue">₹{{ number_format($order->total) }}</div>

            @if ($errors->any())
                <div class="mt-8 bg-red-50 border border-red-200 text-red-700 p-4 text-sm text-left max-w-md mx-auto">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <div class="mt-12 flex flex-col items-center gap-4" data-reveal data-reveal-delay="200">
                <button id="rzp-pay" class="btn-primary text-sm">
                    Pay ₹{{ number_format($order->total) }} via Razorpay
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-3.75 11.25h16.5a1.5 1.5 0 0 0 1.5-1.5V12a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 12v8.25a1.5 1.5 0 0 0 1.5 1.5Z"/>
                    </svg>
                </button>
                <a href="{{ route('cart.show') }}" class="text-[0.78rem] uppercase tracking-[0.2em] text-brand-black/55 hover:text-brand-blue transition-colors">Back to bag</a>
            </div>

            <p class="mt-16 text-xs text-brand-black/50 max-w-sm mx-auto">Razorpay is in test mode. Use a test card to complete checkout without real money.</p>
        </div>
    </section>

    <form id="rzp-verify" action="{{ route('checkout.verify', $order) }}" method="POST" style="display:none;">
        @csrf
        <input type="hidden" name="razorpay_payment_id">
        <input type="hidden" name="razorpay_order_id">
        <input type="hidden" name="razorpay_signature">
    </form>

    @push('scripts')
        <script>
            (function () {
                const options = {
                    key: @json($razorpayKey),
                    amount: {{ $order->total * 100 }},
                    currency: @json($order->currency),
                    name: 'Sutra Conscious',
                    description: 'Order {{ $order->number }}',
                    image: @json(asset('img/brand/logo.png')),
                    order_id: @json($order->razorpay_order_id),
                    prefill: {
                        name: @json($order->customer_name),
                        email: @json($order->customer_email),
                        contact: @json($order->customer_phone),
                    },
                    notes: { order_number: @json($order->number) },
                    theme: { color: '#267696' },
                    handler: function (response) {
                        const form = document.getElementById('rzp-verify');
                        form.querySelector('[name="razorpay_payment_id"]').value = response.razorpay_payment_id;
                        form.querySelector('[name="razorpay_order_id"]').value = response.razorpay_order_id;
                        form.querySelector('[name="razorpay_signature"]').value = response.razorpay_signature;
                        form.submit();
                    },
                };

                const button = document.getElementById('rzp-pay');
                button.addEventListener('click', function () {
                    const rzp = new Razorpay(options);
                    rzp.on('payment.failed', function (resp) {
                        alert('Payment failed: ' + (resp.error && resp.error.description));
                    });
                    rzp.open();
                });
            })();
        </script>
    @endpush
@endsection
