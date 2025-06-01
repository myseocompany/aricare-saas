<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Rips\RipsBillingDocument;
use App\Models\Rips\RipsTenantPayerAgreement;
use App\Models\Rips\RipsPatientService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateRipsPatientServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_a_patient_service_and_billing_document()
    {
        // Crear un usuario para autenticación
        $user = User::factory()->create([
            'tenant_id' => 'test-tenant-id',
        ]);

        // Crear un convenio válido
        $agreement = RipsTenantPayerAgreement::factory()->create([
            'payer_id' => 1,
        ]);

        $this->actingAs($user);

        $data = [
            'patient_id' => 1,
            'doctor_id' => 2,
            'tenant_id' => $user->tenant_id,
            'has_incapacity' => false,
            'service_datetime' => now()->format('Y-m-d H:i:s'),
            'invoice_number' => 'INV-001',
            'agreement_id' => $agreement->id,
        ];

        $response = $this->post(route('filament.hospital-admin.resources.rips-patient-services.create'), $data);

        $this->assertDatabaseHas('rips_billing_documents', [
            'document_number' => 'INV-001',
            'tenant_id' => $user->tenant_id,
            'agreement_id' => $agreement->id,
        ]);

        $this->assertDatabaseHas('rips_patient_services', [
            'patient_id' => 1,
            'doctor_id' => 2,
            'tenant_id' => $user->tenant_id,
        ]);

        // Verificar que efectivamente están enlazados
        $service = RipsPatientService::first();
        $this->assertNotNull($service->billing_document_id);

        $billingDocument = RipsBillingDocument::find($service->billing_document_id);
        $this->assertEquals('INV-001', $billingDocument->document_number);
    }
}
