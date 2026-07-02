<?php

namespace App\Filament\Admin\Resources\PromoCodeResource\Pages;

use App\Filament\Admin\Resources\PromoCodeResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePromoCode extends CreateRecord
{
    protected static string $resource = PromoCodeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['code'] = strtoupper(trim($data['code']));

        if (($data['type'] ?? '') === \App\Models\PromoCode::TYPE_FREE_SHIPPING) {
            $data['value'] = 0;
        }

        return $data;
    }
}
