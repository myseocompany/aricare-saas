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
        return $form->schema([
            Forms\Components\Select::make('patient_id')
                ->label('Paciente')
                ->searchable()
                ->getOptionLabelFromRecordUsing(fn ($record) => $record->user?->first_name . ' ' . $record->user?->last_name)
                ->options(function (string $search = null) {
                    $tenantId = Auth::user()->tenant_id;
                    return \App\Models\Patient::query()
                        ->where('tenant_id', $tenantId)
                        ->whereHas('user', function ($query) use ($search) {
                            $query->where('first_name', 'like', "%{$search}%")
                                  ->orWhere('last_name', 'like', "%{$search}%");
                        })
                        ->with('user')
                        ->limit(20)
                        ->get()
                        ->mapWithKeys(fn ($patient) => [$patient->id => $patient->user?->first_name . ' ' . $patient->user?->last_name]);
                })
                ->required(),
    
            Forms\Components\Select::make('doctor_id')
                ->label('Doctor')
                ->searchable()
                ->getOptionLabelFromRecordUsing(fn ($record) => $record->user?->first_name . ' ' . $record->user?->last_name)
                ->options(function (string $search = null) {
                    $tenantId = Auth::user()->tenant_id;
                    return \App\Models\Doctor::query()
                        ->where('tenant_id', $tenantId)
                        ->whereHas('user', function ($query) use ($search) {
                            $query->where('first_name', 'like', "%{$search}%")
                                  ->orWhere('last_name', 'like', "%{$search}%");
                        })
                        ->with('user')
                        ->limit(20)
                        ->get()
                        ->mapWithKeys(fn ($doctor) => [$doctor->id => $doctor->user?->first_name . ' ' . $doctor->user?->last_name]);
                })
                ->preload()
                ->required(),
    

    
            Forms\Components\Toggle::make('has_incapacity')
                ->label('¿Tiene incapacidad?'),
    
            Forms\Components\DateTimePicker::make('service_datetime')
                ->label('Fecha y hora de atención')
                ->default(now())
                ->required(),
    
            // Consultas
            Forms\Components\Repeater::make('consultations')
    ->label('Consultas')
    ->relationship()
    ->schema([

        Forms\Components\Repeater::make('diagnoses')
    ->label('Diagnósticos')
    ->relationship()
    ->minItems(1)
    ->maxItems(4)
    ->schema([
        Forms\Components\Select::make('cie10_id')
            ->label('Código CIE10')
            ->options(\App\Models\Cie10::all()->pluck('description', 'id'))
            ->searchable()
            ->required(),

        Forms\Components\TextInput::make('sequence')
            ->label('Secuencia')
            ->numeric()
            ->minValue(1)
            ->maxValue(4)
            ->required()
            ->helperText('1 = Principal, 2+ = Relacionados'),
    ])
    ->columns(2)
    ->columnSpanFull()
,
        Forms\Components\Select::make('cups_id')
            ->label('Consulta')
            ->options(\App\Models\Cups::where('description', 'CapItulo 16 CONSULTA, MONITORIZACION Y PROCEDIMIENTOS DIAGNOSTICOS')->pluck('name', 'id'))

            ->searchable()
            ->required(),

        Forms\Components\Select::make('service_group_id')
            ->label('Grupo de Servicio')
            ->options(\App\Models\RipsServiceGroup::all()->pluck('name', 'id'))
            ->searchable()
            ->required(),

        Forms\Components\Select::make('service_id')
            ->label('Servicio')
            ->options(\App\Models\RipsService::all()->mapWithKeys(fn ($s) => [$s->id => "{$s->code} - {$s->name}"]))
            ->searchable()
            ->required(),

        Forms\Components\Select::make('technology_purpose_id')
            ->label('Finalidad Tecnológica')
            ->options(\App\Models\RipsTechnologyPurpose::all()->pluck('name', 'id'))
            ->searchable()
            ->required(),

        Forms\Components\Select::make('collection_concept_id')
            ->label('Concepto de Recaudo')
            ->options(\App\Models\RipsCollectionConcept::all()->pluck('name', 'id'))
            ->searchable()
            ->required(),

        Forms\Components\TextInput::make('service_value')
            ->label('Valor del Servicio')
            ->numeric()
            ->required(),

        Forms\Components\TextInput::make('copayment_value')
            ->label('Valor del Copago')
            ->numeric()
            ->nullable(),

        Forms\Components\TextInput::make('copayment_receipt_number')
            ->label('Número del Recibo')
            ->maxLength(30)
            ->nullable(),
    ])
    ->defaultItems(0)
    ->columnSpanFull()
    ->columns(2),

    
            // Procedimientos
            Forms\Components\Repeater::make('procedures')
                ->label('Procedimientos')
                ->relationship()
                ->schema([
                    Forms\Components\Select::make('cups_id')
                        ->label('CUPS')
                        ->options(\App\Models\Cups::all()->pluck('code', 'id'))
                        ->searchable()
                        ->required(),
    
                    Forms\Components\TextInput::make('authorization_number')
                        ->label('Autorización')
                        ->maxLength(30)
                        ->nullable(),
    
                    Forms\Components\TextInput::make('mipres_id')
                        ->label('MIPRES')
                        ->maxLength(30)
                        ->nullable(),
    
                    Forms\Components\TextInput::make('service_value')
                        ->label('Valor del servicio')
                        ->numeric()
                        ->nullable(),
    
                    Forms\Components\TextInput::make('copayment_value')
                        ->label('Valor del copago')
                        ->numeric()
                        ->nullable(),
    
                    Forms\Components\TextInput::make('copayment_receipt_number')
                        ->label('Recibo de copago')
                        ->maxLength(30)
                        ->nullable(),
                ])
                ->defaultItems(0)
                ->columnSpanFull()
                ->columns(2),
        ]);
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
