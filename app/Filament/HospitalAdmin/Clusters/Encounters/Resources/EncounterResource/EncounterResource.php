<?php

namespace App\Filament\HospitalAdmin\Clusters\Encounters\Resources\EncounterResource;

use App\Filament\HospitalAdmin\Clusters\Encounters\EncountersCluster;
use App\Filament\HospitalAdmin\Clusters\Encounters\Resources\EncounterResource\Pages;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Rda\Encounter;
use App\Models\Rda\EncounterType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EncounterResource extends Resource
{
    protected static ?string $model = Encounter::class;

    protected static ?string $cluster = EncountersCluster::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('patient_id')
                    ->label(__('messages.encounter_patient'))
                    ->options(fn () => static::getPatientOptions())
                    ->default(fn () => request()->integer('patient_id'))
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('doctor_id')
                    ->label(__('messages.encounter_doctor'))
                    ->options(fn () => static::getDoctorOptions())
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('encounter_type_id')
                    ->label(__('messages.encounter_type'))
                    ->options(fn () => static::getEncounterTypeOptions())
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\DateTimePicker::make('start_at')
                    ->label(__('messages.encounter_start_at'))
                    ->required(),
                Forms\Components\DateTimePicker::make('end_at')
                    ->label(__('messages.encounter_end_at')),
                Forms\Components\Textarea::make('reason')
                    ->label(__('messages.encounter_reason')),
                Forms\Components\Select::make('status_id')
                    ->label(__('messages.common.status'))
                    ->relationship('status', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->forTenant()
                ->with(['patient.user', 'doctor.user', 'encounterType', 'status']))
            ->defaultSort('start_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('patient_id')
                    ->label(__('messages.encounter_patient'))
                    ->formatStateUsing(fn ($state, $record) => $record->patient?->user?->full_name ?? $state)
                    ->searchable(['patient.user.first_name', 'patient.user.last_name'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('doctor_id')
                    ->label(__('messages.encounter_doctor'))
                    ->formatStateUsing(fn ($state, $record) => $record->doctor?->user?->full_name ?? $state)
                    ->searchable(['doctor.user.first_name', 'doctor.user.last_name'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('encounterType.name')
                    ->label(__('messages.encounter_type'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_at')
                    ->label(__('messages.encounter_start_at'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status.name')
                    ->label(__('messages.common.status'))
                    ->badge()
                    ->color(fn ($state, $record) => match ($record->status?->code) {
                        'planned' => 'gray',
                        'in-progress' => 'info',
                        'finished' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
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
            'index' => Pages\ListEncounters::route('/'),
            'create' => Pages\CreateEncounter::route('/create'),
            'edit' => Pages\EditEncounter::route('/{record}/edit'),
        ];
    }

    protected static function getPatientOptions(): array
    {
        $tenantId = auth()->user()?->tenant_id;

        return Patient::with('user')
            ->when($tenantId, fn (Builder $query) => $query->where('tenant_id', $tenantId))
            ->orderBy('id')
            ->get()
            ->mapWithKeys(fn (Patient $patient) => [
                $patient->id => $patient->user?->full_name ?? "Paciente #{$patient->id}",
            ])
            ->toArray();
    }

    protected static function getDoctorOptions(): array
    {
        $tenantId = auth()->user()?->tenant_id;

        return Doctor::with('user')
            ->when($tenantId, fn (Builder $query) => $query->where('tenant_id', $tenantId))
            ->orderBy('id')
            ->get()
            ->mapWithKeys(fn (Doctor $doctor) => [
                $doctor->id => $doctor->user?->full_name ?? "Profesional #{$doctor->id}",
            ])
            ->toArray();
    }

    protected static function getEncounterTypeOptions(): array
    {
        return EncounterType::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

}
