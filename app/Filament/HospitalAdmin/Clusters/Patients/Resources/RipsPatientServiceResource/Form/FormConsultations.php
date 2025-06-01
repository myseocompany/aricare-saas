<?php

namespace App\Filament\HospitalAdmin\Clusters\Patients\Resources\RipsPatientServiceResource\Form;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;

class FormConsultations
{
    public static function make(Form $form): Form
    {
        return $form->schema([
            Repeater::make('consultations')
                ->label('')
                ->relationship('consultations')
                ->schema([
                    Grid::make()
                        ->schema([
                            Grid::make(3) // 3 columnas para los campos de datos
                                ->schema([
                                    Select::make('rips_cups_id')
                                        ->label('Procedimiento')
                                        ->options(
                                            \App\Models\Rips\RipsCups::where('description', 'Capítulo 16 CONSULTA, MONITORIZACIÓN Y PROCEDIMIENTOS DIAGNÓSTICOS')
                                                ->pluck('name', 'id')
                                        )
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

                                    Select::make('rips_collection_concept_id')
                                        ->label('Concepto de Recaudo')
                                        ->options(\App\Models\Rips\RipsCollectionConcept::pluck('name', 'id'))
                                        ->searchable()
                                        ->inlineLabel()
                                        ->required(),

                                    
                                ])
                                ->columns(1)
                                ->columnSpan(8), // 2/3 (8 columnas de 12)

                            Grid::make(1) // 1 columna, valores uno debajo del otro
                                ->schema([
                                    TextInput::make('copayment_receipt_number')
                                        ->label('Número del Recibo')
                                        ->maxLength(30)
                                        ->nullable(),
                                    TextInput::make('service_value')
                                        ->label('Valor del Servicio')
                                        ->numeric()
                                        ->prefix('$')
                                        ->default(0)
                                        ->required(),

                                    TextInput::make('copayment_value')
                                        ->label('Valor del Copago')
                                        ->numeric()
                                        ->prefix('$')
                                        ->default(0)
                                        ->required(),

                                    Placeholder::make('total')
                                        ->label('Total')
                                        ->content(function ($get) {
                                            $serviceValue = (float) $get('service_value') ?? 0;
                                            $copaymentValue = (float) $get('copayment_value') ?? 0;
                                            $total = $serviceValue - $copaymentValue;
                                            return '$' . number_format($total, 0, ',', '.');
                                        }),
                                ])
                                ->columns(1)
                                ->columnSpan(4), // 1/3 (4 columnas de 12)
                        ])
                        ->columns(12), // Grid general en 12 columnas

                    // Diagnósticos
                    ...FormConsultationDiagnoses::schema(),
                ])
                ->columns(1)
                ->defaultItems(0)
                ->minItems(0)
                ->maxItems(1)
                ->createItemButtonLabel('Añadir Consulta'),
        ]);
    }
}
