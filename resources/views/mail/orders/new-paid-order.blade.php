<x-mail::message>
# New paid order

**{{ $order->number }}** · ₹{{ number_format($order->total) }}

@component('mail::button', ['url' => $adminUrl])
Open in admin
@endcomponent

## Customer

- **Name:** {{ $order->customer_name }}
- **Email:** {{ $order->customer_email }}
- **Phone:** {{ $order->customer_phone }}

## Ship to

{{ $order->shipping_line1 }}@if($order->shipping_line2)
{{ $order->shipping_line2 }}@endif

{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}

{{ $order->shipping_country }}

@if($order->notes)
## Customer notes

{{ $order->notes }}
@endif

## Items

@component('mail::table')
| Product | Variant | Qty | Line total |
|:--------|:--------|----:|-----------:|
@foreach($order->items as $item)
| {{ $item->product_name }} | {{ $item->variant_label }} | {{ $item->quantity }} | ₹{{ number_format($item->line_total) }} |
@endforeach
@endcomponent

**Subtotal:** ₹{{ number_format($order->subtotal) }}  
**Shipping:** ₹{{ number_format($order->shipping_total) }}  
**Total:** ₹{{ number_format($order->total) }}

@if($order->razorpay_payment_id)
**Razorpay payment ID:** {{ $order->razorpay_payment_id }}
@endif

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
