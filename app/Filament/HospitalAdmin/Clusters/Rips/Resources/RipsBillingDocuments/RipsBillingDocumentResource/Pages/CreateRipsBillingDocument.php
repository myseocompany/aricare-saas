<?php

namespace App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsBillingDocuments\RipsBillingDocumentResource\Pages;

use App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsBillingDocuments\RipsBillingDocumentResource;
use Filament\Resources\Pages\CreateRecord;
use App\Services\RipsBillingDocumentStatusUpdater;

class CreateRipsBillingDocument extends CreateRecord
{
    protected static string $resource = RipsBillingDocumentResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        // ✅ Asociamos automáticamente el tenant actual
        $data['tenant_id'] = auth()->user()->tenant_id;

        // ✅ Creamos la factura
        $documento = static::getModel()::create($data);

        // 📌 Evaluamos su estado y el de sus servicios (esto ya los actualiza a ambos)
        app(RipsBillingDocumentStatusUpdater::class)->updateStatus($documento);

        return $documento;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }


}
