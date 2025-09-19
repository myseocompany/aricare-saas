<?php

/****************************************************************/
/* Module: RIPS Token Service                                   */
/* Author: Julian                                               */
/* Date: 2025-08-07                                             */
/* Description: Retrieves an authentication token from the      */
/*              SISPRO API using tenant credentials.            */
/****************************************************************/

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RipsTokenService
{
    /**
     * Get the SISPRO authentication token for a given tenant.
     *
     * @param string $tenantId Tenant UUID.
     * @return string|null Token string or null on failure.
     */
    public function getToken(string $tenantId): ?string
    {
        // Load tenant credentials
        $tenant = Tenant::find($tenantId);

        if (app()->environment('local')) {
            Log::info('Tenant loaded for RIPS token request', [
                'tenant_id' => $tenantId,
                'exists' => (bool) $tenant,
            ]);
        }

        if (!$tenant) {
            Log::error("Tenant not found", ['tenant_id' => $tenantId]);
            return null;
        }

        $typeCode = null;
        if (!empty($tenant->rips_identification_type_id)) {
            $typeCode = DB::table('rips_identification_types')
                ->where('id', $tenant->rips_identification_type_id)
                ->value('code');
        }

        // Validate required fields (avoid logging secrets)
        $missing = [];
        if (empty($typeCode))   { $missing[] = 'rips_identification_type_id (code)'; }
        if (empty($tenant->rips_idsispro))   { $missing[] = 'rips_idsispro'; }
        if (empty($tenant->rips_identification_number)) { $missing[] = 'rips_identification_number'; }
        if (empty($tenant->rips_passispro))  { $missing[] = 'rips_passispro'; } // see note below

        if (!empty($missing)) {
            Log::error('Missing RIPS auth fields for tenant', [
                'tenant_id' => $tenantId,
                'missing' => $missing,
            ]);
            return null;
        }

        // Build request payload (do not log password)
        $payload = [
            'persona' => [
                'identificacion' => [
                    'tipo'   => $typeCode,
                    'numero' => $tenant->rips_idsispro,
                ],
            ],
            'nit'   => $tenant->rips_identification_number,
            'clave' => $tenant->rips_passispro,
        ];

        $baseUrl = rtrim((string) config('services.rips_api.url'), '/');
        $url = $baseUrl . '/auth/LoginSISPRO';

        try {
            if (app()->environment('local')) {
                Log::info('Requesting SISPRO token', [
                    'url' => $url,
                    // Never log secrets; show only non-sensitive keys
                    'payload_keys' => [
                        'persona.identificacion.tipo' => (bool) $typeCode,
                        'persona.identificacion.numero' => (bool) $tenant->rips_idsispro,
                        'nit' => (bool) $tenant->rips_identification_number,
                        'clave_present' => !empty($tenant->rips_passispro),
                    ],
                ]);
            }

            $response = Http::withoutVerifying() // ignore local SSL issues
                ->timeout(30)
                ->acceptJson()
                ->post($url, $payload);

            if (app()->environment('local')) {
                Log::info('SISPRO token raw response', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }

            if (!$response->successful()) {
                Log::error('Failed to obtain RIPS token', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            $data = $response->json();
            return $data['token'] ?? null;
        } catch (\Throwable $e) {
            Log::error('Exception while obtaining RIPS token', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
