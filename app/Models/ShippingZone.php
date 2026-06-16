<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingZone extends Model
{
    use HasFactory;

    public const MATCH_COUNTRY = 'country';

    public const MATCH_STATE = 'state';

    public const MATCH_POSTAL_PREFIX = 'postal_prefix';

    public const MATCH_POSTAL_EXACT = 'postal_exact';

    protected $fillable = [
        'name',
        'country_code',
        'match_type',
        'match_value',
        'shipping_fee',
        'free_shipping_min',
        'is_serviceable',
        'priority',
        'is_active',
    ];

    protected $casts = [
        'shipping_fee' => 'integer',
        'free_shipping_min' => 'integer',
        'is_serviceable' => 'boolean',
        'priority' => 'integer',
        'is_active' => 'boolean',
    ];

    public static function matchTypes(): array
    {
        return [
            self::MATCH_COUNTRY => 'Entire country',
            self::MATCH_STATE => 'State / region',
            self::MATCH_POSTAL_PREFIX => 'Pin / postal prefix',
            self::MATCH_POSTAL_EXACT => 'Exact pin / postal code',
        ];
    }

    public function formattedFee(): string
    {
        return '₹' . number_format($this->shipping_fee);
    }
}
