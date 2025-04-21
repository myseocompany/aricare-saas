<?php

namespace App\Filament\HospitalAdmin\Clusters\BookableUnits\Resources;

use App\Filament\HospitalAdmin\Clusters\BookableUnits;
use App\Filament\HospitalAdmin\Clusters\BookableUnits\Resources\BookableUnitResource\Pages;
use App\Filament\HospitalAdmin\Clusters\BookableUnits\Resources\BookableUnitResource\RelationManagers;
use App\Filament\HospitalAdmin\Clusters\Doctors\Resources\SchedulesResource\Pages\CreateSchedules;
use App\Models\BookableUnit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BookableUnitResource extends Resource
{
    protected static ?string $model = BookableUnit::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = BookableUnits::class;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('messages.bookable_units.name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label(__('messages.bookable_units.description'))
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_available')
                    ->label(__('messages.bookable_units.fields.is_available'))
                    ->required(),
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('messages.bookable_units.name'))
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_available')
                ->label(__('messages.bookable_units.fields.is_available'))
                ->boolean(),
                Tables\Columns\TextColumn::make('description')
                ->label(__('messages.bookable_units.description'))
                ->searchable(),
                
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'view' => Pages\ViewBookableUnit::route('/{record}'),
            'edit' => Pages\EditBookableUnit::route('/{record}/edit'),
        ];
    }
    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ListBookableUnits::class,
            Pages\ViewBookableUnit::class,
            Pages\EditBookableUnit::class,
            CreateSchedules::class,
            
        ]);
    }
}
