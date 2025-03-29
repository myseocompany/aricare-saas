<?php

namespace App\Filament\HospitalAdmin\Clusters\IpdOpd\Resources;

use App\Models\Bed;
use App\Models\User;
use Filament\Tables;
use App\Models\Patient;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\PatientCase;
use Filament\Resources\Resource;
use App\Models\IpdPatientDepartment;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\SubNavigationPosition;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DateTimePicker;
use App\Filament\HospitalAdmin\Clusters\IpdOpd;
use App\Repositories\IpdPatientDepartmentRepository;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use App\Filament\HospitalAdmin\Clusters\Doctors\Resources\DoctorResource;
use App\Filament\HospitalAdmin\Clusters\Patients\Resources\PatientResource;
use App\Filament\HospitalAdmin\Clusters\IpdOpd\Resources\IpdPatientResource\Pages;
use App\Models\CustomField;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\MultiSelect;

class IpdPatientResource extends Resource
{
    protected static ?string $model = IpdPatientDepartment::class;

    protected static ?string $cluster = IpdOpd::class;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?int $navigationSort = 1;

    public static function shouldRegisterNavigation(): bool
    {
        if (auth()->user()->hasRole(['Patient'])) {
            return true;
        } elseif (auth()->user()->hasRole(['Admin'])  && !getModuleAccess('IPD Patients')) {
            return false;
        } elseif (!auth()->user()->hasRole(['Admin']) && !getModuleAccess('IPD Patients')) {
            return false;
        }
        return true;
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.ipd_patients');
    }

    public static function getLabel(): string
    {
        return __('messages.ipd_patients');
    }
    public static function canCreate(): bool
    {
        if (auth()->user()->hasRole(['Admin', 'Doctor', 'Receptionist', 'Nurse']) && getModuleAccess('IPD Patients')) {
            return true;
        }
        return false;
    }
    public static function canEdit(Model $record): bool
    {
        if (auth()->user()->hasRole(['Admin', 'Doctor', 'Receptionist', 'Nurse']) && getModuleAccess('IPD Patients')) {
            return true;
        }
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        if (auth()->user()->hasRole(['Admin', 'Doctor', 'Receptionist', 'Nurse']) && getModuleAccess('IPD Patients')) {
            return true;
        }
        return false;
    }

    public static function canViewAny(): bool
    {
        if (auth()->user()->hasRole(['Admin', 'Doctor', 'Receptionist', 'Nurse', 'Patient'])) {
            return true;
        }
        return false;
    }

    public static function form(Form $form): Form
    {
        $customFields = CustomField::where('module_name', CustomField::IpdPatient)->Where('tenant_id', getLoggedInUser()->tenant_id)->get();

        $customFieldComponents = [];
        foreach ($customFields as $field) {
            $fieldType = CustomField::FIELD_TYPE_ARR[$field->field_type];
            $fieldName = 'field' . $field->id;
            $fieldLabel = $field->field_name;
            $isRequired = $field->is_required;
            $gridSpan = $field->grid;

            $customFieldComponents[] = match ($fieldType) {
                'text' => TextInput::make($fieldName)
                    ->label($fieldLabel)
                    ->required($isRequired)
                    ->placeholder($fieldLabel)
                    ->columnSpan($gridSpan),

                'textarea' => Textarea::make($fieldName)
                    ->label($fieldLabel)
                    ->required($isRequired)
                    ->placeholder($fieldLabel)
                    ->rows(4)
                    ->columnSpan($gridSpan),

                'toggle' => Toggle::make($fieldName)
                    ->label($fieldLabel)
                    ->required($isRequired)
                    ->columnSpan($gridSpan),

                'number' => TextInput::make($fieldName)
                    ->label($fieldLabel)
                    ->required($isRequired)
                    ->placeholder($fieldLabel)
                    ->numeric()
                    ->minValue(1)
                    ->columnSpan($gridSpan),

                'select' => Select::make($fieldName)
                    ->label($fieldLabel)
                    ->required($isRequired)
                    ->options(explode(',', $field->values))
                    ->placeholder($fieldLabel)
                    ->columnSpan($gridSpan)
                    ->native(false),

                'multiSelect' => MultiSelect::make($fieldName)
                    ->label($fieldLabel)
                    ->required($isRequired)
                    ->options(explode(',', $field->values))
                    ->placeholder($fieldLabel)
                    ->columnSpan($gridSpan),

                'date' => DatePicker::make($fieldName)
                    ->label($fieldLabel)
                    ->required($isRequired)
                    ->columnSpan($gridSpan),

                'date & Time' => DateTimePicker::make($fieldName)
                    ->label($fieldLabel)
                    ->required($isRequired)
                    ->columnSpan($gridSpan),

                default => TextInput::make($fieldName)
                    ->label($fieldLabel)
                    ->required($isRequired)
                    ->placeholder($fieldLabel)
                    ->columnSpan($gridSpan),
            };
        }
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Select::make('patient_id')
                            ->label(__('messages.case.patient') . ':')
                            ->placeholder(__('messages.document.select_patient'))
                            ->required()
                            ->live()
                            ->options(function () {
                                return app(IpdPatientDepartmentRepository::class)->getAssociatedData()['patients'];
                            })
                            ->afterStateUpdated(fn($set, $get) => !empty(PatientCase::where('patient_id', $get('patient_id'))->where('status', 1)->first()->id) ? $set('case_id', PatientCase::where('patient_id', $get('patient_id'))->where('status', 1)->first()->id) : $set('case_id', null))
                            ->searchable()
                            ->native(false)
                            ->preload()
                            ->validationMessages([
                                'required' => __('messages.fields.the') . ' ' . __('messages.case.patient') . ' ' . __('messages.fields.required'),
                            ]),
                        Select::make('case_id')
                            ->live()
                            ->required()
                            ->options(function ($get) {
                                if ($get('patient_id')) {
                                    return PatientCase::where('patient_id', $get('patient_id'))->where('status', 1)->get()->pluck('case_id', 'id')->toArray();                        // dd($case);
                                }
                            })
                            ->placeholder(__('messages.case.select_case'))
                            // ->disabled(function (Get $get) {
                            //     $case = PatientCase::where('patient_id', $get('patient_id'))->where('status', 1)->get()->pluck('case_id', 'id')->toArray();
                            //     if (empty($case)) {
                            //         return true;
                            //     }
                            //     return false;
                            // })
                            ->label(__('messages.case.case') . ':')
                            ->native(false)
                            ->afterStateUpdated(function ($set, $state, $get) {
                                $existsCaseId = IpdPatientDepartment::where('case_id',  $state)->latest()->first();
                                if ($existsCaseId && $existsCaseId->is_discharge == 0) {
                                    Notification::make()
                                        ->title(Patient::where('id', $get('patient_id'))->first()->patientUser->full_name . ' ' . __('messages.lunch_break.case_exist'))
                                        ->danger()
                                        ->send();
                                }
                            })
                            ->searchable()
                            ->preload()
                            ->validationMessages([
                                'required' => __('messages.fields.the') . ' ' . __('messages.case.case') . ' ' . __('messages.fields.required'),
                            ]),
                        TextInput::make('ipd_number')
                            ->label(__('messages.ipd_patient.ipd_number'))
                            ->default(IpdPatientDepartment::generateUniqueIpdNumber())
                            ->maxLength(255)
                            ->readOnly(),
                        TextInput::make('height')
                            ->label(__('messages.ipd_patient.height') . ':')
                            ->placeholder(__('messages.ipd_patient.height'))
                            ->maxLength(255)
                            ->numeric()
                            ->minValue(1),
                        TextInput::make('weight')
                            ->label(__('messages.ipd_patient.weight') . ':')
                            ->maxLength(255)
                            ->placeholder(__('messages.ipd_patient.weight')),
                        TextInput::make('bp')
                            ->label(__('messages.ipd_patient.bp') . ':')
                            ->maxLength(255)
                            ->placeholder(__('messages.ipd_patient.bp')),
                        DateTimePicker::make('admission_date')
                            ->native(false)
                            ->label(__('messages.ipd_patient.admission_date') . ':')
                            ->placeholder(__('messages.ipd_patient.admission_date'))
                            ->required()
                            ->validationAttribute(__('messages.ipd_patient.admission_date'))
                            ->default(now()),
                        Select::make('doctor_id')
                            ->label(__('messages.case.doctor') . ':')
                            ->placeholder(__('messages.web_home.select_doctor'))
                            ->required()
                            ->options(function () {
                                $repo = app(IpdPatientDepartmentRepository::class);
                                return $repo->getAssociatedData()['doctors'];
                            })
                            ->searchable()
                            ->native(false)
                            ->preload()
                            ->validationMessages([
                                'required' => __('messages.fields.the') . ' ' . __('messages.case.doctor') . ' ' . __('messages.fields.required'),
                            ]),
                        Select::make('bed_type_id')
                            ->label(__('messages.bed.bed_type') . ':')
                            ->placeholder(__('messages.bed.select_bed_type'))
                            ->required()
                            ->live()
                            ->options(function () {
                                $repo = app(IpdPatientDepartmentRepository::class);
                                return $repo->getAssociatedData()['bedTypes'];
                            })
                            ->optionsLimit(function () {
                                $repo = app(IpdPatientDepartmentRepository::class);
                                return count($repo->getAssociatedData()['bedTypes']);
                            })
                            ->afterStateUpdated(fn($set, $get) => !empty(Bed::where('is_available', 1)->where('tenant_id', getLoggedInUser()->tenant_id)->where('bed_type', $get('bed_type_id'))->first()->id) ? $set('bed_id', Bed::where('is_available', 1)->where('tenant_id', getLoggedInUser()->tenant_id)->where('bed_type', $get('bed_type_id'))->first()->id) : $set('bed_id', null))
                            ->searchable()
                            ->native(false)
                            ->preload()
                            ->validationMessages([
                                'required' => __('messages.fields.the') . ' ' . __('messages.bed.bed_type') . ' ' . __('messages.fields.required'),
                            ]),
                        Select::make('bed_id')
                            ->label(__('messages.bed_assign.bed') . ':')
                            ->placeholder(__('messages.bed.select_bed'))
                            ->required()
                            ->options(function ($get, $record, $operation) {
                                if ($operation == 'edit') {
                                    $editBad = [];
                                    $editBad = Bed::where('id', $record->bed->id)->where('tenant_id', getLoggedInUser()->tenant_id)->where('bed_type', $get('bed_type_id'))->get()->pluck('name', 'id')->toArray();
                                    return Bed::where('is_available', 1)->where('tenant_id', getLoggedInUser()->tenant_id)->where('bed_type', $get('bed_type_id'))->get()->pluck('name', 'id')->toArray() + $editBad;
                                } elseif ($get('bed_type_id')) {
                                    return Bed::where('is_available', 1)->where('tenant_id', getLoggedInUser()->tenant_id)->where('bed_type', $get('bed_type_id'))->get()->pluck('name', 'id')->toArray();
                                }
                            })
                            // ->disabled(function (Get $get) {
                            //     $bed = Bed::where('tenant_id', getLoggedInUser()->tenant_id)->where('bed_type', $get('bed_type_id'))->get()->pluck('name', 'id')->toArray();
                            //     if (empty($bed)) {
                            //         return true;
                            //     }
                            //     return false;
                            // })
                            ->searchable()
                            ->native(false)
                            ->preload()
                            ->validationMessages([
                                'required' => __('messages.fields.the') . ' ' . __('messages.bed_assign.bed') . ' ' . __('messages.fields.required'),
                            ]),
                        Toggle::make('is_old_patient')
                            ->live()
                            ->label(__('messages.ipd_patient.is_old_patient') . ':'),
                        Section::make('')
                            ->schema($customFieldComponents)
                            ->columns(12)
                            ->visible(function () {
                                $customFields = CustomField::where('module_name', CustomField::IpdPatient)->Where('tenant_id', getLoggedInUser()->tenant_id)->get();
                                if ($customFields->count() == 0) {
                                    return false;
                                } else {
                                    return true;
                                }
                            }),
                        Textarea::make('symptoms')
                            ->label(__('messages.ipd_patient.symptoms') . ':')
                            ->maxLength(255)
                            ->rows(4)
                            ->columnSpanFull()
                            ->placeholder(__('messages.ipd_patient.symptoms')),
                        Textarea::make('notes')
                            ->label(__('messages.ipd_patient.notes') . ':')
                            ->maxLength(255)
                            ->rows(4)
                            ->columnSpanFull()
                            ->placeholder(__('messages.ipd_patient.notes')),
                    ])->columns(4),
            ])->columns(4);
    }

    public static function table(Table $table): Table
    {
        if (auth()->user()->hasRole(['Admin', 'Doctor', 'Receptionist', 'Nurse']) && !getModuleAccess('IPD Patients')) {
            abort(404);
        }
        return
            $table = $table->modifyQueryUsing(function (Builder $query) {
                if (auth()->user()->hasRole('Patient')) {
                    $query->whereTenantId(auth()->user()->tenant_id)->where('patient_id', auth()->user()->owner_id);
                }
                $query->whereTenantId(auth()->user()->tenant_id);
                return $query;
            })
            ->paginated([10,25,50])
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('ipd_number')
                    ->label(__('messages.ipd_patient.ipd_number'))
                    ->badge()
                    ->url(fn($record) => IpdPatientResource::getUrl('view', ['record' => $record->id]))
                    ->color('info'),
                SpatieMediaLibraryImageColumn::make('patient.user.profile')->collection(User::COLLECTION_PROFILE_PICTURES)->rounded()->label(__('messages.case.patient'))->width(50)->height(50)
                    ->url(fn($record) => PatientResource::getUrl('view', ['record' => $record->patient->id]))
                    ->defaultImageUrl(function ($record) {
                        if (!$record->doctor->user->hasMedia(User::COLLECTION_PROFILE_PICTURES)) {
                            return getUserImageInitial($record->id, $record->patient->user->first_name);
                        }
                    }),
                TextColumn::make('patient.user.full_name')
                    ->label('')
                    ->description(function ($record) {
                        return $record->patient->user->email;
                    })
                    ->color('primary')
                    ->weight(FontWeight::SemiBold)
                    ->formatStateUsing(fn($record) => '<a href="' . PatientResource::getUrl('view', ['record' => $record->patient->id]) . '" class="hoverLink">' . $record->patient->user->full_name . '</a>')
                    ->html()
                    ->searchable(['first_name', 'last_name', 'email']),
                SpatieMediaLibraryImageColumn::make('doctor.user.profile')->collection(User::COLLECTION_PROFILE_PICTURES)->rounded()->label(__('messages.case.doctor'))->width(50)->height(50)
                    ->url(fn($record) => DoctorResource::getUrl('view', ['record' => $record->doctor->id]))
                    ->defaultImageUrl(function ($record) {
                        if (!$record->doctor->user->hasMedia(User::COLLECTION_PROFILE_PICTURES)) {
                            return getUserImageInitial($record->id, $record->doctor->user->first_name);
                        }
                    }),
                TextColumn::make('doctor.user.full_name')
                    ->label('')
                    ->color('primary')
                    ->weight(FontWeight::SemiBold)
                    ->formatStateUsing(fn($record) => '<a href="' . DoctorResource::getUrl('view', ['record' => $record->doctor->id]) . '" class="hoverLink">' . $record->doctor->user->full_name . '</a>')
                    ->html()
                    ->description(function ($record) {
                        return $record->doctor->user->email;
                    })
                    ->searchable(['first_name', 'last_name', 'email']),
                TextColumn::make('admission_date')
                    ->label(__('messages.ipd_patient.admission_date'))
                    ->view('tables.columns.hospitalAdmin.admission_date')
                    ->searchable(),
                TextColumn::make('bed.name')
                    ->label(__('messages.ipd_patient.bed_id'))
                    ->color('primary')
                    // ->getStateUsing(function ($record) {
                    //     if ($record->bed->name) {
                    //         //add url when url is exist
                    //     }
                    //     return __('messages.common.n/a');
                    // })
                    // ->html()
                    ->searchable(),
                TextColumn::make('bill_status')
                    ->label(__('messages.ipd_patient.bill_status'))
                    ->badge()
                    ->getStateUsing(function ($record) {
                        if ($record->bill_status == 1 && $record->bill) {
                            if ($record->bill->net_payable_amount <= 0) {
                                return __('messages.invoice.paid');
                            } else {
                                return __('messages.employee_payroll.unpaid');
                            }
                        } else {
                            return __('messages.employee_payroll.unpaid');
                        }
                    })
                    ->color(function ($record) {
                        if ($record->bill_status == 1 && $record->bill) {
                            if ($record->bill->net_payable_amount <= 0) {
                                return 'success';
                            } else {
                                return 'danger';
                            }
                        } else {
                            return 'danger';
                        }
                    })
                    ->searchable(),
            ])
            ->recordUrl(null)
            ->filters([
                SelectFilter::make('is_discharge')
                    ->label(__('messages.common.status') . ':')
                    ->options([
                        '' => __('messages.filter.all'),
                        0 => __('messages.filter.active'),
                        1 => __('messages.ipd_patient.discharged'),
                    ])->native(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->iconButton()
                    ->visible(fn($record) => $record->bill_status == 0)->successNotificationTitle(__('messages.flash.IPD_Patient_updated')),
                Tables\Actions\DeleteAction::make()->iconButton()
                    ->action(function ($record) {
                        if (! canAccessRecord(IpdPatientDepartment::class, $record->id)) {
                            return Notification::make()->danger()->title(__('messages.flash.ipd_patient_not_found'))->send();
                        }

                        app(IpdPatientDepartmentRepository::class)->deleteIpdPatientDepartment($record);

                        Notification::make()
                            ->success()
                            ->title(__('messages.flash.IPD_Patient_deleted'))
                            ->send();
                    })->successNotificationTitle(__('messages.flash.IPD_Patient_deleted')),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ])
            ->emptyStateHeading(__('messages.common.no_data_found'));
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
            'index' => Pages\ListIpdPatients::route('/'),
            'create' => Pages\CreateIpdPatient::route('/create'),
            'view' => Pages\ViewIpdPatient::route('/{record}'),
            'edit' => Pages\EditIpdPatient::route('/{record}/edit'),
        ];
    }
}
