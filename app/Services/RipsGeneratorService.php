<?php

namespace App\Services;

use Carbon\Carbon;

class RipsGeneratorService
{
    /**
     * Genera los datos RIPS agrupados por factura y paciente, a partir de servicios en un rango de fechas y un convenio.
     *
     * @param int $agreementId ID del convenio
     * @param string $startDate Fecha inicio (formato string)
     * @param string $endDate Fecha fin (formato string)
     * @return array Datos RIPS estructurados para cada factura
     */
    public function generateByServices($agreementId, $startDate, $endDate)
    {
        // 1. Obtener todos los servicios en el rango de fechas para el convenio dado.
        //    Se usa tabla 'rips_patient_services', filtrando por convenio y rango de fechas.
        //    También se cargan relaciones necesarias para luego obtener detalles: paciente, factura, consultas y procedimientos.
        $services = \DB::table('rips_patient_services')
            ->where('rips_agreement_id', $agreementId)
            ->whereBetween('service_datetime', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ])
            ->with([
                'patient',
                'invoice', // relación con tabla de facturas (rips_tenant_invoices)
                'consultations.cups',
                'procedures.cups'
            ])
            ->get()
            // Agrupar servicios por el ID de la factura a la que pertenecen.
            ->groupBy('rips_tenant_invoices_id');

        $ripsData = [];

        // Recorrer cada grupo de servicios agrupados por factura
        foreach ($services as $invoiceId => $invoiceServices) {
            // Tomar el primer servicio para acceder a la información de la factura (porque todos comparten factura)
            $firstService = $invoiceServices->first();
            $invoice = $firstService->invoice;

            // Construir la estructura básica del elemento RIPS para esta factura
            $ripsItem = [
                'numDocumentoIdObligado' => $invoice->provider_document, // Documento del proveedor
                'numFactura' => $invoice->invoice_number,               // Número de factura
                'tipoNota' => $invoice->note_type,                      // Tipo de nota (si hay)
                'numNota' => $invoice->note_number,                     // Número de nota (si hay)
                'usuarios' => []                                        // Aquí irán los pacientes con sus servicios
            ];

            // Agrupar los servicios por paciente para esta factura
            $servicesByPatient = $invoiceServices->groupBy('patient_id');

            // Recorrer cada paciente y sus servicios dentro de esta factura
            foreach ($servicesByPatient as $patientId => $patientServices) {
                // Obtener el paciente desde el primer servicio del grupo (todos son del mismo paciente)
                $patient = $patientServices->first()->patient;

                // Mapear paciente a la estructura RIPS estándar
                $usuario = $this->mapPatientToRips($patient, $patientServices);

                // Agregar un consecutivo para identificar al paciente en esta factura
                $usuario['consecutivo'] = count($ripsItem['usuarios']) + 1;

                // Procesar los servicios de este paciente y agregarlos
                $usuario['servicios'] = $this->processServices($patientServices);

                // Agregar este paciente con sus servicios al listado de usuarios de la factura
                $ripsItem['usuarios'][] = $usuario;
            }

            // Agregar este grupo de datos RIPS junto con el archivo XML codificado en base64 (si la factura es electrónica)
            $ripsData[] = [
                'rips' => $ripsItem,
                'xmlFevFile' => $invoice->is_electronic ? base64_encode($invoice->xml_content) : null
            ];
        }

        // Retornar todo el conjunto de datos RIPS
        return $ripsData;
    }

    /**
     * Mapea un objeto paciente a la estructura requerida para RIPS.
     *
     * @param object $patient Objeto paciente
     * @return array Datos del paciente en formato RIPS
     */
    protected function mapPatientToRips($patient, $patientServices)
    {
        return [
            'tipoDocumentoIdentificacion' => $patient->document_type,
            'numDocumentoIdentificacion' => $patient->patient_unique_id,
            'tipoUsuario' => '12', // Valor fijo predeterminado (por ejemplo, paciente general)
            'fechaNacimiento' => $patient->birth_date,
            'codSexo' => $patient->sex_code,
            // Código país, municipio y zona territorial con valores por defecto si no están disponibles
            'codPaisResidencia' => $patient->ripsCountry->code ?? '170',
            'codMunicipioResidencia' => $patient->ripsMunicipality->code ?? '',
            'codZonaTerritorialResidencia' => $patient->zone_code ?? '',
            // Incapacidad: si alguno de los servicios tiene incapacidad, marcar "SI", sino "NO"
            'incapacidad' => $patientServices->contains('has_incapacity', 1) ? 'SI' : 'NO',
            'codPaisOrigen' => $patient->countryOfOrigin->code ?? '170'
        ];
    }

    /**
     * Procesa los servicios de un paciente, separándolos en consultas y procedimientos.
     *
     * @param \Illuminate\Support\Collection $services Servicios del paciente
     * @return array Servicios agrupados en consultas y procedimientos
     */
    protected function processServices($services)
    {
        $result = [
            'consultas' => [],
            'procedimientos' => []
        ];

        $consultaConsecutivo = 1;     // Contador para consultas
        $procedimientoConsecutivo = 1; // Contador para procedimientos

        foreach ($services as $service) {
            // Procesar la consulta (solo la primera por servicio)
            if ($service->consultations->isNotEmpty()) {
                $consulta = $service->consultations->first();
                $result['consultas'][] = $this->mapConsulta($service, $consulta, $consultaConsecutivo++);
            }

            // Procesar procedimientos (puede haber varios por servicio)
            foreach ($service->procedures as $procedure) {
                $result['procedimientos'][] = $this->mapProcedimiento($service, $procedure, $procedimientoConsecutivo++);
            }
        }

        return $result;
    }

    /**
     * Mapea una consulta a la estructura RIPS con los campos necesarios.
     *
     * @param object $service Servicio asociado
     * @param object $consulta Consulta médica
     * @param int $consecutivo Número consecutivo de consulta
     * @return array Datos de la consulta en formato RIPS
     */
    protected function mapConsulta($service, $consulta, $consecutivo)
    {
        return [
            'codPrestador' => $service->location_code,
            'fechaInicioAtencion' => $service->service_datetime,
            'codConsulta' => $consulta->cups->code,
            'modalidadGrupoServicioTecSal' => '01',
            'grupoServicios' => $consulta->serviceGroup->code ?? '01',
            'codServicio' => $consulta->service->code ?? '',
            'finalidadTecnologiaSalud' => $consulta->technologyPurpose->code ?? '12',
            'causaMotivoAtencion' => '35',
            'codDiagnosticoPrincipal' => $consulta->cie10->code ?? '',
            'tipoDiagnosticoPrincipal' => '02',
            'tipoDocumentoIdentificacion' => $service->patient->document_type,
            'numDocumentoIdentificacion' => $service->patient->patient_unique_id,
            'vrServicio' => $consulta->service_value ?? 0,
            'conceptoRecaudo' => $consulta->collectionConcept->code ?? '05',
            'valorPagoModerador' => $consulta->copayment_value ?? 0,
            'numFEVPagoModerador' => $consulta->copayment_receipt_number ?? '',
            'consecutivo' => $consecutivo
        ];
    }

    /**
     * Mapea un procedimiento a la estructura RIPS con los campos necesarios.
     *
     * @param object $service Servicio asociado
     * @param object $procedure Procedimiento médico
     * @param int $consecutivo Número consecutivo de procedimiento
     * @return array Datos del procedimiento en formato RIPS
     */
    protected function mapProcedimiento($service, $procedure, $consecutivo)
    {
        return [
            'codPrestador' => $service->location_code,
            'fechaInicioAtencion' => $service->service_datetime,
            'idMIPRES' => $procedure->mipres_id ?? '',
            'numAutorizacion' => $procedure->authorization_number ?? '',
            'codProcedimiento' => $procedure->cups->code,
            'viaIngresoServicioSalud' => '01',
            'modalidadGrupoServicioTecSal' => '09',
            'grupoServicios' => '01',
            'finalidadTecnologiaSalud' => '12',
            'tipoDocumentoIdentificacion' => $service->patient->document_type,
            'numDocumentoIdentificacion' => $service->patient->patient_unique_id,
            'codDiagnosticoPrincipal' => $procedure->cie10->code ?? '',
            'codDiagnosticoRelacionado' => $procedure->surgeryCie10->code ?? null,
            'vrServicio' => $procedure->service_value ?? 0,
            'conceptoRecaudo' => '05',
            'valorPagoModerador' => $procedure->copayment_value ?? 0,
            'numFEVPagoModerador' => $procedure->copayment_receipt_number ?? '',
            'consecutivo' => $consecutivo
        ];
    }
}
