<?php

namespace App\Filament\HospitalAdmin\Resources\RipsPatientServiceResource\Sections;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class Procedures
{
    public static function make(): array
    {
        return [
            Repeater::make('procedures')
                ->label('Procedimientos')
                ->relationship()
                ->schema([
                    Select::make('cups_id')
                        ->label('CUPS')
                        ->options(\App\Models\Cups::all()->pluck('code', 'id'))
                        ->searchable()
                        ->required(),

                    TextInput::make('authorization_number')
                        ->label('Autorización')
                        ->maxLength(30)
                        ->nullable(),

                    TextInput::make('mipres_id')
                        ->label('MIPRES')
                        ->maxLength(30)
                        ->nullable(),

                    TextInput::make('service_value')
                        ->label('Valor del servicio')
                        ->numeric()
                        ->nullable(),

                    TextInput::make('copayment_value')
                        ->label('Valor del copago')
                        ->numeric()
                        ->nullable(),

                    TextInput::make('copayment_receipt_number')
                        ->label('Recibo de copago')
                        ->maxLength(30)
                        ->nullable(),
                ])
                ->defaultItems(0)
                ->columns(2)
                ->columnSpanFull(),
        ];
    }
}
