<?php

namespace App\Filament\HospitalAdmin\Clusters\RipsPayers\RelationManagers;

use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;

class RipsPayerAgreementRelationManager extends RelationManager
{
    // Nombre del método en RipsPayer.php
    protected static string $relationship = 'agreements';

    public function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nombre del acuerdo')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('code')
                ->label('Código del acuerdo')
                ->maxLength(50),

            Forms\Components\Textarea::make('description')
                ->label('Descripción')
                ->maxLength(500),

            Forms\Components\DatePicker::make('start_date')
                ->label('Fecha de inicio')
                ->required(),

            Forms\Components\DatePicker::make('end_date')
                ->label('Fecha de finalización')
                ->afterOrEqual('start_date')
                ->label('Fecha de fin'),
        ]);
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nombre'),
                Tables\Columns\TextColumn::make('code')->label('Código'),
                Tables\Columns\TextColumn::make('start_date')->label('Inicio')->date(),
                Tables\Columns\TextColumn::make('end_date')->label('Fin')->date(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
