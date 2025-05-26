<?php

namespace App\Filament\HospitalAdmin\Clusters\Patients\Resources\RipsPatientServiceResource\Form;

use Filament\Forms;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Auth;

class FormProcedures
{
    public static function make(Form $form): Form
    {
        return $form->schema([
            // **Formulario de procedimientos (debajo de las consultas)**
            Forms\Components\Repeater::make('procedures')  // Usamos Repeater para agregar múltiples procedimientos
                ->relationship() // Vincula la relación de procedimientos
                ->schema([
                    Forms\Components\TextInput::make('mipres_id')
                        ->label('Mipres ID'),

                    Forms\Components\TextInput::make('authorization_number')
                        ->label('Número de autorización'),

                    Forms\Components\Select::make('rips_cups_id')
                        ->label('CUPS')
                        ->searchable() // Habilita la búsqueda en vivo
                        ->options(function (string $search = null) {
                            return \App\Models\Rips\RipsCups::query()
                                ->when($search, function ($query, $search) {
                                    return $query->where('name', 'like', "%{$search}%");
                                })
                                ->limit(20)
                                ->pluck('name', 'id'); // Devuelve solo los campos necesarios
                        })
                        ->required(),

                    Forms\Components\Select::make('cie10_id')
                        ->label('CIE10')
                        ->searchable()
                        ->options(function (string $search = null) {
                            return \App\Models\Rips\Cie10::query()
                                ->when($search, function ($query, $search) {
                                    return $query->where('description', 'like', "%{$search}%");
                                })
                                ->limit(20)
                                ->pluck('description', 'id');
                        }),

                    Forms\Components\Select::make('surgery_cie10_id')
                        ->label('CIE10 Cirugía')
                        ->searchable()
                        ->options(function (string $search = null) {
                            return \App\Models\Rips\Cie10::query()
                                ->when($search, function ($query, $search) {
                                    return $query->where('description', 'like', "%{$search}%");
                                })
                                ->limit(20)
                                ->pluck('description', 'id');
                        }),

                    Forms\Components\TextInput::make('service_value')
                        ->label('Valor del servicio')
                        ->numeric(),

                    Forms\Components\TextInput::make('copayment_value')
                        ->label('Valor del copago')
                        ->numeric(),

                    Forms\Components\TextInput::make('copayment_receipt_number')
                        ->label('Número de recibo del copago'),
                ])
                ->columns(2) // Número de columnas para los campos del procedimiento
                ->defaultItems(fn () => 0)
                ->createItemButtonLabel('Añadir procedimiento'), // Botón para agregar procedimiento
        ]);
    }
}
