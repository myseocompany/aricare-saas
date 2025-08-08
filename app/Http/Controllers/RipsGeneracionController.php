<?php

/****************************************************************/
/* Module: RIPS Generation Controller                            */
/* Author: Julian                                               */
/* Date: 2025-08-08                                             */
/* Description: Confirms RIPS JSON generation (download) and    */
/*              submission (send to SISPRO) flows.              */
/****************************************************************/

namespace App\Http\Controllers;

use App\Models\Rips\RipsPatientService;
use App\Services\RipsCoordinatorService;
use App\Services\RipsGeneratorService;
use Illuminate\Support\Facades\Log;

class RipsGeneracionController extends Controller
{
    /**
     * Confirm JSON generation (download) for selected services stored in session.
     * Session flags used:
     * - rips_confirmado
     * - rips_servicios_seleccionados
     * - rips_servicios_incluidos (cleared after building JSON)
     */
    public function confirmarGeneracion()
    {
        if (app()->environment('local')) {
            Log::info('GET /rips/confirmar-generacion');
        }

        // Mark that user confirmed the warning step
        session(['rips_confirmado' => true]);

        $ids = session('rips_servicios_seleccionados', []);
        if (empty($ids)) {
            abort(404, 'No hay servicios seleccionados.');
        }

        $patientServices = RipsPatientService::with([
            'billingDocument',
            'patient.user',
            'consultations.diagnoses.cie10',
            'consultations.cups',
            'consultations.serviceGroup',
            'consultations.service',
            'consultations.technologyPurpose',
            'consultations.collectionConcept',
            'procedures.cups',
            'procedures.cie10',
            'procedures.surgeryCie10',
            'procedures.admissionRoute',
            'procedures.serviceGroup',
            'procedures.service',
            'procedures.technologyPurpose',
            'procedures.collectionConcept',
            'doctor.user',
        ])->whereIn('id', $ids)->get();

        $service  = app(RipsGeneratorService::class);
        $ripsData = $service->buildRipsFromSelectedServices($patientServices);

        $filename = 'rips_' . now()->format('Ymd_His') . '.json';
        $content  = json_encode($ripsData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        // Cleanup session
        session()->forget(['rips_confirmado', 'rips_servicios_seleccionados', 'rips_servicios_incluidos']);

        return response($content)
            ->header('Content-Type', 'application/json')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Confirm JSON submission (send) for selected services stored in session.
     * It does not download; it delegates to the coordinator to submit.
     */
    public function confirmarEnvio()
    {
        if (app()->environment('local')) {
            Log::info('GET /rips/confirmar-envio');
        }

        // Mark that user confirmed the warning step
        session(['rips_confirmado' => true]);

        $ids = session('rips_servicios_seleccionados', []);
        if (empty($ids)) {
            abort(404, 'No hay servicios seleccionados para enviar.');
        }

        $patientServices = RipsPatientService::with([
            'billingDocument',
            'patient.user',
            'consultations.diagnoses.cie10',
            'consultations.cups',
            'consultations.serviceGroup',
            'consultations.service',
            'consultations.technologyPurpose',
            'consultations.collectionConcept',
            'procedures.cups',
            'procedures.cie10',
            'procedures.surgeryCie10',
            'procedures.admissionRoute',
            'procedures.serviceGroup',
            'procedures.service',
            'procedures.technologyPurpose',
            'procedures.collectionConcept',
            'doctor.user',
        ])->whereIn('id', $ids)->get();

        $coordinator = app(RipsCoordinatorService::class);
        $tenantId    = auth()->user()->tenant_id;

        $coordinator->submitFromSelection($patientServices, $tenantId);

        // Cleanup session
        session()->forget(['rips_confirmado', 'rips_servicios_seleccionados', 'rips_servicios_incluidos']);

        return redirect()->back();
    }
}
