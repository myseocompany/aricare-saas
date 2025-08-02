<?php

namespace App\Services;

use App\Models\Rips\RipsPatientService;

class RipsPatientServiceStatusUpdater
{
    /**
     * Evalúa y actualiza el estado de un servicio.
     */
    public function actualizarEstado(RipsPatientService $servicio, ?array $facturaJson = null): void
    {
        $documento = $servicio->billingDocument;

        if (!$documento) {
            $servicio->status_id = 1; // Incompleto (no tiene documento asociado)
            $servicio->save();
            return;
        }

        // 🟡 Si el estado es 'accepted' o 'rejected' lo respetamos
        if ($documento->submission_status === 'accepted') {
            $servicio->status_id = 4; // Aceptado
        } elseif ($documento->submission_status === 'rejected') {
            $servicio->status_id = 5; // Rechazado
        }
        // 📦 Si se pasó el JSON de la factura (se está enviando un grupo seleccionado)
        elseif ($facturaJson && !$this->servicioIncluidoEnFactura($servicio, $facturaJson)) {
            $servicio->status_id = 3; // SinEnviar
        }
        // ❌ Si es factura y falta XML
        elseif ($documento->type_id === 1 && (empty($documento->xml_path) || !file_exists(storage_path('app/public/' . $documento->xml_path)))) {
            $servicio->status_id = 1; // Incompleto
        }
        // 🔍 Validación de datos del servicio
        elseif (!$this->datosCompletos($servicio)) {
            $servicio->status_id = 1; // Incompleto
        }
        else {
            $servicio->status_id = 2; // Listo para enviar
        }

        $servicio->save();
    }

    protected function servicioIncluidoEnFactura($servicio, $factura): bool
    {
        return collect($factura['consultas'] ?? [])->pluck('id')->contains($servicio->id)
            || collect($factura['procedimientos'] ?? [])->pluck('id')->contains($servicio->id);
    }


    /**
     * Evalúa si el servicio tiene los datos mínimos completos
     */
    private function datosCompletos(RipsPatientService $servicio): bool
    {
        // Valida campos claves que debe tener un servicio para considerarse completo
        return $servicio->patient_id && $servicio->cups_code && $servicio->service_datetime;
    }
}
