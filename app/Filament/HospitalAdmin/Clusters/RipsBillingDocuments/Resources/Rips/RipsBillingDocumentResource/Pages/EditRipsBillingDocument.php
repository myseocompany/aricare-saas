<?php

namespace App\Filament\HospitalAdmin\Clusters\RipsBillingDocuments\Resources\Rips\RipsBillingDocumentResource\Pages;

use App\Filament\HospitalAdmin\Clusters\RipsBillingDocuments\Resources\Rips\RipsBillingDocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRipsBillingDocument extends EditRecord
{
    protected static string $resource = RipsBillingDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
