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
                        ->mapWithKeys(function ($patient) {
                            $name = $patient->user?->first_name . ' ' . $patient->user?->last_name;
                            return [$patient->id => $name];
                        });
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
                            ->mapWithKeys(function ($doctor) {
                                $name = $doctor->user?->first_name . ' ' . $doctor->user?->last_name;
                                return [$doctor->id => $name];
                            });
                    })
                    ->preload()
                    ->required(),

            
    

    
            Forms\Components\Toggle::make('has_incapacity')
                ->label(__('messages.rips.patientservice.has_incapacity')),
    
            Forms\Components\DateTimePicker::make('service_datetime')
                ->label(__('messages.rips.patientservice.service_datetime'))
                ->default(now())
                ->required(),
Forms\Components\Select::make('rips_service_group_id')
    ->label('Grupo de Servicio')
    ->options(
        \App\Models\RipsServiceGroup::all()->pluck('name', 'id')
    )
    ->searchable()
    ->required(),

Forms\Components\Select::make('rips_service_id')
    ->label('Servicio')
    ->options(
        \App\Models\RipsService::all()->mapWithKeys(fn ($item) => [$item->id => "{$item->code} - {$item->name}"])
    )
    ->searchable()
    ->required(),

Forms\Components\Select::make('rips_technology_purpose_id')
    ->label('Finalidad Tecnológica')
    ->options(
        \App\Models\RipsTechnologyPurpose::all()->pluck('name', 'id')
    )
    ->searchable()
    ->required(),




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
                Tables\Columns\TextColumn::make('location_code')
                    ->searchable(),
                Tables\Columns\IconColumn::make('has_incapacity')
                    ->boolean(),
                Tables\Columns\TextColumn::make('service_datetime')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('service_group_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('service_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('technology_purpose_id')
                    ->searchable(),
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
