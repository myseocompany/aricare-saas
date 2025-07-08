<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RipsSubmissionService
{
    /**
     * URL base de la API a la cual se enviarán los RIPS.
     * Asegúrate de colocar esta URL en tu archivo .env y configurarla correctamente.
     */
    protected string $baseUrl;

    /**
     * Token de autenticación para la API.
     */
    protected string $token;

    /**
     * Constructor que inicializa el servicio con la URL y token.
     */
    public function __construct(string $token)
    {
        $this->baseUrl = config('services.rips_api.url'); // Usa una variable de entorno
        $this->token = $token;
    }

    /**
     * Envía una sola factura RIPS a la API y obtiene la respuesta.
     *
     * @param array $ripsData JSON generado para una sola factura.
     * @param bool $conFactura Indica si el RIPS incluye factura (true) o es una nota (false).
     * @return array Respuesta procesada de la API.
     */
    public function enviarFactura(array $ripsData, bool $conFactura): array
    {
        // Determinar la URL de envío según si es con o sin factura
        $endpoint = $conFactura ? '/PaquetesFevRips/CargarFevRips' : '/PaquetesFevRips/CargarRipsSinFactura';
        $url = $this->baseUrl . $endpoint;

        // Si no es con factura, adaptamos el JSON como nota (numNota y tipoNota)
        if (!$conFactura) {
            $ripsData['rips']['numNota'] = $ripsData['rips']['numFactura'];
            $ripsData['rips']['tipoNota'] = 'RS';
            $ripsData['rips']['numFactura'] = null;
        }

        // Eliminamos 'idRelacion' si existe, porque no se debe enviar
        unset($ripsData['rips']['idRelacion']);

        // Enviamos la solicitud HTTP con el token de autorización
        $response = Http::withToken($this->token)
            ->acceptJson()
            ->timeout(60) // Tiempo máximo de espera en segundos
            ->post($url, $ripsData);

        // Manejo de errores si la API no responde correctamente
        if ($response->failed()) {
            Log::error('Error al enviar RIPS', [
                'status' => $response->status(),
                'body' => $response->body(),
                'ripsData' => $ripsData,
            ]);

            return [
                'success' => false,
                'message' => 'Error de comunicación con la API',
                'response' => $response->json()
            ];
        }

        // Retornamos la respuesta en formato arreglo
        return [
            'success' => true,
            'response' => $response->json()
        ];
    }
}
