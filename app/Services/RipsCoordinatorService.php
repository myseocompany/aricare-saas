<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;
use App\Services\RipsTokenService;
use App\Services\RipsGeneratorService;
use App\Services\RipsSubmissionService;

class RipsCoordinatorService
{
    protected RipsGeneratorService $generatorService;
    protected RipsTokenService $tokenService;
    protected RipsSubmissionService $submissionService;

    /**
     * Constructor: inyectamos los servicios necesarios.
     */
    public function __construct(
        RipsGeneratorService $generatorService,
        RipsTokenService $tokenService
    ) {
        $this->generatorService = $generatorService;
        $this->tokenService = $tokenService;
    }

    /**
     * Procesa y envía individualmente cada factura RIPS.
     *
     * @param string $tenantId ID del tenant (UUID) para obtener credenciales.
     * @param int $agreementId ID del convenio.
     * @param string $startDate Fecha inicial (formato Y-m-d).
     * @param string $endDate Fecha final (formato Y-m-d).
     * @param bool $conFactura Indica si se trata de facturas (true) o notas (false).
     */
    public function procesarYEnviarRips(string $tenantId, int $agreementId, string $startDate, string $endDate, bool $conFactura = true): void
    {
        // 1. Obtener el token desde el servicio
        $token = $this->tokenService->obtenerToken($tenantId);

        if (!$token) {
            Notification::make()
                ->title('Error de autenticación')
                ->body("No se pudo obtener el token de autenticación para el tenant ID: {$tenantId}")
                ->danger()
                ->persistent()
                ->send();
            return;
        }

        // 2. Generar los RIPS agrupados por factura
        $facturas = $this->generatorService->previsualizarRipsPorFactura($agreementId, $startDate, $endDate, $conFactura);

        if (empty($facturas)) {
            Notification::make()
                ->title('Sin resultados')
                ->body('No se encontraron facturas RIPS para enviar.')
                ->danger()
                ->persistent()
                ->send();
            return;
        }

        // 3. Instanciar el servicio de envío
        $this->submissionService = new RipsSubmissionService($token);

        $resultados = [];

        // 4. Recorrer y enviar cada factura individualmente
        foreach ($facturas as $index => $factura) {
            $respuesta = $this->submissionService->enviarFactura($factura, $conFactura);

            $numero = $factura['rips']['numFactura'] ?? $factura['rips']['numNota'] ?? 'factura_' . $index;
            $filename = "respuesta_rips_{$numero}_" . now()->format('Ymd_His') . '.json';

            Storage::put("respuestas/{$filename}", json_encode($respuesta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            $resultados[] = [
                'factura' => $numero,
                'success' => $respuesta['success'],
                'respuesta' => $respuesta['response'],
                'archivo' => $filename,
            ];
        }

        // 5. Mostrar resumen con enlaces de respuesta
        $errores = collect($resultados)->where('success', false)->count();
        $exitos = collect($resultados)->where('success', true)->count();

        $body = "Facturas exitosas: $exitos<br>Errores: $errores<br><br>";
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
