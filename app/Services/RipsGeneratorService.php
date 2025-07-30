<?php

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
use Illuminate\Support\Facades\File;




class RipsGeneratorService
{
    public function generateOnlySelected(Collection $patientServices)
    {
        $missingServicesByDocument = [];

        // Agrupar los servicios seleccionados por documento
        $grouped = $patientServices->groupBy('billing_document_id');

        foreach ($grouped as $documentId => $selectedServices) {
            // Cargar todos los servicios que existen para ese documento
            $allServices = RipsPatientService::where('billing_document_id', $documentId)->get();

            if ($selectedServices->count() < $allServices->count()) {
                $missingServicesByDocument[$documentId] = $allServices->diff($selectedServices);
            }
        }

        // Si hay documentos con servicios faltantes, mostrar advertencia
        if (!empty($missingServicesByDocument)) {
            // Guardamos los IDs seleccionados para luego generar desde sesión
            session(['rips_servicios_seleccionados' => $patientServices->pluck('id')->toArray()]);

            // Obtenemos los números de factura/nota con servicios faltantes
            $documentNumbers = RipsBillingDocument::whereIn('id', array_keys($missingServicesByDocument))
                ->pluck('document_number')
                ->implode(', ');

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
                        ->url(route('rips.confirmar-generacion')),
                    Action::make('cancelar')
                        ->label('Cancelar')
                        ->button()
                        ->color('danger')
                        ->close()
                ])
                ->send();

            return null;
        }

        // ✅ Si no hay servicios faltantes, redirige directo a descarga
        session(['rips_servicios_seleccionados' => $patientServices->pluck('id')->toArray()]);

        return redirect()->route('rips.confirmar-generacion');
    }



    public function buildRipsFromSelectedServices(Collection $patientServices)
    {
        $ripsData = [];

        // Agrupamos por factura/nota
        $grouped = $patientServices->groupBy('billing_document_id');

        foreach ($grouped as $documentId => $services) {
            $document = \App\Models\Rips\RipsBillingDocument::with('patientServices.patient.user')->find($documentId);
            if (!$document) continue;

            // Se mantiene el formato actual de cabecera del documento
            $tenantDocumentNumber = \DB::table('tenants')
                ->where('id', $document->tenant_id)
                ->value('document_number');

            $documentData = $document->type_id === 1
                ? [
                    'numDocumentoIdObligado'=> $tenantDocumentNumber,
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

            foreach ($servicesByPatient as $patientId => $groupedServices) {
                $patient = $groupedServices->first()->patient;
                $usuario = $this->mapPatientToRips($patient, $groupedServices);
                $usuario['consecutivo'] = count($ripsItem['usuarios']) + 1;
                $usuario['servicios'] = $this->processServices($groupedServices, $document->tenant);

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

    public function confirmarGeneracionDesdeSesion()
    {
        Log::info('➡️ Redirigiendo a la ruta de descarga');
        return redirect()->route('rips.confirmar-generacion');
    }






    
    //public function generateByServices($agreementId, $startDate, $endDate, $withInvoice = true)
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

        
        

        $billingDocuments = $billingDocuments->with(['patientServices' => function($query) {
            $query->with([
                'patient.user',
                'patient.ripsCountry',
                'patient.ripsMunicipality',
                'patient.originCountry',
                'consultations' => function($q) {
                    $q->with([
                        'cups', 
                        'serviceGroup', 
                        'service', 
                        'technologyPurpose',
                        'diagnoses' => function($dq) {
                            $dq->with('cie10')->orderBy('sequence');
                        },
                        'collectionConcept'
                    ]);
                },
                'procedures' => function($q) {
                    $q->with(['cups', 'cie10', 'surgeryCie10']);
                },
                'doctor'
            ]);
        }])->get();
        
        $ripsData = [];

        foreach ($billingDocuments as $document) {
            // Obtener el número de documento del tenant SIN usar modelo
            $tenantDocumentNumber = DB::table('tenants')
                ->where('id', $document->tenant_id)
                ->value('document_number');

            
            if ($document->type_id === 1) {
                $documentData = [
                    'numDocumentoIdObligado'=> $tenantDocumentNumber,
                    'numFactura' => $document->document_number ?? null,
                    'tipoNota' => null,
                    'numNota' => null,
                ];
            } else {
                $documentData = [
                    'numDocumentoIdObligado' => $tenantDocumentNumber,
                    'numFactura' => null,
                    'tipoNota' => 'RS',
                    'numNota' => $document->document_number ?? null,
                ];
            }


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

    protected function mapPatientToRips($patient, $patientServices)
    {
        return [
            'tipoDocumentoIdentificacion' => $patient->patientUser->ripsIdentificationType->code ?? '',
            'numDocumentoIdentificacion' => $patient->patientUser->rips_identification_number ?? '',
            //'tipoUsuario' => $patient-> ripsUserType->id ?? '',
            'tipoUsuario' => str_pad((string) ($patient->ripsUserType->id ?? ''), 2, '0', STR_PAD_LEFT),
            'fechaNacimiento' => $patient->birth_date,
            'codSexo' => $patient->sex_code,
            //'codPaisResidencia' => $patient->residenceCountry->code ?? '',
            'codPaisResidencia' => (string) ($patient->residenceCountry->code ?? ''),
            //'codMunicipioResidencia' => $patient->ripsMunicipality->code ?? '',
            'codMunicipioResidencia' => str_pad((string) ($patient->ripsMunicipality->code ?? ''), 5, '0', STR_PAD_LEFT),

            //'codZonaTerritorialResidencia' => $patient->zone_code ?? '',
            'codZonaTerritorialResidencia' => str_pad((string) ($patient->zone_code ?? ''), 2, '0', STR_PAD_LEFT),
            'incapacidad' => $patientServices->contains('has_incapacity', 1) ? 'SI' : 'NO',
            //'codPaisOrigen' => $patient->originCountry->code ?? '',
            'codPaisOrigen' => (string) ($patient->originCountry->code ?? ''),
        ];
    }

    
    protected function processServices($services, $tenant)
    {
        $result = [];
        $consultas = $this->mapConsultas($services, $tenant);
        if (!empty($consultas)) $result['consultas'] = $consultas;

        $procedimientos = $this->mapProcedimientos($services, $tenant);
        if (!empty($procedimientos)) $result['procedimientos'] = $procedimientos;

        return $result;
    }

    protected function mapConsultas($services, $tenant)
    {
        $consultas = [];
        $consecutivo = 1;

        foreach ($services as $service) {
            foreach ($service->consultations as $consulta) {
                $diagnosticos = $consulta->diagnoses->sortBy('sequence');
                $diagnosticoPrincipal = $diagnosticos->firstWhere('sequence', 1);
                //$diagnosticosRelacionados = $diagnosticos->where('sequence', '>', 1)->take(3);
                $diagnosticosRelacionados = $diagnosticos->where('sequence', '>', 1)->take(3)->values(); // 👈 Reindexar aquí

                $consultas[] = [
                    //'codPrestador' => $service->doctor->rips_provider_code,
                    'codPrestador' => $tenant->provider_code,
                    //'fechaInicioAtencion' => $service->service_datetime,
                    'fechaInicioAtencion' => \Carbon\Carbon::parse($service->service_datetime)->format('Y-m-d H:i'),
                    'codConsulta' => $consulta->cups->code ?? '',
                    //'modalidadGrupoServicioTecSal' => $consulta->serviceGroupMode->id ?? '',
                    'modalidadGrupoServicioTecSal' => str_pad((string) ($consulta->serviceGroupMode->id ?? ''), 2, '0', STR_PAD_LEFT),
                    //'grupoServicios' => $consulta->serviceGroup->id ?? '',
                    'grupoServicios' => str_pad((string) ($consulta->serviceGroup->id ?? ''), 2, '0', STR_PAD_LEFT),
                    //'codServicio' => $consulta->service->code ?? '',
                    'codServicio' => (int) ($consulta->service->code ?? 334),
                    //'finalidadTecnologiaSalud' => $consulta->technologyPurpose->code ?? '12',
                    'finalidadTecnologiaSalud' => (string) ($consulta->technologyPurpose->code ?? '12'),
                    'causaMotivoAtencion' => (string) ($consulta->serviceReason->code ?? '35'),
                    'codDiagnosticoPrincipal' => $diagnosticoPrincipal->cie10->code ?? 'Z012',
                    'codDiagnosticoRelacionado1' => $diagnosticosRelacionados->get(0)->cie10->code ?? null,
                    'codDiagnosticoRelacionado2' => $diagnosticosRelacionados->get(1)->cie10->code ?? null,
                    'codDiagnosticoRelacionado3' => $diagnosticosRelacionados->get(2)->cie10->code ?? null,
                    'tipoDiagnosticoPrincipal' => str_pad((string) ($diagnosticoPrincipal->diagnosisType->code ?? ''), 2, '0', STR_PAD_LEFT),
                    //'tipoDiagnosticoPrincipal' => $diagnosticoPrincipal->diagnosisType->code ?? null,
                    'tipoDocumentoIdentificacion' => $service->doctor->doctorUser->ripsIdentificationType->code ?? '',
                    'numDocumentoIdentificacion' => $service->doctor->doctorUser->rips_identification_number,
                    'vrServicio' => $consulta->service_value ?? 0,
                    //'conceptoRecaudo' => $consulta->collectionConcept->code ?? '05',
                    'conceptoRecaudo' => str_pad($consulta->collectionConcept->code ?? '05', 2, '0', STR_PAD_LEFT),
                    'valorPagoModerador' => $consulta->copayment_value ?? 0,
                    'numFEVPagoModerador' => $consulta->copayment_receipt_number ?? '',
                    'consecutivo' => $consecutivo++
                ];
            }
        }
        return $consultas;
    }

    protected function mapProcedimientos($services, $tenant)
    {
        $procedimientos = [];
        $consecutivo = 1;

        foreach ($services as $service) {
            foreach ($service->procedures as $procedure) {
                //sdd($procedure);
                $procedimientos[] = [
                    //'codPrestador' => $service->doctor->rips_provider_code,
                    'codPrestador' => $tenant->provider_code, // Usar el código del proveedor del tenant
                    //'fechaInicioAtencion' => $service->service_datetime,
                    'fechaInicioAtencion' => \Carbon\Carbon::parse($service->service_datetime)->format('Y-m-d H:i'),

                    'idMIPRES' => $procedure->mipres_id ?? '',
                    'numAutorizacion' => $procedure->authorization_number ?? '',
                    'codProcedimiento' => $procedure->cups->code ?? '',
                    //'viaIngresoServicioSalud' =>  $procedure->admissionRoute->code ?? 'Z012',
                    'viaIngresoServicioSalud' => str_pad((string) ($procedure->admissionRoute->code ?? '01'), 2, '0', STR_PAD_LEFT),
                    //'modalidadGrupoServicioTecSal' =>  $procedure->serviceGroupMode->id ?? '',
                    'modalidadGrupoServicioTecSal' => str_pad((string) ($procedure->serviceGroupMode->id ?? ''), 2, '0', STR_PAD_LEFT),
                    //'grupoServicios' => $procedure->serviceGroup->id ?? '',
                    'grupoServicios' => str_pad((string) ($procedure->serviceGroup->id ?? ''), 2, '0', STR_PAD_LEFT),
                    //'codServicio' => $procedure->service->code ?? '334',
                    'codServicio' => (int) ($procedure->service->code ?? 334),
                    //'finalidadTecnologiaSalud' => $procedure->technologyPurpose->code ?? '',
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


    // Función que genera los RIPS
    public function generateByPatientServices(Collection $patientServices)
    {
        Log::info('Generando RIPS agrupados por convenio.', [
            'total_servicios' => $patientServices->count()
        ]);

        $groupedByAgreement = $patientServices->groupBy(fn ($item) => optional($item->billingDocument)->agreement_id);

        $generatedFiles = [];

        foreach ($groupedByAgreement as $agreementId => $group) {
            if (!$agreementId) {
                Log::warning('Registro sin convenio asociado, se omite este grupo.');
                continue;
            }

            $start = $group->pluck('service_datetime')->filter()->min();
            $end = $group->pluck('service_datetime')->filter()->max();

            $startDate = $start ? Carbon::parse($start)->format('Y-m-d') : null;
            $endDate = $end ? Carbon::parse($end)->format('Y-m-d') : null;

            if (!$startDate || !$endDate) {
                Log::warning("Fechas no válidas para convenio $agreementId, se omite.");
                continue;
            }

            Log::info("Generando RIPS para convenio $agreementId desde $startDate hasta $endDate");

            $ripsData = $this->generateByServices($agreementId, $startDate, $endDate);

            if (empty($ripsData)) {
                Log::warning("No se generó información para convenio $agreementId.");
                continue;
            }

            $filename = "rips_agreement_{$agreementId}_" . now()->timestamp . ".json";
            //Storage::put($filename, json_encode($ripsData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
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
                ->body($body) // HTML como en múltiples
                ->success()
                ->persistent()
                ->send();

            return;
        }



        // Si hay múltiples archivos, mostramos los enlaces con Filament Notification
        $downloadLinks = collect($generatedFiles)->map(fn($file) => asset('uploads/' . $file))->all();

        // Creamos una lista HTML de los enlaces para mejorar la presentación
        $body = collect($generatedFiles)->map(function ($file) {
            $url = asset('uploads/' . $file);
            return "<a href='{$url}' target='_blank'>Descargar {$file}</a>";
        })->implode("<br>"); // Usamos <br> para saltos de línea en lugar de solo texto

        Notification::make()
            ->title('Archivos RIPS generados')
            ->body($body) // Ahora usamos el cuerpo HTML
            ->success()
            ->persistent()
            ->send();

    }

    /**
     * Permite obtener los datos RIPS agrupados por factura sin enviarlos.
     * Este método sirve para previsualizar antes de hacer el envío real.
     *
     * @param int $agreementId ID del convenio (EPS).
     * @param string $startDate Fecha inicial del rango (formato Y-m-d).
     * @param string $endDate Fecha final del rango (formato Y-m-d).
     * @param bool $conFactura True = facturas normales, False = notas.
     * @return array Arreglo de facturas RIPS.
     */
    public function previsualizarRipsPorFactura(int $agreementId, string $startDate, string $endDate, bool $conFactura = true): array
    {
        return $this->generateByServices($agreementId, $startDate, $endDate, $conFactura);
    }
}