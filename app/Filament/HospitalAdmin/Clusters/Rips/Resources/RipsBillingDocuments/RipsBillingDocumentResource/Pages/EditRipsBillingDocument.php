<?php

namespace App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsBillingDocuments\RipsBillingDocumentResource\Pages;
use App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsBillingDocuments\RipsBillingDocumentResource;


use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Log;

class EditRipsBillingDocument extends EditRecord
{
    protected static string $resource = RipsBillingDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        Log::info('Datos antes de guardar:', $data);

        // Puedes imprimir también el valor del campo xml_path directamente
        Log::info('Ruta del archivo XML:', ['xml_path' => $data['xml_path'] ?? 'NO DEFINIDO']);

        return $data;
    }
}
