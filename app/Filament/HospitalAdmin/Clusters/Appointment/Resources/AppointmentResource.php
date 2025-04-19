<?php

namespace App\Filament\HospitalAdmin\Clusters\Appointment\Resources;
use App\Services\ScheduleService;

use Carbon\Carbon;
use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Doctor;
use App\Models\Patient;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Schedule;
use Filament\Forms\Form;
use App\Models\LunchBreak;
use Filament\Tables\Table;
use App\Models\CustomField;
use App\Models\ScheduleDay;
use App\Models\DoctorHoliday;
use App\Models\DoctorDepartment;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\SubNavigationPosition;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\TextEntry;
use App\Models\Appointment as AppointmentModel;
use App\Filament\HospitalAdmin\Clusters\Appointment;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use App\Filament\HospitalAdmin\Clusters\Doctors\Resources\DoctorResource;
use App\Filament\HospitalAdmin\Clusters\Patients\Resources\PatientResource;
use App\Filament\HospitalAdmin\Clusters\Doctors\Resources\DoctorDepartmentResource;
use App\Filament\HospitalAdmin\Clusters\Appointment\Resources\AppointmentResource\Pages;
use Filament\Tables\Filters\Filter;
use Stevebauman\Location\Facades\Location;
use Illuminate\Support\Facades\Log;

class AppointmentResource extends Resource
{
    protected static ?string $model = AppointmentModel::class;

    protected static ?string $cluster = Appointment::class;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?int $navigationSort = 100;

    public static function shouldRegisterNavigation(): bool
    {
        if (auth()->user()->hasRole(['Admin']) && !getModuleAccess('Appointments')) {
            return false;
        } elseif (!auth()->user()->hasRole('Admin') && !getModuleAccess('Appointments')) {
            return false;
        }
        return true;
    }

    public static function getLabel(): string
    {
        return __('messages.appointments');
    }
    public static function canEdit(Model $record): bool
    {

        if (auth()->user()->hasRole(['Admin', 'Receptionist'])) {
            return true;
        }
        return false;
    }
    public static function canCreate(): bool
    {
        if (auth()->user()->hasRole(['Admin', 'Receptionist', 'Patient'])) {
            return true;
        }
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        if (auth()->user()->hasRole(['Admin', 'Receptionist', 'Patient'])) {

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


    public static function form(Form $form): Form
    {
        $customFields = CustomField::where('module_name', CustomField::Appointment)->Where('tenant_id', getLoggedInUser()->tenant_id)->get();

        $customFieldComponents = [];
        foreach ($customFields as $field) {
            $fieldType = CustomField::FIELD_TYPE_ARR[$field->field_type];
            $fieldName = 'field' . $field->id;
            $fieldLabel = $field->field_name;
            $isRequired = $field->is_required;
            $gridSpan = $field->grid; //

            $customFieldComponents[] = match ($fieldType) {
                'text' => Forms\Components\TextInput::make($fieldName)
                    ->label($fieldLabel)
                    ->required($isRequired)
                    ->placeholder($fieldLabel)
                    ->columnSpan($gridSpan)
                    ->default(fn($record) => $record?->{$fieldName} ?? null),

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
                    ->extraInputAttributes(['oninput' => "this.value = this.value.replace(/[e\+\-]/gi, '')"])
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

        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Forms\Components\Select::make('patient_id')
                            ->label(__('messages.document.patient') . ': ')
                            ->placeholder(__('messages.document.select_patient'))
                            ->options(Patient::with('patientUser')
                                ->whereHas('patientUser', function ($query) {
                                    $query->where('status', 1);
                                })
                                ->where('tenant_id', getLoggedInUser()->tenant_id)
                                ->orderBy('id', 'desc')
                                ->get()
                                ->pluck('patientUser.full_name', 'id'))
                            ->native(false)
                            ->searchable()
                            ->hidden(fn() => (getLoggedinPatient()) ? true : false)
                            ->required()
                            ->validationAttribute(__('messages.document.patient'))
                            ->searchable()
                            ->id('appointmentsPatientId')
                            ->validationMessages([
                                'required' => __('messages.fields.the') . ' ' . __('messages.document.patient') . ' ' . __('messages.fields.required'),
                            ]),
                        Forms\Components\Select::make('department_id')
                            ->label(__('messages.appointment.doctor_department') . ':')
                            ->placeholder(__('messages.web_appointment.select_department'))
                            ->options(DoctorDepartment::where('tenant_id', getLoggedInUser()->tenant_id)->orderBy('id', 'desc')->get()->pluck('title', 'id'))
                            ->live()
                            ->searchable()
                            ->native(false)
                            ->required()
                            ->validationAttribute(__('messages.appointment.doctor_department'))
                            ->reactive()
                            ->afterStateUpdated(function (Set $set) {
                                $set('opd_date', null);
                                $set('doctor_id', null);
                            })
                            ->validationMessages([
                                'required' => __('messages.fields.the') . ' ' . __('messages.appointment.doctor_department') . ' ' . __('messages.fields.required'),
                            ]),
                        Forms\Components\Select::make('doctor_id')
                            ->label(__('messages.case.doctor') . ':')
                            ->searchable()
                            ->placeholder(__('messages.web_appointment.select_doctor'))
                            ->options(function (Get $get) {
                                $departmentId = $get('department_id');
                                if ($departmentId) {
                                    $doctors = Doctor::where('doctor_department_id', $departmentId)
                                        ->withWhereHas('doctorUser', fn($query) => $query->where('status', true))
                                        ->get()->pluck('doctorUser.full_name', 'id')->toArray();
                                    return $doctors;
                                }
                                return [];
                            })
                            ->preload()
                            ->native(false)
                            ->live()
                            ->required()
                            ->validationAttribute(__('messages.case.doctor'))
                            ->reactive()
                            ->disabled(fn($get) => !$get('department_id'))
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                $doctorId = $get('doctor_id');
                                if ($doctorId) {
                                    $doctor = Doctor::find($doctorId);
                                    $set('appointment_charge', $doctor->appointment_charge);
                                }
                                $set('opd_date', null);
                            })
                            ->validationMessages([
                                'required' => __('messages.fields.the') . ' ' . __('messages.case.doctor') . ' ' . __('messages.fields.required'),
                            ]),
                        TextInput::make('appointment_charge')
                            ->label(__('messages.appointment_charge') . ':')
                            ->readOnly()
                            ->afterStateHydrated(function ($get, $operation, $set) {
                                if ($operation == 'edit') {
                                    $doctorId = $get('doctor_id');
                                    $doctor = Doctor::find($doctorId);
                                    $charge = $doctor->appointment_charge;
                                    $set('appointment_charge', $charge);
                                }
                            })
                            ->hidden(function (Get $get, $operation) {
                                $doctorId = $get('doctor_id');
                                if ($doctorId) {
                                    $doctor = Doctor::find($doctorId);
                                    $charge = $doctor->appointment_charge;
                                    if ($charge) {
                                        return false;
                                    };
                                    return true;
                                }
                                return true;
                            }),
                        Select::make('payment_type')
                            ->options(
                                function ($operation) {
                                    if ($operation == "create") {
                                        return getAppointmentPaymentTypes();
                                    } else {
                                        return AppointmentModel::EDIT_PAYMENT_TYPES;
                                    }
                                }
                            )
                            ->native(false)
                            ->searchable()
                            ->required()
                            ->validationAttribute(__('messages.subscription_plans.payment_type'))
                            ->hidden(function (Get $get) {
                                $doctorId = $get('doctor_id');
                                if ($doctorId) {
                                    $doctor = Doctor::find($doctorId);
                                    $charge = $doctor->appointment_charge;
                                    if ($charge) {
                                        return false;
                                    };
                                    return true;
                                }
                                return true;
                            })
                            ->label(__('messages.subscription_plans.payment_type') . ':')
                            ->validationMessages([
                                'required' => __('messages.fields.the') . ' ' . __('messages.subscription_plans.payment_type') . ' ' . __('messages.fields.required'),
                            ]),

                        DatePicker::make('opd_date')
                            ->label(__('messages.appointment.date') . ':')
                            ->placeholder(__('messages.appointment.date'))
                            ->required()
                            ->validationAttribute(__('messages.appointment.date'))
                            ->live()
                            ->native(false)
                            ->minDate(function () {
                                return Carbon::now()->startOfDay()->format('Y-m-d');
                            })
                            ->afterStateUpdated(function ($set, $get) {
                                $doctorId = $get('doctor_id');
                                $opd_date = $get('opd_date');
                            
                                $carbonDate = \Carbon\Carbon::parse($opd_date);
                                $dayName = \App\Models\ScheduleDay::getDayNameFromCarbon($carbonDate);
                            
                                
                                $scheduleDay = \App\Models\ScheduleDay::where('doctor_id', $doctorId)
                                    ->where('available_on', $dayName)
                                    ->get();
                            
                                
                                $doctorHoliday = \App\Models\DoctorHoliday::where('doctor_id', $doctorId)
                                    ->where('date', $carbonDate->format('Y-m-d'))
                                    ->get();
                            
                                
                                if ($scheduleDay->count() != 0 && $doctorHoliday->count() == 0) {
                                    $doctorEndTime = $carbonDate->format('Y-m-d') . " " . $scheduleDay->first()->available_to;
                            
                                
                                    if (\Carbon\Carbon::parse($doctorEndTime)->isBefore(\Carbon\Carbon::now())) {
                                        \Filament\Notifications\Notification::make()
                                            ->title(__('js.doctor_schedule_not_available_on_this_date'))
                                            ->warning()
                                            ->send();
                                    }
                                }
                            })
                            
                            
                            ->validationMessages([
                                'required' => __('messages.fields.the') . ' ' . __('messages.appointment.date') . ' ' . __('messages.fields.required'),
                            ])
                            ->maxDate(Carbon::now()->copy()->addDays(36500)->format('Y-m-d')),

                        Textarea::make('problem')
                            ->label(__('messages.appointment.description') . ':')
                            ->placeholder(__('messages.appointment.description'))
                            ->rows(4),
                        Toggle::make('is_completed')
                            ->label(__('messages.common.status') . ':')
                            ->default(true)
                            ->extraAlpineAttributes(['class' => 'mt-5']),
                        Forms\Components\Select::make('payment_type')
                            ->label(__('messages.ipd_payments.payment_mode'))
                            ->placeholder(__('messages.ipd_payments.payment_mode'))
                            ->options(AppointmentModel::EDIT_PAYMENT_TYPES)
                            ->native(false)
                            ->required()
                            ->validationAttribute(__('messages.ipd_payments.payment_mode'))
                            ->id('appointmentPayment')
                            ->hidden(true)
                            ->validationMessages([
                                'required' => __('messages.fields.the') . ' ' . __('messages.ipd_payments.payment_mode') . ' ' . __('messages.fields.required'),
                            ]),
                        ToggleButtons::make('time')
                            ->label(__('messages.available_slots') . ':')
                            ->visibleOn('create')
                            ->inline()
                            ->extraAttributes(['class' => 'booked-appointment-slot'])
                            ->live()
                            ->afterStateHydrated(fn($component, $record, $operation) => $operation == 'edit' ? $component->state($record->opd_date ? \Carbon\Carbon::parse($record->opd_date)->format('H:i') : null) : null)
                            ->options(function (Get $get) {
                                $doctorId = $get('doctor_id');
                                $opdDate = $get('opd_date');
                            
                                if (!$doctorId || !$opdDate) {
                                    return [];
                                }
                            
                                $date = \Carbon\Carbon::parse($opdDate);
                                $result = \App\Services\ScheduleService::getSlotsForAppointmentForm($doctorId, $date, $opdDate);
                            
                                // Renderizamos el bloque informativo solo si hay horarios
                                if (!empty($result['label'])) {
                                    echo '<div class="schedule-time text-sm font-medium leading-6 text-gray-950 w-full dark:text-white mb-2">'
                                        . $result['label'] . '</div>';
                                } else {
                                    \Filament\Notifications\Notification::make()
                                        ->title(__('messages.appointment.doctor_schedule_not_available_on_this_date'))
                                        ->warning()
                                        ->send();
                                }
                            
                                return $result['slots'];
                            })
                            
                            ->visible(function (Get $get) {
                                return $get('opd_date') != null;
                            })
                            ->disableOptionWhen(function ($value, Get $get) {
                                $doctorId = $get('doctor_id');
                                $opd_date = $get('opd_date');
                                $date = Carbon::parse($opd_date)->format('Y-m-d');

                                $bookedAppointments = AppointmentModel::where('doctor_id', $doctorId)
                                    ->whereDate('opd_date', $date)
                                    ->pluck('opd_date')
                                    ->map(function ($appointment) {
                                        return Carbon::parse($appointment)->format('H:i'); // Format as H:i (hours:minutes)
                                    })
                                    ->toArray();
                                return in_array($value, $bookedAppointments);
                            })
                            ->required()
                            ->validationAttribute(__('messages.available_slots') . ':'),
                    ])->columns(2),
                Section::make('')
                    ->hidden(empty($customFieldComponents))
                    ->schema($customFieldComponents)
                    ->columns(12)
                    ->visible(function () {
                        $customFields = CustomField::where('module_name', CustomField::Appointment)->Where('tenant_id', getLoggedInUser()->tenant_id)->get();
                        if ($customFields->count() == 0) {
                            return false;
                        } else {
                            return true;
                        }
                    }),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('patient.user.full_name')
                    ->label(__('messages.case.patient') . ':'),
                TextEntry::make('doctor.department.title')
                    ->label(__('messages.appointment.doctor_department') . ':'),
                TextEntry::make('doctor.user.full_name')
                    ->label(__('messages.case.doctor') . ':'),
                TextEntry::make('opd_date')
                    ->label(__('messages.appointment.date') . ':'),
                TextEntry::make('is_completed')
                    ->label(__('messages.common.status') . ':')
                    ->formatStateUsing(function ($state) {
                        return $state === 1 ? __('messages.appointment.completed') : __('messages.appointment.pending');
                    }),
                TextEntry::make('problem')
                    ->label(__('messages.common.description') . ':')
                    ->formatStateUsing(fn($state) => $state ?: __('messages.common.n/a')),

            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        if (auth()->user()->hasRole(['Admin', 'Receptionist', 'Doctor']) && !getModuleAccess('Appointments')) {
            abort(404);
        }
        $table = $table->modifyQueryUsing(function ($query) {
            $query->where('tenant_id', auth()->user()->tenant_id)->where('id', '!=', auth()->user()->id);

            if (! getLoggedinDoctor()) {
                if (getLoggedinPatient()) {
                    $patient = Auth::user();
                    $query->whereHas('patient', function (Builder $query) use ($patient) {
                        $query->where('user_id', '=', $patient->id);
                    });
                }
            } else {
                $doctorId = Doctor::where('user_id', getLoggedInUserId())->first();
                $query = $query->where('doctor_id', $doctorId->id);
            }
        });

        return $table
            ->paginated([10, 25, 50])
            ->columns([
                SpatieMediaLibraryImageColumn::make('patient.patientUser.profile')
                    ->label(__('messages.role.patient'))
                    ->circular()
                    ->defaultImageUrl(function ($record) {
                        if (!$record->patient->user->hasMedia(User::COLLECTION_PROFILE_PICTURES)) {
                            return getUserImageInitial($record->id, $record->patient->user->full_name);
                        }
                    })
                    ->sortable(['first_name'])
                    ->url(fn($record) => PatientResource::getUrl('view', ['record' => $record->patient->id]))
                    ->collection('profile')
                    ->width(50)->height(50),
                TextColumn::make('patient.user.full_name')
                    ->label('')
                    ->color('primary')
                    ->description(fn($record) => $record->patient->patientUser->email ?? __('messages.common.n/a'))
                    ->weight(FontWeight::SemiBold)
                    ->formatStateUsing(fn($record) => '<a href="' . PatientResource::getUrl('view', ['record' => $record->patient->id]) . '"class="hoverLink">' . $record->patient->patientUser->full_name . '</a>')
                    ->html()
                    ->searchable(['users.first_name', 'users.last_name']),
                SpatieMediaLibraryImageColumn::make('doctor.doctorUser.profile')
                    ->label(__('messages.role.doctor'))
                    ->circular()
                    ->defaultImageUrl(function ($record) {
                        if (!$record->doctor->user->hasMedia(User::COLLECTION_PROFILE_PICTURES)) {
                            return getUserImageInitial($record->id, $record->doctor->user->full_name);
                        }
                    })
                    ->sortable(['first_name'])
                    ->url(
                        fn($record) => Auth::user()->hasRole(['Admin', 'Doctor', 'Case Manager', 'Receptionist', 'Pharmacist', 'Lab Technician']) ? DoctorResource::getUrl('view', ['record' => $record->doctor->id]) : ''
                    )
                    ->collection('profile')
                    ->width(50)->height(50),
                TextColumn::make('doctor.doctorUser.full_name')
                    ->label('')
                    ->color('primary')
                    ->weight(FontWeight::SemiBold)
                    ->formatStateUsing(
                        function ($record) {
                            $user = auth()->user();
                            $allowedRoles = ['Admin', 'Doctor', 'Case Manager', 'Receptionist', 'Pharmacist', 'Lab Technician'];

                            if ($user->hasRole($allowedRoles)) {
                                return '<a href="' . DoctorResource::getUrl('view', ['record' => $record->doctor->id]) . '" class="hoverLink">' . $record->doctor->doctorUser->full_name . '</a>';
                            }

                            return $record->doctor->doctorUser->full_name;
                        }
                    )
                    ->html()
                    ->description(fn($record) => $record->doctor->doctorUser->email ?? __('messages.common.n/a'))
                    ->searchable(['users.first_name', 'users.last_name']),
                TextColumn::make('department.title')
                    ->label(__('messages.doctor_department.doctor_department'))
                    ->sortable()
                    ->color('primary')
                    ->formatStateUsing(
                        function ($record) {
                            $user = auth()->user();
                            $allowedRoles = ['Admin', 'Doctor', 'Case Manager', 'Receptionist', 'Pharmacist', 'Lab Technician'];
                            if ($user->hasRole($allowedRoles)) {
                                return '<a href="' . DoctorDepartmentResource::getUrl('view', ['record' => $record->department->id]) . '" class="hoverLink">' . $record->department->title . '</a>';
                            }

                            return $record->department->title;
                        }
                    )
                    ->html()
                    ->searchable(),
                TextColumn::make('opd_date')
                    ->label(__('messages.appointment.date'))
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(
                        fn($state) =>
                        '<div class="text-center">' . Carbon::parse($state)->format('g:i A') . '</div>' . Carbon::parse($state)->format('jS M, Y')
                    )
                    ->badge()
                    ->html(),
                TextColumn::make('is_completed')
                    ->label(__('messages.common.status'))
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        switch ($state) {
                            case 0:
                                return __('messages.appointment.pending');
                            case 1:
                                return __('messages.appointment.completed');
                            case 3:
                                return __('messages.common.canceled');
                        }
                    })
                    ->color(function ($state) {
                        switch ($state) {
                            case 0:
                                return 'warning';
                            case 1:
                                return 'success';
                            case 3:
                                return 'danger';
                        }
                    }),
            ])
            ->recordUrl(null)
            ->recordAction(null)
            ->filters([
                Filter::make('is_completed')
                    ->form([
                        Select::make('is_completed')
                            ->label(__('messages.common.status'))
                            ->options([
                                'all' => __('messages.filter.all'),
                                1 => __('messages.appointment.pending'),
                                2 => __('messages.appointment.completed'),
                                3 => __('messages.common.canceled')
                            ])
                            ->native(false)
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (isset($data['is_completed'])) {
                            if ($data['is_completed'] == 'all') {
                                return $query;
                            }
                            if ($data['is_completed'] == 1) {
                                return $query->where('is_completed', 0);
                            }
                            if ($data['is_completed'] == 2) {
                                return $query->where('is_completed', 1);
                            }
                            if ($data['is_completed'] == 3) {
                                return $query->where('is_completed', 3);
                            }
                        }
                    })->indicateUsing(function ($data) {
                        if (!$data['is_completed']) {
                            return null;
                        }
                        if ($data['is_completed'] == 'all') {
                            return __('messages.filter.all');
                        }
                        if ($data['is_completed'] == 1) {
                            return __('messages.appointment.pending');
                        }
                        if ($data['is_completed'] == 2) {
                            return __('messages.appointment.completed');
                        }
                        if ($data['is_completed'] == 3) {
                            return __('messages.common.canceled');
                        }
                    }),
                Filter::make('date')
                    ->form([
                        Select::make('date')
                            ->label(__('messages.appointment.date'))
                            ->options([
                                'today' => __('messages.appointment.today'),
                                'yesterday' => __('messages.appointment.yesterday'),
                                'last_7_days' => __('messages.appointment.last_7_days'),
                                'last_30_days' => __('messages.appointment.last_30_days'),
                                'this_month' => __('messages.appointment.this_month'),
                                'last_month' => __('messages.appointment.last_month'),
                                'custom' => __('messages.appointment.custom'),
                            ])
                            ->native(false),
                        DatePicker::make('start_date')
                            ->label(__('messages.appointment.start_date'))
                            ->visible(fn($get) => $get('date') === 'custom'),
                        DatePicker::make('end_date')
                            ->label(__('messages.appointment.end_date'))
                            ->visible(fn($get) => $get('date') === 'custom'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        $dateRange = $data['date'] ?? null;
                        switch ($dateRange) {
                            case 'today':
                                $query->whereDate('opd_date', Carbon::today());
                                break;
                            case 'yesterday':
                                $query->whereDate('opd_date', Carbon::yesterday());
                                break;
                            case 'last_7_days':
                                $query->whereBetween('opd_date', [Carbon::now()->subDays(6), Carbon::today()]);
                                break;
                            case 'last_30_days':
                                $query->whereBetween('opd_date', [Carbon::now()->subDays(29), Carbon::today()]);
                                break;
                            case 'this_month':
                                $query->whereBetween('opd_date', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
                                break;
                            case 'last_month':
                                $query->whereBetween('opd_date', [
                                    Carbon::now()->subMonth()->startOfMonth(),
                                    Carbon::now()->subMonth()->endOfMonth(),
                                ]);
                                break;
                            case 'custom':
                                if (!empty($data['start_date']) && !empty($data['end_date'])) {
                                    $query->whereBetween('opd_date', [$data['start_date'], $data['end_date']]);
                                }
                                break;
                        }
                    })
                    ->indicateUsing(function ($data) {
                        $dateRange = $data['date'] ?? null;
                        if ($dateRange === 'custom' && !empty($data['start_date']) && !empty($data['end_date'])) {
                            return __('messages.appointment.custom') . ': ' . $data['start_date'] . ' - ' . $data['end_date'];
                        }

                        return match ($dateRange) {
                            'today' => __('messages.appointment.today'),
                            'yesterday' => __('messages.appointment.yesterday'),
                            'last_7_days' => __('messages.appointment.last_7_days'),
                            'last_30_days' => __('messages.appointment.last_30_days'),
                            'this_month' => __('messages.appointment.this_month'),
                            'last_month' => __('messages.appointment.last_month'),
                            default => null,
                        };
                    }),
            ])
            ->actions([
                // Tables\Actions\ViewAction::make()->iconButton()->modalCancelAction(false)->modalWidth("md")->hidden(auth()->user()->hasRole('Patient')),
                Tables\Actions\Action::make('approve')
                    ->icon('heroicon-o-calendar')
                    ->iconButton()
                    ->color('success')
                    ->action(function ($record) {
                        $record->is_completed = AppointmentModel::STATUS_COMPLETED;
                        $record->save();
                        Notification::make()
                            ->title(__('messages.flash.appointment_approved_successfully'))
                            ->success()
                            ->send();
                    })->requiresConfirmation()->hidden(function ($record) {
                        return $record->is_completed != AppointmentModel::STATUS_PENDING;
                    }),
                Tables\Actions\Action::make('cancel')
                    ->icon('heroicon-o-calendar')
                    ->iconButton()->color('danger')
                    ->action(function ($record) {
                        $record->is_completed = AppointmentModel::STATUS_CANCELLED;
                        $record->save();
                        Notification::make()
                            ->title(__('messages.flash.appointment_cancelled_successfully'))
                            ->danger()
                            ->send();
                    })->requiresConfirmation()->hidden(function ($record) {
                        return $record->is_completed != AppointmentModel::STATUS_PENDING;
                    }),
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->hidden(function ($record) {
                        if (($record->is_completed == 0 || $record->is_completed == 1) && ($record->payment_type == 4  || $record->payment_type == 6  || $record->payment_type == NULL)) {
                            return false;
                        }
                        return true;
                    }),
            ])
            ->defaultSort('id', 'desc')
            ->actionsColumnLabel(__('messages.common.action'))
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
            'index' => Pages\ListAppointments::route('/'),
            'create' => Pages\CreateAppointment::route('/create'),
            'edit' => Pages\EditAppointment::route('/{record}/edit'),
        ];
    }


}
