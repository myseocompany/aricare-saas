<?php

namespace App\Filament\HospitalAdmin\Resources\RipsPatientServiceResource\Sections;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class Consultations
{
    public static function make(): array
    {
        return [
            Repeater::make('consultations')
                ->label('Consulta')
                ->relationship()
                ->schema([
                   
                    Diagnoses::make(),
                    Select::make('cups_id')
                        ->label('Consulta')
                        ->options(\App\Models\Cups::where('description', 'CapItulo 16 CONSULTA, MONITORIZACION Y PROCEDIMIENTOS DIAGNOSTICOS')->pluck('name', 'id'))
                        ->searchable()
                        ->required(),

                    Select::make('service_group_id')
                        ->label('Grupo de Servicio')
                        ->options(\App\Models\RipsServiceGroup::all()->pluck('name', 'id'))
                        ->searchable()
                        ->required(),

                    Select::make('service_id')
                        ->label('Servicio')
                        ->options(\App\Models\RipsService::all()->mapWithKeys(fn ($s) => [$s->id => "{$s->code} - {$s->name}"]))
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
                ])
                ->defaultItems(0)
                ->columns(2)
                ->columnSpanFull(),
        ];
    }
}
