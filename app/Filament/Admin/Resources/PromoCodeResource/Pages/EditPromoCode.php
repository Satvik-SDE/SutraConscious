<?php

namespace App\Filament\Admin\Resources\PromoCodeResource\Pages;

use App\Filament\Admin\Resources\PromoCodeResource;
use Filament\Resources\Pages\EditRecord;

class EditPromoCode extends EditRecord
{
    protected static string $resource = PromoCodeResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['code'] = strtoupper(trim($data['code']));

        if (($data['type'] ?? '') === \App\Models\PromoCode::TYPE_FREE_SHIPPING) {
            $data['value'] = 0;
        }

        return $data;
    }
}
