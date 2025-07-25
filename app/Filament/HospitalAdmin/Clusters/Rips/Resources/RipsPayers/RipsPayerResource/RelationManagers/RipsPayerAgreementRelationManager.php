<?php


namespace App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsPayers\RipsPayerResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Illuminate\Support\Facades\Lang;

class RipsPayerAgreementRelationManager extends RelationManager
{
    protected static string $relationship = 'agreements'; // <- Este nombre debe ser el método en el modelo RipsPayer

    protected static ?string $title = 'Acuerdos';
    protected static ?string $modelLabel = 'Acuerdo';
    protected static ?string $pluralModelLabel = 'Acuerdos';




    public function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label(__('messages.rips.payer_agreement.name'))
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('code')
                ->label(__('messages.rips.payer_agreement.code'))
                ->maxLength(50),

            Forms\Components\Textarea::make('description')
                ->label(__('messages.rips.payer_agreement.description'))
                ->maxLength(500),

            Forms\Components\DatePicker::make('start_date')
                ->label(__('messages.rips.payer_agreement.start_date'))
                ->required(),

            Forms\Components\DatePicker::make('end_date')
                ->label(__('messages.rips.payer_agreement.end_date'))
                ->afterOrEqual('start_date')
                ->nullable(),
        ]);
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('messages.rips.payer_agreement.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('code')
                    ->label(__('messages.rips.payer_agreement.code'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label(__('messages.rips.payer_agreement.start_label'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label(__('messages.rips.payer_agreement.end_label'))
                    ->date()
                    ->sortable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('messages.rips.payer_agreement.create')),
            ])

            ->actions([
                Tables\Actions\EditAction::make()
                    ->label(__('messages.rips.payer_agreement.edit')),
                Tables\Actions\DeleteAction::make()
                    ->label(__('messages.rips.payer_agreement.delete')),
            ]);

    }
}