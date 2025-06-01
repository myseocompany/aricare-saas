<?php

namespace App\Filament\HospitalAdmin\Clusters\RipsPayers\Resources\Rips\RipsPayerResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;

class RipsPayerAgreementRelationManager extends RelationManager
{
    protected static string $relationship = 'agreements'; // <- Este nombre debe ser el método en el modelo RipsPayer

    protected static ?string $title = 'Acuerdos';

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
                ->nullable(),
        ]);
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('code')
                    ->label('Código')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Inicio')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Fin')
                    ->date()
                    ->sortable(),
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
