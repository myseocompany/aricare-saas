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
        // Obtiene el token de autenticación
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

        $this->submissionService = new RipsSubmissionService($token);
        $resultados = [];

        foreach ($facturas as $index => $factura) {
            $numero = $factura['rips']['numFactura'] ?? $factura['rips']['numNota'] ?? 'documento_' . $index;

            $documento = RipsBillingDocument::where('tenant_id', $tenantId)
                ->where('document_number', $numero)
                ->first();

            $respuesta = $this->submissionService->enviarFactura($factura, $conFactura);

            $filename = "respuesta_rips_{$numero}_" . now()->format('Ymd_His') . '.json';
            Storage::put("respuestas/{$filename}", json_encode($respuesta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            $estado = 'pending';
            if (isset($respuesta['response']['ResultState'])) {
                $estado = $respuesta['response']['ResultState'] === true ? 'accepted' : 'rejected';
            }

            if ($documento) {
                $documento->update(['submission_status' => $estado]);
            }

            $resultados[] = [
                'factura' => $numero,
                'success' => $respuesta['success'],
                'respuesta' => $respuesta['response'],
                'archivo' => $filename,
            ];
        }

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
