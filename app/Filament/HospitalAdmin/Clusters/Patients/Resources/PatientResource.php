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
                            ->label(__('messages.patient.document_type').':')
                            ->relationship('documentType', 'name')
                            ->required()
                            ->native(false)
                            ->searchable()
                            ->preload()
                            ->placeholder('Seleccione tipo de documento'),
                        Forms\Components\TextInput::make('document_number')
                            ->label(__('messages.patient.document_number').':')
                            ->required()
                            ->maxLength(15)
                            ->placeholder('Ingrese número de documento'),
                        Forms\Components\Select::make('patient_type_id')
                            ->label(__('messages.patient.patient_type') . ':')
                            ->relationship('patientType', 'name')
                            ->required()
                            ->native(false)
                            ->searchable()
                            ->preload()
                            ->placeholder('Seleccione tipo de paciente'),
                        
                        /* 
                        Forms\Components\TextInput::make('record_number')
                            ->label('N°. Historia'),
                        Forms\Components\TextInput::make('affiliate_number')
                            ->label('N°. Afiliación'),
                        
                               
                        Forms\Components\DateTimePicker::make('admission_date')
                            ->label('Fecha y Hora de Ingreso')
                            ->required()
                            ->native(false)
                            ->maxDate(now()),*/
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
                            )->label(__('messages.user.email') . ':')
                            ->validationMessages([
                                'unique' => __('messages.user.email') . ' ' . __('messages.common.is_already_exists'),
                            ])
                            ->placeholder(__('messages.user.email'))
                            ->email()
                            ->required(),
                        DatePicker::make('dob')
                            ->native(false)
                            ->maxDate(now())
                            ->label(__('messages.user.dob') . ':'),
                        PhoneInput::make('phone')
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
                        Hidden::make('region_code'),
                        Group::make()->schema([
                            Radio::make('gender')
                                ->label(__('messages.user.gender') . ':')
                                ->required()
                                ->validationAttribute(__('messages.user.gender'))
                                ->default(0)
                                ->options([
                                    0 => __('messages.user.male'),
                                    1 => __('messages.user.female'),
                                ])->columns(2)->columnSpan(2),
                            Toggle::make('status')
                                ->default(1)
                                ->live()
                                ->label(__('messages.user.status') . ':')
                                ->inline(false)
                                ->columnSpan(1)
                        ])->columns(3),
                        Select::make('blood_group')
                            ->label(__('messages.user.blood_group') . ':')
                            ->options(
                                getBloodGroups()
                            )
                            ->native(false),
                        Group::make()->schema([
                            Forms\Components\TextInput::make('password')
                                ->revealable()
                                ->visible(function (?string $operation) {
                                    return $operation == 'create';
                                })
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
                                ->visible(function (?string $operation) {
                                    return $operation == 'create';
                                })
                                ->label(__('messages.user.password_confirmation') . ':')
                                ->placeholder(__('messages.user.password_confirmation'))
                                ->validationAttribute(__('messages.user.password_confirmation'))
                                ->revealable()
                                ->required()
                                ->password()
                                ->maxLength(20),
                        ])->columns(2),
                        Section::make('')
                            ->schema($customFieldComponents)
                            ->columns(12)
                            ->visible(function () {
                                $customFields = CustomField::where('module_name', CustomField::Patient)->Where('tenant_id', getLoggedInUser()->tenant_id)->get();
                                if ($customFields->count() == 0) {
                                    return false;
                                } else {
                                    return true;
                                }
                            }),
                        SpatieMediaLibraryFileUpload::make('user.profile')
                            ->label(__('messages.common.profile') . ':')
                            ->avatar()
                            ->image()
                            ->disk(config('app.media_disk'))
                            ->collection(User::COLLECTION_PROFILE_PICTURES),
                        Fieldset::make('Address Details')
                            ->schema([
                                TextInput::make('address1')
                                    ->label(__('messages.user.address1') . ':')
                                    ->placeholder(__('messages.user.address1')),
                                TextInput::make('address2')
                                    ->label(__('messages.user.address2') . ':')
                                    ->placeholder(__('messages.user.address2')),
                                Group::make()->schema([
                                    TextInput::make('city')
                                        ->label(__('messages.user.city') . ':')
                                        ->placeholder(__('messages.user.city')),
                                    TextInput::make('zip')
                                        ->label(__('messages.user.zip') . ':')
                                        ->placeholder(__('messages.user.zip')),
                                ])->columns(2),
                            ]),
                        Fieldset::make(__('messages.setting.social_details'))
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
                            ])
                    ])->columns(2),
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
}
