<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RipsGeneratorService;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use App\Models\Rips\RipsPatientService;


class RipsGeneracionController extends Controller
{
    /**
     * Ejecuta la generación de RIPS desde la sesión después de confirmar.
     */
    public function confirmarGeneracion()
    {
        Log::info('📥 Ejecutando ruta /rips/confirmar-generacion');

        // Obtenemos los IDs de servicios previamente seleccionados y confirmados por el usuario
        $ids = session('rips_servicios_seleccionados', []);
        Log::info('📦 IDs en sesión: ', $ids);

        // Si no hay nada en sesión, abortamos
        if (empty($ids)) {
            abort(404, 'No hay servicios seleccionados.');
        }

        // Cargamos los servicios con todas sus relaciones necesarias
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

        // Generamos la estructura JSON
        
        $service = app(\App\Services\RipsGeneratorService::class);
        $ripsData = $service->buildRipsFromSelectedServices($patientServices);


        // Nombre del archivo
        $nombreArchivo = 'rips_' . now()->format('Ymd_His') . '.json';

        // Codificamos el contenido JSON
        $contenido = json_encode($ripsData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        // Devolvemos una respuesta con encabezado para descargar directamente
        return response($contenido)
            ->header('Content-Type', 'application/json')
            ->header('Content-Disposition', 'attachment; filename="' . $nombreArchivo . '"');
    }

}
