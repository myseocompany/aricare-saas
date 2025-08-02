<?php

namespace App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsBillingDocuments\RipsBillingDocumentResource\Pages;

use App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsBillingDocuments\RipsBillingDocumentResource;
use App\Services\RipsBillingDocumentStatusUpdater;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Log;

class EditRipsBillingDocument extends EditRecord
{
    protected static string $resource = RipsBillingDocumentResource::class;

    /**
     * Botones de acción del encabezado (Eliminar por defecto)
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * Antes de guardar, puedes mutar los datos del formulario si lo necesitas.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        Log::info('Datos antes de guardar:', $data);

        // Verifica si la ruta XML está definida
        Log::info('Ruta del archivo XML:', ['xml_path' => $data['xml_path'] ?? 'NO DEFINIDO']);

        return $data;
    }

    /**
     * Después de guardar, actualiza automáticamente el estado del documento y sus servicios
     */
    protected function afterSave(): void
    {
        // Obtenemos el documento recién editado
        $documento = $this->record;

        // ✅ Actualizamos su estado y el de los servicios asociados
        app(RipsBillingDocumentStatusUpdater::class)->actualizarEstado($documento);
    }
}
