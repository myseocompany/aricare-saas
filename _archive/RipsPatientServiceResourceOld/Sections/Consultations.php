<?php

namespace App\Filament\HospitalAdmin\Resources\RipsPatientServiceResource\Sections;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Components\Actions\Action;

class Consultations
{
    public static function make(): array
    {
        return [
            Section::make('Consulta')
                ->schema([
                    Repeater::make('consultations')
                        ->label('')
                        ->relationship('consultations')
                        ->schema([
                            Select::make('rips_cups_id')
                                ->label('Procedimiento')
                                ->options(
                                    \App\Models\RipsCups::where('description', 'CapItulo 16 CONSULTA, MONITORIZACION Y PROCEDIMIENTOS DIAGNOSTICOS')
                                        ->pluck('name', 'id')
                                )
                                ->searchable()
                                ->required(),

                            Select::make('rips_service_group_id')
                                ->label('Grupo de Servicio')
                                ->options(\App\Models\RipsServiceGroup::all()->pluck('name', 'id'))
                                ->searchable()
                                ->required(),

                            Select::make('rips_service_id')
                                ->label('Servicio')
                                ->options(
                                    \App\Models\RipsService::all()->mapWithKeys(fn ($s) => [
                                        $s->id => "{$s->code} - {$s->name}"
                                    ])
                                )
                                ->searchable()
                                ->required(),

                            Select::make('rips_technology_purpose_id')
                                ->label('Finalidad Tecnológica')
                                ->options(\App\Models\RipsTechnologyPurpose::all()->pluck('name', 'id'))
                                ->searchable()
                                ->required(),

                            Select::make('rips_collection_concept_id')
                                ->label('Concepto de Recaudo')
                                ->options(\App\Models\RipsCollectionConcept::all()->pluck('name', 'id'))
                                ->searchable()
                                ->required(),

                            TextInput::make('service_value')
                                ->label('Valor del Servicio')
                                ->numeric()
                                ->default(0)
                                ->rules(['required', 'numeric', 'min:0']),

                            TextInput::make('copayment_value')
                                ->label('Valor del Copago')
                                ->numeric()
                                ->default(0)
                                ->rules(['required', 'numeric', 'min:0']),

                            TextInput::make('copayment_receipt_number')
                                ->label('Número del Recibo')
                                ->maxLength(30)
                                ->nullable(),

                            Diagnoses::make(),
                        ])
                        ->columns(2)
                        ->columnSpanFull()
                        ->createItemButtonLabel('+')
                        ->deletable(true)
                        ->reorderable(false)
                        ->defaultItems(0),
                ])
                ->collapsible(false)
                ->headerActions([
                    Action::make('add-consultation')
                        ->label('Añadir consulta')
                        ->icon('heroicon-m-plus')
                        ->color('primary')
                        ->hidden(fn (Get $get) => count($get('consultations') ?? []) >= 1)
                        ->action(function (Get $get, Set $set) {
                            $current = $get('consultations') ?? [];
                            $current[] = [];
                            $set('consultations', $current);
                        }),
                ]),
        ];
    }
}
