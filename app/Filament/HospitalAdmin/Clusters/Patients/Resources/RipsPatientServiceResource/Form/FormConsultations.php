<?php

namespace App\Filament\HospitalAdmin\Clusters\Patients\Resources\RipsPatientServiceResource\Form;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Group;
use App\Filament\HospitalAdmin\Clusters\Patients\Resources\RipsPatientServiceResource\Form\FormConsultationDiagnoses;
use App\Filament\HospitalAdmin\Clusters\Patients\Resources\RipsPatientServiceResource\Form\FormConsultationSimpleDiagnoses;

class FormConsultations
{
    public static function make(Form $form): Form
    {
        return $form->schema([
            Repeater::make('consultations')
                ->label('')
                ->reorderable(false)
                ->default([])
                ->schema([
                    Grid::make()
                        ->schema([
                            Grid::make(3)
                                ->schema([
                                    Select::make('rips_cups_id')
                                        ->label(__('messages.rips.patientservice.rips_cups_id'))
                                        ->searchable()
                                        ->inlineLabel()
                                        ->required()
                                        ->getSearchResultsUsing(function (string $search) {
                                            return \App\Models\Rips\RipsCups::query()
                                                ->where('description', 'Capítulo 16 CONSULTA, MONITORIZACIÓN Y PROCEDIMIENTOS DIAGNÓSTICOS')
                                                ->where('name', 'like', "%{$search}%")
                                                ->orderBy('name')
                                                ->limit(20)
                                                ->pluck('name', 'id')
                                                ->toArray();
                                        })
                                        ->getOptionLabelUsing(fn ($value) =>
                                            optional(\App\Models\Rips\RipsCups::find($value))->name
                                        ),

                                    Select::make('rips_service_group_mode_id')
                                        ->label('Modo del Grupo de Servicio')
                                        ->options(\App\Models\Rips\RipsServiceGroupMode::pluck('name', 'id'))
                                        ->searchable()
                                        ->inlineLabel()
                                        ->required(),

                                    Select::make('rips_service_group_id')
                                        ->label('Grupo de Servicio')
                                        ->options(\App\Models\Rips\RipsServiceGroup::pluck('name', 'id'))
                                        ->searchable()
                                        ->inlineLabel()
                                        ->required(),

                                    Select::make('rips_service_id')
                                        ->label('Servicio')
                                        ->options(
                                            \App\Models\Rips\RipsService::all()->mapWithKeys(fn ($s) => [
                                                $s->id => "{$s->code} - {$s->name}"
                                            ])
                                        )
                                        ->searchable()
                                        ->inlineLabel()
                                        ->required(),

                                    Select::make('rips_technology_purpose_id')
                                        ->label('Finalidad Tecnológica')
                                        ->options(\App\Models\Rips\RipsTechnologyPurpose::pluck('name', 'id'))
                                        ->searchable()
                                        ->inlineLabel()
                                        ->required(),                                    

                                    Select::make('rips_service_reason_id')
                                        ->label('Motivo de Servicio')
                                        ->options(\App\Models\Rips\RipsServiceReason::all()->mapWithKeys(fn ($reason) => [
                                            $reason->id => "{$reason->code} - {$reason->name}"
                                        ]))
                                        ->searchable()
                                        ->inlineLabel()
                                        ->required(),

                                ])
                                ->columns(1)
                                ->columnSpan(8),

                            Grid::make(1)
                                ->schema([
                                    Select::make('rips_collection_concept_id')
                                        ->label('Concepto de Recaudo')
                                        ->options(\App\Models\Rips\RipsCollectionConcept::pluck('name', 'id'))
                                        ->searchable()
                                        ->required(),

                                    TextInput::make('copayment_receipt_number')
                                        ->label('Número FEV Pago Moderador')
                                        ->maxLength(30)
                                        ->nullable(),

                                    TextInput::make('copayment_value')
                                        ->label('Valor del Copago')
                                        ->numeric()
                                        ->prefix('$')
                                        ->default(0),

                                    TextInput::make('service_value')
                                        ->label('Valor del Servicio')
                                        ->numeric()
                                        ->prefix('$')
                                        ->default(0),
                                ])
                                ->columns(1)
                                ->columnSpan(4),
                        ])
                        ->columns(12),

                    Group::make([
                        Repeater::make('principal_diagnoses')
                            ->label(__('messages.rips.patientservice.principal_diagnoses'))
                            ->reorderable(false)
                            ->default([])
                            ->schema(FormConsultationDiagnoses::schema(true, 1))
                            ->minItems(1)
                            ->maxItems(1)
                            ->defaultItems(1)
                            ->columns(2)
                            ->createItemButtonLabel('Add Principal Diagnosis'),

                        Repeater::make('related_diagnoses')
                            ->label(__('messages.rips.patientservice.related_diagnoses'))
                            ->reorderable(false)
                            ->default([])
                            ->schema([
                                Select::make('cie10_id')
                                    ->label('Diagnóstico relacionado')
                                    ->options(function () {
                                        return \App\Models\Cie10::all()->mapWithKeys(function ($item) {
                                            return [$item->id => "{$item->code} - {$item->name}"];
                                        });
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                            ])
                            ->minItems(0)
                            ->maxItems(3)
                            ->columns(2)
                            ->createItemButtonLabel(__('messages.rips.patientservice.add_related_diagnosis'))
                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data, Forms\Components\RepeaterItem $item) {
                                $data['sequence'] = $item->getIndex() + 2;
                                return $data;
                            }),

                    ]),
                ])
                ->columns(1)
                ->defaultItems(0)
                ->minItems(0)
                ->maxItems(1)
                ->createItemButtonLabel('Añadir Consulta'),
        ]);
    }
}
