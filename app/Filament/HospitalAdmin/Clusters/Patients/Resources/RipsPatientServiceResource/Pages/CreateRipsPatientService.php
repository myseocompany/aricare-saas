<?php

namespace App\Filament\HospitalAdmin\Clusters\Patients\Resources\RipsPatientServiceResource\Pages;

use App\Filament\HospitalAdmin\Clusters\Patients\Resources\RipsPatientServiceResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Rips\RipsBillingDocument;
use App\Models\Rips\RipsTenantPayerAgreement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class CreateRipsPatientService extends CreateRecord
{
    protected static string $resource = RipsPatientServiceResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $tenantId = auth()->user()->tenant_id;

        // 🚨 Validaciones
        if (empty($data['invoice_number'])) {
            throw ValidationException::withMessages([
                'invoice_number' => 'El número de factura es requerido.',
            ]);
        }

        if (empty($data['agreement_id'])) {
            throw ValidationException::withMessages([
                'agreement_id' => 'El convenio/contrato es requerido.',
            ]);
        }

        // Verificar que no exista el número de factura en este tenant
        if (RipsBillingDocument::where('tenant_id', $tenantId)
            ->where('document_number', $data['invoice_number'])
            ->exists()) {
            throw ValidationException::withMessages([
                'invoice_number' => 'El número de factura ya existe en este tenant.',
            ]);
        }

        // Verificar que el acuerdo exista
        if (!RipsTenantPayerAgreement::where('id', $data['agreement_id'])->exists()) {
            throw ValidationException::withMessages([
                'agreement_id' => 'El convenio/contrato seleccionado no es válido.',
            ]);
        }

        // 🚀 Crear la factura
        $billingDocument = RipsBillingDocument::create([
            'tenant_id' => $tenantId,
            'type_id' => 1, // Factura
            'document_number' => $data['invoice_number'],
            'issued_at' => $data['service_datetime'],
            'agreement_id' => $data['agreement_id'],
            'total_amount' => 0,
            'net_amount' => 0,
        ]);

        // Asignar factura al servicio
        $data['billing_document_id'] = $billingDocument->id;

        // 🚨 Opcional: Limpiar campos si no quieres que se guarden
        unset($data['invoice_number']);
        unset($data['agreement_id']);

        // 🚀 Crear el servicio
        return static::getModel()::create($data);
    }
}
