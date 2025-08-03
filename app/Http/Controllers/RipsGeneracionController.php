<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RipsGeneratorService;
use App\Services\RipsCoordinatorService;
use App\Models\Rips\RipsPatientService;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;

class RipsGeneracionController extends Controller
{
    /**
     * 🧾 Confirmación para GENERAR el JSON (descargar)
     */
    public function confirmarGeneracion()
    {
        Log::info('📥 Ejecutando ruta /rips/confirmar-generacion');

        // ✅ Marca que el usuario confirmó la advertencia
        session(['rips_confirmado' => true]);

        $ids = session('rips_servicios_seleccionados', []);
        Log::info('📦 IDs en sesión: ', $ids);

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

        $service = app(RipsGeneratorService::class);
        $ripsData = $service->buildRipsFromSelectedServices($patientServices);

        $nombreArchivo = 'rips_' . now()->format('Ymd_His') . '.json';
        $contenido = json_encode($ripsData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return response($contenido)
            ->header('Content-Type', 'application/json')
            ->header('Content-Disposition', 'attachment; filename="' . $nombreArchivo . '"');
    }

    /**
     * 📤 Confirmación para ENVIAR el JSON (no descarga)
     */
    public function confirmarEnvio()
    {
        Log::info('📥 Ejecutando ruta /rips/confirmar-envio');

        // ✅ Marca que el usuario confirmó la advertencia
        session(['rips_confirmado' => true]);

        $ids = session('rips_servicios_seleccionados', []);
        Log::info('📦 IDs en sesión para envío: ', $ids);

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
        $tenantId = auth()->user()->tenant_id;

        $coordinator->enviarDesdeSeleccion($patientServices, $tenantId);

        return redirect()->back(); // O a donde quieras redirigir después del envío
    }
}
