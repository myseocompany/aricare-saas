<?php

namespace App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsPayer\RipsPayerResource\Form;

use App\Models\Rips\RipsPayerType;
use Filament\Forms;

class PayerForm
{
    public static function schema(): array
    {
        return [
            Forms\Components\Hidden::make('tenant_id')
                ->default(fn () => auth()->user()->tenant_id)
                ->required(),

            Forms\Components\Section::make('Entidad responsable')
                ->schema([
                    Forms\Components\Select::make('type_id')
                        ->label(__('messages.rips.payer.type_id'))
                        ->options(RipsPayerType::pluck('name', 'id'))
                        ->required()
                        ->searchable()
                        ->native(false)
                        ->placeholder(__('messages.common.select')),

                    Forms\Components\TextInput::make('name')
                        ->label(__('messages.rips.payer.name'))
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('identification')
                        ->label(__('messages.rips.payer.identification'))
                        ->required()
                        ->maxLength(20),

                    Forms\Components\TextInput::make('address')
                        ->label(__('messages.rips.payer.address'))
                        ->maxLength(255),

                    Forms\Components\TextInput::make('phone')
                        ->label(__('messages.rips.payer.phone'))
                        ->tel()
                        ->maxLength(20),

                    Forms\Components\TextInput::make('email')
                        ->label(__('messages.rips.payer.email'))
                        ->email()
                        ->maxLength(100),
                ])
                ->columns(2),
        ];
    }
}
