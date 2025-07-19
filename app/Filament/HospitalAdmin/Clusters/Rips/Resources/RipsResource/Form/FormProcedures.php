<?php

namespace App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsResource\Form;

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
                                        ->options(
                                            \App\Models\Rips\RipsAdmissionRoute::all()
                                                ->mapWithKeys(fn ($route) => [$route->id => "{$route->code} - {$route->name}"])
                                        )
                                        ->searchable()
                                        ->preload() // 👈 fuerza carga inmediata
                                        ->inlineLabel()
                                        ->required(),



                                    Select::make('rips_service_group_mode_id')
                                        ->label('Modo del Grupo de Servicio')
                                        ->options(
                                            \App\Models\Rips\RipsServiceGroupMode::all()
                                                ->mapWithKeys(function ($item) {
                                                    $formatted = ucfirst(strtolower($item->name));
                                                    return [$item->id => "{$item->id} - {$formatted}"];
                                                })
                                                ->toArray()
                                        )
                                        ->searchable()
                                        ->inlineLabel()
                                        ->required(),

                                    Select::make('rips_service_group_id')
                                        ->label('Grupo de Servicio')
                                        ->options(
                                            \App\Models\Rips\RipsServiceGroup::all()
                                                ->mapWithKeys(function ($item) {
                                                    return [$item->id => "{$item->id} - {$item->name}"];
                                                })
                                                ->toArray()
                                        )
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
                                        ->searchable()
                                        ->preload()
                                        ->options(function () {
                                            return \App\Models\Rips\RipsTechnologyPurpose::query()
                                                ->select('id', 'code', 'name')
                                                ->orderBy('code')
                                                ->limit(20)
                                                ->get()
                                                ->mapWithKeys(fn ($item) => [
                                                    $item->id => "{$item->code} - {$item->name}"
                                                ]);
                                        })
                                        ->getSearchResultsUsing(function (string $search) {
                                            return \App\Models\Rips\RipsTechnologyPurpose::query()
                                                ->select('id', 'code', 'name')
                                                ->where('code', 'like', "%{$search}%")
                                                ->orWhere('name', 'like', "%{$search}%")
                                                ->orderBy('code')
                                                ->limit(20)
                                                ->get()
                                                ->mapWithKeys(fn ($item) => [
                                                    $item->id => "{$item->code} - {$item->name}"
                                                ]);
                                        })
                                        ->getOptionLabelUsing(function ($value) {
                                            $item = \App\Models\Rips\RipsTechnologyPurpose::select('code', 'name')->find($value);
                                            return $item ? "{$item->code} - {$item->name}" : $value;
                                        })
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
                                        ->label('Procedimientos')
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
                                        ->inlineLabel(),
                                        


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
                                        ->inlineLabel(),
                                        


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
