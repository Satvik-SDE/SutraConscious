<?php

/**
 * Creates a ₹100 Razorpay test order and opens checkout in the default browser.
 */

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$service = app(App\Services\RazorpayService::class);

if (! $service->isConfigured()) {
    fwrite(STDERR, "Razorpay is not configured.\n");
    exit(1);
}

$rzpOrder = $service->createPaymentOrder(10000, 'INR', 'manual-'.time());
$key = $service->publicKey();

$htmlPath = __DIR__.'/../storage/app/razorpay-e2e.html';
$html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Razorpay Test — Sutra Conscious</title>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <style>body{font-family:system-ui;max-width:32rem;margin:3rem auto;text-align:center}button{padding:.75rem 1.5rem;font-size:1rem;cursor:pointer}</style>
</head>
<body>
    <h1>Razorpay test checkout</h1>
    <p>Order: '.htmlspecialchars($rzpOrder['id']).' · ₹100</p>
    <button id="pay">Pay ₹100</button>
    <p id="status"></p>
    <script>
        document.getElementById("pay").onclick = function () {
            const rzp = new Razorpay({
                key: '.json_encode($key).',
                amount: '.(int) $rzpOrder['amount'].',
                currency: '.json_encode($rzpOrder['currency']).',
                name: "Sutra Conscious",
                description: "Test transaction",
                order_id: '.json_encode($rzpOrder['id']).',
                prefill: { name: "Test User", email: "test@sutraconscious.com", contact: "9123456789" },
                theme: { color: "#267696" },
                handler: function (r) {
                    document.getElementById("status").textContent =
                        "Success! payment_id=" + r.razorpay_payment_id;
                }
            });
            rzp.on("payment.failed", function (e) {
                document.getElementById("status").textContent =
                    "Failed: " + ((e.error && e.error.description) || "unknown");
            });
            rzp.open();
        };
    </script>
</body>
</html>';

file_put_contents($htmlPath, $html);

echo "Razorpay order: {$rzpOrder['id']}\n";
echo "Opening checkout page...\n";

$uri = 'file:///'.str_replace('\\', '/', realpath($htmlPath));
if (PHP_OS_FAMILY === 'Windows') {
    exec('start "" '.escapeshellarg($uri));
} elseif (PHP_OS_FAMILY === 'Darwin') {
    exec('open '.escapeshellarg($uri));
} else {
    exec('xdg-open '.escapeshellarg($uri));
}

echo "Use test card: 4111 1111 1111 1111 · expiry 12/30 · CVV 123 · any OTP.\n";
