<?php

namespace App\Filament\HospitalAdmin\Clusters\Patients\Resources;

use App\Filament\HospitalAdmin\Clusters\Patients;
use App\Filament\HospitalAdmin\Clusters\Patients\Resources\RipsPatientServiceResource\Pages;
use App\Filament\HospitalAdmin\Clusters\Patients\Resources\RipsPatientServiceResource\RelationManagers;
use App\Models\Rips\RipsPatientService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Pages\SubNavigationPosition;

use App\Filament\HospitalAdmin\Clusters\Patients\Resources\RipsPatientServiceResource\Form\FormPatientDoctor;
use App\Filament\HospitalAdmin\Clusters\Patients\Resources\RipsPatientServiceResource\Form\FormConsultations;
use App\Filament\HospitalAdmin\Clusters\Patients\Resources\RipsPatientServiceResource\Form\FormProcedures;

use Filament\Forms\Components\Grid;


class RipsPatientServiceResource extends Resource
{
    protected static ?string $model = RipsPatientService::class;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;


    protected static ?string $cluster = Patients::class;

    public static function getNavigationLabel(): string
    {
        return __('messages.rips_patient_service_navigation');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Grid::make(1) 
                ->schema(array_merge(
                    FormPatientDoctor::make($form)->getComponents(),
                    FormConsultations::make($form)->getComponents(),
                    FormProcedures::make($form)->getComponents()
                )),
        ]);
    }

public static function table(Table $table): Table
{
    return $table
        ->columns([
            // Muestra el nombre completo del paciente
            Tables\Columns\TextColumn::make('patient.user.full_name')
                ->label('Paciente')
                ->sortable()
                ->searchable(),

            // Muestra el nombre completo del doctor
            Tables\Columns\TextColumn::make('doctor.user.full_name')
                ->label('Doctor')
                ->sortable()
                ->searchable(),

            // Estado de incapacidad (booleano)
            Tables\Columns\IconColumn::make('has_incapacity')
                ->boolean()
                ->label('Tiene incapacidad'),

            // Fecha del servicio, más clara
            Tables\Columns\TextColumn::make('service_datetime')
                ->label('Fecha de Servicio')
                ->dateTime('d/m/Y H:i:s')
                ->sortable(),

            // Otros campos
            Tables\Columns\TextColumn::make('collection_concept_id')
                ->searchable()
                ->label('Concepto de Colección'),

            // Fechas de creación y actualización
            Tables\Columns\TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true)
                ->label('Fecha de Creación'),

            Tables\Columns\TextColumn::make('updated_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true)
                ->label('Fecha de Actualización'),
        ])
        ->filters([
            // Agregar filtros si es necesario
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
            'edit' => Pages\EditRipsPatientService::route('/{record}/edit'),
        ];
    }


    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'billingDocument',
                'consultations.diagnoses',
                'procedures',
            ]);
    }




}
