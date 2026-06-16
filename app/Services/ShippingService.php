<?php

namespace App\Services;

use App\Models\ShippingZone;

class ShippingService
{
    /** @return array{serviceable: bool, shipping_fee: int, zone_name: ?string, message: string, free_shipping_applied: bool} */
    public function quote(string $countryCode, string $postalCode, ?string $state, int $subtotal): array
    {
        $countryCode = strtoupper(trim($countryCode));
        $postalCode = $this->normalizePostal($postalCode);
        $state = $state ? trim($state) : null;

        if ($countryCode === '' || $postalCode === '') {
            return $this->unserviceable('Enter your pin / postal code to check delivery.');
        }

        $zones = ShippingZone::query()
            ->where('is_active', true)
            ->where('country_code', $countryCode)
            ->get();

        if ($zones->isEmpty()) {
            return $this->unserviceable('We do not ship to this country yet.');
        }

        $best = null;
        $bestScore = -1;

        foreach ($zones as $zone) {
            $score = $this->matchScore($zone, $postalCode, $state);

            if ($score < 0) {
                continue;
            }

            $score += $zone->priority;

            if ($score > $bestScore) {
                $bestScore = $score;
                $best = $zone;
            }
        }

        if (! $best) {
            return $this->unserviceable('Sorry — we do not deliver to this pin / postal code yet.');
        }

        if (! $best->is_serviceable) {
            return $this->unserviceable('Sorry — this location is not serviceable at the moment.');
        }

        $fee = $best->shipping_fee;
        $freeApplied = false;

        if ($best->free_shipping_min !== null && $subtotal >= $best->free_shipping_min) {
            $fee = 0;
            $freeApplied = true;
        }

        $message = $freeApplied
            ? 'Free shipping applied for your order total.'
            : 'We deliver to your location.';

        return [
            'serviceable' => true,
            'shipping_fee' => $fee,
            'zone_name' => $best->name,
            'message' => $message,
            'free_shipping_applied' => $freeApplied,
        ];
    }

    protected function matchScore(ShippingZone $zone, string $postalCode, ?string $state): int
    {
        $value = $zone->match_value ? trim($zone->match_value) : '';

        return match ($zone->match_type) {
            ShippingZone::MATCH_POSTAL_EXACT => strcasecmp($postalCode, $value) === 0 ? 1000 : -1,
            ShippingZone::MATCH_POSTAL_PREFIX => ($value !== '' && str_starts_with($postalCode, $value))
                ? 800 + strlen($value)
                : -1,
            ShippingZone::MATCH_STATE => ($state !== null && strcasecmp($state, $value) === 0) ? 500 : -1,
            ShippingZone::MATCH_COUNTRY => 100,
            default => -1,
        };
    }

    /** @return array{serviceable: bool, shipping_fee: int, zone_name: ?string, message: string, free_shipping_applied: bool} */
    protected function unserviceable(string $message): array
    {
        return [
            'serviceable' => false,
            'shipping_fee' => 0,
            'zone_name' => null,
            'message' => $message,
            'free_shipping_applied' => false,
        ];
    }

    public function normalizePostal(string $postalCode): string
    {
        return strtoupper(preg_replace('/\s+/', '', trim($postalCode)) ?? '');
    }

    /** @return array<string, string> */
    public function availableCountries(): array
    {
        $codes = ShippingZone::query()
            ->where('is_active', true)
            ->distinct()
            ->orderBy('country_code')
            ->pluck('country_code');

        $labels = [
            'IN' => 'India',
            'AE' => 'United Arab Emirates',
            'US' => 'United States',
            'GB' => 'United Kingdom',
            'SG' => 'Singapore',
            'AU' => 'Australia',
            'CA' => 'Canada',
        ];

        return $codes->mapWithKeys(fn (string $code) => [
            $code => $labels[$code] ?? $code,
        ])->all();
    }
}
