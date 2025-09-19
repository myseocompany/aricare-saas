<?php

/****************************************************************/
/* Module: RIPS Patient Service Status Updater                  */
/* Author: Julian                                               */
/* Date: 2025-08-07 (revisado)                                  */
/* Description: Evalúa y actualiza el status_id de un servicio  */
/*              según integridad, inclusión en el envío y       */
/*              resultado del documento (aceptado/rechazado).   */
/****************************************************************/

namespace App\Services;

use App\Models\Rips\RipsPatientService;
use Illuminate\Support\Facades\Log;

class RipsPatientServiceStatusUpdater
{
    // Mapa de estados (asegúrate que existan en rips_statuses)
    private const STATUS_INCOMPLETO = 1;
    private const STATUS_LISTO      = 2;
    private const STATUS_SIN_ENVIAR = 3;
    private const STATUS_ACEPTADO   = 4;
    private const STATUS_RECHAZADO  = 5;

    /**
     * Evalúa y actualiza el estado del servicio.
     *
     * Comportamiento por $included:
     * - null  -> modo creación/edición → 'Listo' si tiene mínimos, si no 'Incompleto'.
     * - true  -> fue incluido en el envío → mapea el resultado del documento:
     *            'accepted/Aceptado' → 'Aceptado', 'rejected/Rechazado' → 'Rechazado',
     *            en otro caso → 'Listo'.
     * - false -> no fue incluido en el envío → 'SinEnviar'.
     */
    public function updateStatus(RipsPatientService $service, ?bool $included = null): void
    {
        $document = $service->billingDocument;

        // Sin documento de cobro → Incompleto
        if (!$document) {
            $this->persistIfChanged($service, self::STATUS_INCOMPLETO);
            return;
        }

        if (is_null($included)) {
            // Creación/edición → según datos mínimos
            $newStatus = $this->hasMinimumData($service)
                ? self::STATUS_LISTO
                : self::STATUS_INCOMPLETO;

            $this->persistIfChanged($service, $newStatus);
            return;
        }

        if ($included === true) {
            // Incluido en el envío → mapea resultado del documento (multilenguaje)
            $docStatus = mb_strtolower((string) $document->submission_status);

            if (in_array($docStatus, ['accepted', 'aceptado'], true)) {
                $this->persistIfChanged($service, self::STATUS_ACEPTADO);
                return;
            }

            if (in_array($docStatus, ['rejected', 'rechazado'], true)) {
                $this->persistIfChanged($service, self::STATUS_RECHAZADO);
                return;
            }

            // Si el documento quedó en otro estado (p. ej. pending), lo dejamos como Listo
            $this->persistIfChanged($service, self::STATUS_LISTO);
            return;
        }

        // No incluido en el envío → SinEnviar
        $this->persistIfChanged($service, self::STATUS_SIN_ENVIAR);
    }

    /**
     * Guarda el estado solo si cambia.
     */
    private function persistIfChanged(RipsPatientService $service, int $newStatus): void
    {
        if ($service->status_id !== $newStatus) {
            $old = $service->status_id;
            $service->status_id = $newStatus;
            $service->save();

            if (app()->environment('local')) {
                Log::info("Service [ID {$service->id}] status changed", [
                    'from' => $old,
                    'to'   => $newStatus,
                ]);
            }
        }
    }

    /**
     * Comprueba si el servicio tiene los datos mínimos para quedar 'Listo'.
     * Reglas:
     * - Debe tener patient_id y service_datetime.
     * - Debe tener billingDocument.
     * - Si el documento es factura (type_id === 1), debe tener XML (xml_path).
     */
    private function hasMinimumData(RipsPatientService $service): bool
    {
        if (app()->environment('local')) {
            Log::info('Validating service minimum data', [
                'service_id'      => $service->id,
                'patient_id'      => $service->patient_id,
                'service_datetime'=> $service->service_datetime,
            ]);
        }

        // Campos básicos
        if (!($service->patient_id && $service->service_datetime)) {
            return false;
        }

        // Documento de cobro
        $document = $service->billingDocument;
        if (!$document) {
            Log::warning("Service [ID {$service->id}] has no linked billing document.");
            return false;
        }

        if (app()->environment('local')) {
            Log::info('Linked billing document found', [
                'document_id'     => $document->id,
                'document_number' => $document->document_number,
                'type_id'         => $document->type_id,
                'xml_path'        => $document->xml_path,
            ]);
        }

        // Si es factura, requiere XML
        if ((int) $document->type_id === 1 && empty($document->xml_path)) {
            Log::warning("Invoice document without XML. Document ID: {$document->id}");
            return false;
        }

        return true;
    }
}
