<?php

namespace App\Filament\HospitalAdmin\Clusters\Patients\Resources\RipsPatientServiceResource\Form;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class FormConsultations
{
    public static function make(Form $form): Form
    {
        return $form->schema([
            Repeater::make('consultations')
                ->label('')
                ->relationship('consultations')
                ->schema([
                    Select::make('rips_cups_id')
                        ->label('Procedimiento')
                        ->options(
                            \App\Models\Rips\RipsCups::where('description', 'CapItulo 16 CONSULTA, MONITORIZACION Y PROCEDIMIENTOS DIAGNOSTICOS')
                                ->pluck('name', 'id')
                        )
                        ->searchable()
                        ->required(),

                    Select::make('rips_service_group_id')
                        ->label('Grupo de Servicio')
                        ->options(\App\Models\Rips\RipsServiceGroup::pluck('name', 'id'))
                        ->searchable()
                        ->required(),

                    Select::make('rips_service_id')
                        ->label('Servicio')
                        ->options(
                            \App\Models\Rips\RipsService::all()->mapWithKeys(fn ($s) => [
                                $s->id => "{$s->code} - {$s->name}"
                            ])
                        )
                        ->searchable()
                        ->required(),

                    Select::make('rips_technology_purpose_id')
                        ->label('Finalidad Tecnológica')
                        ->options(\App\Models\Rips\RipsTechnologyPurpose::pluck('name', 'id'))
                        ->searchable()
                        ->required(),

                    Select::make('rips_collection_concept_id')
                        ->label('Concepto de Recaudo')
                        ->options(\App\Models\Rips\RipsCollectionConcept::pluck('name', 'id'))
                        ->searchable()
                        ->required(),

                    TextInput::make('service_value')
                        ->label('Valor del Servicio')
                        ->numeric()
                        ->default(0)
                        ->required(),

                    TextInput::make('copayment_value')
                        ->label('Valor del Copago')
                        ->numeric()
                        ->default(0)
                        ->required(),

                    TextInput::make('copayment_receipt_number')
                        ->label('Número del Recibo')
                        ->maxLength(30)
                        ->nullable(),
                    ...FormConsultationDiagnoses::schema(),
                ])
                ->columns(2)
                ->defaultItems(0)
                ->createItemButtonLabel('Añadir consulta'),
        ]);
    }
}
