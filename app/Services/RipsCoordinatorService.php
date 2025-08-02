<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;
use App\Services\RipsTokenService;
use App\Services\RipsGeneratorService;
use App\Services\RipsSubmissionService;
use App\Models\Rips\RipsBillingDocument;
use Carbon\Carbon;

class RipsCoordinatorService
{
    // Servicios que se inyectan en el constructor
    protected RipsGeneratorService $generatorService;
    protected RipsTokenService $tokenService;
    protected RipsSubmissionService $submissionService;

    /**
     * Constructor del coordinador.
     * Recibe los servicios necesarios para:
     * - generar el JSON RIPS,
     * - obtener el token de autenticación,
     * - enviar el RIPS (se crea en tiempo de ejecución).
     */
    public function __construct(
        RipsGeneratorService $generatorService,
        RipsTokenService $tokenService
    ) {
        $this->generatorService = $generatorService;
        $this->tokenService = $tokenService;
    }

    /**
     * Procesa y envía automáticamente los documentos RIPS (facturas o notas),
     * agrupados por tipo, dentro de un rango de fechas.
     */
    public function procesarYEnviarRips(string $tenantId, int $agreementId, string $startDate, string $endDate): void
    {
        // Busca todos los documentos de ese tenant, convenio y fechas
        $documentos = RipsBillingDocument::where('tenant_id', $tenantId)
            ->where('agreement_id', $agreementId)
            ->whereBetween('issued_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ])
            ->get();

        // Agrupa por tipo de documento: factura (1) o nota (cualquier otro)
        $agrupadosPorTipo = $documentos->groupBy(fn($doc) => $doc->type_id == 1 ? 'factura' : 'nota');

        // Procesa facturas (si existen)
        if ($agrupadosPorTipo->has('factura')) {
            $this->procesarYEnviarGrupo($tenantId, $agreementId, $startDate, $endDate, true);
        }

        // Procesa notas (si existen)
        if ($agrupadosPorTipo->has('nota')) {
            $this->procesarYEnviarGrupo($tenantId, $agreementId, $startDate, $endDate, false);
        }
    }

    /**
     * Procesa y envía un grupo completo de facturas o notas.
     */
    protected function procesarYEnviarGrupo(string $tenantId, int $agreementId, string $startDate, string $endDate, bool $conFactura): void
    {
        // Intenta obtener el token para ese tenant
        $token = $this->tokenService->obtenerToken($tenantId);

        if (!$token) {
            Notification::make()
                ->title('Error de autenticación')
                ->body("No se pudo obtener el token para el tenant {$tenantId}")
                ->danger()
                ->persistent()
                ->send();
            return;
        }

        // Genera el JSON RIPS para el grupo
        $facturas = $this->generatorService->previsualizarRipsPorFactura($agreementId, $startDate, $endDate, $conFactura);

        if (empty($facturas)) {
            Notification::make()
                ->title('Sin resultados')
                ->body('No se encontraron datos RIPS.')
                ->danger()
                ->persistent()
                ->send();
            return;
        }

        // Instancia el servicio de envío con el token
        $this->submissionService = new RipsSubmissionService($token);

        $resultados = [];

        // Recorre cada factura o nota y la envía individualmente
        foreach ($facturas as $index => $factura) {
            $numero = $factura['rips']['numFactura'] ?? $factura['rips']['numNota'] ?? 'documento_' . $index;

            // Verifica si ya fue aceptada anteriormente
            $documento = RipsBillingDocument::where('tenant_id', $tenantId)
                ->where('document_number', $numero)
                ->first();

            if ($documento?->submission_status === 'accepted') {
                continue; // No se reenvía si ya fue aceptado
            }

            // Envía el documento a SISPRO
            $respuesta = $this->submissionService->enviarFactura($factura, $conFactura);

            // Guarda el archivo de respuesta como respaldo
            $filename = "respuesta_rips_{$numero}_" . now()->format('Ymd_His') . '.json';
            Storage::put("respuestas/{$filename}", json_encode($respuesta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            // Determina el estado (accepted, rejected o pending)
            $estado = 'pending';
            if (isset($respuesta['response']['ResultState'])) {
                $estado = $respuesta['response']['ResultState'] === true ? 'accepted' : 'rejected';
            }

            // Actualiza el estado en base de datos
            if ($documento) {
                $documento->update(['submission_status' => $estado]);
            }

            // Guarda el resultado para la notificación
            $resultados[] = [
                'factura' => $numero,
                'success' => $respuesta['success'],
                'respuesta' => $respuesta['response'],
                'archivo' => $filename,
            ];
        }

        // Construye notificación resumen para el usuario
        $errores = collect($resultados)->where('success', false)->count();
        $exitos = collect($resultados)->where('success', true)->count();

        $body = "Facturas exitosas: {$exitos}<br>Errores: {$errores}<br><br>";
        $body .= collect($resultados)->map(function ($r) {
            return "<strong>{$r['factura']}</strong>: <a href='" . asset("storage/respuestas/{$r['archivo']}") . "' target='_blank'>Ver respuesta</a>";
        })->implode("<br>");

        Notification::make()
            ->title('Resultado del envío de RIPS')
            ->body($body)
            ->success()
            ->persistent()
            ->send();
    }

    /**
     * Procesa y envía un grupo específico de facturas (ya generadas),
     * ideal para envío manual desde selección del usuario.
     */
    public function procesarYEnviarGrupoManual(string $tenantId, int $agreementId, string $startDate, string $endDate, bool $conFactura, array $facturas): void
    {
        // 🔐 Intenta obtener el token de autenticación desde SISPRO para este tenant
        $token = $this->tokenService->obtenerToken($tenantId);

        // ⚠️ Si no obtiene token, muestra una notificación de error y detiene todo
        if (!$token) {
            Notification::make()
                ->title('Error de autenticación')
                ->body("No se pudo obtener el token para el tenant {$tenantId}")
                ->danger()
                ->persistent()
                ->send();
            return;
        }

        // 🛠️ Preparamos el servicio que se encargará de enviar los RIPS con ese token
        $this->submissionService = new RipsSubmissionService($token);

        // 🗃️ Aquí se irán guardando los resultados del envío de cada factura
        $resultados = [];

        // ✅ Recorremos cada factura que fue generada en el JSON
        foreach ($facturas as $index => $factura) {
            // 🧾 Obtenemos el número del documento (factura o nota)
            $numero = $factura['rips']['numFactura'] ?? $factura['rips']['numNota'] ?? 'documento_' . $index;

            // 🔎 Buscamos ese documento en la base de datos
            $documento = RipsBillingDocument::where('tenant_id', $tenantId)
                ->where('document_number', $numero)
                ->first();

            // 🚀 Enviamos el documento a la API SISPRO (puede ser factura o nota)
            $respuesta = $this->submissionService->enviarFactura($factura, $conFactura);

            // 💾 Guardamos una copia de la respuesta en el disco (respaldo)
            $filename = "respuesta_rips_{$numero}_" . now()->format('Ymd_His') . '.json';
            Storage::put("respuestas/{$filename}", json_encode($respuesta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            // 📌 Evaluamos el estado del envío según la respuesta de la API
            $estado = 'pending'; // por defecto
            if (isset($respuesta['response']['ResultState'])) {
                $estado = $respuesta['response']['ResultState'] === true ? 'accepted' : 'rejected';
            }

            // 📝 Actualizamos el estado del documento en la base de datos
            if ($documento) {
                $documento->update(['submission_status' => $estado]);

                // 🔁 Recorremos todos los servicios asociados a este documento
                $servicioUpdater = app(\App\Services\RipsPatientServiceStatusUpdater::class);
                foreach ($documento->patientServices as $servicio) {
                    // 📌 Verificamos si este servicio fue incluido en el JSON enviado
                    if ($this->servicioIncluidoEnFactura($servicio, $factura)) {
                        // ✅ Se envió, actualizamos con el resultado (aceptado o rechazado)
                        $servicioUpdater->actualizarEstado($servicio, $estado);
                    } else {
                        // ❗ No se envió (no fue seleccionado) => se marca como SinEnviar
                        $servicio->status_id = 3; // SinEnviar
                        $servicio->save();
                    }
                }
            }

            // 📊 Guardamos el resultado de esta factura para mostrar en la notificación final
            $resultados[] = [
                'factura' => $numero,
                'success' => $respuesta['success'],
                'respuesta' => $respuesta['response'],
                'archivo' => $filename,
            ];
        }

        // 📈 Contamos cuántas fueron exitosas y cuántas fallaron
        $errores = collect($resultados)->where('success', false)->count();
        $exitos = collect($resultados)->where('success', true)->count();

        // 📝 Construimos el mensaje del resumen para el usuario
        $body = "Facturas exitosas: {$exitos}<br>Errores: {$errores}<br><br>";
        $body .= collect($resultados)->map(function ($r) {
            return "<strong>{$r['factura']}</strong>: <a href='" . asset("storage/respuestas/{$r['archivo']}") . "' target='_blank'>Ver respuesta</a>";
        })->implode("<br>");

        // 📢 Mostramos la notificación resumen con enlaces a cada respuesta
        Notification::make()
            ->title('Resultado del envío de RIPS')
            ->body($body)
            ->success()
            ->persistent()
            ->send();
    }

    /**
     * 🔍 Verifica si un servicio RIPS (consulta o procedimiento) fue incluido
     * en el JSON de una factura enviada a SISPRO.
     *
     * Esto es útil para saber si un servicio fue realmente enviado o no,
     * ya que una factura puede tener varios servicios, pero quizás solo se seleccionaron algunos.
     *
     * @param  mixed  $servicio El servicio RIPS (consulta o procedimiento) que queremos verificar.
     * @param  array  $factura  El JSON completo de la factura enviada (incluye listas de consultas y procedimientos).
     * @return bool             Devuelve true si el servicio sí está incluido en esa factura.
     */
    protected function servicioIncluidoEnFactura($servicio, $factura): bool
    {
        // ✅ Recorremos la lista de consultas del JSON (si existen), y extraemos solo los ID
        $idsConsultas = collect($factura['consultas'] ?? [])->pluck('id');

        // ✅ Recorremos la lista de procedimientos del JSON (si existen), y extraemos solo los ID
        $idsProcedimientos = collect($factura['procedimientos'] ?? [])->pluck('id');

        // 🧠 Si el ID del servicio que estamos evaluando aparece en cualquiera de las dos listas, retornamos true
        return $idsConsultas->contains($servicio->id) || $idsProcedimientos->contains($servicio->id);
    }



    /**
     * Flujo completo cuando el usuario selecciona manualmente documentos desde la tabla.
     * - Genera el JSON solo con los seleccionados.
     * - Envía uno por uno, solo si no están aceptados.
     */
    public function enviarDesdeSeleccion(array $records, string $tenantId): void
    {
        // Limpia la sesión por si quedó un JSON anterior
        session()->forget('rips_json_generado');

        // Genera el JSON solo con los documentos seleccionados
        $jsonRips = $this->generatorService->generateOnlySelected(collect($records));

        // Si hay algún error y no se puede generar, detenemos el flujo
        if (is_null($jsonRips)) return;

        // Procesa y envía cada documento por separado
        foreach ($jsonRips as $factura) {
            $numero = $factura['rips']['numFactura'] ?? $factura['rips']['numNota'] ?? 'documento';

            $documento = RipsBillingDocument::where('tenant_id', $tenantId)
                ->where('document_number', $numero)
                ->first();

            if (!$documento || !$documento->agreement_id) continue;
            if ($documento->submission_status === 'accepted') continue;

            $start = optional($documento->patientServices)->pluck('service_datetime')->filter()->min();
            $end = optional($documento->patientServices)->pluck('service_datetime')->filter()->max();

            if (!$start || !$end) continue;

            // Envía el documento usando el grupo manual
            $this->procesarYEnviarGrupoManual(
                tenantId: $tenantId,
                agreementId: $documento->agreement_id,
                startDate: Carbon::parse($start)->format('Y-m-d'),
                endDate: Carbon::parse($end)->format('Y-m-d'),
                conFactura: $documento->type_id === 1,
                facturas: [$factura]
            );
        }

        // Limpia la sesión nuevamente al finalizar
        session()->forget('rips_json_generado');
    }
}
