<?php

namespace App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsPayers\RipsPayerResource;


use App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsPayers\RipsPayerResource\RelationManagers\RipsPayerAgreementRelationManager;


use App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsPayers\RipsPayerResource\Pages;
use App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsPayers\RipsPayerResource\RelationManagers;

use App\Models\Rips\RipsPayer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Pages\SubNavigationPosition;

use App\Filament\HospitalAdmin\Clusters\RipsCluster;




class RipsPayerResource extends Resource
{
    protected static ?string $model = RipsPayer::class;

    
    protected static ?string $cluster = RipsCluster::class;
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;
    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('tenant_id')
                    ->label(__('messages.rips.payer.tenant_id'))
                    ->required()
                    ->maxLength(36),
                Forms\Components\TextInput::make('type_id')
                    ->label(__('messages.rips.payer.type_id'))
                    ->required()
                    ->numeric(),
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
            ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tenant_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('identification')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }


public static function getRelations(): array
{
    return [
        RipsPayerAgreementRelationManager::class,
    ];
}


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRipsPayers::route('/'),
            'create' => Pages\CreateRipsPayer::route('/create'),
            'edit' => Pages\EditRipsPayer::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('messages.rips.payer.title');
    }

    public static function getPluralModelLabel(): string
    {
        return __('messages.rips.payer.title_plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.rips.payer.title_plural');
    }

}
