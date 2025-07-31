<?php

namespace App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsPayers\RipsPayerResource\Form;

use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use App\Models\Rips\RipsPayerType;

class RipsPayerMinimalForm
{
    public static function schema(): array
    {
        return [
            Section::make()
                ->schema([
                    Select::make('type_id')
                        ->label(__('messages.rips.payer.type_id'))
                        ->options(RipsPayerType::pluck('name', 'id'))
                        ->searchable()
                        ->required(),

                    TextInput::make('name')
                        ->label(__('messages.rips.payer.name'))
                        ->required()
                        ->maxLength(255),

                    TextInput::make('identification')
                        ->label(__('messages.rips.payer.identification'))
                        ->required()
                        ->maxLength(20),

                    TextInput::make('address')
                        ->label(__('messages.rips.payer.address'))
                        ->maxLength(255),

                    TextInput::make('phone')
                        ->label(__('messages.rips.payer.phone'))
                        ->maxLength(20),

                    TextInput::make('email')
                        ->label(__('messages.rips.payer.email'))
                        ->email()
                        ->maxLength(100),

                    Hidden::make('tenant_id')->default(fn () => getLoggedInUser()->tenant_id),
                ])
        ];
    }
}