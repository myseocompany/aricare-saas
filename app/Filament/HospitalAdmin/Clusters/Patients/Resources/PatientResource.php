<?php

namespace App\Filament\HospitalAdmin\Clusters\Patients\Resources;

use Filament\Forms;
use App\Models\Bill;
use App\Models\User;
use Filament\Tables;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Setting;
use Filament\Forms\Form;
use App\Models\BedAssign;
use Filament\Tables\Table;
use App\Models\Appointment;
use App\Models\BirthReport;
use App\Models\CustomField;
use App\Models\DeathReport;
use App\Models\PatientCase;
use Illuminate\Support\Str;
use App\Models\Prescription;
use App\Models\AdvancedPayment;
use App\Models\OperationReport;
use App\Models\PatientAdmission;
use Filament\Resources\Resource;
use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Support\Collection;
use App\Models\InvestigationReport;
use App\Models\IpdPatientDepartment;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\SubNavigationPosition;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\MultiSelect;
use App\Livewire\PatientCasesRelationTable;
use Filament\Infolists\Components\Livewire;
use Filament\Infolists\Components\TextEntry;
use Filament\Forms\Components\BaseFileUpload;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use App\Filament\HospitalAdmin\Clusters\Patients;
use Ysfkaya\FilamentPhoneInput\Tables\PhoneColumn;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Filament\Infolists\Components\Group as InfolistGroup;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use App\Filament\HospitalAdmin\Clusters\Patients\Resources\PatientResource\Pages;
use App\Models\RipsMunicipality;
use App\Models\RipsDepartment;
use App\Models\DepartmentCountry;
use App\Models\RipsCountry;
use App\Enums\Gender;
use App\Models\RipsGenderType;
use App\Models\RipsTerritorialZoneType;



class PatientResource extends Resource
{
    protected static ?string $model = Patient::class;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('messages.patient_admission.patient');
    }

    public static function shouldRegisterNavigation(): bool
    {
        if (auth()->user()->hasRole(['Case Manager'])) {
            return false;
        } elseif (auth()->user()->hasRole(['Doctor'])  && getModuleAccess('Patients')) {
            return true;
        } elseif (auth()->user()->hasRole(['Admin'])  && !getModuleAccess('Patients')) {
            return false;
        } elseif (!auth()->user()->hasRole(['Admin']) && !getModuleAccess('Patients')) {
            return false;
        } elseif (auth()->user()->hasRole(['Admin', 'Receptionist'])) {
            return true;
        }
        return false;
    }

    public static function getLabel(): string
    {
        return __('messages.patient_admission.patient');
    }
    public static function canCreate(): bool
    {
        if (auth()->user()->hasRole(['Admin', 'Doctor', 'Receptionist'])) {
            return true;
        }
        return false;
    }
    public static function canEdit(Model $record): bool
    {
        if (auth()->user()->hasRole(['Admin', 'Doctor', 'Receptionist'])) {
            return true;
        }
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        if (auth()->user()->hasRole(['Admin', 'Doctor', 'Receptionist'])) {
            return true;
        }
        return false;
    }

    public static function canViewAny(): bool
    {
        if (auth()->user()->hasRole(['Admin', 'Doctor', 'Receptionist', 'Patient'])) {
            return true;
        }
        return false;
    }

    protected static ?string $cluster = Patients::class;

    public static function form(Form $form): Form
    {
        $customFields = CustomField::where('module_name', CustomField::Patient)
            ->where('tenant_id', getLoggedInUser()->tenant_id)
            ->get();
        // Obtener los tipos de género desde el modelo RipsGenderType    
        $genderOptions = \App\Models\RipsGenderType::pluck('name', 'id')->toArray();
        $territorialZoneOptions = RipsTerritorialZoneType::pluck('name', 'code')->toArray();
    
        $customFieldComponents = [];
        foreach ($customFields as $field) {
            $fieldType = CustomField::FIELD_TYPE_ARR[$field->field_type];
            $fieldName = 'field' . $field->id;
            $fieldLabel = $field->field_name;
            $isRequired = $field->is_required;
            $gridSpan = $field->grid;
    
            $customFieldComponents[] = match ($fieldType) {
                'text' => Forms\Components\TextInput::make($fieldName)->label($fieldLabel)->required($isRequired)->placeholder($fieldLabel)->columnSpan($gridSpan),
                'textarea' => Forms\Components\Textarea::make($fieldName)->label($fieldLabel)->required($isRequired)->placeholder($fieldLabel)->rows(4)->columnSpan($gridSpan),
                'toggle' => Forms\Components\Toggle::make($fieldName)->label($fieldLabel)->required($isRequired)->columnSpan($gridSpan),
                'number' => Forms\Components\TextInput::make($fieldName)->label($fieldLabel)->required($isRequired)->placeholder($fieldLabel)->numeric()->columnSpan($gridSpan),
                'select' => Forms\Components\Select::make($fieldName)->label($fieldLabel)->required($isRequired)->options(explode(',', $field->values))->placeholder($fieldLabel)->columnSpan($gridSpan),
                'multiSelect' => Forms\Components\MultiSelect::make($fieldName)->label($fieldLabel)->required($isRequired)->options(explode(',', $field->values))->placeholder($fieldLabel)->columnSpan($gridSpan),
                'date' => Forms\Components\DatePicker::make($fieldName)->label($fieldLabel)->required($isRequired)->columnSpan($gridSpan),
                'date & Time' => Forms\Components\DateTimePicker::make($fieldName)->label($fieldLabel)->required($isRequired)->columnSpan($gridSpan),
                default => Forms\Components\TextInput::make($fieldName)->label($fieldLabel)->required($isRequired)->placeholder($fieldLabel)->columnSpan($gridSpan),
            };
        }
    
        return $form->schema([
            Section::make()->schema([
                Forms\Components\Select::make('user.rips_identification_type_id')
                    ->label('Tipo de documento (RIPS)')
                    ->options(\App\Models\RipsIdentificationType::pluck('name', 'id'))
                    ->required()
                    ->native(false)
                    ->searchable()
                    ->preload()
                    ->placeholder('Seleccione tipo de documento'),

    
                Forms\Components\TextInput::make('user.rips_identification_number')
                    ->label(__('messages.patient.document_number') . ':')
                    ->required()
                    ->maxLength(15),
    

    
                Forms\Components\TextInput::make('first_name')
                    ->required()
                    ->label(__('messages.user.first_name') . ':')
                    ->maxLength(255),
    
                Forms\Components\TextInput::make('last_name')
                    ->required()
                    ->label(__('messages.user.last_name') . ':')
                    ->maxLength(255),
                Forms\Components\Select::make('type_id')
                    ->label(__('messages.patient.patient_type') . ':')
                    
                    ->options(\App\Models\RipsUserType::pluck('name', 'id'))
                    ->required()
                    ->native(false)
                    ->searchable()
                    ->preload(),
    
                Forms\Components\Select::make('country_of_origin_id')
                    ->label(__('messages.patient.origin_country') . ':')
                    ->options(RipsCountry::all()->pluck('name', 'id'))
                    ->required()
                    ->native(false)
                    ->searchable()
                    ->default(fn () => RipsCountry::where('name', 'Colombia')->value('id'))
                    ->preload()
                    ->placeholder('Seleccione país de origen'),
    
                
                Forms\Components\Radio::make('gender')
                    ->label(__('messages.user.gender') . ':')
                    ->required()
                    ->options($genderOptions)
                    ->columns(count($genderOptions)),
    
                Forms\Components\DatePicker::make('dob')
                    ->native(false)
                    ->maxDate(now())
                    ->label(__('messages.user.dob') . ':'),

            ])->columns(2),
    
            Fieldset::make('Detalles de residencia')->schema([
                Group::make()->schema([
                    Forms\Components\Select::make('rips_country_id')
                        ->label(__('messages.patient.residence_country') . ':')
                        ->options(RipsCountry::all()->pluck('name', 'id'))
                        ->default(fn () => RipsCountry::where('name', 'Colombia')->value('id'))
                        ->required()
                        ->searchable()
                        ->live()
                        ->afterStateUpdated(fn (callable $set) => $set('rips_department_id', null)),
    
                    Forms\Components\Select::make('rips_department_id')
                        ->label(__('messages.patient.residence_department') . ':')
                        ->options(function (callable $get) {
                            if (!$get('rips_country_id')) return [];
                            return RipsDepartment::where('rips_country_id', $get('rips_country_id'))->pluck('name', 'id');
                        })
                        ->required()
                        ->searchable()
                        ->default(fn () => RipsDepartment::where('name', 'Antioquia')->value('id'))
                        ->live()
                        ->afterStateUpdated(fn (callable $set) => $set('rips_municipality_id', null)),
                ])->columns(2),
    
                Group::make()->schema([
                    Forms\Components\Select::make('rips_municipality_id')
                        ->label(__('messages.patient.residence_city') . ':')
                        ->options(function (callable $get) {
                            if (!$get('rips_department_id')) return [];
                            return RipsMunicipality::where('rips_department_id', $get('rips_department_id'))->pluck('name', 'id');
                        })
                        ->default(fn () => RipsMunicipality::where('name', 'Medellín')->value('id'))
                        ->required()
                        ->searchable(),

                    Forms\Components\Select::make('zone_code')
                        ->label(__('messages.patient.residence_zone') . ':')
                        ->options($territorialZoneOptions)
                        ->required()
                        ->default('01')
                        ->placeholder('Seleccione zona territorial'),
                ])->columns(2)
            ]),
    
            Section::make('')
                ->schema($customFieldComponents)
                ->columns(12)
                ->visible(fn () => $customFields->count() > 0),
    
            self::hiddenFieldsSection(),
        ]);
    }
    
    
    

    public static function table(Table $table): Table
    {
        if (auth()->user()->hasRole(['Admin', 'Doctor', 'Receptionist']) && !getModuleAccess('Patients')) {
            abort(404);
        }
    
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return $query->with([
                    'patientUser.media',
                    'originCountry',
                    'ripsCountry',
                    'ripsMunicipality',
                    'ripsIdentificationType',
                ])->whereTenantId(auth()->user()->tenant_id);
            })
            ->emptyStateHeading(__('messages.common.no_data_found'))
            ->paginated([10, 25, 50])
            ->defaultSort('id', 'desc')
            ->columns([
                SpatieMediaLibraryImageColumn::make('user.profile')
                ->label(__('messages.invoice.patient'))
                ->circular()
                ->defaultImageUrl(function ($record) {
                    if (!$record->user->hasMedia(User::COLLECTION_PROFILE_PICTURES)) {
                        return getUserImageInitial($record->id, $record->user->full_name);
                    }
                })
                ->url(fn($record) => PatientResource::getUrl('view', ['record' => $record->id]))
                ->collection('profile')
                ->width(50)->height(50),

            TextColumn::make('user.full_name')
                ->label('')
                ->color('primary')
                ->weight(FontWeight::SemiBold)
                ->html()
                ->formatStateUsing(function ($record) {
                    if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Doctor')) {
                        return '<a href="' . PatientResource::getUrl('view', ['record' => $record->id]) . '" class="hoverLink">' . $record->user->full_name . '</a>';
                    }
                    return $record->user->full_name;
                })
                ->description(fn($record) => $record->user->email)
                ->searchable(['first_name', 'last_name', 'email']),
                
                TextColumn::make('originCountry.name')
                    ->label('País de Origen')
                    ->searchable()
                    ->sortable(),
    
                TextColumn::make('ripsCountry.name')
                    ->label('País Residencia')
                    ->searchable()
                    ->sortable(),
    
                TextColumn::make('ripsMunicipality.name')
                    ->label('Municipio')
                    ->searchable()
                    ->sortable(),
    
                TextColumn::make('ripsIdentificationType.name')
                    ->label('Tipo de documento')
                    ->searchable()
                    ->sortable(),
    
                TextColumn::make('document_number')
                    ->label('Número de documento')
                    ->searchable()
                    ->sortable(),
    

    
                ToggleColumn::make('user.status')
                    ->label(__('messages.common.status'))
                    ->afterStateUpdated(function ($record) {
                        return Notification::make()
                            ->success()
                            ->title(__('messages.common.status_updated_successfully'))
                            ->send();
                    }),
            ])
            ->recordUrl(null)
            ->actions([
                Tables\Actions\EditAction::make()->iconButton(),
                Tables\Actions\DeleteAction::make()->iconButton()
                    ->action(function ($record) {
                        if (! canAccessRecord(Patient::class, $record->id)) {
                            return Notification::make()
                                ->danger()
                                ->title(__('messages.flash.patient_not_found'))
                                ->send();
                        }
    
                        $patientModels = [
                            BirthReport::class,
                            DeathReport::class,
                            InvestigationReport::class,
                            OperationReport::class,
                            Appointment::class,
                            BedAssign::class,
                            PatientAdmission::class,
                            PatientCase::class,
                            Bill::class,
                            Invoice::class,
                            AdvancedPayment::class,
                            Prescription::class,
                            IpdPatientDepartment::class,
                        ];
                        if (canDelete($patientModels, 'patient_id', $record->id)) {
                            return Notification::make()
                                ->warning()
                                ->title(__('messages.flash.Patient_cant_deleted'))
                                ->send();
                        }
    
                        $record->user()->delete();
                        $record->address()->delete();
                        $record->delete();
    
                        Notification::make()
                            ->success()
                            ->title(__('messages.flash.Patient_deleted'))
                            ->send();
                    }),
            ])
            ->actionsColumnLabel(__('messages.common.action'));
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
            'index' => Pages\ListPatients::route('/'),
            'create' => Pages\CreatePatient::route('/create'),
            'view' => Pages\ViewPatient::route('/{record}'),
            'edit' => Pages\EditPatient::route('/{record}/edit'),
        ];
    }
    
    //Ubicar campos requeridos que no se ven en el formulario
    protected static function hiddenFieldsSection(): Section
    {
        return Section::make('')
            ->visible(false)
            ->schema([
                // Email (requerido y único)
                Hidden::make('email')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->default(fn() => 'temp_' . Str::random(8) . '@example.com'),

                // Contraseña (requerida solo en creación)
                Hidden::make('password')
                    ->required(fn(string $operation): bool => $operation === 'create')
                    ->default('TempPassword123!')
                    ->dehydrated(fn(?string $state): bool => filled($state)),

                // Confirmación de contraseña
                Hidden::make('password_confirmation')
                    ->required(fn(string $operation): bool => $operation === 'create')
                    ->default('TempPassword123!')
                    ->dehydrated(false),

                // Teléfono
                Hidden::make('phone')
                    ->required()
                    ->default('+573001234567'), // Número genérico colombiano
                
                Hidden::make('region_code')
                    ->default('+57'),
                
                Hidden::make('designation')
                    ->default('patient'),
                

                // Estado
                Hidden::make('status')
                    ->default(true)
                    ->required(),

                // Campos de dirección básica (si son requeridos)
                Hidden::make('address1')
                    ->default('Dirección generada automáticamente'),

                Hidden::make('address2')
                    ->default('N/A'),

                // Grupo sanguíneo (si es requerido)
                Hidden::make('blood_group')
                    ->default('O+')
                    ->required(),
            ])
            ->columnSpanFull(); // Para ocupar el ancho completo sin ser visible
    }
}
