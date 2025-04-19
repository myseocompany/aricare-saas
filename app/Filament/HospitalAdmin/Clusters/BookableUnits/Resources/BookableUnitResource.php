<?php

namespace App\Filament\HospitalAdmin\Clusters\BookableUnits\Resources;

use App\Filament\HospitalAdmin\Clusters\BookableUnits;
use App\Filament\HospitalAdmin\Clusters\BookableUnits\Resources\BookableUnitResource\Pages;
use App\Filament\HospitalAdmin\Clusters\BookableUnits\Resources\BookableUnitResource\RelationManagers;
use App\Models\BookableUnit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Pages\SubNavigationPosition;

class BookableUnitResource extends Resource
{
    protected static ?string $model = BookableUnit::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $cluster = BookableUnits::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_available')
                    ->required(),
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_available')
                    ->boolean(),

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
            'index' => Pages\ListBookableUnits::route('/'),
            'create' => Pages\CreateBookableUnit::route('/create'),
            'edit' => Pages\EditBookableUnit::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('messages.bookable_units');
    }

    public static function getPluralModelLabel(): string
    {
        return __('messages.bookable_units');
    }
    public static function getNavigationLabel(): string
    {
        return __('messages.bookable_units');
    }

    public static function getLabel(): string
    {
        return __('messages.bookable_units');
    }
}
