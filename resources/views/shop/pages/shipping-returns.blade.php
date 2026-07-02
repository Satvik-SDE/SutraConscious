@extends('shop.layouts.app', ['title' => 'Shipping, Returns & Cancellation — Sutra Conscious'])

@section('content')
    <section class="container-narrow py-16 lg:py-24" data-reveal>
        <p class="eyebrow">Policy</p>
        <h1 class="mt-4 font-display text-display-md text-brand-black">Shipping, Returns &amp; Cancellation</h1>
        <p class="mt-5 text-brand-black/70 text-lg leading-relaxed max-w-2xl">
            Clear timelines, worldwide delivery, and how we handle returns, exchanges, and order cancellations.
        </p>

        <nav class="mt-10 flex flex-wrap gap-x-6 gap-y-2 text-sm" aria-label="On this page">
            <a href="#returns" class="text-brand-blue link-underline">Returns &amp; Refunds</a>
            <a href="#shipping" class="text-brand-blue link-underline">Shipping</a>
            <a href="#cancellation" class="text-brand-blue link-underline">Cancellation</a>
        </nav>

        <div class="mt-14 space-y-14">

            {{-- Return, Exchange & Refund --}}
            <article id="returns">
                <h2 class="font-display text-2xl lg:text-3xl text-brand-black">Return, Exchange &amp; Refund Policy</h2>
                <p class="mt-5 text-brand-black/80 leading-relaxed">
                    At Sutra Conscious, every kurta undergoes thorough quality checks before dispatch. Due to the nature of our products and inventory management process, we generally do not accept returns, exchanges, or refunds for reasons such as incorrect size selection, change of mind, personal preference, or color expectations arising from screen display differences.
                </p>
                <p class="mt-4 text-brand-black/80 leading-relaxed">
                    We encourage customers to carefully review the size chart, product description, and images before placing an order.
                </p>

                <h3 class="mt-10 font-display text-xl text-brand-black">Exceptions</h3>
                <p class="mt-4 text-brand-black/80 leading-relaxed">If you receive:</p>
                <ul class="mt-4 space-y-3 text-brand-black/80 leading-relaxed">
                    <li class="flex items-start gap-3"><span class="w-1.5 h-1.5 rounded-full bg-brand-blue mt-2.5 flex-shrink-0"></span>A damaged product</li>
                    <li class="flex items-start gap-3"><span class="w-1.5 h-1.5 rounded-full bg-brand-blue mt-2.5 flex-shrink-0"></span>A defective product</li>
                    <li class="flex items-start gap-3"><span class="w-1.5 h-1.5 rounded-full bg-brand-blue mt-2.5 flex-shrink-0"></span>An incorrect item different from what was ordered</li>
                </ul>
                <p class="mt-5 text-brand-black/80 leading-relaxed">
                    Please contact us within <strong class="font-medium text-brand-black">24 hours</strong> of delivery by emailing
                    <a href="mailto:support@sutraconscious.com" class="text-brand-blue link-underline">support@sutraconscious.com</a>
                    along with your order number and clear photographs of the issue as proof.
                </p>
                <p class="mt-4 text-brand-black/80 leading-relaxed">After verification, we may, at our discretion:</p>
                <ul class="mt-4 space-y-3 text-brand-black/80 leading-relaxed">
                    <li class="flex items-start gap-3"><span class="w-1.5 h-1.5 rounded-full bg-brand-blue mt-2.5 flex-shrink-0"></span>Send a replacement product, or</li>
                    <li class="flex items-start gap-3"><span class="w-1.5 h-1.5 rounded-full bg-brand-blue mt-2.5 flex-shrink-0"></span>Issue a refund if a replacement is not available.</li>
                </ul>

                <h3 class="mt-10 font-display text-xl text-brand-black">Damaged, Defective, or Incorrect Products</h3>
                <p class="mt-4 text-brand-black/80 leading-relaxed">
                    If you receive a damaged, defective, or incorrect product, please contact us within 24 hours of delivery at
                    <a href="mailto:support@sutraconscious.com" class="text-brand-blue link-underline">support@sutraconscious.com</a>.
                </p>
                <p class="mt-4 text-brand-black/80 leading-relaxed">To be eligible for review, customers must provide:</p>
                <ul class="mt-4 space-y-3 text-brand-black/80 leading-relaxed">
                    <li class="flex items-start gap-3"><span class="w-1.5 h-1.5 rounded-full bg-brand-blue mt-2.5 flex-shrink-0"></span>A complete and uninterrupted unboxing video clearly showing the sealed package being opened.</li>
                    <li class="flex items-start gap-3"><span class="w-1.5 h-1.5 rounded-full bg-brand-blue mt-2.5 flex-shrink-0"></span>Clear photographs of the product and the issue.</li>
                    <li class="flex items-start gap-3"><span class="w-1.5 h-1.5 rounded-full bg-brand-blue mt-2.5 flex-shrink-0"></span>The order number and relevant details.</li>
                </ul>
                <p class="mt-5 text-brand-black/80 leading-relaxed">
                    Claims submitted without a valid unboxing video may not be eligible for replacement or refund review.
                </p>
                <p class="mt-4 text-brand-black/80 leading-relaxed">
                    If requested by us, the customer must return the product to the address provided by our support team. Return shipping costs shall be borne by the customer.
                </p>
                <p class="mt-4 text-brand-black/80 leading-relaxed">
                    Upon receiving and inspecting the returned product, we will determine whether the claim qualifies for a replacement or refund. Any refund or replacement will be issued solely at the discretion of Sutra Conscious after verification of the claim.
                </p>
                <p class="mt-4 text-brand-black/80 leading-relaxed">
                    Products returned without prior authorization from our support team may not be accepted.
                </p>

                <h3 class="mt-10 font-display text-xl text-brand-black">Product Variations</h3>
                <p class="mt-4 text-brand-black/80 leading-relaxed">Please note that:</p>
                <ul class="mt-4 space-y-3 text-brand-black/80 leading-relaxed">
                    <li class="flex items-start gap-3"><span class="w-1.5 h-1.5 rounded-full bg-brand-blue mt-2.5 flex-shrink-0"></span>Slight color variations may occur due to lighting conditions, photography, and screen settings.</li>
                    <li class="flex items-start gap-3"><span class="w-1.5 h-1.5 rounded-full bg-brand-blue mt-2.5 flex-shrink-0"></span>Due to the natural characteristics of cotton fabrics and traditional manufacturing processes, slight variations in texture, weave, color tone, or stitching may occur. These variations are not defects and contribute to the uniqueness of each product.</li>
                </ul>

                <p class="mt-8 text-brand-black/80 leading-relaxed">
                    Questions about your order?
                    <a href="mailto:support@sutraconscious.com" class="text-brand-blue link-underline">support@sutraconscious.com</a>
                </p>
            </article>

            <div class="rule"></div>

            {{-- Shipping --}}
            <article id="shipping">
                <h2 class="font-display text-2xl lg:text-3xl text-brand-black">Shipping Policy</h2>
                <p class="mt-5 text-brand-black/80 leading-relaxed">We proudly ship our products worldwide.</p>

                <p class="mt-6 text-brand-black/80 leading-relaxed">
                    <strong class="font-medium text-brand-blue">Free shipping across India on orders above ₹{{ number_format(config('shipping.india.free_shipping_min', 2000)) }}.</strong>
                    Shipping charges for your order are calculated at checkout based on your delivery pincode.
                </p>

                <h3 class="mt-10 font-display text-xl text-brand-black">Processing Time</h3>
                <p class="mt-4 text-brand-black/80 leading-relaxed">
                    Orders are typically processed and dispatched within <strong class="font-medium text-brand-black">1–3 business days</strong> after successful payment confirmation.
                </p>

                <h3 class="mt-10 font-display text-xl text-brand-black">Delivery Time</h3>
                <p class="mt-4 text-brand-black/80 leading-relaxed">Estimated delivery times:</p>
                <ul class="mt-4 space-y-3 text-brand-black/80 leading-relaxed">
                    <li class="flex items-start gap-3"><span class="w-1.5 h-1.5 rounded-full bg-brand-blue mt-2.5 flex-shrink-0"></span><span><strong class="font-medium text-brand-black">India:</strong> 3–7 business days</span></li>
                    <li class="flex items-start gap-3"><span class="w-1.5 h-1.5 rounded-full bg-brand-blue mt-2.5 flex-shrink-0"></span><span><strong class="font-medium text-brand-black">International orders:</strong> 7–21 business days</span></li>
                </ul>
                <p class="mt-5 text-brand-black/80 leading-relaxed">
                    Delivery timelines are estimates and may vary due to customs clearance, carrier delays, weather conditions, public holidays, or other circumstances beyond our control.
                </p>

                <h3 class="mt-10 font-display text-xl text-brand-black">Customs Duties &amp; Taxes</h3>
                <p class="mt-4 text-brand-black/80 leading-relaxed">
                    International orders may be subject to customs duties, import taxes, VAT, GST, or other local charges imposed by the destination country. These charges are the responsibility of the customer and are not included in the product price or shipping charges unless explicitly stated otherwise.
                </p>

                <h3 class="mt-10 font-display text-xl text-brand-black">Tracking</h3>
                <p class="mt-4 text-brand-black/80 leading-relaxed">
                    Tracking information will be provided once the order has been dispatched.
                </p>

                <h3 class="mt-10 font-display text-xl text-brand-black">Incorrect Address</h3>
                <p class="mt-4 text-brand-black/80 leading-relaxed">
                    Customers are responsible for providing accurate shipping information. We are not responsible for delays, losses, or additional shipping costs arising from incorrect or incomplete addresses.
                </p>

                <h3 class="mt-10 font-display text-xl text-brand-black">Package Refused by Customer</h3>
                <p class="mt-4 text-brand-black/80 leading-relaxed">
                    If an international shipment is refused by the customer or remains unclaimed and is returned by the carrier, original shipping charges, return shipping charges, customs duties, and handling fees may be deducted from any eligible refund.
                </p>

                <h3 class="mt-10 font-display text-xl text-brand-black">Force Majeure</h3>
                <p class="mt-4 text-brand-black/80 leading-relaxed">
                    We shall not be responsible for delays or failures caused by events beyond our reasonable control, including natural disasters, strikes, customs delays, government actions, pandemics, transportation disruptions, or carrier service interruptions.
                </p>
            </article>

            <div class="rule"></div>

            {{-- Cancellation --}}
            <article id="cancellation">
                <h2 class="font-display text-2xl lg:text-3xl text-brand-black">Cancellation Policy</h2>
                <p class="mt-5 text-brand-black/80 leading-relaxed">
                    Orders may be cancelled, provided they have not yet been processed or shipped. Once an order has been processed, packed, or dispatched, cancellation requests cannot be accommodated.
                </p>
                <p class="mt-4 text-brand-black/80 leading-relaxed">
                    To request a cancellation, please contact us as soon as possible at
                    <a href="mailto:support@sutraconscious.com" class="text-brand-blue link-underline">support@sutraconscious.com</a>.
                </p>
                <p class="mt-4 text-brand-black/80 leading-relaxed">
                    For approved cancellations, the refund will be processed to the original payment method within <strong class="font-medium text-brand-black">5–10 business days</strong>.
                </p>
                <p class="mt-4 text-brand-black/80 leading-relaxed">
                    Please note that a small cancellation and payment processing fee may be deducted from the refund amount to cover payment gateway charges and administrative costs incurred during order processing. The exact deduction, if applicable, will be communicated to the customer at the time of cancellation approval.
                </p>
            </article>

        </div>

        <p class="mt-14 text-sm text-brand-black/50">
            See also our <a href="{{ route('terms') }}" class="text-brand-blue link-underline">Terms &amp; Conditions</a>.
        </p>
    </section>
@endsection
