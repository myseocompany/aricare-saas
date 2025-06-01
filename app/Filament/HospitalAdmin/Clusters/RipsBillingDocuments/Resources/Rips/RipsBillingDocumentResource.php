<?php

namespace App\Filament\HospitalAdmin\Clusters\RipsBillingDocuments\Resources\Rips;

use App\Filament\HospitalAdmin\Clusters\RipsBillingDocuments;
use App\Filament\HospitalAdmin\Clusters\RipsBillingDocuments\Resources\Rips\RipsBillingDocumentResource\Pages;
use App\Filament\HospitalAdmin\Clusters\RipsBillingDocuments\Resources\Rips\RipsBillingDocumentResource\RelationManagers;
use App\Models\Rips\RipsBillingDocument;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Pages\SubNavigationPosition;

class RipsBillingDocumentResource extends Resource
{
    protected static ?string $model = RipsBillingDocument::class;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $cluster = RipsBillingDocuments::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('tenant_id')
                    ->required()
                    ->maxLength(36),
                Forms\Components\TextInput::make('agreement_id')
                    ->numeric(),
                Forms\Components\TextInput::make('type_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('document_number')
                    ->required()
                    ->maxLength(30),
                Forms\Components\DateTimePicker::make('issued_at')
                    ->required(),
                Forms\Components\TextInput::make('cufe')
                    ->maxLength(100),
                Forms\Components\TextInput::make('uuid_dian')
                    ->maxLength(100),
                Forms\Components\TextInput::make('total_amount')
                    ->numeric(),
                Forms\Components\TextInput::make('copay_amount')
                    ->numeric(),
                Forms\Components\TextInput::make('discount_amount')
                    ->numeric(),
                Forms\Components\TextInput::make('net_amount')
                    ->numeric(),
                Forms\Components\TextInput::make('xml_path')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tenant_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('agreement_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('document_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('issued_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cufe')
                    ->searchable(),
                Tables\Columns\TextColumn::make('uuid_dian')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('copay_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('discount_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('net_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('xml_path')
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRipsBillingDocuments::route('/'),
            'create' => Pages\CreateRipsBillingDocument::route('/create'),
            'edit' => Pages\EditRipsBillingDocument::route('/{record}/edit'),
        ];
    }
}
