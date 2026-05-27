<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('status')->default('pending');
            $table->string('payment_status')->default('unpaid');
            $table->string('payment_provider')->nullable();
            $table->string('razorpay_order_id')->nullable()->index();
            $table->string('razorpay_payment_id')->nullable()->index();
            $table->string('razorpay_signature')->nullable();

            $table->string('currency', 3)->default('INR');
            $table->unsignedInteger('subtotal')->default(0);
            $table->unsignedInteger('shipping_total')->default(0);
            $table->unsignedInteger('discount_total')->default(0);
            $table->unsignedInteger('total')->default(0);

            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');

            $table->string('shipping_line1');
            $table->string('shipping_line2')->nullable();
            $table->string('shipping_city');
            $table->string('shipping_state');
            $table->string('shipping_country', 2)->default('IN');
            $table->string('shipping_postal_code', 12);

            $table->text('notes')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
