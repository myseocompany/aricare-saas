<?php

/****************************************************************/
/* Module: RIPS Patient Service Status Updater                  */
/* Author: Julian                                               */
/* Date: 2025-08-07                                             */
/* Description: Evaluates and updates the status (status_id)    */
/*              of a RIPS patient service based on completeness,*/
/*              inclusion in a submission, and billing response */
/*              (accepted/rejected).                            */
/****************************************************************/

namespace App\Services;

use App\Models\Rips\RipsPatientService;
use Illuminate\Support\Facades\Log;

class RipsPatientServiceStatusUpdater
{
    /**
     * Evaluate and update a service status.
     *
     * Behavior by $included flag:
     * - null: creation/update mode → sets 'Listo' (2) if complete, otherwise 'Incompleto' (1).
     * - true: service was included in a submission → maps billing document result:
     *         'accepted' → 'Aceptado' (4), 'rejected' → 'Rechazado' (5), otherwise 'Listo' (2).
     * - false: service not included in submission → 'SinEnviar' (3).
     *
     * Domain status_id map:
     * 1 = Incompleto, 2 = Listo, 3 = SinEnviar, 4 = Aceptado, 5 = Rechazado
     */
    public function updateStatus(RipsPatientService $service, ?bool $included = null): void
    {
        $document = $service->billingDocument;

        // No billing document linked → Incompleto
        if (!$document) {
            $service->status_id = 1; // Incompleto
            $service->save();
            return;
        }

        if (is_null($included)) {
            // Creation/edition mode → evaluate completeness
            $service->status_id = $this->hasMinimumData($service) ? 2 : 1; // 2: Listo, 1: Incompleto
        } elseif ($included === true) {
            // Included in submission → evaluate billing document response
            if ($document->submission_status === 'accepted') {
                $service->status_id = 4; // Aceptado
            } elseif ($document->submission_status === 'rejected') {
                $service->status_id = 5; // Rechazado
            } else {
                $service->status_id = 2; // Listo
            }
        } else {
            // Not included in submission → SinEnviar
            $service->status_id = 3; // SinEnviar
        }

        $service->save();
    }

    /**
     * (Optional helper) Checks whether a given service ID appears inside a built RIPS
     * document payload (used when you keep IDs alongside the JSON).
     *
     * @param RipsPatientService $service
     * @param array $documentPayload RIPS payload for a billing document
     */
    protected function isServiceIncludedInDocument(RipsPatientService $service, array $documentPayload): bool
    {
        $sentServiceIds = collect($documentPayload['rips']['usuarios'] ?? [])
            ->flatMap(fn ($usuario) => collect($usuario['servicios'] ?? []))
            ->pluck('id')
            ->filter()
            ->unique();

        return $sentServiceIds->contains($service->id);
    }

    /**
     * Check whether the service has minimum required data to be considered 'Listo'.
     * Rules:
     * - Must have patient_id and service_datetime.
     * - Must have a linked billing document.
     * - If billing document is an invoice (type_id === 1), it must have XML set (xml_path).
     */
    private function hasMinimumData(RipsPatientService $service): bool
    {
        if (app()->environment('local')) {
            Log::info('Validating service minimum data', [
                'patient_id' => $service->patient_id,
                'service_datetime' => $service->service_datetime,
            ]);
        }

        // Basic fields: patient and service datetime
        if (!($service->patient_id && $service->service_datetime)) {
            return false;
        }

        // Billing document validation
        $document = $service->billingDocument;
        if (!$document) {
            Log::warning("Service [ID {$service->id}] has no linked billing document.");
            return false;
        }

        if (app()->environment('local')) {
            Log::info('Linked billing document found', [
                'document_id' => $document->id,
                'document_number' => $document->document_number,
                'type_id' => $document->type_id,
                'xml_path' => $document->xml_path,
            ]);
        }

        // If it's an invoice (type_id === 1), XML must be set
        if ((int) $document->type_id === 1 && empty($document->xml_path)) {
            Log::warning("Invoice document without XML. Document ID: {$document->id}");
            return false;
        }

        if (app()->environment('local')) {
            Log::info("Service [ID {$service->id}] has all required data.");
        }
        return true;
    }
}
