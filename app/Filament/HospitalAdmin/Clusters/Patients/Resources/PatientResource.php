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
use App\Models\Municipality;
use App\Models\DepartmentCountry;
use App\Models\RipsCountry;

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
        $customFields = CustomField::where('module_name', CustomField::Patient)->Where('tenant_id', getLoggedInUser()->tenant_id)->get();
        $customFieldComponents = [];
        foreach ($customFields as $field) {
            $fieldType = CustomField::FIELD_TYPE_ARR[$field->field_type];
            $fieldName = 'field' . $field->id;
            $fieldLabel = $field->field_name;
            $isRequired = $field->is_required;
            $gridSpan = $field->grid;

            $customFieldComponents[] = match ($fieldType) {
                'text' => Forms\Components\TextInput::make($fieldName)
                    ->label($fieldLabel)
                    ->required($isRequired)
                    ->placeholder($fieldLabel)
                    ->columnSpan($gridSpan),

                'textarea' => Forms\Components\Textarea::make($fieldName)
                    ->label($fieldLabel)
                    ->required($isRequired)
                    ->placeholder($fieldLabel)
                    ->rows(4)
                    ->columnSpan($gridSpan),

                'toggle' => Forms\Components\Toggle::make($fieldName)
                    ->label($fieldLabel)
                    ->required($isRequired)
                    ->columnSpan($gridSpan),

                'number' => Forms\Components\TextInput::make($fieldName)
                    ->label($fieldLabel)
                    ->required($isRequired)
                    ->placeholder($fieldLabel)
                    ->numeric()
                    ->columnSpan($gridSpan),

                'select' => Forms\Components\Select::make($fieldName)
                    ->label($fieldLabel)
                    ->required($isRequired)
                    ->options(explode(',', $field->values))
                    ->placeholder($fieldLabel)
                    ->columnSpan($gridSpan),

                'multiSelect' => Forms\Components\MultiSelect::make($fieldName)
                    ->label($fieldLabel)
                    ->required($isRequired)
                    ->options(explode(',', $field->values))
                    ->placeholder($fieldLabel)
                    ->columnSpan($gridSpan),

                'date' => Forms\Components\DatePicker::make($fieldName)
                    ->label($fieldLabel)
                    ->required($isRequired)
                    ->columnSpan($gridSpan),

                'date & Time' => Forms\Components\DateTimePicker::make($fieldName)
                    ->label($fieldLabel)
                    ->required($isRequired)
                    ->columnSpan($gridSpan),

                default => Forms\Components\TextInput::make($fieldName)
                    ->label($fieldLabel)
                    ->required($isRequired)
                    ->placeholder($fieldLabel)
                    ->columnSpan($gridSpan),
            };
        }


        if ($form->getOperation() === 'edit') {
            $caseHandler = $form->model;
            $form->model = User::find($caseHandler->user_id);
        }
        return $form
    ->schema([
        Section::make()
            ->schema([
                Forms\Components\Select::make('document_type')
                    ->label(__('messages.patient.document_type') . ':')
                    ->relationship('ripsIdentificationType', 'name')
                    ->required()
                    ->native(false)
                    ->searchable()
                    ->preload()
                    ->placeholder('Seleccione tipo de documento'),

                Forms\Components\TextInput::make('document_number')
                    ->label(__('messages.patient.document_number') . ':')
                    ->required()
                    ->maxLength(15)
                    ->placeholder('Ingrese número de documento'),

                Forms\Components\Select::make('patient_type_id')
                    ->label(__('messages.patient.patient_type') . ':')
                    ->relationship('ripsUserType', 'name')
                    ->required()
                    ->native(false)
                    ->searchable()
                    ->preload()
                    ->placeholder('Seleccione tipo de paciente'),

                Forms\Components\TextInput::make('first_name')
                    ->required()
                    ->label(__('messages.user.first_name') . ':')
                    ->validationAttribute(__('messages.user.first_name'))
                    ->placeholder(__('messages.user.first_name'))
                    ->maxLength(255),

                Forms\Components\TextInput::make('last_name')
                    ->required()
                    ->validationAttribute(__('messages.user.last_name'))
                    ->label(__('messages.user.last_name') . ':')
                    ->placeholder(__('messages.user.last_name'))
                    ->maxLength(255),

                // Selector de País de Origen (versión comentada)
                Forms\Components\Select::make('country_of_origin')
                    ->label(__('messages.patient.origin_country') . ':') 
                    ->options(RipsCountry::all()->pluck('name', 'code'))
                    ->required()
                    ->native(false)  // Select personalizado
                    ->searchable()->preload()  // Precarga opciones
                    ->placeholder('Seleccione país de origen')  // Texto guía
                    ->columnSpan(1),

                Radio::make('gender')
                        ->label(__('messages.user.gender') . ':')
                        ->required()
                        ->validationAttribute(__('messages.user.gender'))
                        ->default(0)
                        ->options([
                            0 => __('messages.user.male'),
                            1 => __('messages.user.female'),
                        ])
                        ->columns(2),


                

                DatePicker::make('dob')
                    ->native(false)
                    ->maxDate(now())
                    ->label(__('messages.user.dob') . ':'),

                /*PhoneInput::make('phone')
                    ->defaultCountry('IN')
                    ->rules(function ($get) {
                        return [
                            'phone:AUTO,' . strtoupper($get('prefix_code')),
                        ];
                    })
                    ->countryStatePath('region_code')
                    ->afterStateHydrated(function ($component, $record, $operation) {
                        if ($operation == 'edit') {
                            if (!empty($record->phone)) {
                                $phoneNumber = (empty($record->region_code) ? '+' : $record->region_code) . getPhoneNumber($record->phone);
                            } else {
                                $phoneNumber = null;
                            }
                            $component->state($phoneNumber);
                        }
                    })
                    ->validationMessages([
                        'phone' => __('messages.common.invalid_number'),
                    ])
                    ->label(__('messages.user.phone') . ':')
                    ->required(),

                Hidden::make('region_code'),*/

                /*Group::make()
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->unique(
                                'users',
                                'email',
                                null,
                                false,
                                function ($rule, $record) {
                                    if ($record) {
                                        $rule->whereNot('id', $record->id);
                                    }
                                    return $rule;
                                }
                            )
                            ->label(__('messages.user.email') . ':')
                            ->validationMessages([
                                'unique' => __('messages.user.email') . ' ' . __('messages.common.is_already_exists'),
                            ])
                            ->placeholder(__('messages.user.email'))
                            ->email()
                            ->required(),

                        Toggle::make('status')
                            ->default(1)
                            ->live()
                            ->label(__('messages.user.status') . ':')
                            ->inline(false)
                            ->columnSpan(1),
                    ])
                    ->columns(1),

                Select::make('blood_group')
                    ->label(__('messages.user.blood_group') . ':')
                    ->options(getBloodGroups())
                    ->native(false),

                Group::make()
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->revealable()
                            ->visible(fn (?string $operation) => $operation === 'create')
                            ->rules(['min:8', 'max:20'])
                            ->confirmed()
                            ->label(__('messages.user.password') . ':')
                            ->placeholder(__('messages.user.password'))
                            ->validationAttribute(__('messages.user.password'))
                            ->required()
                            ->password()
                            ->maxLength(20),

                        TextInput::make('password_confirmation')
                            ->dehydrated(false)
                            ->visible(fn (?string $operation) => $operation === 'create')
                            ->label(__('messages.user.password_confirmation') . ':')
                            ->placeholder(__('messages.user.password_confirmation'))
                            ->validationAttribute(__('messages.user.password_confirmation'))
                            ->revealable()
                            ->required()
                            ->password()
                            ->maxLength(20),
                    ])
                    ->columns(2),

                Section::make('')
                    ->schema($customFieldComponents)
                    ->columns(12)
                    ->visible(function () {
                        $customFields = CustomField::where('module_name', CustomField::Patient)
                            ->where('tenant_id', getLoggedInUser()->tenant_id)
                            ->get();
                        return $customFields->count() > 0;
                    }),

                SpatieMediaLibraryFileUpload::make('user.profile')
                    ->label(__('messages.common.profile') . ':')
                    ->avatar()
                    ->image()
                    ->disk(config('app.media_disk'))
                    ->collection(User::COLLECTION_PROFILE_PICTURES),*/

                Fieldset::make('Address Details')
                    ->schema([
                        /*TextInput::make('address1')
                            ->label(__('messages.user.address1') . ':')
                            ->placeholder(__('messages.user.address1')),

                        TextInput::make('address2')
                            ->label(__('messages.user.address2') . ':')
                            ->placeholder(__('messages.user.address2')),*/

                        // Grupo de Países y Departamentos (comentado)
        Group::make()  // Agrupa campos en un layout
        ->schema([  // Define los campos internos
            // País de Residencia (trigger inicial)
            Forms\Components\Select::make('country_code') // Usamos el campo country_code de patients
                ->label(__('messages.patient.residence_country') . ':')
                ->options(RipsCountry::all()->pluck('name', 'code'))
                ->required()
                ->placeholder('Seleccione país de residencia')
                ->live() // Hace que el campo sea reactivo
                ->afterStateUpdated(fn (callable $set) => $set('department_code', null)), // Limpia departamento al cambiar país
            
                /*Forms\Components\Select::make('country_code')
                ->label(__('messages.patient.residence_country') . ':')
                ->options(Country::all()->pluck('name', 'code'))
                ->required()
                ->native(false)
                ->searchable()
                ->preload()
                ->placeholder('Seleccione país de residencia')
                ->live(),*/
                            
            // Selector de Departamento (dependiente)
            // Departamento (dependiente del país)
            Forms\Components\Select::make('department_code')
                ->label(__('messages.patient.residence_department') . ':')
                ->options(function (callable $get) {
                    if (!$get('country_code')) {
                        return [];
                    }
                    
                    return DepartmentCountry::whereHas('country', fn($q) => $q->where('code', $get('country_code')))
                        ->where('is_active', true)
                        ->pluck('name', 'code');
                })
                ->required()
                ->live() // Hace que el campo sea reactivo
                ->afterStateUpdated(fn (callable $set) => $set('municipality_code', null)), // Limpia municipio al cambiar departamento
            
            /*Forms\Components\Select::make('department_country_id')
                ->label(__('residence_department') . ':')
                ->options(function (callable $get) {  // Opciones dinámicas
                    // Filtra departamentos por país seleccionado:
                    return $get('residence_country_id')  // ID del país
                        ? \App\Models\DepartmentCountry::where('country_id', $get('residence_country_id'))->pluck('name', 'id')
                        : [];  // Array vacío si no hay país
                })
                ->searchable()
                ->preload()
                ->required()
                ->placeholder('Seleccione departamento')
                ->live(),  // Actualiza municipios al cambiar*/

                // Municipio (dependiente del departamento)
            
        ])
        ->columns(2),  // Distribuye en 2 columnas

        // ----------------------------------------------------------

        // Grupo de Municipio y Zona (comentado)
        Group::make()
        ->schema([
            // Selector de Municipio (dependiente)
            // Municipio (municipality_code)
            Forms\Components\Select::make('municipality_code')
                ->label(__('messages.patient.residence_city') . ':')
                ->options(function (callable $get) {
                    if (!$get('department_code')) {
                        return [];
                    }
                    
                    return Municipality::whereHas('department', fn($q) => $q->where('code', $get('department_code')))
                        ->where('is_active', true)
                        ->pluck('name', 'code');
                })
                ->required()
                ->searchable(),

            
                    
            // Selector de Zona Territorial
            Forms\Components\Select::make('territorial_zone')
                ->label(__('messages.patient.residence_zone') . ':')
                ->options([  // Opciones fijas
                    '01' => 'Urbana',  // Valor: '01', Label: 'Urbana'
                    '02' => 'Rural',
                ])
                ->required()
                ->placeholder('Seleccione zona territorial')
        ])
        ->columns(2)  // 2 columnas

                        /*Fieldset::make(__('messages.setting.social_details'))
                            ->schema([
                                TextInput::make('facebook_url')
                                    ->label(__('messages.facebook_url') . ':')
                                    ->url()
                                    ->placeholder(__('messages.facebook_url')),

                                TextInput::make('twitter_url')
                                    ->label(__('messages.twitter_url') . ':')
                                    ->url()
                                    ->placeholder(__('messages.twitter_url')),

                                TextInput::make('instagram_url')
                                    ->label(__('messages.instagram_url') . ':')
                                    ->url()
                                    ->placeholder(__('messages.instagram_url')),

                                TextInput::make('linkedIn_url')
                                    ->label(__('messages.linkedIn_url') . ':')
                                    ->url()
                                    ->placeholder(__('messages.linkedIn_url')),
                            ]),*/
                    ])
                    ->columns(2),
                    // Nueva sección para campos ocultos
                

            ])
            ->columns(2),
            self::hiddenFieldsSection(),
    ]);

    }

    public static function table(Table $table): Table
    {
        if (auth()->user()->hasRole(['Admin', 'Doctor', 'Receptionist']) && !getModuleAccess('Patients')) {
            abort(404);
        }
        return
            $table = $table->modifyQueryUsing(function (Builder $query) {
                $query->with('patientUser.media')->whereTenantId(auth()->user()->tenant_id);
                return $query;
            })->emptyStateHeading(__('messages.common.no_data_found'))
            ->paginated([10,25,50])
            ->defaultSort('id', 'desc')
            ->columns([
                // Nueva columna para país de origen
                TextColumn::make('user.originCountry.name')
                    ->label(__('País de Origen'))
                    ->searchable()
                    ->sortable(),

                // Nueva columna para país de residencia
                TextColumn::make('user.residenceCountry.name')
                    ->label(__('País Residencia'))
                    ->searchable()
                    ->sortable(),
            
                // Nueva columna para municipio
                TextColumn::make('user.municipality.name')
                    ->label(__('Municipio'))
                    ->searchable()
                    ->sortable(),

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
                            return '<a href="' . PatientResource::getUrl('view', ['record' => $record->id]) . '"class="hoverLink">' . $record->user->full_name . '</a>';
                        } else {
                            return $record->user->full_name;
                        }
                    })
                    ->description(function ($record) {
                        return $record->user->email;
                    })
                    ->searchable(['first_name', 'last_name', 'email']),
                PhoneColumn::make('user.phone')
                    ->label(__('messages.user.phone'))
                    ->default(__('messages.common.n/a'))
                    ->searchable()
                    ->formatStateUsing(function ($state, $record) {
                        if (str_starts_with($state, '+') && strlen($state) > 4) {
                            return $state;
                        }
                        if (empty($record->user->phone)) {
                            return __('messages.common.n/a');
                        }

                        return $record->user->region_code . $record->user->phone;
                    })
                    ->sortable(),
                TextColumn::make('user.blood_group')
                    ->label(__('messages.user.blood_group'))
                    ->getStateUsing(fn($record) => $record->user->blood_group ?? __('messages.common.n/a'))
                    ->badge()
                    ->color(fn($record) => $record->user->blood_group ? 'success' : 'blank')
                    ->searchable()
                    ->sortable(),
                ToggleColumn::make('user.status')
                    ->label(__('messages.common.status'))
                    ->afterStateUpdated(function ($record) {
                        $record->user->status;

                        return Notification::make()
                            ->success()
                            ->title(__('messages.common.status_updated_successfully'))
                            ->send();
                    })
            ])
            ->filters([
                //
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
                        $result = canDelete($patientModels, 'patient_id', $record->id);
                        if ($result) {
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
            ])->actionsColumnLabel(__('messages.common.action'))
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);

        // }
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
