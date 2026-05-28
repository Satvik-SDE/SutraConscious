@extends('shop.layouts.app', ['title' => 'My orders — Sutra Conscious'])

@section('content')
    <section class="container-wide py-12 lg:py-20">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-10" data-reveal>
            <div>
                <p class="eyebrow">Account</p>
                <h1 class="mt-3 font-display text-display-md text-brand-black">Your orders</h1>
                <p class="mt-2 text-sm text-brand-black/60">Hi {{ auth()->user()->name }} — track status and shipments here.</p>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-[0.7rem] uppercase tracking-[0.18em] text-brand-black/50 hover:text-brand-blue transition-colors">Sign out</button>
            </form>
        </div>

        @if(session('status'))
            <p class="mb-8 text-sm text-brand-blue" data-reveal>{{ session('status') }}</p>
        @endif

        @if($orders->isEmpty())
            <div class="border border-surface-line bg-surface-cream p-10 text-center" data-reveal>
                <p class="text-brand-black/70">You have not placed any orders yet.</p>
                <a href="{{ route('shop') }}" class="btn-primary mt-8 inline-flex">Shop kurtas</a>
            </div>
        @else
            <ul class="divide-y divide-surface-line border border-surface-line bg-surface-cream" data-reveal>
                @foreach($orders as $order)
                    <li>
                        <a href="{{ route('account.orders.show', $order) }}" class="block p-6 lg:p-8 hover:bg-brand-skin/20 transition-colors group">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                <div>
                                    <p class="font-medium text-brand-black group-hover:text-brand-blue transition-colors">{{ $order->number }}</p>
                                    <p class="mt-1 text-xs text-brand-black/50">{{ $order->created_at->format('j M Y') }} · {{ $order->items_count }} {{ Str::plural('item', $order->items_count) }}</p>
                                </div>
                                <div class="flex flex-wrap items-center gap-3 sm:justify-end">
                                    <span class="inline-flex text-[0.65rem] uppercase tracking-[0.16em] px-2.5 py-1 border border-surface-line text-brand-black/70">{{ $order->statusLabel() }}</span>
                                    <span class="inline-flex text-[0.65rem] uppercase tracking-[0.16em] px-2.5 py-1 {{ $order->payment_status === 'paid' ? 'bg-brand-blue/10 text-brand-blue' : 'bg-brand-black/5 text-brand-black/55' }}">{{ $order->paymentStatusLabel() }}</span>
                                    <span class="font-display text-xl text-brand-blue sm:ml-2">₹{{ number_format($order->total) }}</span>
                                </div>
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="mt-10">{{ $orders->links() }}</div>
        @endif
    </section>
@endsection
