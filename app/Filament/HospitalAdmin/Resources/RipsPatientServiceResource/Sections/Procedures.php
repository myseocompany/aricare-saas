<?php

namespace App\Filament\HospitalAdmin\Resources\RipsPatientServiceResource\Sections;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Components\Actions\Action;

class Procedures
{
    public static function make(): array
    {
        return [
            Section::make('Procedimientos')
                ->schema([
                    Repeater::make('procedures')
                        ->label('Procedimiento')
                        ->relationship()
                        ->schema([
                            Select::make('cups_id')
                                ->label('CUPS')
                                ->options(
                                    \App\Models\Cups::all()
                                        ->mapWithKeys(fn ($cups) => [
                                            $cups->id => "{$cups->code} - {$cups->name}"
                                        ])
                                )
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
                                ->label('Número de FEV')
                                ->maxLength(30)
                                ->nullable(),
                        ])
                        ->defaultItems(0)
                        ->columns(2)
                        ->columnSpanFull()
                        ->createItemButtonLabel('+') // 🔇 Ocultar botón por defecto
                        ->deletable(true)
                        ->reorderable(false),
                ])
                ->collapsible(false)
                ->headerActions([
                    Action::make('add-procedure')
                        ->label('Añadir procedimiento')
                        ->icon('heroicon-m-plus')
                        ->color('primary')
                        ->action(function (Get $get, Set $set) {
                            $current = $get('procedures') ?? [];
                            $current[] = []; // ➕ Añadir procedimiento vacío
                            $set('procedures', $current);
                        }),
                ]),
        ];
    }
}
