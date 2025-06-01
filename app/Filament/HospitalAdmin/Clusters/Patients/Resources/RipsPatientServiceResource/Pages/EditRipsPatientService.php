<?php

namespace App\Filament\HospitalAdmin\Clusters\Patients\Resources\RipsPatientServiceResource\Pages;

use App\Filament\HospitalAdmin\Clusters\Patients\Resources\RipsPatientServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;


class EditRipsPatientService extends EditRecord
{
    protected static string $resource = RipsPatientServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Actualiza primero la factura asociada
        $billingDocument = $record->billingDocument;

        if ($billingDocument) {
            $billingDocument->update([
                'document_number' => $data['invoice_number'],
                'agreement_id' => $data['agreement_id'],
            ]);
        }

        // Opcionalmente puedes limpiar estos campos del $data
        unset($data['invoice_number']);
        unset($data['agreement_id']);

        // Ahora actualiza el registro principal
        $record->update($data);

        return $record;
    }


     


}
