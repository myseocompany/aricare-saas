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

                                    Select::make('rips_collection_concept_id')
                                        ->label('Concepto de Recaudo')
                                        ->options(\App\Models\Rips\RipsCollectionConcept::pluck('name', 'id'))
                                        ->searchable()
                                        ->inlineLabel()
                                        ->required(),

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
                                        ->options(function (string $search = null) {
                                            return \App\Models\Rips\RipsCups::query()
                                                ->when($search, fn($query, $search) => $query->where('name', 'like', "%{$search}%"))
                                                ->limit(20)
                                                ->pluck('name', 'id');
                                        })
                                        ->required()
                                        ->inlineLabel(),

                                    Select::make('cie10_id')
                                        ->label('Diagnóstico')
                                        ->searchable()
                                        ->options(function (string $search = null) {
                                            return \App\Models\Rips\Cie10::query()
                                                ->when($search, fn($query, $search) => $query->where('description', 'like', "%{$search}%")
                                                    ->orWhere('code', 'like', "%{$search}%"))
                                                ->limit(20)
                                                ->pluck('description', 'id');
                                        })
                                        ->inlineLabel(),

                                    Select::make('surgery_cie10_id')
                                        ->label('CIE10 Cirugía')
                                        ->searchable()
                                        ->options(function (string $search = null) {
                                            return \App\Models\Rips\Cie10::query()
                                                ->when($search, fn($query, $search) => $query->where('description', 'like', "%{$search}%"))
                                                ->limit(20)
                                                ->pluck('description', 'id');
                                        })
                                        ->inlineLabel(),

                                    Select::make('rips_complication_cie10_id')
                                        ->label('Diagnóstico de Complicación (CIE10)')
                                        ->searchable()
                                        ->options(function (string $search = null) {
                                            return \App\Models\Rips\Cie10::query()
                                                ->when($search, fn($query, $search) => $query->where('description', 'like', "%{$search}%")
                                                    ->orWhere('code', 'like', "%{$search}%"))
                                                ->limit(20)
                                                ->pluck('description', 'id');
                                        })
                                        ->inlineLabel(),

                                ])
                                ->columns(1)
                                ->columnSpan(8),

                            // 🟥 Derecha - Valores económicos
                            Grid::make(1)
                                ->schema([
                                    TextInput::make('copayment_receipt_number')
                                        ->label('Número del Recibo')
                                        ->maxLength(30)
                                        ->nullable()
                                        ->inlineLabel(),

                                    TextInput::make('service_value')
                                        ->label('Valor del Servicio')
                                        ->numeric()
                                        ->prefix('$')
                                        ->default(0)
                                        ->required()
                                        ->inlineLabel(),

                                    TextInput::make('copayment_value')
                                        ->label('Valor del Copago')
                                        ->numeric()
                                        ->prefix('$')
                                        ->default(0)
                                        ->required()
                                        ->inlineLabel(),

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
