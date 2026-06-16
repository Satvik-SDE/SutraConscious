<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$service = app(App\Services\OrderPaymentService::class);

$orders = App\Models\Order::query()
    ->where('payment_status', App\Models\Order::PAYMENT_PAID)
    ->whereNull('team_notified_at')
    ->orderBy('id')
    ->get();

foreach ($orders as $order) {
    echo "Notifying {$order->number}...\n";
    $service->notifyProcessingTeam($order->fresh(['items']));
}

echo "Done. Processed {$orders->count()} order(s).\n";
