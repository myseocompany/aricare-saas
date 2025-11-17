<?php

namespace App\Filament\HospitalAdmin\Clusters\Patients\Resources\PatientBackgroundResource;

use App\Filament\HospitalAdmin\Clusters\Patients;
use App\Filament\HospitalAdmin\Clusters\Patients\Resources\PatientBackgroundResource\Pages;
use App\Models\Patient;
use App\Models\Rda\PatientBackground;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PatientBackgroundResource extends Resource
{
    protected static ?string $model = PatientBackground::class;

    protected static ?string $cluster = Patients::class;

    protected static ?string $navigationLabel = 'Antecedentes clínicos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('patient_id')
                    ->label(__('messages.encounter_patient'))
                    ->relationship('patient', 'id')
                    ->getOptionLabelFromRecordUsing(fn (Patient $record) => $record->patientUser?->full_name ?? $record->user?->full_name ?? "Paciente #{$record->id}")
                    ->default(fn () => request()->integer('patient_id'))
                    ->preload()
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('background_type_id')
                    ->label(__('messages.background_type'))
                    ->relationship('backgroundType', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->label('Descripción')
                    ->required()
                    ->rows(3),
                Forms\Components\Select::make('cie10_id')
                    ->label('Diagnóstico (CIE10)')
                    ->relationship('cie10', 'code')
                    ->searchable()
                    ->getOptionLabelFromRecordUsing(fn ($record) => trim($record->code.' - '.($record->description ?? ''))),
                Forms\Components\Select::make('rips_cups_id')
                    ->label('Procedimiento (CUPS)')
                    ->relationship('cups', 'code')
                    ->searchable()
                    ->getOptionLabelFromRecordUsing(fn ($record) => trim($record->code.' - '.($record->name ?? ''))),
                Forms\Components\TextInput::make('medication_name')
                    ->label('Medicamento en uso')
                    ->maxLength(150),
                Forms\Components\TextInput::make('procedure_name')
                    ->label('Procedimiento / cirugía')
                    ->maxLength(150),
                Forms\Components\TextInput::make('related_person')
                    ->label('Familiar relacionado')
                    ->maxLength(100),
                Forms\Components\DatePicker::make('start_date')
                    ->label('Fecha inicio'),
                Forms\Components\DatePicker::make('end_date')
                    ->label('Fecha fin'),
                Forms\Components\Toggle::make('is_active')
                    ->label('Activo')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->forTenant()->with(['patient.user', 'backgroundType']))
            ->columns([
                Tables\Columns\TextColumn::make('patient_id')
                    ->label('Paciente')
                    ->formatStateUsing(fn ($state, $record) => $record->patient?->user?->full_name ?? $state)
                    ->searchable(['patient.user.first_name', 'patient.user.last_name'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('backgroundType.name')
                    ->label(__('messages.background_type'))
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Descripción')
                    ->limit(40)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Fecha inicio')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Fecha fin')
                    ->date()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPatientBackgrounds::route('/'),
            'create' => Pages\CreatePatientBackground::route('/create'),
            'edit' => Pages\EditPatientBackground::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
}
