<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Rips\RipsPatientServices;
use App\Models\Rips\RipsBillingDocument;
use Illuminate\Support\Facades\Auth;

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
            // Configuración diferente para facturas vs notas
            if ($withInvoice) {
                $documentData = [
                    'numDocumentoIdObligado' => $tenantId, // Usamos el tenant_id aquí
                    'numFactura' => $document->document_number ?? null,
                    'tipoNota' => null,
                    'numNota' => null,
                    'idRelacion' => $document->id ?? null
                ];
            } else {
                $documentData = [
                    'numDocumentoIdObligado' => $tenantId, // Usamos el tenant_id aquí
                    'numFactura' => null,
                    'tipoNota' => 'RS',
                    'numNota' => $document->document_number ?? null,
                    'idRelacion' => $document->id ?? null
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
            'tipoDocumentoIdentificacion' => $patient->document_type,
            'numDocumentoIdentificacion' => $patient->patient_unique_id,
            'tipoUsuario' => '12',
            'fechaNacimiento' => $patient->birth_date,
            'codSexo' => $patient->sex_code,
            'codPaisResidencia' => $patient->ripsCountry->code ?? '170',
            'codMunicipioResidencia' => $patient->ripsMunicipality->code ?? '',
            'codZonaTerritorialResidencia' => $patient->zone_code ?? '01',
            'incapacidad' => $patientServices->contains('has_incapacity', 1) ? 'SI' : 'NO',
            'codPaisOrigen' => $patient->countryOfOrigin->code ?? '170'
        ];
    }

    protected function processServices($services)
    {
        return [
            'consultas' => $this->mapConsultas($services),
            'procedimientos' => $this->mapProcedimientos($services)
        ];
    }

    protected function mapConsultas($services)
    {
        $consultas = [];
        $consecutivo = 1;

        foreach ($services as $service) {
            foreach ($service->consultations as $consulta) {
                $diagnosticos = $consulta->diagnoses->sortBy('sequence');
                $diagnosticoPrincipal = $diagnosticos->firstWhere('sequence', 1);
                $diagnosticosRelacionados = $diagnosticos->where('sequence', '>', 1)->take(3);

                $consultas[] = [
                    'codPrestador' => $service->location_code,
                    'fechaInicioAtencion' => $service->service_datetime,
                    'codConsulta' => $consulta->cups->code ?? '',
                    'modalidadGrupoServicioTecSal' => '01',
                    'grupoServicios' => $consulta->serviceGroup->code ?? '01',
                    'codServicio' => $consulta->service->code ?? 334,
                    'finalidadTecnologiaSalud' => $consulta->technologyPurpose->code ?? '12',
                    'causaMotivoAtencion' => '35',
                    'codDiagnosticoPrincipal' => $diagnosticoPrincipal->cie10->code ?? 'Z012',
                    'codDiagnosticoRelacionado1' => $diagnosticosRelacionados->get(0)->cie10->code ?? null,
                    'codDiagnosticoRelacionado2' => $diagnosticosRelacionados->get(1)->cie10->code ?? null,
                    'codDiagnosticoRelacionado3' => $diagnosticosRelacionados->get(2)->cie10->code ?? null,
                    'tipoDiagnosticoPrincipal' => '3',
                    'tipoDocumentoIdentificacion' => $service->patient->document_type,
                    'numDocumentoIdentificacion' => $service->patient->patient_unique_id,
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
                $procedimientos[] = [
                    'codPrestador' => $service->location_code,
                    'fechaInicioAtencion' => $service->service_datetime,
                    'idMIPRES' => $procedure->mipres_id ?? '',
                    'numAutorizacion' => $procedure->authorization_number ?? '',
                    'codProcedimiento' => $procedure->cups->code ?? '',
                    'viaIngresoServicioSalud' => '01',
                    'modalidadGrupoServicioTecSal' => '09',
                    'grupoServicios' => '01',
                    'codServicio' => 334,
                    'finalidadTecnologiaSalud' => '12',
                    'tipoDocumentoIdentificacion' => $service->patient->document_type,
                    'numDocumentoIdentificacion' => $service->patient->patient_unique_id,
                    'codDiagnosticoPrincipal' => $procedure->cie10->code ?? 'Z012',
                    'codDiagnosticoRelacionado' => $procedure->surgeryCie10->code ?? null,
                    'codComplicacion' => null,
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