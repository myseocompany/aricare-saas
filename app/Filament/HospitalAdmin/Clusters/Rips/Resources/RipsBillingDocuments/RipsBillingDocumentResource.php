<?php

namespace App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsBillingDocuments;

//use app\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsBillingDocumentsCluster;
//use app\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsBillingDocumentsCluster;
//use App\Filament\HospitalAdmin\Clusters\Rips\Resources\RIPSResource;
use App\Filament\HospitalAdmin\Clusters\RipsCluster;

use App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsBillingDocuments\RipsBillingDocumentResource\Pages;


use App\Models\Rips\RipsBillingDocument;

use App\Models\Patient;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Pages\SubNavigationPosition;

class RipsBillingDocumentResource extends Resource
{
    protected static ?string $model = RipsBillingDocument::class;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $cluster = RipsCluster::class;
    //protected static ?string $cluster = RipsCluster::class;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('tenant_id')
                    ->default(fn() => Auth::user()->tenant_id)
                    ->required(),
                Select::make('agreement_id')
                    ->label('Convenio')
                    ->relationship('agreement', 'name')
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('type_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('document_number')
                    ->required()
                    ->maxLength(30),
                Forms\Components\DateTimePicker::make('issued_at')
                    ->required(),
                Forms\Components\TextInput::make('cufe')
                    ->maxLength(100),
                Forms\Components\TextInput::make('uuid_dian')
                    ->maxLength(100),
                Forms\Components\TextInput::make('total_amount')
                    ->numeric(),
                Forms\Components\TextInput::make('copay_amount')
                    ->numeric(),
                Forms\Components\TextInput::make('discount_amount')
                    ->numeric(),
                Forms\Components\TextInput::make('net_amount')
                    ->numeric(),
                FileUpload::make('xml_path')
                    ->label('Archivo XML')
                    ->disk('public')
                    ->directory(fn ($get, $record) =>
                        Auth::user()->tenant_id.'/'.($get('agreement_id') ?? $record?->agreement_id).'/'.($record?->patientServices()->first()?->patient_id ?? '0')
                    )
                    ->visibility('public')
                    ->preserveFilenames()
                    ->acceptedFileTypes(['text/xml','application/xml'])
                    ->downloadable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        $table = $table->modifyQueryUsing(function (Builder $query) {
            return $query->with(['agreement', 'patientServices.patient']);
        });

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('agreement.name')
                    ->label('Convenio')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('patientServices')
                    ->label('Pacientes')
                    ->formatStateUsing(function ($state, $record) {
                        return $record->patientServices
                            ->map(fn($ps) => $ps->patient->full_name)
                            ->implode(', ');
                    })
                    ->limit(30),
                Tables\Columns\TextColumn::make('type_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('document_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('issued_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cufe')
                    ->searchable(),
                Tables\Columns\TextColumn::make('uuid_dian')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('copay_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('discount_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('net_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('xml_path')
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
                DateRangeFilter::make('issued_at')
                    ->label('Fecha de Emisión'),
                SelectFilter::make('agreement_id')
                    ->label('Convenio')
                    ->relationship('agreement', 'name'),
                Filter::make('document_number')
                    ->form([
                        TextInput::make('document_number')
                            ->label('Número de Factura'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        $query->when($data['document_number'], function (Builder $query, $value) {
                            $query->where('document_number', 'like', "%{$value}%");
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
                            $query->whereHas('patientServices', function (Builder $subQuery) use ($value) {
                                $subQuery->where('patient_id', $value);
                            });
                        });
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRipsBillingDocuments::route('/'),
            'create' => Pages\CreateRipsBillingDocument::route('/create'),
            'edit' => Pages\EditRipsBillingDocument::route('/{record}/edit'),
        ];
    }
}
