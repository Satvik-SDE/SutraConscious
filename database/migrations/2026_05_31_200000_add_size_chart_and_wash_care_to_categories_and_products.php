<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('size_chart_image_path')->nullable()->after('hero_image_path');
            $table->longText('wash_care_content')->nullable()->after('size_chart_image_path');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string('size_chart_image_path')->nullable()->after('fabric');
            $table->longText('wash_care_content')->nullable()->after('size_chart_image_path');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['size_chart_image_path', 'wash_care_content']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['size_chart_image_path', 'wash_care_content']);
        });
    }
};
