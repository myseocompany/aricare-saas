<?php

namespace App\Filament\HospitalAdmin\Resources\RipsPatientServiceResource\Pages;

use App\Filament\HospitalAdmin\Resources\RipsPatientServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateRipsPatientService extends CreateRecord
{
    protected static string $resource = RipsPatientServiceResource::class;
    public ?array $data = [];

    public function mount(): void
    {
        /*
        parent::mount();
        $emptyDiagnosis = [
            'cie10_id' => null,
            'rips_diagnosis_type_id' => null,
        ];

        $this->data = [
            'patient_id' => null,
            'doctor_id' => null,
            'has_incapacity' => false,
            'service_datetime' => now(),

            'collection_concept_id' => null,

            // Consultas inicializadas con 1 consulta y 1 diagnóstico mínimo
            'consultations' => [
                [
                    'rips_cups_id' => null,
                    'rips_service_group_id' => null,
                    'rips_service_id' => null,
                    'rips_technology_purpose_id' => null,
                    'rips_collection_concept_id' => null,
                    'diagnoses' => [
                        $emptyDiagnosis,
                        $emptyDiagnosis,
                        $emptyDiagnosis,
                        $emptyDiagnosis,
                    ],
                ],
            ],

            // Estructura vacía por si se agregan procedimientos luego
            'procedures' => [
                [
                    'rips_cups_id' => null,
                    'rips_service_group_id' => null,
                    'rips_service_id' => null,
                    'rips_technology_purpose_id' => null,
                    'rips_collection_concept_id' => null,
                    'diagnoses' => [
                        [
                            'cie10_id' => null,
                            'rips_diagnosis_type_id' => null,
                        ],
                    ],
                ],
            ],
        ];
        */
    }



    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id'] = Auth::user()->tenant_id;
        /*
        // Eliminar consultas no diligenciadas
        $data['consultations'] = collect($data['consultations'] ?? [])
            ->filter(function ($consultation) {
                // Considera la consulta diligenciada si al menos uno de estos campos tiene valor
                return $consultation['rips_cups_id'] || $consultation['rips_service_id'];
            })
            ->values()
            ->all();
        */
        return $data;
    }

    protected function rules(): array
    {
        return [
            // Aquí podrías reactivar y ajustar las reglas cuando estés listo
        ];
    }
}
