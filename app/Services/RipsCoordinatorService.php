<?php

/****************************************************************/
/* Module: RIPS Coordinator Service                             */
/* Author: Julian                                               */
/* Date: 2025-08-07                                             */
/* Description: Orchestrates the full RIPS flow: generate JSON, */
/*              obtain token, submit each billing document,     */
/*              persist API responses, update DB statuses, and  */
/*              notify the user with a summary.                 */
/****************************************************************/

namespace App\Services;

use App\Models\Rips\RipsBillingDocument;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RipsCoordinatorService
{
    /** Services injected via constructor */
    protected RipsGeneratorService $generatorService;
    protected RipsTokenService $tokenService;

    /** Built on demand once token is available */
    protected RipsSubmissionService $submissionService;

    /**
     * Constructor.
     *
     * @param RipsGeneratorService $generatorService
     * @param RipsTokenService $tokenService
     */
    public function __construct(
        RipsGeneratorService $generatorService,
        RipsTokenService $tokenService,
    ) {
        $this->generatorService = $generatorService;
        $this->tokenService = $tokenService;
    }

    /**
     * Process and submit RIPS billing documents (invoices and notes) grouped by type,
     * within the given date range.
     */
    public function processAndSubmitRips(string $tenantId, int $agreementId, string $startDate, string $endDate): void
    {
        // Fetch all billing documents for tenant, agreement and date range
        $documents = RipsBillingDocument::where('tenant_id', $tenantId)
            ->where('agreement_id', $agreementId)
            ->whereBetween('issued_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ])
            ->get();

        // Group by type: invoice (1) or note (others)
        $groupedByType = $documents->groupBy(fn ($doc) => $doc->type_id === 1 ? 'invoice' : 'note');

        // Process invoices (if any)
        if ($groupedByType->has('invoice')) {
            $this->processAndSubmitGroup($tenantId, $agreementId, $startDate, $endDate, true);
        }

        // Process notes (if any)
        if ($groupedByType->has('note')) {
            $this->processAndSubmitGroup($tenantId, $agreementId, $startDate, $endDate, false);
        }
    }

    /**
     * Process and submit a full group of documents (invoices or notes).
     */
    protected function processAndSubmitGroup(string $tenantId, int $agreementId, string $startDate, string $endDate, bool $withInvoice): void
    {
        // Obtain token for this tenant
        $token = $this->tokenService->getToken($tenantId);

        if (!$token) {
            Notification::make()
                ->title('Error de autenticación')
                ->body("No se pudo obtener el token para el tenant {$tenantId}")
                ->danger()
                ->persistent()
                ->send();
            return;
        }

        // Modo solo autenticación
        if (env('RIPS_AUTH_ONLY', false)) {
            if (app()->environment('local')) {
                Log::info('RIPS_AUTH_ONLY: Token OK, deteniendo flujo tras autenticación.', [
                    'tenant' => $tenantId,
                ]);
            }

            Notification::make()
                ->title('Autenticación SISPRO')
                ->body('✅ Token obtenido correctamente. (Modo solo autenticación activo)')
                ->success()
                ->persistent()
                ->send();

            return; // 👈 No genera JSON ni envía nada
        }

        // Generate RIPS JSON for the group
        $documentsPayload = $this->generatorService->previsualizarRipsPorFactura($agreementId, $startDate, $endDate, $withInvoice);

        if (empty($documentsPayload)) {
            Notification::make()
                ->title('Sin resultados')
                ->body('No se encontraron datos RIPS.')
                ->danger()
                ->persistent()
                ->send();
            return;
        }

        // Build submission service with the obtained token
        $this->submissionService = new RipsSubmissionService($token);

        $results = [];

        // Submit each document individually
        foreach ($documentsPayload as $index => $docPayload) {
            $number = $docPayload['rips']['numFactura'] ?? $docPayload['rips']['numNota'] ?? ('documento_' . $index);

            // Skip if already accepted
            $document = RipsBillingDocument::where('tenant_id', $tenantId)
                ->where('document_number', $number)
                ->first();

            if ($document?->submission_status === 'accepted') {
                continue;
            }

            // Submit to SISPRO
            $response = $this->submissionService->submitDocument($docPayload, $withInvoice);

            // Persist response file as a backup
            $filename = "respuesta_rips_{$number}_" . now()->format('Ymd_His') . '.json';
            Storage::disk('public')->put("respuestas/{$filename}", json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            // Determine status (accepted, rejected or pending)
            $status = 'pending';
            if (isset($response['response']['ResultState'])) {
                $status = $response['response']['ResultState'] === true ? 'accepted' : 'rejected';
            }

            // Update DB status
            if ($document) {
                $document->update(['submission_status' => $status]);
            }

            // Store result for the summary
            $results[] = [
                'document' => $number,
                'success' => $response['success'] ?? false,
                'response' => $response['response'] ?? null,
                'file' => $filename,
            ];
        }

        // Build summary notification
        $errors = collect($results)->where('success', false)->count();
        $success = collect($results)->where('success', true)->count();

        $body = "Facturas exitosas: {$success}<br>Errores: {$errors}<br><br>";
        $body .= collect($results)->map(function ($r) {
            return "<strong>{$r['document']}</strong>: <a href='" . asset("storage/respuestas/{$r['file']}") . "' target='_blank'>Ver respuesta</a>";
        })->implode('<br>');

        Notification::make()
            ->title('Resultado del envío de RIPS')
            ->body($body)
            ->success()
            ->persistent()
            ->send();
    }

    /**
     * Manual flow when the user selects specific documents in the table:
     * - Generate JSON using only selected documents (mode: 'enviar').
     * - Submit each one, skipping already accepted ones.
     * - Update document and service statuses accordingly.
     */
    public function processAndSubmitGroupManual(string $tenantId, int $agreementId, string $startDate, string $endDate, bool $withInvoice, array $documentsPayload): void
    {
        if (app()->environment('local')) {
            Log::info("Starting manual RIPS submission for tenant {$tenantId}");
        }

        // Obtain auth token
        $token = $this->tokenService->getToken($tenantId);
        if (app()->environment('local')) {
            Log::info("Token obtained for tenant {$tenantId}: " . ($token ? 'YES' : 'NO'));
        }

        if (!$token) {
            Notification::make()
                ->title('Error de autenticación')
                ->body("No se pudo obtener el token para el tenant {$tenantId}")
                ->danger()
                ->persistent()
                ->send();

            Log::error("Token not obtained for tenant {$tenantId}. Aborting process.");
            return;
        }

        // Modo solo autenticación (NO limpiar sesión aquí)
        if (env('RIPS_AUTH_ONLY', false)) {
            if (app()->environment('local')) {
                Log::info('RIPS_AUTH_ONLY (manual): Token OK, deteniendo flujo tras autenticación.', [
                    'tenant' => $tenantId,
                ]);
            }

            Notification::make()
                ->title('Autenticación SISPRO')
                ->body('✅ Token obtenido correctamente. (Modo solo autenticación activo)')
                ->success()
                ->persistent()
                ->send();

            return; // 👈 No envía nada
        }

        // Build submission service
        $this->submissionService = new RipsSubmissionService($token);

        $results = [];
        $includedIds = session('rips_servicios_incluidos', []);

        foreach ($documentsPayload as $index => $docPayload) {
            $number = $docPayload['rips']['numFactura'] ?? $docPayload['rips']['numNota'] ?? ('documento_' . $index);
            if (app()->environment('local')) {
                Log::info("Processing document: {$number}");
            }

            $document = RipsBillingDocument::where('tenant_id', $tenantId)
                ->where('document_number', $number)
                ->first();

            if (!$document) {
                Log::warning("Document not found in DB: {$number}");
            }

            if (app()->environment('local')) {
                Log::info("Submitting document {$number} to SISPRO...");
            }
            $response = $this->submissionService->submitDocument($docPayload, $withInvoice);

            if (app()->environment('local')) {
                Log::info("Response received for {$number}", ['response' => $response]);
            }

            $filename = "respuesta_rips_{$number}_" . now()->format('Ymd_His') . '.json';
            Storage::disk('public')->put("respuestas/{$filename}", json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            $status = 'pending';
            if (isset($response['response']['ResultState'])) {
                $status = $response['response']['ResultState'] === true ? 'accepted' : 'rejected';
            }
            if (app()->environment('local')) {
                Log::info("Submission status for {$number}: {$status}");
            }

            if ($document) {
                // Actualiza estado del documento
                $document->update(['submission_status' => $status]);

                // Actualiza estado de CADA servicio relacionado
                $serviceUpdater = app(RipsPatientServiceStatusUpdater::class);
                foreach ($document->patientServices as $service) {
                    // Incluido = está en la lista y pertenece a ESTE documento
                    $included = in_array($service->id, $includedIds, true)
                        && (int)$service->billing_document_id === (int)$document->id;

                    if (app()->environment('local')) {
                        Log::info("Service {$service->id} included in submission: " . ($included ? 'YES' : 'NO'));
                    }
                    $serviceUpdater->updateStatus($service, $included);
                }
            }

            $results[] = [
                'document' => $number,
                'success' => $response['success'] ?? false,
                'response' => $response['response'] ?? null,
                'file' => $filename,
            ];
        }

        $errors = collect($results)->where('success', false)->count();
        $success = collect($results)->where('success', true)->count();

        $body = "Facturas exitosas: {$success}<br>Errores: {$errors}<br><br>";
        $body .= collect($results)->map(function ($r) {
            return "<strong>{$r['document']}</strong>: <a href='" . asset("storage/respuestas/{$r['file']}") . "' target='_blank'>Ver respuesta</a>";
        })->implode('<br>');

        Notification::make()
            ->title('Resultado del envío de RIPS')
            ->body($body)
            ->success()
            ->persistent()
            ->send();

        if (app()->environment('local')) {
            Log::info("Manual submission finished. Success: {$success}, Errors: {$errors}");
        }

        // 👉 NO limpiar sesión aquí. La limpieza se hace una sola vez al final de submitFromSelection()
    }

    /**
     * Full flow when the user selects records from a table:
     * - Generate JSON only for selected records (mode 'enviar').
     * - Submit one by one, skipping already accepted documents.
     */
    public function submitFromSelection(EloquentCollection $records, string $tenantId): void
    {
        if (app()->environment('local')) {
            Log::info("Starting RIPS submission from selection. Tenant: {$tenantId}");
        }

        // Clear leftover session data (solo rips_json_generado aquí)
        session()->forget('rips_json_generado');

        // Generate JSON for selected documents (returns array or null)
        $jsonRips = $this->generatorService->generateOnlySelected(collect($records), 'enviar');

        if (!is_array($jsonRips)) {
            Log::error('generateOnlySelected did not return an array. Aborting.');
            return;
        }

        if (is_null($jsonRips)) {
            Log::warning('No JSON generated. Aborting.');
            return;
        }

        foreach ($jsonRips as $docPayload) {
            $number = $docPayload['rips']['numFactura'] ?? $docPayload['rips']['numNota'] ?? 'documento';

            if (app()->environment('local')) {
                Log::info("Processing document: {$number}");
            }

            $document = RipsBillingDocument::where('tenant_id', $tenantId)
                ->where('document_number', $number)
                ->first();

            if (!$document) {
                Log::error("Document not found for number: {$number}");
                continue;
            }

            if (!$document->agreement_id) {
                Log::error("Document without agreement: {$number}");
                continue;
            }

            if ($document->submission_status === 'accepted') {
                if (app()->environment('local')) {
                    Log::info("Document already accepted. Skipping: {$number}");
                }
                continue;
            }

            $start = optional($document->patientServices)->pluck('service_datetime')->filter()->min();
            $end = optional($document->patientServices)->pluck('service_datetime')->filter()->max();

            if (!$start || !$end) {
                Log::warning("Invalid dates for document: {$number}");
                continue;
            }

            if (app()->environment('local')) {
                Log::info("Submitting document {$number} from {$start} to {$end}");
            }

            // Submit using the manual-group method to reuse flow
            $this->processAndSubmitGroupManual(
                tenantId: $tenantId,
                agreementId: $document->agreement_id,
                startDate: Carbon::parse($start)->format('Y-m-d'),
                endDate: Carbon::parse($end)->format('Y-m-d'),
                withInvoice: $document->type_id === 1,
                documentsPayload: [$docPayload]
            );
        }

        // ✅ Limpieza de sesión SOLO UNA VEZ, al final
        session()->forget('rips_servicios_incluidos');
        session()->forget('rips_servicios_seleccionados');
        session()->forget('rips_confirmado');
        session()->forget('rips_json_generado');

        if (app()->environment('local')) {
            Log::info('Finished submission for selected RIPS documents.');
        }
    }
}
