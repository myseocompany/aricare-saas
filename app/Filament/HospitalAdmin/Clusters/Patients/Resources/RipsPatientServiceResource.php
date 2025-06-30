<?php

namespace App\Filament\HospitalAdmin\Clusters\Patients\Resources;

use App\Filament\HospitalAdmin\Clusters\Patients;
use App\Filament\HospitalAdmin\Clusters\Patients\Resources\RipsPatientServiceResource\Pages;
use App\Filament\HospitalAdmin\Clusters\Patients\Resources\RipsPatientServiceResource\RelationManagers;
use App\Models\Rips\RipsPatientService;
use App\Models\Patient;
use App\Models\Rips\RipsTenantPayerAgreement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Pages\SubNavigationPosition;

use App\Filament\HospitalAdmin\Clusters\Patients\Resources\RipsPatientServiceResource\Form\FormService;
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
                    FormService::make($form)->getComponents(),
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
            DateRangeFilter::make('service_datetime')
                ->label('Fecha de Servicio'),
            SelectFilter::make('agreement_id')
                ->label('Convenio')
                ->options(RipsTenantPayerAgreement::pluck('name', 'id'))
                ->query(function (Builder $query, $value) {
                    $query->whereHas('billingDocument', function (Builder $subQuery) use ($value) {
                        $subQuery->where('agreement_id', $value);
                    });
                }),
            Filter::make('document_number')
                ->form([
                    TextInput::make('document_number')
                        ->label('Número de Factura'),
                ])
                ->query(function (Builder $query, array $data) {
                    $query->when($data['document_number'], function (Builder $query, $value) {
                        $query->whereHas('billingDocument', function (Builder $subQuery) use ($value) {
                            $subQuery->where('document_number', 'like', "%{$value}%");
                        });
                    });
                }),
            Filter::make('patient_id')
                ->form([
                    Select::make('patient_id')
                        ->label('Paciente')
                        ->searchable()
                        ->options(Patient::getActivePatientNames()->toArray()),
                ])
                ->query(function (Builder $query, array $data) {
                    $query->when($data['patient_id'], function (Builder $query, $value) {
                        $query->where('patient_id', $value);
                    });
                }),
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
