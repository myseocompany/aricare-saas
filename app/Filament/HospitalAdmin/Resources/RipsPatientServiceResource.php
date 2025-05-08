<?php

namespace App\Filament\HospitalAdmin\Resources;

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

class RipsPatientServiceResource extends Resource
{
    protected static ?string $model = RipsPatientService::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('patient_id')
                ->label(__('messages.rips.patientservice.patient'))
                ->relationship(
                    name: 'patient',
                    titleAttribute: 'full_name',
                    modifyQueryUsing: fn ($query) => $query->orderBy('first_name')
                )
                ->searchable()
                ->required()
            ,

    
            Forms\Components\TextInput::make('tenant_code')
                ->label(__('messages.rips.patientservice.tenant_code'))
                ->required()
                ->maxLength(20),
    
                Forms\Components\Select::make('doctor_id')
                ->label(__('messages.rips.patientservice.doctor'))
                ->relationship(
                    name: 'doctor',
                    titleAttribute: 'full_name',
                    modifyQueryUsing: fn ($query) => $query->orderBy('first_name')
                )
                ->searchable()
            ,
            
    
            Forms\Components\TextInput::make('location_code')
                ->label(__('messages.rips.patientservice.location_code'))
                ->maxLength(12),
    
            Forms\Components\Toggle::make('has_incapacity')
                ->label(__('messages.rips.patientservice.has_incapacity')),
    
            Forms\Components\DateTimePicker::make('service_datetime')
                ->label(__('messages.rips.patientservice.service_datetime'))
                ->required(),
    
            Forms\Components\TextInput::make('service_group_code')
                ->label(__('messages.rips.patientservice.service_group_code'))
                ->maxLength(5),
    
            Forms\Components\TextInput::make('service_code')
                ->label(__('messages.rips.patientservice.service_code'))
                ->numeric(),
    
            Forms\Components\TextInput::make('technology_purpose_code')
                ->label(__('messages.rips.patientservice.technology_purpose_code'))
                ->maxLength(10),
    
            Forms\Components\TextInput::make('collection_concept_code')
                ->label(__('messages.rips.patientservice.collection_concept_code'))
                ->maxLength(10),
        ]);
    }
    

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('patient_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tenant_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('doctor_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('location_code')
                    ->searchable(),
                Tables\Columns\IconColumn::make('has_incapacity')
                    ->boolean(),
                Tables\Columns\TextColumn::make('service_datetime')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('service_group_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('service_code')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('technology_purpose_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('collection_concept_code')
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
