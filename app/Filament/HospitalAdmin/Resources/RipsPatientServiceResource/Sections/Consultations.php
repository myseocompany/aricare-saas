<?php

namespace App\Filament\HospitalAdmin\Resources\RipsPatientServiceResource\Sections;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
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
                        ->label('Consulta')
                        ->relationship()
                        ->schema([
                            

                            Select::make('cups_id')
                                ->label('Consulta')
                                ->options(
                                    \App\Models\Cups::where('description', 'CapItulo 16 CONSULTA, MONITORIZACION Y PROCEDIMIENTOS DIAGNOSTICOS')
                                        ->pluck('name', 'id')
                                )
                                ->searchable()
                                ->required(),

                            Select::make('service_group_id')
                                ->label('Grupo de Servicio')
                                ->options(\App\Models\RipsServiceGroup::all()->pluck('name', 'id'))
                                ->searchable()
                                ->required(),

                            Select::make('service_id')
                                ->label('Servicio')
                                ->options(
                                    \App\Models\RipsService::all()->mapWithKeys(fn ($s) => [
                                        $s->id => "{$s->code} - {$s->name}"
                                    ])
                                )
                                ->searchable()
                                ->required(),

                            Select::make('technology_purpose_id')
                                ->label('Finalidad Tecnológica')
                                ->options(\App\Models\RipsTechnologyPurpose::all()->pluck('name', 'id'))
                                ->searchable()
                                ->required(),

                            Select::make('collection_concept_id')
                                ->label('Concepto de Recaudo')
                                ->options(\App\Models\RipsCollectionConcept::all()->pluck('name', 'id'))
                                ->searchable()
                                ->required(),

                            TextInput::make('service_value')
                                ->label('Valor del Servicio')
                                ->numeric()
                                ->required(),

                            TextInput::make('copayment_value')
                                ->label('Valor del Copago')
                                ->numeric()
                                ->nullable(),

                            TextInput::make('copayment_receipt_number')
                                ->label('Número del Recibo')
                                ->maxLength(30)
                                ->nullable(),
                            Diagnoses::make(),
                        ])
                        ->columns(2)
                        ->columnSpanFull()
                        ->createItemButtonLabel('+') // Oculta botón por defecto
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
                            $current[] = []; // Añadir consulta vacía
                            $set('consultations', $current);
                        }),
                ]),
        ];
    }
}
