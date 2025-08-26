<?php

namespace App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsBillingDocuments;

//use app\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsBillingDocumentsCluster;
//use app\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsBillingDocumentsCluster;
//use App\Filament\HospitalAdmin\Clusters\Rips\Resources\RIPSResource;
use App\Filament\HospitalAdmin\Clusters\RipsCluster;
use App\Models\Rips\RipsBillingDocument;
use App\Models\Rips\RipsTenantPayerAgreement;

use App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsBillingDocuments\RipsBillingDocumentResource\Pages;


use App\Models\Rips\RipsBillingDocumentType;

use App\Models\Patient;
use App\Models\Rips\RipsPayer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
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
                    ->label(__('messages.rips.billingdocument.agreement_id'))
                    ->searchable(false) // para mostrar todos de una vez
                    ->preload()         // muy importante: carga todos sin escribir
                    
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre del convenio')
                            ->required(),
                        Forms\Components\TextInput::make('code')
                            ->label('Código')
                            ->maxLength(50),
                        Forms\Components\Hidden::make('tenant_id')
                            ->default(fn () => Auth::user()->tenant_id)

                            ->required(),
                    ])
                    ->createOptionUsing(function (array $data) {
                        return \App\Models\Rips\RipsTenantPayerAgreement::create($data)->id;
                    })
                    ->required()
                    ->options(function () {
                        return RipsTenantPayerAgreement::where('tenant_id', Auth::user()->tenant_id)
                            ->pluck('name', 'id');
                    }),

                Forms\Components\Select::make('type_id')
                    ->label(__('messages.rips.billingdocument.type_id'))
                    ->options(\App\Models\Rips\RipsBillingDocumentType::pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('document_number', static::getNextDocumentNumber($state));
                    }),

                Forms\Components\TextInput::make('document_number')
                    ->label(__('messages.rips.billingdocument.document_number'))
                    ->required()
                    ->maxLength(30)
                    ->default(fn (callable $get) => static::getNextDocumentNumber($get('type_id')))
                    ->helperText(fn (callable $get) => $get('type_id') ? 'Sugerido: '.static::getNextDocumentNumber($get('type_id')) : null),
                Forms\Components\DateTimePicker::make('issued_at')
                    ->label(__('messages.rips.billingdocument.issued_at'))
                    ->required(),
                Forms\Components\TextInput::make('cufe')
                    ->maxLength(100),
                Forms\Components\TextInput::make('uuid_dian')
                    ->maxLength(100),
                Forms\Components\TextInput::make('copay_amount')
                    ->label(__('messages.rips.billingdocument.copay_amount'))
                    ->numeric(),
                Forms\Components\TextInput::make('copay_amount')
                    ->label(__('messages.rips.billingdocument.copay_amount'))
                    ->numeric(),
                Forms\Components\TextInput::make('discount_amount')
                    ->label(__('messages.rips.billingdocument.discount_amount'))
                    ->numeric(),
                Forms\Components\TextInput::make('net_amount')
                    ->label(__('messages.rips.billingdocument.net_amount'))
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
                    ->label(__('messages.rips.billingdocument.agreement_id'))
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

    public static function getModelLabel(): string
    {
        return __('messages.rips.billingdocument.title');
    }

    public static function getPluralModelLabel(): string
    {
        return __('messages.rips.billingdocument.title_plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.rips.billingdocument.title_plural');
    }


    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('tenant_id', Auth::user()->tenant_id);
    }
    protected static function getNextDocumentNumber(?int $typeId = null): string
    {
        if (! $typeId) {
            return '';
        }

        $tenantId = Auth::user()->tenant_id;
        $lastNumber = RipsBillingDocument::where('tenant_id', $tenantId)
            ->where('type_id', $typeId)
            ->orderByDesc('id')
            ->value('document_number');

        if (! $lastNumber) {
            return '1';
        }

        if (preg_match('/(\d+)$/', $lastNumber, $matches)) {
            $number = $matches[1];
            $prefix = substr($lastNumber, 0, -strlen($number));
            $next = (int) $number + 1;
            return $prefix . str_pad((string) $next, strlen($number), '0', STR_PAD_LEFT);
        }

        if (is_numeric($lastNumber)) {
            return (string) ((int) $lastNumber + 1);
        }

        return $lastNumber . '-1';
    }
}
