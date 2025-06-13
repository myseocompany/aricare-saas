<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Rips\RipsPatientServices;
use App\Models\Rips\RipsBillingDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class RipsGeneratorService
{
    public function generateByServices($agreementId, $startDate, $endDate, $withInvoice = true)
    {
        $tenantId = Auth::user()->tenant_id;

        $billingDocuments = RipsBillingDocument::where('tenant_id', $tenantId)
            ->where('agreement_id', $agreementId)
            ->whereBetween('issued_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);

        if ($withInvoice) {
            $billingDocuments->where('type_id', 1); // Con factura
        } else {
            $billingDocuments->where('type_id', '!=', 1); // Sin factura (notas)
        }

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

            // Configuración diferente para facturas vs notas
            if ($withInvoice) {
                $documentData = [
                    'numDocumentoIdObligado'=> $tenantDocumentNumber, // Usamos el tenant_id aquí
                    'numFactura' => $document->document_number ?? null,
                    'tipoNota' => null,
                    'numNota' => null,
                    //'idRelacion' => $document->id ?? null
                ];
            } else {
                $documentData = [
                    'numDocumentoIdObligado' => $tenantDocumentNumber, // Usamos el tenant_id aquí
                    'numFactura' => null,
                    'tipoNota' => 'RS',
                    'numNota' => $document->document_number ?? null,
                    //'idRelacion' => $document->id ?? null
                ];
            }

            $ripsItem = array_merge($documentData, ['usuarios' => []]);

            $servicesByPatient = $document->patientServices->groupBy('patient_id');

            foreach ($servicesByPatient as $patientId => $patientServices) {
                $patient = $patientServices->first()->patient;

                $usuario = $this->mapPatientToRips($patient, $patientServices);
                $usuario['consecutivo'] = count($ripsItem['usuarios']) + 1;
                $usuario['servicios'] = $this->processServices($patientServices);

                $ripsItem['usuarios'][] = $usuario;
            }

            $ripsData[] = [
                'rips' => $ripsItem,
                'xmlFevFile' => $withInvoice && !empty($document->xml_path) 
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
            'tipoUsuario' => $patient-> ripsUserType->name ?? '',
            'fechaNacimiento' => $patient->birth_date,
            'codSexo' => $patient->sex_code,
            'codPaisResidencia' => $patient->residenceCountry->code ?? '',
            'codMunicipioResidencia' => $patient->ripsMunicipality->code ?? '',
            'codZonaTerritorialResidencia' => $patient->zone_code ?? '',
            'incapacidad' => $patientServices->contains('has_incapacity', 1) ? 'SI' : 'NO',
            'codPaisOrigen' => $patient->originCountry->code ?? '',
        ];
    }

    /*protected function processServices($services)
    {
        return [
            'consultas' => $this->mapConsultas($services),
            'procedimientos' => $this->mapProcedimientos($services)
        ];
    }*/
    //para evitar que salga la palabra consultas y procedimientos en el rips si estan vacios
    protected function processServices($services)
    {
        $result = [];
        
        $consultas = $this->mapConsultas($services);
        if (!empty($consultas)) {
            $result['consultas'] = $consultas;
        }

        $procedimientos = $this->mapProcedimientos($services);
        if (!empty($procedimientos)) {
            $result['procedimientos'] = $procedimientos;
        }
        return $result;
        
    }

    protected function mapConsultas($services)
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
                    'codPrestador' => $service->doctor->rips_provider_code,
                    'fechaInicioAtencion' => $service->service_datetime,
                    'codConsulta' => $consulta->cups->code ?? '',
                    'modalidadGrupoServicioTecSal' => $consulta->serviceGroupMode->id ?? '',
                    'grupoServicios' => $consulta->serviceGroup->id ?? '',
                    'codServicio' => $consulta->service->code ?? '',
                    'finalidadTecnologiaSalud' => $consulta->technologyPurpose->code ?? '12',
                    'causaMotivoAtencion' => '35',
                    'codDiagnosticoPrincipal' => $diagnosticoPrincipal->cie10->code ?? 'Z012',
                    'codDiagnosticoRelacionado1' => $diagnosticosRelacionados->get(0)->cie10->code ?? null,
                    'codDiagnosticoRelacionado2' => $diagnosticosRelacionados->get(1)->cie10->code ?? null,
                    'codDiagnosticoRelacionado3' => $diagnosticosRelacionados->get(2)->cie10->code ?? null,
                    'tipoDiagnosticoPrincipal' => $diagnosticoPrincipal->diagnosisType->code ?? null,
                    'tipoDocumentoIdentificacion' => $service->doctor->doctorUser->ripsIdentificationType->code ?? '',
                    'numDocumentoIdentificacion' => $service->doctor->doctorUser->rips_identification_number,
                    'vrServicio' => $consulta->service_value ?? 0,
                    'conceptoRecaudo' => $consulta->collectionConcept->code ?? '05',
                    'valorPagoModerador' => $consulta->copayment_value ?? 0,
                    'numFEVPagoModerador' => $consulta->copayment_receipt_number ?? '',
                    'consecutivo' => $consecutivo++
                ];
            }
        }

        return $consultas;
    }

    protected function mapProcedimientos($services)
    {
        $procedimientos = [];
        $consecutivo = 1;

        foreach ($services as $service) {
            foreach ($service->procedures as $procedure) {
                //sdd($procedure);
                $procedimientos[] = [
                    'codPrestador' => $service->doctor->rips_provider_code,
                    'fechaInicioAtencion' => $service->service_datetime,
                    'idMIPRES' => $procedure->mipres_id ?? '',
                    'numAutorizacion' => $procedure->authorization_number ?? '',
                    'codProcedimiento' => $procedure->cups->code ?? '',
                    'viaIngresoServicioSalud' =>  $procedure->admissionRoute->code ?? 'Z012',
                    'modalidadGrupoServicioTecSal' =>  $procedure->serviceGroupMode->id ?? '',
                    'grupoServicios' => $procedure->serviceGroup->id ?? '',
                    'codServicio' => $procedure->service->code ?? '',
                    'finalidadTecnologiaSalud' => $procedure->technologyPurpose->code ?? '',
                    'tipoDocumentoIdentificacion' => $service->doctor->doctorUser->ripsIdentificationType->code ?? '',
                    'numDocumentoIdentificacion' => $service->doctor->doctorUser->rips_identification_number,
                    'codDiagnosticoPrincipal' => $procedure->cie10->code ?? 'Z012',
                    'codDiagnosticoRelacionado' => $procedure->surgeryCie10->code ?? null,
                    'codComplicacion' => $procedure->complicationCie10->code ?? null,
                    'vrServicio' => $procedure->service_value ?? 0,
                    'conceptoRecaudo' => '05',
                    'valorPagoModerador' => $procedure->copayment_value ?? 0,
                    'numFEVPagoModerador' => $procedure->copayment_receipt_number ?? '',
                    'consecutivo' => $consecutivo++
                ];
            }
        }

        return $procedimientos;
    }
}