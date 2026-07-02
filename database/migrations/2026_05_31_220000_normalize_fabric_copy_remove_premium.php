<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach (DB::table('products')->orderBy('id')->get() as $product) {
            $fabric = preg_replace('/\bpremium\s+/i', '', $product->fabric ?? '');
            $shortDescription = preg_replace('/\bpremium\s+/i', '', $product->short_description ?? '');

            DB::table('products')->where('id', $product->id)->update([
                'fabric' => trim($fabric) ?: '100% Cotton',
                'short_description' => trim($shortDescription) ?: null,
            ]);
        }
    }

    public function down(): void
    {
        // Copy normalization is not reversed.
    }
};
