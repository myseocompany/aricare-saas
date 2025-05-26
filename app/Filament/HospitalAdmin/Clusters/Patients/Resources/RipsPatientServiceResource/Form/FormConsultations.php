<?php

namespace App\Filament\HospitalAdmin\Clusters\Patients\Resources\RipsPatientServiceResource\Form;

use Filament\Forms;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Auth;

class FormConsultations
{
    public static function make(Form $form): Form
    {
        return $form->schema([
            // **Formulario de consultas (por encima de los procedimientos)**
            Forms\Components\Repeater::make('consultations')  // Repetidor para agregar múltiples consultas
                ->relationship() // Vincula la relación de consultas
                ->schema([
                    Forms\Components\TextInput::make('consultation_id')
                        ->label('ID de consulta'),

                    Forms\Components\TextInput::make('consultation_description')
                        ->label('Descripción de la consulta'),

                    Forms\Components\Select::make('rips_cups_id')
                        ->label('CUPS')
                        ->searchable() // Habilita la búsqueda en vivo
                        ->options(function (string $search = null) {
                            return \App\Models\Rips\RipsCups::query()
                                ->when($search, function ($query, $search) {
                                    return $query->where('name', 'like', "%{$search}%");
                                })
                                ->limit(20) // Limita la cantidad de registros devueltos
                                ->pluck('name', 'id'); // Devuelve solo los campos necesarios
                        })
                        ->required(),

                    Forms\Components\TextInput::make('service_value')
                        ->label('Valor del servicio')
                        ->numeric(),

                    Forms\Components\TextInput::make('copayment_value')
                        ->label('Valor del copago')
                        ->numeric(),

                    Forms\Components\TextInput::make('copayment_receipt_number')
                        ->label('Número de recibo del copago'),
                ])
                ->columns(2) // Número de columnas para los campos de la consulta
                ->defaultItems(fn () => 0)
                ->createItemButtonLabel('Añadir consulta'), // Botón para agregar consulta
        ]);
    }
}
