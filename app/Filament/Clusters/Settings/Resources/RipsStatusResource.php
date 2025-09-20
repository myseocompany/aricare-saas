<?php

namespace App\Filament\Clusters\Settings\Resources;

use App\Filament\Clusters\Settings;
use App\Filament\Clusters\Settings\Resources\RipsStatusResource\Pages;
use App\Models\Rips\RipsStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RipsStatusResource extends Resource
{
    protected static ?string $model = RipsStatus::class;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?int $navigationSort = 7;

    protected static ?string $cluster = Settings::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('messages.common.name'))
                    ->required()
                    ->maxLength(255)
                    ->unique('rips_statuses', 'name', ignoreRecord: true),
                Forms\Components\Textarea::make('description')
                    ->label(__('messages.common.description'))
                    ->columnSpanFull(),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('messages.common.name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label(__('messages.common.description'))
                    ->limit(70)
                    ->wrap(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('messages.common.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('messages.common.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListRipsStatuses::route('/'),
            'create' => Pages\CreateRipsStatus::route('/create'),
            'view' => Pages\ViewRipsStatus::route('/{record}'),
            'edit' => Pages\EditRipsStatus::route('/{record}/edit'),
        ];
    }
}
