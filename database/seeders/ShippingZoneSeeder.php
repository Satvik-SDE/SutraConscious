<?php

namespace Database\Seeders;

use App\Models\ShippingZone;
use Illuminate\Database\Seeder;

class ShippingZoneSeeder extends Seeder
{
    public function run(): void
    {
        $zones = [
            [
                'name' => 'India — All pincodes',
                'country_code' => 'IN',
                'match_type' => ShippingZone::MATCH_COUNTRY,
                'match_value' => null,
                'shipping_fee' => 99,
                'free_shipping_min' => 1999,
                'is_serviceable' => true,
                'priority' => 0,
            ],
            [
                'name' => 'International — Standard',
                'country_code' => 'US',
                'match_type' => ShippingZone::MATCH_COUNTRY,
                'match_value' => null,
                'shipping_fee' => 999,
                'free_shipping_min' => null,
                'is_serviceable' => true,
                'priority' => 0,
            ],
            [
                'name' => 'International — UK',
                'country_code' => 'GB',
                'match_type' => ShippingZone::MATCH_COUNTRY,
                'match_value' => null,
                'shipping_fee' => 999,
                'free_shipping_min' => null,
                'is_serviceable' => true,
                'priority' => 0,
            ],
            [
                'name' => 'International — UAE',
                'country_code' => 'AE',
                'match_type' => ShippingZone::MATCH_COUNTRY,
                'match_value' => null,
                'shipping_fee' => 799,
                'free_shipping_min' => null,
                'is_serviceable' => true,
                'priority' => 0,
            ],
        ];

        foreach ($zones as $zone) {
            ShippingZone::updateOrCreate(
                [
                    'country_code' => $zone['country_code'],
                    'match_type' => $zone['match_type'],
                    'match_value' => $zone['match_value'],
                ],
                $zone,
            );
        }

        ShippingZone::query()
            ->where('country_code', 'IN')
            ->where('match_type', ShippingZone::MATCH_POSTAL_PREFIX)
            ->delete();
    }
}
