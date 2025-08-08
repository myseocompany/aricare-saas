<?php

/****************************************************************/
/* Module: RIPS Billing Document Status Updater                 */
/* Author: Julian                                               */
/* Date: 2025-08-07                                             */
/* Description: Evaluates and updates a billing document's      */
/*              submission_status based on required fields,     */
/*              requiring XML only for invoices (type_id=1),    */
/*              and cascades status updates to its services.    */
/****************************************************************/

namespace App\Services;

use App\Models\Rips\RipsBillingDocument;
use Illuminate\Support\Facades\Storage;
use App\Services\RipsPatientServiceStatusUpdater;

class RipsBillingDocumentStatusUpdater
{
    /**
     * Evaluate and update the billing document submission_status.
     * If already 'Aceptado', it remains unchanged.
     * XML is required only for invoices (type_id === 1).
     * Also updates the status of related patient services.
     */
    public function updateStatus(RipsBillingDocument $document): void
    {
        // If already accepted, do not modify
        if ($document->submission_status === 'Aceptado') {
            return;
        }

        // Base required fields for both invoices and notes
        $hasBaseFields =
            !empty($document->document_number) &&
            !empty($document->agreement_id) &&
            !empty($document->type_id);

        // XML is required only when it's an invoice (type_id === 1)
        $requiresXml = ((int) $document->type_id) === 1;

        $hasRequiredXml = true; // default true for notes
        if ($requiresXml) {
            $hasRequiredXml =
                !empty($document->xml_path) &&
                Storage::disk('public')->exists($document->xml_path);
        }

        $isReady = $hasBaseFields && $hasRequiredXml;

        // Domain terms remain in Spanish per RIPS domain vocabulary
        $document->submission_status = $isReady ? 'Listo' : 'Incompleto';
        $document->save();

        // Cascade status update to related services
        $serviceUpdater = app(RipsPatientServiceStatusUpdater::class);
        foreach ($document->patientServices as $service) {
            $serviceUpdater->updateStatus($service);
        }
    }
}
