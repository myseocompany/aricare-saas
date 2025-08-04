<?php

namespace App\Services;

use App\Models\Rips\RipsPatientService;

class RipsPatientServiceStatusUpdater
{
    /**
     * Evalúa y actualiza el estado de un servicio.
     */
    public function actualizarEstado(RipsPatientService $servicio, bool $fueIncluido = false): void
    {
        $documento = $servicio->billingDocument;

        if (!$documento) {
            $servicio->status_id = 1; // Incompleto (sin documento asociado)
            $servicio->save();
            return;
        }

        if (is_null($fueIncluido)) {
        // Modo creación/edición → evaluar si está completo o no
            $servicio->status_id = $this->datosCompletos($servicio) ? 2 : 1; // 2: Listo, 1: Incompleto
        } elseif ($fueIncluido === true) {
            // Fue enviado → evaluar respuesta
            if ($documento->submission_status === 'accepted') {
                $servicio->status_id = 4; // Aceptado
            } elseif ($documento->submission_status === 'rejected') {
                $servicio->status_id = 5; // Rechazado
            } else {
                $servicio->status_id = 2; // Listo
            }
        } else {
            // No fue incluido en el envío → SinEnviar
            $servicio->status_id = 3; // SinEnviar
        }

        $servicio->save();
    }




    /*protected function servicioIncluidoEnFactura($servicio, $factura): bool
    {
        return collect($factura['consultas'] ?? [])->pluck('id')->contains($servicio->id)
            || collect($factura['procedimientos'] ?? [])->pluck('id')->contains($servicio->id);
    }*/

    protected function servicioIncluidoEnFactura(RipsPatientService $servicio, array $factura): bool
    {
        $serviciosEnviados = collect($factura['rips']['usuarios'] ?? [])
            ->flatMap(fn ($usuario) => collect($usuario['servicios'] ?? []))
            ->pluck('id')
            ->filter()
            ->unique();

        return $serviciosEnviados->contains($servicio->id);
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
