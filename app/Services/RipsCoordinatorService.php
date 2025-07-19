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
    protected RipsGeneratorService $generatorService;
    protected RipsTokenService $tokenService;
    protected RipsSubmissionService $submissionService;

    /**
     * Constructor: Inyecta los servicios necesarios.
     */
    public function __construct(
        RipsGeneratorService $generatorService,
        RipsTokenService $tokenService
    ) {
        $this->generatorService = $generatorService;
        $this->tokenService = $tokenService;
    }

    /**
     * Método principal para procesar y enviar documentos RIPS.
     * Detecta automáticamente si es factura o nota.
     */
    public function procesarYEnviarRips(string $tenantId, int $agreementId, string $startDate, string $endDate): void
    {
        // Obtiene todos los documentos dentro del rango
        $documentos = RipsBillingDocument::where('tenant_id', $tenantId)
            ->where('agreement_id', $agreementId)
            ->whereBetween('issued_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ])
            ->get();

        // Agrupa por tipo: factura o nota
        $agrupadosPorTipo = $documentos->groupBy(fn($doc) => $doc->type_id == 1 ? 'factura' : 'nota');

        // Procesa facturas si existen
        if ($agrupadosPorTipo->has('factura')) {
            $this->procesarYEnviarGrupo($tenantId, $agreementId, $startDate, $endDate, true);
        }

        // Procesa notas si existen
        if ($agrupadosPorTipo->has('nota')) {
            $this->procesarYEnviarGrupo($tenantId, $agreementId, $startDate, $endDate, false);
        }
    }

    /**
     * Procesa y envía facturas o notas según el valor de $conFactura
     */
    protected function procesarYEnviarGrupo(string $tenantId, int $agreementId, string $startDate, string $endDate, bool $conFactura): void
    {
        // Obtiene token de autenticación
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

        // Genera RIPS para las facturas o notas
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

        // Instancia el servicio de envío
        $this->submissionService = new RipsSubmissionService($token);

        $resultados = [];

        // Recorre y envía cada factura o nota
        foreach ($facturas as $index => $factura) {
            $numero = $factura['rips']['numFactura'] ?? $factura['rips']['numNota'] ?? 'documento_' . $index;

            // Evita reenviar facturas aceptadas
            $documento = RipsBillingDocument::where('tenant_id', $tenantId)
                ->where('document_number', $numero)
                ->first();

            if ($documento?->submission_status === 'accepted') {
                continue; // Ya fue aceptado, no reenviar
            }

            Log::info("Enviando documento RIPS a SISPRO", [
                'numero' => $numero,
                'json_enviado' => $factura
            ]);


            // Envía el documento
            $respuesta = $this->submissionService->enviarFactura($factura, $conFactura);
            Log::info('Respuesta de la API SISPRO', ['numero' => $numero, 'respuesta' => $respuesta]);
            // Guarda archivo de respuesta
            $filename = "respuesta_rips_{$numero}_" . now()->format('Ymd_His') . '.json';
            Storage::put("respuestas/{$filename}", json_encode($respuesta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            Log::info("Archivo de respuesta guardado", ['archivo' => $filename]);
            // Determina estado: accepted / rejected / pending
            $estado = 'pending';
            if (isset($respuesta['response']['ResultState'])) {
                $estado = $respuesta['response']['ResultState'] === true ? 'accepted' : 'rejected';
            }

            Log::info("Estado evaluado para {$numero}", ['estado' => $estado]);

            // Actualiza el estado en la base de datos
            if ($documento) {
                Log::info("Actualizando estado de documento {$numero}", ['submission_status' => $estado]);
                $documento->update(['submission_status' => $estado]);
            } else {
                Log::warning("Documento no encontrado para actualizar estado", ['numero' => $numero]);
            }


            // Agrega al resumen
            $resultados[] = [
                'factura' => $numero,
                'success' => $respuesta['success'],
                'respuesta' => $respuesta['response'],
                'archivo' => $filename,
            ];
        }

        // Cuenta aceptados y rechazados
        $errores = collect($resultados)->where('success', false)->count();
        $exitos = collect($resultados)->where('success', true)->count();

        // Muestra notificación resumen
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
}
