<?php

namespace App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsTenantPayerAgreement\RipsTenantPayerAgreementResource\Form;

use Filament\Forms;

class AgreementMinimalForm
{
    public static function schema(): array
    {
        return [
            Forms\Components\Hidden::make('tenant_id')
                ->default(fn () => auth()->user()->tenant_id)
                ->required(),

            Forms\Components\TextInput::make('name')
                ->label('Nombre del convenio')
                ->required(),

            Forms\Components\TextInput::make('code')
                ->label('Código')
                ->required(),
            /*
            Forms\Components\Textarea::make('description')
                ->label('Descripción')
                ->maxLength(500),

            Forms\Components\DatePicker::make('start_date')
                ->label('Fecha de inicio')
                ->required(),

            Forms\Components\DatePicker::make('end_date')
                ->label('Fecha de finalización')
                ->afterOrEqual('start_date')
                ->nullable(),
                */
        ];
    }
}
