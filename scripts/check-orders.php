<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

foreach (App\Models\Order::orderByDesc('id')->take(5)->get() as $o) {
    echo $o->number . ' | ' . $o->payment_status . ' | notified=' . ($o->team_notified_at ?? 'no') . PHP_EOL;
}
