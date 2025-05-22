<?php

namespace App\Filament\HospitalAdmin\Resources;

use Illuminate\Support\Facades\Auth;
use App\Filament\HospitalAdmin\Resources\RipsPatientServiceResource\Pages;
use App\Filament\HospitalAdmin\Resources\RipsPatientServiceResource\RelationManagers;
use App\Models\RipsPatientService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\HospitalAdmin\Resources\RipsPatientServiceResource\FormSchema;


class RipsPatientServiceResource extends Resource
{
    protected static ?string $model = RipsPatientService::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    public static function getNavigationLabel(): string
    {
        return __('messages.rips_patient_service_navigation');
    }

    public static function getModelLabel(): string
    {
        return __('messages.rips_patient_service_model');
    }

    public static function getPluralModelLabel(): string
    {
        return __('messages.rips_patient_service_plural_model');
    }

    public static function form(Form $form): Form
    {
        return FormSchema::make($form);
    }
    
    

    public static function table(Table $table): Table
    {
        return $table
            ->columns([


                Tables\Columns\TextColumn::make('tenant_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('doctor_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('has_incapacity')
                    ->boolean(),
                Tables\Columns\TextColumn::make('service_datetime')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('collection_concept_id')
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
            'index' => Pages\ListRipsPatientServices::route('/'),
            'create' => Pages\CreateRipsPatientService::route('/create'),
            'view' => Pages\ViewRipsPatientService::route('/{record}'),
            'edit' => Pages\EditRipsPatientService::route('/{record}/edit'),
        ];
    }
}
