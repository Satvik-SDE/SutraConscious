<?php

namespace App\Filament\Admin\Resources\ShippingZoneResource\Pages;

use App\Filament\Admin\Resources\ShippingZoneResource;
use Filament\Resources\Pages\EditRecord;

class EditShippingZone extends EditRecord
{
    protected static string $resource = ShippingZoneResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['country_code'] = strtoupper($data['country_code']);

        if (($data['match_type'] ?? '') === \App\Models\ShippingZone::MATCH_COUNTRY) {
            $data['match_value'] = null;
        }

        return $data;
    }
}
