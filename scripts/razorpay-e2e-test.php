<?php

/**
 * Creates a Razorpay test order and runs browser checkout (Playwright).
 * Usage: php scripts/razorpay-e2e-test.php
 */

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$service = app(App\Services\RazorpayService::class);

if (! $service->isConfigured()) {
    fwrite(STDERR, "Razorpay is not configured. Set RAZORPAY_KEY_ID and RAZORPAY_KEY_SECRET in .env\n");
    exit(1);
}

$receipt = 'e2e-'.time();
$rzpOrder = $service->createPaymentOrder(10000, 'INR', $receipt);
$key = $service->publicKey();

$htmlPath = __DIR__.'/../storage/app/razorpay-e2e.html';
$html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Razorpay E2E Test</title>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body>
    <button id="pay">Pay ₹100 (test)</button>
    <pre id="status">Ready</pre>
    <script>
        window.__razorpayResult = null;
        window.__razorpayError = null;
        document.getElementById("pay").onclick = function () {
            const rzp = new Razorpay({
                key: '.json_encode($key).',
                amount: '.(int) $rzpOrder['amount'].',
                currency: '.json_encode($rzpOrder['currency']).',
                name: "Sutra Conscious",
                description: "E2E test payment",
                order_id: '.json_encode($rzpOrder['id']).',
                prefill: {
                    name: "Test User",
                    email: "test@sutraconscious.com",
                    contact: "9123456789",
                },
                theme: { color: "#267696" },
                handler: function (response) {
                    window.__razorpayResult = response;
                    document.getElementById("status").textContent = "Payment success";
                },
                modal: {
                    ondismiss: function () {
                        window.__razorpayError = "cancelled";
                    }
                }
            });
            rzp.on("payment.failed", function (resp) {
                window.__razorpayError = (resp.error && resp.error.description) || "failed";
            });
            rzp.open();
        };
    </script>
</body>
</html>';

if (! is_dir(dirname($htmlPath))) {
    mkdir(dirname($htmlPath), 0755, true);
}
file_put_contents($htmlPath, $html);

echo "Razorpay order: {$rzpOrder['id']}\n";
echo "Amount: {$rzpOrder['amount']} paise (₹100)\n";
echo "Receipt: {$receipt}\n";

$nodeScript = __DIR__.'/razorpay-e2e.mjs';
$cmd = 'node '.escapeshellarg($nodeScript).' '.escapeshellarg($htmlPath);
passthru($cmd, $exitCode);

if ($exitCode !== 0) {
    exit($exitCode);
}

$resultFile = __DIR__.'/../storage/app/razorpay-e2e-result.json';
if (! file_exists($resultFile)) {
    fwrite(STDERR, "No payment result captured.\n");
    exit(1);
}

$result = json_decode(file_get_contents($resultFile), true);
@unlink($resultFile);

$verifyUrl = 'http://127.0.0.1:8000/api/verify-payment';
$ch = curl_init($verifyUrl);
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Accept: application/json'],
    CURLOPT_POSTFIELDS => json_encode([
        'razorpay_order_id' => $result['razorpay_order_id'],
        'razorpay_payment_id' => $result['razorpay_payment_id'],
        'razorpay_signature' => $result['razorpay_signature'],
    ]),
    CURLOPT_RETURNTRANSFER => true,
]);
$verifyBody = curl_exec($ch);
$verifyCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "\n--- Verify payment (HTTP {$verifyCode}) ---\n";
echo $verifyBody."\n";

if ($verifyCode !== 200) {
    exit(1);
}

echo "\nTest transaction completed successfully.\n";
echo "Payment ID: {$result['razorpay_payment_id']}\n";
