<?php

namespace App\Filament\HospitalAdmin\Clusters\Patients\Resources\RipsPatientServiceResource\Form;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;

class FormProcedures
{
    public static function make(Form $form): Form
    {
        return $form->schema([
            Repeater::make('procedures')
                ->label('')
                ->reorderable(false)
                ->default([])
                ->schema([
                    Grid::make()
                        ->schema([
                            // 🟦 Izquierda - Detalles del procedimiento
                            Grid::make(3)
                                ->schema([
                                    Select::make('rips_admission_route_id')
                                        ->label('Vía de Ingreso')
                                        ->options(\App\Models\Rips\RipsAdmissionRoute::pluck('name', 'id'))
                                        ->searchable()
                                        ->inlineLabel()
                                        ->required(),

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

                                    Select::make('rips_technology_purpose_id')
                                        ->label('Finalidad Tecnológica')
                                        ->options(\App\Models\Rips\RipsTechnologyPurpose::pluck('name', 'id'))
                                        ->searchable()
                                        ->inlineLabel(),


                                    TextInput::make('mipres_id')
                                        ->label('Mipres ID')
                                        ->maxLength(30)
                                        ->inlineLabel(),

                                    TextInput::make('authorization_number')
                                        ->label('Número de autorización')
                                        ->maxLength(30)
                                        ->inlineLabel(),

                                    Select::make('rips_cups_id')
                                        ->label('CUPS')
                                        ->searchable()
                                        ->getSearchResultsUsing(function (string $search) {
                                            return \App\Models\Rips\RipsCups::query()
                                                ->where('name', 'like', "%{$search}%")
                                                ->orWhere('code', 'like', "%{$search}%")
                                                ->limit(30)
                                                ->get()
                                                ->mapWithKeys(fn ($cups) => [$cups->id => "{$cups->code} - {$cups->name}"]);
                                        })
                                        ->getOptionLabelUsing(function ($value): ?string {
                                            $cups = \App\Models\Rips\RipsCups::find($value);
                                            return $cups ? "{$cups->code} - {$cups->name}" : null;
                                        })
                                        ->required()
                                        ->inlineLabel(),

                                    Select::make('cie10_id')
                                        ->label('Diagnóstico')
                                        ->searchable()
                                        ->getSearchResultsUsing(function (string $search) {
                                            return \App\Models\Rips\Cie10::query()
                                                ->where('description', 'like', "%{$search}%")
                                                ->orWhere('code', 'like', "%{$search}%")
                                                ->limit(50)
                                                ->get()
                                                ->mapWithKeys(fn ($cie) => [$cie->id => "{$cie->code} - {$cie->description}"]);
                                        })
                                        ->getOptionLabelUsing(function ($value): ?string {
                                            $cie = \App\Models\Rips\Cie10::find($value);
                                            return $cie ? "{$cie->code} - {$cie->description}" : null;
                                        })
                                        ->inlineLabel()
                                        ->required(),                               


                                    Select::make('surgery_cie10_id')
                                        ->label('CIE10 Cirugía')
                                        ->searchable()
                                        ->getSearchResultsUsing(function (string $search) {
                                            return \App\Models\Rips\Cie10::query()
                                                ->where('description', 'like', "%{$search}%")
                                                ->orWhere('code', 'like', "%{$search}%")
                                                ->limit(50)
                                                ->get()
                                                ->mapWithKeys(fn ($cie) => [$cie->id => "{$cie->code} - {$cie->description}"]);
                                        })
                                        ->getOptionLabelUsing(function ($value): ?string {
                                            $cie = \App\Models\Rips\Cie10::find($value);
                                            return $cie ? "{$cie->code} - {$cie->description}" : null;
                                        })
                                        ->inlineLabel()
                                        ->required(),


                                    Select::make('rips_complication_cie10_id')
                                        ->label('Diagnóstico de Complicación (CIE10)')
                                        ->searchable()
                                        ->getSearchResultsUsing(function (string $search) {
                                            return \App\Models\Rips\Cie10::query()
                                                ->where('description', 'like', "%{$search}%")
                                                ->orWhere('code', 'like', "%{$search}%")
                                                ->limit(50)
                                                ->get()
                                                ->mapWithKeys(fn ($cie) => [$cie->id => "{$cie->code} - {$cie->description}"]);
                                        })
                                        ->getOptionLabelUsing(function ($value): ?string {
                                            $cie = \App\Models\Rips\Cie10::find($value);
                                            return $cie ? "{$cie->code} - {$cie->description}" : null;
                                        })
                                        ->inlineLabel()
                                        ->required(),


                                ])
                                ->columns(1)
                                ->columnSpan(8),

                            // 🟥 Derecha - Valores económicos
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
                ])
                ->columns(1)
                ->defaultItems(0)
                ->minItems(0)
                ->createItemButtonLabel('Añadir Procedimiento'),
        ]);
    }
}
