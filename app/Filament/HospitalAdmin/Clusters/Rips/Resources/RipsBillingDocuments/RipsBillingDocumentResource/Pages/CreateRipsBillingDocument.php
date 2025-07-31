<?php

namespace App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsBillingDocuments\RipsBillingDocumentResource\Pages;

use App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsBillingDocuments\RipsBillingDocumentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRipsBillingDocument extends CreateRecord
{

    
    protected static string $resource = RipsBillingDocumentResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $data['tenant_id'] = auth()->user()->tenant_id;

        return static::getModel()::create($data); // ✅ debe retornar el modelo
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
