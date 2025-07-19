<?php

namespace App\Filament\HospitalAdmin\Clusters\RIPS\Resources\RipsResource\Pages;

use App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsResource;
use App\Models\Rips\RipsBillingDocument;


use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Filament\Actions;

use App\Actions\Rips\CreateServiceTemplateFromService;
use App\Actions\Rips\LoadTemplateToForm;
use App\Actions\Rips\SyncConsultationsAndProcedures;
use Livewire\Attributes\On;

class CreateRips extends CreateRecord
{

    protected static string $resource = RipsResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        Log::info("CreateRipsPatientService=>", $data);
        $tenantId = auth()->user()->tenant_id;

        $billingDocument = null;
        if (!empty($data['billing_document_id'])) {
            $billingDocument = RipsBillingDocument::where('tenant_id', $tenantId)
                ->find($data['billing_document_id']);
            if ($billingDocument) {
                $billingDocument->update([
                    'issued_at' => $data['service_datetime'],
                ]);
            }
        }

        $record = static::getModel()::create($data);

        if ($billingDocument) {
            $record->billing_document_id = $billingDocument->id;
            $record->save();
        }
        
        app(SyncConsultationsAndProcedures::class)($record, $data);

        return $record;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        foreach ($data['consultations'] as &$consultation) {
            $diagnoses = [];

            if (!empty($consultation['principal_diagnoses'])) {
                foreach ($consultation['principal_diagnoses'] as $diagnosis) {
                    $diagnosis['sequence'] = 1;
                    $diagnoses[] = $diagnosis;
                }
            }

            if (!empty($consultation['related_diagnoses'])) {
                foreach ($consultation['related_diagnoses'] as $index => $cie10Id) {
                    $diagnoses[] = [
                        'cie10_id' => $cie10Id,
                        'rips_diagnosis_type_id' => null,
                        'sequence' => $index + 2,
                    ];
                }
            }

            $consultation['diagnoses'] = $diagnoses;

            // Eliminamos estos campos temporales, no existen en la DB
            unset($consultation['principal_diagnoses']);
            unset($consultation['related_diagnoses']);
        }

        return $data;
    }
    protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('index');
    }

    protected function afterCreate(): void
    {
        $data = $this->form->getState();

        if (!empty($data['save_as_template']) && !empty($data['template_name'])) {
            app(CreateServiceTemplateFromService::class)(
                $this->record, // RipsPatientService
                $data['template_name']
            );
        }
    }

    #[On('templateLoaded')]
    public function refreshForm(): void
    {
        // Recarga los datos en estado del formulario
        //$this->refreshFormData(/* opcional: campos específicos */);
        $this->fillForm();
    }
    
public function loadTemplate($templateId)
{
    $data = app(LoadTemplateToForm::class)($templateId);
    dd($data);
    if ($data) {
        $this->form->fill($data);
        $this->dispatch('templateLoaded');
    }
}




}
