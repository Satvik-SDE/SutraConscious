<?php

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$service = app(App\Services\RazorpayService::class);
$order = $service->createPaymentOrder(10000, 'INR', 'api-pay-'.time());

$key = config('services.razorpay.key');
$secret = config('services.razorpay.secret');

$payload = http_build_query([
    'amount' => 10000,
    'currency' => 'INR',
    'order_id' => $order['id'],
    'email' => 'test@sutraconscious.com',
    'contact' => '9123456789',
    'method' => 'card',
    'card[number]' => '4111111111111111',
    'card[name]' => 'Test User',
    'card[expiry_month]' => '12',
    'card[expiry_year]' => '2030',
    'card[cvv]' => '123',
]);

$ch = curl_init('https://api.razorpay.com/v1/payments');
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_USERPWD => $key.':'.$secret,
    CURLOPT_POSTFIELDS => $payload,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
]);
$body = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err = curl_error($ch);
curl_close($ch);

if ($err) {
    echo "cURL error: {$err}\n";
}

echo "Order: {$order['id']}\n";
echo "HTTP {$code}\n";
echo $body."\n";
