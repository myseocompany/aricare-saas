<?php

/****************************************************************/
/* Module: RIPS Generator Service                               */
/* Author: Julian                                               */
/* Date: 2025-08-07                                             */
/* Description: Service to generate RIPS JSON files from        */
/*              selected services or by date range.             */
/****************************************************************/

namespace App\Services;

use Carbon\Carbon;
use App\Models\Rips\RipsPatientService;
use App\Models\Rips\RipsBillingDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;

class RipsGeneratorService
{
    // IDs of the services included in the generated JSON
    public array $includedServiceIds = [];

    /**
     * Returns the IDs of the services included in the JSON.
     */
    public function getIncludedServiceIds(): array
    {
        return $this->includedServiceIds;
    }

    /**
     * Generates RIPS JSON using only the services selected by the user.
     * Validations performed:
     * - If a billing document (invoice or note) has unselected services, a warning is shown.
     * - If an invoice requires an XML FEV file and it's missing, the process is stopped.
     * Modes:
     * - 'generar': only generates the JSON and redirects to download.
     * - 'enviar': generates the JSON for later submission to the API.
     *
     * @param Collection $patientServices Selected services list.
     * @param string $modo Either 'generar' or 'enviar'.
     * @return array|null Generated RIPS JSON or null when pending confirmation/validation.
     */
    public function generateOnlySelected(Collection $patientServices, string $modo = 'generar')
    {
        if (app()->environment('local')) {
            Log::info("Starting validation for selected services. Mode: {$modo}");
        }

        // If already confirmed, skip missing-services validation
        if (session('rips_confirmado') === true) {
            if (app()->environment('local')) {
                Log::info('Previous confirmation detected. Skipping missing-services validations.');
                Log::info("Confirmation detected, mode: {$modo}. Proceeding without warnings.");
            }

            session()->forget('rips_confirmado');

            // Re-store selected IDs in session for generation
            session(['rips_servicios_seleccionados' => $patientServices->pluck('id')->toArray()]);

            if ($modo === 'generar') {
                if (app()->environment('local')) {
                    Log::info('Redirecting to direct download after prior confirmation.');
                }
                return redirect()->to(route('rips.confirmar-generacion'));
            }

            // For 'enviar' mode, build and return the JSON
            return $this->buildRipsFromSelectedServices($patientServices);
        }

        // Regular validations
        $missingServicesByDocument = [];
        $missingXmlDocuments = [];

        $grouped = $patientServices->groupBy('billing_document_id');

        foreach ($grouped as $documentId => $selectedServices) {
            $allServices = RipsPatientService::where('billing_document_id', $documentId)->get();

            if ($selectedServices->count() < $allServices->count()) {
                $missingServicesByDocument[$documentId] = $allServices->diff($selectedServices);
                Log::warning("Missing services in billing document ID {$documentId}");
            }

            $document = RipsBillingDocument::find($documentId);
            if ($document && $document->type_id === 1) {
                $fullPath = storage_path('app/public/' . $document->xml_path);
                if (empty($document->xml_path) || !file_exists($fullPath)) {
                    $missingXmlDocuments[] = $document->document_number;
                    Log::warning("XML file missing for invoice {$document->document_number}");
                }
            }
        }

        // Stop if there are invoices without required XML FEV file
        if (!empty($missingXmlDocuments)) {
            $facturasSinXml = implode(', ', $missingXmlDocuments);
            Notification::make()
                ->title('Facturas sin archivo XML')
                ->body("Las siguientes facturas no tienen cargado el XML FEV requerido: <strong>{$facturasSinXml}</strong>. Por favor cárguelos antes de generar el RIPS.")
                ->danger()
                ->persistent()
                ->send();

            Log::warning("Process stopped due to missing XML in: {$facturasSinXml}");
            return null;
        }

        // Ask for confirmation if there are missing services
        if (!empty($missingServicesByDocument)) {
            session(['rips_servicios_seleccionados' => $patientServices->pluck('id')->toArray()]);

            $documentNumbers = RipsBillingDocument::whereIn('id', array_keys($missingServicesByDocument))
                ->pluck('document_number')
                ->implode(', ');

            $url = $modo === 'enviar'
                ? route('rips.confirmar-envio')
                : route('rips.confirmar-generacion');

            Notification::make()
                ->title('Servicios faltantes')
                ->body("Hay servicios no seleccionados en las facturas o notas: <strong>{$documentNumbers}</strong>. ¿Deseas continuar con solo los seleccionados?")
                ->warning()
                ->persistent()
                ->actions([
                    Action::make('continuar')
                        ->label('Sí, continuar')
                        ->button()
                        ->color('success')
                        ->url($url)
                        ->close(),
                    Action::make('cancelar')
                        ->label('Cancelar')
                        ->button()
                        ->color('danger')
                        ->close()
                ])
                ->send();

            if (app()->environment('local')) {
                Log::info("Warning shown for missing services. Awaiting user confirmation. Mode: {$modo}");
            }
            return null;
        }

        // Everything OK from the start (no warnings)
        session(['rips_confirmado' => true]);
        session(['rips_servicios_seleccionados' => $patientServices->pluck('id')->toArray()]);

        if ($modo === 'generar') {
            if (app()->environment('local')) {
                Log::info('Redirecting to direct download of generated JSON.');
            }
            return redirect()->to(route('rips.confirmar-generacion'));
        }

        return $this->buildRipsFromSelectedServices($patientServices);
    }

    /**
     * Builds the final RIPS JSON structure from the selected services.
     * Groups by billing document (invoice or note), then by patient.
     * Stores included service IDs in session for later status updates.
     *
     * @param Collection $patientServices Selected services with relations loaded.
     * @return array RIPS payload grouped by billing document.
     */
    public function buildRipsFromSelectedServices(Collection $patientServices)
    {
        if (app()->environment('local')) {
            Log::info('Starting RIPS generation from selected services', [
                'total_services' => $patientServices->count(),
            ]);
        }

        $ripsData = []; // Final JSON array

        // Group selected services by billing document (invoice or note)
        $grouped = $patientServices->groupBy('billing_document_id');
        if (app()->environment('local')) {
            Log::info('Services grouped by billing document', [
                'document_ids' => $grouped->keys()->all(),
            ]);
        }

        foreach ($grouped as $documentId => $services) {
            if (app()->environment('local')) {
                Log::info("Processing billing document ID: {$documentId}", [
                    'total_services' => $services->count(),
                ]);
            }

            $document = RipsBillingDocument::with('patientServices.patient.user')->find($documentId);

            if (!$document) {
                Log::warning("Billing document not found: {$documentId}");
                continue;
            }

            $tenantDocumentNumber = DB::table('tenants')
                ->where('id', $document->tenant_id)
                ->value('rips_identification_number');

            if (app()->environment('local')) {
                Log::info('Billing document loaded', [
                    'document_number' => $document->document_number,
                    'type_id' => $document->type_id,
                    'tenant_document_number' => $tenantDocumentNumber,
                ]);
            }

            $documentData = $document->type_id === 1
                ? [
                    'numDocumentoIdObligado' => $tenantDocumentNumber,
                    'numFactura' => $document->document_number ?? null,
                    'tipoNota' => null,
                    'numNota' => null,
                ]
                : [
                    'numDocumentoIdObligado' => $tenantDocumentNumber,
                    'numFactura' => null,
                    'tipoNota' => 'RS',
                    'numNota' => $document->document_number ?? null,
                ];

            $ripsItem = array_merge($documentData, ['usuarios' => []]);

            $servicesByPatient = $services->groupBy('patient_id');
            if (app()->environment('local')) {
                Log::info('Services grouped by patient', [
                    'patient_ids' => $servicesByPatient->keys()->all(),
                ]);
            }

            foreach ($servicesByPatient as $patientId => $groupedServices) {
                $patient = $groupedServices->first()->patient;
                $usuario = $this->mapPatientToRips($patient, $groupedServices);
                $usuario['consecutivo'] = count($ripsItem['usuarios']) + 1;

                if (app()->environment('local')) {
                    Log::info('Processing patient', [
                        'id' => $patientId,
                        'name' => $patient->full_name ?? 'N/A',
                        'consecutivo' => $usuario['consecutivo'],
                    ]);
                }

                $usuario['servicios'] = $this->processServices($groupedServices, $document->tenant);
                $ripsItem['usuarios'][] = $usuario;
            }

            $fullPath = storage_path('app/public/' . $document->xml_path);
            $xmlBase64 = null;

            if ($document->type_id === 1 && $fullPath && file_exists($fullPath)) {
                $xmlBase64 = base64_encode(file_get_contents($fullPath));
                if (app()->environment('local')) {
                    Log::info('XML FEV found and encoded', [
                        'path' => $fullPath,
                        'size_bytes' => strlen($xmlBase64),
                    ]);
                }
            } else {
                Log::warning('XML not found or not required', [
                    'path' => $fullPath,
                    'required' => $document->type_id === 1,
                ]);
            }

            $ripsData[] = [
                'rips' => $ripsItem,
                'xmlFevFile' => $xmlBase64,
            ];
        }

        $this->includedServiceIds = array_unique($this->includedServiceIds);

        if (app()->environment('local')) {
            Log::info('Service IDs included in the final JSON', [
                'includedServiceIds' => $this->includedServiceIds,
            ]);
        }

        session(['rips_servicios_incluidos' => $this->includedServiceIds]);

        if (app()->environment('local')) {
            Log::info('RIPS generation finished', [
                'generated_documents' => count($ripsData),
            ]);
        }

        return $ripsData;
    }

    /**
     * Generates RIPS JSON grouped by agreement and date range.
     *
     * @param mixed $agreementId Agreement (EPS) ID.
     * @param string $startDate Start date (Y-m-d).
     * @param string $endDate End date (Y-m-d).
     * @return array RIPS payload grouped by billing document.
     */
    public function generateByServices($agreementId, $startDate, $endDate)
    {
        $tenantId = Auth::user()->tenant_id;
        $tenant = DB::table('tenants')->where('id', $tenantId)->first();

        $billingDocuments = RipsBillingDocument::where('tenant_id', $tenantId)
            ->where('agreement_id', $agreementId)
            ->whereBetween('issued_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);

        $billingDocuments = $billingDocuments->with(['patientServices' => function ($query) {
            $query->with([
                'patient.user',
                'patient.ripsCountry',
                'patient.ripsMunicipality',
                'patient.originCountry',
                'consultations' => function ($q) {
                    $q->with([
                        'cups',
                        'serviceGroup',
                        'service',
                        'technologyPurpose',
                        'diagnoses' => function ($dq) {
                            $dq->with('cie10')->orderBy('sequence');
                        },
                        'collectionConcept'
                    ]);
                },
                'procedures' => function ($q) {
                    $q->with(['cups', 'cie10', 'surgeryCie10']);
                },
                'doctor'
            ]);
        }])->get();

        $ripsData = [];

        foreach ($billingDocuments as $document) {
            // Fetch tenant document number without using a model
            $tenantDocumentNumber = DB::table('tenants')
                ->where('id', $document->tenant_id)
                ->value('document_number');

            $documentData = $document->type_id === 1
                ? [
                    'numDocumentoIdObligado' => $tenantDocumentNumber,
                    'numFactura' => $document->document_number ?? null,
                    'tipoNota' => null,
                    'numNota' => null,
                ]
                : [
                    'numDocumentoIdObligado' => $tenantDocumentNumber,
                    'numFactura' => null,
                    'tipoNota' => 'RS',
                    'numNota' => $document->document_number ?? null,
                ];

            $ripsItem = array_merge($documentData, ['usuarios' => []]);

            $servicesByPatient = $document->patientServices->groupBy('patient_id');

            foreach ($servicesByPatient as $patientId => $patientServices) {
                $patient = $patientServices->first()->patient;

                $usuario = $this->mapPatientToRips($patient, $patientServices);
                $usuario['consecutivo'] = count($ripsItem['usuarios']) + 1;
                $usuario['servicios'] = $this->processServices($patientServices, $tenant);

                $ripsItem['usuarios'][] = $usuario;
            }

            $ripsData[] = [
                'rips' => $ripsItem,
                'xmlFevFile' => $document->type_id === 1 && !empty($document->xml_path)
                    ? base64_encode(file_get_contents($document->xml_path))
                    : null
            ];
        }
        return $ripsData;
    }

    /**
     * Maps patient and residency data to the RIPS structure.
     *
     * @param mixed $patient Patient entity.
     * @param Collection|array $patientServices Services for the current patient.
     * @return array RIPS-compliant patient data.
     */
    protected function mapPatientToRips($patient, $patientServices)
    {
        return [
            'tipoDocumentoIdentificacion' => $patient->patientUser->ripsIdentificationType->code ?? '',
            'numDocumentoIdentificacion' => $patient->patientUser->rips_identification_number ?? '',
            'tipoUsuario' => str_pad((string) ($patient->ripsUserType->id ?? ''), 2, '0', STR_PAD_LEFT),
            'fechaNacimiento' => $patient->birth_date,
            'codSexo' => $patient->patientUser->ripsGenderType->code ?? '',
            'codPaisResidencia' => (string) ($patient->residenceCountry->code ?? ''),
            'codMunicipioResidencia' => str_pad((string) ($patient->ripsMunicipality->code ?? ''), 5, '0', STR_PAD_LEFT),
            'codZonaTerritorialResidencia' => str_pad((string) ($patient->zone_code ?? ''), 2, '0', STR_PAD_LEFT),
            'incapacidad' => $patientServices->contains('has_incapacity', 1) ? 'SI' : 'NO',
            'codPaisOrigen' => (string) ($patient->originCountry->code ?? ''),
        ];
    }

    /**
     * Builds the services subsection for a patient (consultations and procedures).
     */
    protected function processServices($services, $tenant)
    {
        $result = [];
        $consultas = $this->mapConsultas($services, $tenant);
        if (!empty($consultas)) {
            $result['consultas'] = $consultas;
        }

        $procedimientos = $this->mapProcedimientos($services, $tenant);
        if (!empty($procedimientos)) {
            $result['procedimientos'] = $procedimientos;
        }

        return $result;
    }

    /**
     * Maps consultations to the RIPS structure.
     */
    protected function mapConsultas($services, $tenant)
    {
        $consultas = [];
        $consecutivo = 1;

        foreach ($services as $service) {
            foreach ($service->consultations as $consulta) {
                // Track included service ID
                $this->includedServiceIds[] = $service->id;

                $diagnosticos = $consulta->diagnoses->sortBy('sequence');
                $diagnosticoPrincipal = $diagnosticos->firstWhere('sequence', 1);
                // Reindex related diagnoses to avoid gaps when accessing by index
                $diagnosticosRelacionados = $diagnosticos->where('sequence', '>', 1)->take(3)->values();

                $consultas[] = [
                    'codPrestador' => $tenant->rips_provider_code,
                    'fechaInicioAtencion' => \Carbon\Carbon::parse($service->service_datetime)->format('Y-m-d H:i'),
                    'codConsulta' => $consulta->cups->code ?? '',
                    'modalidadGrupoServicioTecSal' => str_pad((string) ($consulta->serviceGroupMode->id ?? ''), 2, '0', STR_PAD_LEFT),
                    'grupoServicios' => str_pad((string) ($consulta->serviceGroup->id ?? ''), 2, '0', STR_PAD_LEFT),
                    'codServicio' => (int) ($consulta->service->code ?? 334),
                    'finalidadTecnologiaSalud' => (string) ($consulta->technologyPurpose->code ?? '12'),
                    'causaMotivoAtencion' => (string) ($consulta->serviceReason->code ?? '35'),
                    'codDiagnosticoPrincipal' => $diagnosticoPrincipal->cie10->code ?? 'Z012',
                    'codDiagnosticoRelacionado1' => $diagnosticosRelacionados->get(0)->cie10->code ?? null,
                    'codDiagnosticoRelacionado2' => $diagnosticosRelacionados->get(1)->cie10->code ?? null,
                    'codDiagnosticoRelacionado3' => $diagnosticosRelacionados->get(2)->cie10->code ?? null,
                    'tipoDiagnosticoPrincipal' => str_pad((string) ($diagnosticoPrincipal->diagnosisType->code ?? ''), 2, '0', STR_PAD_LEFT),
                    'tipoDocumentoIdentificacion' => $service->doctor->doctorUser->ripsIdentificationType->code ?? '',
                    'numDocumentoIdentificacion' => $service->doctor->doctorUser->rips_identification_number,
                    'vrServicio' => $consulta->service_value ?? 0,
                    'conceptoRecaudo' => str_pad($consulta->collectionConcept->code ?? '05', 2, '0', STR_PAD_LEFT),
                    'valorPagoModerador' => $consulta->copayment_value ?? 0,
                    'numFEVPagoModerador' => $consulta->copayment_receipt_number ?? '',
                    'consecutivo' => $consecutivo++
                ];
            }
        }
        return $consultas;
    }

    /**
     * Maps procedures to the RIPS structure.
     */
    protected function mapProcedimientos($services, $tenant)
    {
        $procedimientos = [];
        $consecutivo = 1;

        foreach ($services as $service) {
            foreach ($service->procedures as $procedure) {
                // Track included service ID
                $this->includedServiceIds[] = $service->id;

                $procedimientos[] = [
                    'codPrestador' => $tenant->rips_provider_code,
                    'fechaInicioAtencion' => \Carbon\Carbon::parse($service->service_datetime)->format('Y-m-d H:i'),
                    'idMIPRES' => $procedure->mipres_id ?? '',
                    'numAutorizacion' => $procedure->authorization_number ?? '',
                    'codProcedimiento' => $procedure->cups->code ?? '',
                    'viaIngresoServicioSalud' => str_pad((string) ($procedure->admissionRoute->code ?? '01'), 2, '0', STR_PAD_LEFT),
                    'modalidadGrupoServicioTecSal' => str_pad((string) ($procedure->serviceGroupMode->id ?? ''), 2, '0', STR_PAD_LEFT),
                    'grupoServicios' => str_pad((string) ($procedure->serviceGroup->id ?? ''), 2, '0', STR_PAD_LEFT),
                    'codServicio' => (int) ($procedure->service->code ?? 334),
                    'finalidadTecnologiaSalud' => (string) ($procedure->technologyPurpose->code ?? ''),
                    'tipoDocumentoIdentificacion' => $service->doctor->doctorUser->ripsIdentificationType->code ?? '',
                    'numDocumentoIdentificacion' => $service->doctor->doctorUser->rips_identification_number,
                    'codDiagnosticoPrincipal' => $procedure->cie10->code ?? 'Z012',
                    'codDiagnosticoRelacionado' => $procedure->surgeryCie10->code ?? null,
                    'codComplicacion' => $procedure->complicationCie10->code ?? null,
                    'vrServicio' => $procedure->service_value ?? 0,
                    'conceptoRecaudo' => str_pad($procedure->collectionConcept->code ?? '05', 2, '0', STR_PAD_LEFT),
                    'valorPagoModerador' => $procedure->copayment_value ?? 0,
                    'numFEVPagoModerador' => $procedure->copayment_receipt_number ?? '',
                    'consecutivo' => $consecutivo++
                ];
            }
        }

        return $procedimientos;
    }

    /**
     * Generates RIPS grouped by agreement from a collection of services and stores files.
     */
    public function generateByPatientServices(Collection $patientServices)
    {
        if (app()->environment('local')) {
            Log::info('Generating RIPS grouped by agreement.', [
                'total_services' => $patientServices->count()
            ]);
        }

        $groupedByAgreement = $patientServices->groupBy(fn($item) => optional($item->billingDocument)->agreement_id);

        $generatedFiles = [];

        foreach ($groupedByAgreement as $agreementId => $group) {
            if (!$agreementId) {
                Log::warning('Record without associated agreement. Skipping this group.');
                continue;
            }

            $start = $group->pluck('service_datetime')->filter()->min();
            $end = $group->pluck('service_datetime')->filter()->max();

            $startDate = $start ? Carbon::parse($start)->format('Y-m-d') : null;
            $endDate = $end ? Carbon::parse($end)->format('Y-m-d') : null;

            if (!$startDate || !$endDate) {
                Log::warning("Invalid dates for agreement {$agreementId}. Skipping.");
                continue;
            }

            if (app()->environment('local')) {
                Log::info("Generating RIPS for agreement {$agreementId} from {$startDate} to {$endDate}");
            }

            $ripsData = $this->generateByServices($agreementId, $startDate, $endDate);

            if (empty($ripsData)) {
                Log::warning("No data generated for agreement {$agreementId}.");
                continue;
            }

            $filename = "rips_agreement_{$agreementId}_" . now()->timestamp . ".json";
            Storage::disk('public')->put($filename, json_encode($ripsData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            $generatedFiles[] = $filename;
        }

        if (empty($generatedFiles)) {
            Notification::make()
                ->title('Sin resultados')
                ->body('No se generaron archivos RIPS para los registros seleccionados.')
                ->danger()
                ->persistent()
                ->send();

            return;
        }

        if (count($generatedFiles) === 1) {
            $url = asset('uploads/' . $generatedFiles[0]);

            $body = "<a href='{$url}' target='_blank'>📥 Descargar archivo RIPS</a>";

            Notification::make()
                ->title('Archivo RIPS generado')
                ->body($body)
                ->success()
                ->persistent()
                ->send();

            return;
        }

        // Multiple files: build HTML list of links for Filament Notification
        $body = collect($generatedFiles)->map(function ($file) {
            $url = asset('uploads/' . $file);
            return "<a href='{$url}' target='_blank'>Descargar {$file}</a>";
        })->implode('<br>');

        Notification::make()
            ->title('Archivos RIPS generados')
            ->body($body)
            ->success()
            ->persistent()
            ->send();
    }

    /**
     * Returns RIPS data grouped by billing document, without sending it.
     * Useful for previewing before the actual submission.
     *
     * @param int $agreementId Agreement (EPS) ID.
     * @param string $startDate Start date (Y-m-d).
     * @param string $endDate End date (Y-m-d).
     * @param bool $conFactura True = invoices, False = notes.
     * @return array RIPS data per billing document.
     */
    public function previsualizarRipsPorFactura(int $agreementId, string $startDate, string $endDate, bool $conFactura = true): array
    {
        // Note: generateByServices currently ignores $conFactura; kept for backward compatibility
        return $this->generateByServices($agreementId, $startDate, $endDate, $conFactura);
    }

    /**
     * Helper that builds the JSON from services stored in session after user confirmation.
     *
     * @param string $modo Either 'generar' or 'enviar'.
     * @return array|null
     */
    public function confirmarGeneracionDesdeSesion(string $modo = 'generar'): ?array
    {
        $ids = session('rips_servicios_seleccionados', []);

        if (empty($ids)) {
            Log::warning('No selected services found in session (confirmation).');
            return null;
        }

        // Load services with required relations
        $patientServices = RipsPatientService::with([
            'billingDocument',
            'patient.user',
            'consultations.diagnoses.cie10',
            'consultations.cups',
            'consultations.serviceGroup',
            'consultations.service',
            'consultations.technologyPurpose',
            'consultations.collectionConcept',
            'procedures.cups',
            'procedures.cie10',
            'procedures.surgeryCie10',
            'procedures.admissionRoute',
            'procedures.serviceGroup',
            'procedures.service',
            'procedures.technologyPurpose',
            'procedures.collectionConcept',
            'doctor.user',
        ])->whereIn('id', $ids)->get();

        // Mark session as confirmed
        session(['rips_confirmado' => true]);

        // Reuse the same flow and validations respecting the selected mode
        return $this->generateOnlySelected($patientServices, $modo);
    }
}
