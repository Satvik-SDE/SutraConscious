<?php

use App\Models\ShippingZone;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        ShippingZone::query()
            ->where('country_code', 'IN')
            ->where('match_type', ShippingZone::MATCH_COUNTRY)
            ->update([
                'name' => 'India — All pincodes',
                'shipping_fee' => 99,
                'free_shipping_min' => 1999,
                'is_serviceable' => true,
            ]);

        ShippingZone::query()
            ->where('country_code', 'IN')
            ->where('match_type', ShippingZone::MATCH_POSTAL_PREFIX)
            ->delete();
    }

    public function down(): void
    {
        // Rates are recalculated in application code; no rollback needed.
    }
};
