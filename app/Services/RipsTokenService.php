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


    // ⬇⬇⬇ Añadir dentro de la clase RipsTokenService ⬇⬇⬇
    // ⬇⬇ Dentro de App\Services\RipsTokenService ⬇⬇
    public function probe(string $tenantId): array
    {
        $tenant = \App\Models\Tenant::find($tenantId);
        if (!$tenant) {
            return [
                'ok'               => false,
                'status'           => null,
                'url'              => null,
                'payload_keys'     => [],
                'payload_preview'  => [],
                'payload_json'     => null,
                'login'            => null,
                'registrado'       => null,
                'errors'           => null,
                'token_present'    => false,
                'token_masked'     => null,
                'raw_body'         => null,
                'error'            => 'Tenant no encontrado',
            ];
        }

        // === Resolver typeCode exactamente como en getToken() ===
        $typeCode = null;
        if (!empty($tenant->rips_identification_type_id)) {
            $typeCode = \Illuminate\Support\Facades\DB::table('rips_identification_types')
                ->where('id', $tenant->rips_identification_type_id)
                ->value('code');
        }

        // === Resolver los mismos campos que usas en getToken() ===
        $numero = $tenant->rips_idsispro;                // persona.identificacion.numero
        $nit    = $tenant->rips_identification_number;   // nit
        $clave  = $tenant->rips_passispro;               // clave

        // Para mostrar sin exponer secretos
        $claveMasked = empty($clave) ? null : ('[masked]…' . substr($clave, -4));

        // Preview legible de lo que se envía
        $payloadPreview = [
            'tipo'         => $typeCode,
            'numero'       => $numero,
            'nit'          => $nit,
            'clave_masked' => $claveMasked,
        ];

        // Payload real que se postea (sin loguear la clave en claro)
        $payload = [
            'persona' => [
                'identificacion' => [
                    'tipo'   => $typeCode,
                    'numero' => $numero,
                ],
            ],
            'nit'   => $nit,
            'clave' => $clave,
        ];

        // También te retorno un JSON “safe” para ver la forma exacta sin exponer la clave
        $payloadSafe = $payload;
        $payloadSafe['clave'] = $claveMasked;

        $payloadKeys = [
            'persona.identificacion.tipo'   => !empty($typeCode),
            'persona.identificacion.numero' => !empty($numero),
            'nit'                           => !empty($nit),
            'clave_present'                 => !empty($clave),
        ];

        $baseUrl = rtrim((string) config('services.rips_api.url'), '/');
        $url     = $baseUrl . '/auth/LoginSISPRO';

        try {
            $response = \Illuminate\Support\Facades\Http::withoutVerifying()
                ->timeout(30)
                ->acceptJson()
                ->post($url, $payload);

            $status  = $response->status();
            $rawBody = $response->body();

            // Si no es JSON válido, $data será []
            $data = [];
            try {
                $data = $response->json();
            } catch (\Throwable $e) {
                $data = [];
            }

            $token = is_array($data) ? ($data['token'] ?? null) : null;
            $tokenMasked = ($token && is_string($token)) ? ('[masked]…' . substr($token, -8)) : null;

            return [
                'ok'              => $response->successful() && !empty($token),
                'status'          => $status,
                'url'             => $url,
                'payload_keys'    => $payloadKeys,
                'payload_preview' => $payloadPreview,
                'payload_json'    => json_encode($payloadSafe, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
                'login'           => $data['login'] ?? null,
                'registrado'      => $data['registrado'] ?? null,
                'errors'          => $data['errors'] ?? null,
                'token_present'   => (bool) $token,
                'token_masked'    => $tokenMasked,
                'raw_body'        => $rawBody,
                'error'           => null,
            ];
        } catch (\Throwable $e) {
            return [
                'ok'              => false,
                'status'          => null,
                'url'             => $url,
                'payload_keys'    => $payloadKeys,
                'payload_preview' => $payloadPreview,
                'payload_json'    => json_encode($payloadSafe, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
                'login'           => null,
                'registrado'      => null,
                'errors'          => null,
                'token_present'   => false,
                'token_masked'    => null,
                'raw_body'        => null,
                'error'           => $e->getMessage(),
            ];
        }
    }

}
