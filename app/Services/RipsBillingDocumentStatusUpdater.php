<?php

// Archivo: app/Services/RipsBillingDocumentStatusUpdater.php

namespace App\Services;

use App\Models\Rips\RipsBillingDocument;
use Illuminate\Support\Facades\Storage;
use App\Services\RipsPatientServiceStatusUpdater;

class RipsBillingDocumentStatusUpdater
{
    /**
     * Evalúa y actualiza el estado (submission_status) de la factura.
     */
    public function actualizarEstado(RipsBillingDocument $documento): void
    {
        if ($documento->submission_status === 'Aceptado') {
        return; // Ya fue aceptado, no se debe modificar
    }


        $hasAllRequiredFields =
            !empty($documento->document_number) &&
            !empty($documento->agreement_id) &&
            !empty($documento->type_id) &&
            !empty($documento->xml_path) &&
            Storage::disk('public')->exists($documento->xml_path);

        $documento->submission_status = $hasAllRequiredFields ? 'Listo' : 'Incompleto';
        $documento->save();

        // ✅ También actualiza el estado de los servicios asociados
        $servicioUpdater = app(RipsPatientServiceStatusUpdater::class);
        foreach ($documento->patientServices as $servicio) {
            $servicioUpdater->actualizarEstado($servicio);
        }
    }
}
