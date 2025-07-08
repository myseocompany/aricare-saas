<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Tenant;

class RipsTokenService
{
    /**
     * Obtiene el token de autenticación desde la API SISPRO.
     *
     * @param string $tenantId ID del tenant (UUID).
     * @return string|null Token obtenido desde la API o null si falla.
     */
    public function obtenerToken(string $tenantId): ?string
    {
        // Buscar los datos necesarios en la tabla tenants
        $tenant = Tenant::find($tenantId);

        if (!$tenant) {
            Log::error("No se encontró el tenant con ID: {$tenantId}");
            return null;
        }

        // Validar que todos los campos necesarios estén presentes
        if (
            empty($tenant->document_type) ||
            empty($tenant->rips_idsispro) ||
            empty($tenant->document_number) ||
            empty($tenant->rips_passispro)
        ) {
            Log::error("Campos incompletos para autenticación RIPS en tenant ID: {$tenantId}", [
                'document_type' => $tenant->document_type,
                'rips_idsispro' => $tenant->rips_idsispro,
                'document_number' => $tenant->document_number,
                'rips_passispro' => $tenant->rips_passispro,
            ]);
            return null;
        }

        // Construir el cuerpo del JSON que espera la API SISPRO
        $payload = [
            'persona' => [
                'identificacion' => [
                    'tipo' => $tenant->document_type,
                    'numero' => $tenant->rips_idsispro,
                ]
            ],
            'nit' => $tenant->document_number,
            'clave' => $tenant->rips_passispro,
        ];

        try {
            $response = Http::withoutVerifying() // Para ignorar errores de SSL locales
                ->timeout(30)
                ->acceptJson()
                ->post(config('services.rips_api.url') . '/auth/LoginSISPRO', $payload);

            if (!$response->successful()) {
                Log::error('Error al obtener token RIPS', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            $data = $response->json();
            return $data['token'] ?? null;
        } catch (\Exception $e) {
            Log::error('Excepción al obtener token RIPS', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
