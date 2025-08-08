<?php

/****************************************************************/
/* Module: RIPS Submission Service                              */
/* Author: Julian                                               */
/* Date: 2025-08-07                                             */
/* Description: Submits a single RIPS package (invoice or note) */
/*              to the SISPRO API and returns the processed     */
/*              response.                                       */
/****************************************************************/

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RipsSubmissionService
{
    /**
     * Base API URL for SISPRO.
     * Make sure it's configured in services.rips_api.url (env).
     */
    protected string $baseUrl;

    /**
     * Bearer token for the API.
     */
    protected string $token;

    /**
     * Initialize with token and config url.
     */
    public function __construct(string $token)
    {
        $this->baseUrl = (string) config('services.rips_api.url');
        $this->token = $token;
    }

    /**
     * Submit a single RIPS payload (invoice or note) to the API and return the response.
     *
     * @param array $ripsData RIPS JSON for a single billing document.
     * @param bool  $withInvoice True = invoice (CargarFevRips), False = note (CargarRipsSinFactura).
     * @return array{success:bool, response:array|null, message?:string}
     */
    public function submitDocument(array $ripsData, bool $withInvoice): array
    {
        if (app()->environment('local')) {
            Log::debug('Received in submitDocument()', [
                'withInvoice' => $withInvoice,
                'numFactura' => $ripsData['rips']['numFactura'] ?? null,
                'numNota' => $ripsData['rips']['numNota'] ?? null,
                // Avoid logging the full payload in prod; only in local for debugging.
                'payload_keys' => array_keys($ripsData),
            ]);
        }

        // Decide endpoint based on invoice/note
        $endpoint = $withInvoice
            ? '/PaquetesFevRips/CargarFevRips'
            : '/PaquetesFevRips/CargarRipsSinFactura';

        $url = rtrim($this->baseUrl, '/') . $endpoint;

        // If it's a note, adapt JSON (numNota/tipoNota) and nullify numFactura
        if (!$withInvoice) {
            $originalNumber = $ripsData['rips']['numFactura'] ?? ($ripsData['rips']['numNota'] ?? null);
            $ripsData['rips']['numNota'] = $originalNumber;
            $ripsData['rips']['tipoNota'] = 'RS';
            $ripsData['rips']['numFactura'] = null;
        }

        // Remove idRelacion if present (should not be sent)
        unset($ripsData['rips']['idRelacion']);

        if (app()->environment('local')) {
            Log::debug('Before POST to SISPRO', [
                'endpoint' => $url,
                'numFactura' => $ripsData['rips']['numFactura'] ?? null,
                'numNota' => $ripsData['rips']['numNota'] ?? null,
                'tipoNota' => $ripsData['rips']['tipoNota'] ?? null,
            ]);
        }

        $response = Http::withoutVerifying() // ignore local SSL issues
            ->withToken($this->token)
            ->acceptJson()
            ->timeout(60)
            ->post($url, $ripsData);

        if ($response->failed()) {
            Log::error('RIPS submission failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'numFactura' => $ripsData['rips']['numFactura'] ?? null,
                'numNota' => $ripsData['rips']['numNota'] ?? null,
            ]);

            return [
                'success' => false,
                'message' => 'API communication error',
                'response' => $response->json(),
            ];
        }

        return [
            'success' => true,
            'response' => $response->json(),
        ];
    }
}
