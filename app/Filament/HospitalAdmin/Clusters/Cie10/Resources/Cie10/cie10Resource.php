<?php

namespace App\Filament\HospitalAdmin\Clusters\cie10\Resources\Cie10;

use App\Filament\HospitalAdmin\Clusters\cie10;
use App\Filament\HospitalAdmin\Clusters\cie10\Resources\Cie10\cie10Resource\Pages;
use App\Filament\HospitalAdmin\Clusters\cie10\Resources\Cie10\cie10Resource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class cie10Resource extends Resource
{
    protected static ?string $model = cie10::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    //protected static ?string $cluster = cie10::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\Listcie10s::route('/'),
            'create' => Pages\Createcie10::route('/create'),
            'edit' => Pages\Editcie10::route('/{record}/edit'),
        ];
    }
}
