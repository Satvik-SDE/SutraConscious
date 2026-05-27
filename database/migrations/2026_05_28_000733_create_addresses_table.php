<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('label')->nullable();
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('line1');
            $table->string('line2')->nullable();
            $table->string('city');
            $table->string('state');
            $table->string('country', 2)->default('IN');
            $table->string('postal_code', 12);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
