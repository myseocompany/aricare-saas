<?php

namespace App\Livewire;

use Closure;
use Exception;
use Carbon\Carbon;
use Livewire\Component;
use App\Models\Medicine;
use Filament\Tables\Table;
use Illuminate\Support\Arr;
use App\Models\MedicineBill;
use App\Models\Prescription;
use App\Models\SaleMedicine;
use Filament\Tables\Actions;
use App\Models\OpdPrescription;
use Illuminate\Support\HtmlString;
use App\Models\OpdPrescriptionItem;
use App\Models\OpdPatientDepartment;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Route;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Livewire;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use App\Repositories\OpdPrescriptionRepository;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Repositories\OpdPatientDepartmentRepository;
use Ysfkaya\FilamentPhoneInput\Infolists\PhoneEntry;
use Filament\Infolists\Components\Group as InfolistGroup;
use Filament\Infolists\Components\Actions as InfolistGroupAction;
use Filament\Infolists\Components\Actions\Action as InfolistAction;

class OpdPatientPrescriptionTable extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public $record;
    public $id;
    public $opdPrescriptionId;

    public function mount()
    {
        $this->id = Route::current()->parameter('record');
        $this->opdPrescriptionId;
    }

    public function GetRecord()
    {
        $opdPatients = OpdPrescription::whereOpdPatientDepartmentId($this->id)->orderBy('id', 'desc');
        return $opdPatients;
    }

    public function getFormFields(): array
    {
        return [
            Hidden::make('opd_patient_department_id')->default($this->id),
            Textarea::make('header_note')
                ->rows(4)
                ->placeholder(__('messages.ipd_patient_prescription.header_note') . ':')
                ->label(__('messages.ipd_patient_prescription.header_note')),
            Repeater::make('prescription')
                ->schema([
                    Select::make('category_id')
                        ->label(__('messages.medicine.medicine_category') . ':')
                        ->placeholder(__('messages.medicine_bills.select_medicine'))
                        ->options(app(OpdPatientDepartmentRepository::class)->getMedicineCategoriesList())
                        ->searchable()
                        ->preload()
                        ->live()
                        ->native(false)
                        ->required()
                        ->columnSpan(3)
                        ->validationMessages([
                            'required' => __('messages.fields.the') . ' ' . __('messages.medicine.medicine_category') . ' ' . __('messages.fields.required'),
                        ]),
                    Select::make('medicine_id')
                        ->label(__('messages.medicine.medicine_category') . ':')
                        ->placeholder(__('messages.medicine_bills.select_medicine'))
                        ->options(fn($get) => Medicine::where('tenant_id', getLoggedInUser()->tenant_id)->where('category_id', '=', $get('category_id'))->pluck('name', 'id')->toArray())
                        ->disabled(function ($get) {
                            if (!empty(Medicine::where('tenant_id', getLoggedInUser()->tenant_id)->where('category_id', '=', $get('category_id'))->get()->toArray())) {
                                return false;
                            }
                            return true;
                        })
                        ->live()
                        ->helperText(function ($state) {
                            $qty =  Medicine::whereId($state)->where('tenant_id', getLoggedInUser()->tenant_id)->value('available_quantity');
                            if (isset($qty) && $qty > 10) {
                                return new HtmlString('<span style="color:#4BB543;">' . __('messages.item.available_quantity') . ' : ' . $qty . '</span>');
                            } elseif (isset($qty) && $qty <= 10) {
                                return new HtmlString('<span style="color:red;">' . __('messages.item.available_quantity') . ' : ' . $qty . '</span>');
                            }
                            return null;
                        })
                        ->rules([
                            fn(): Closure => function (string $attribute, $value, Closure $fail, $sta) {
                                if (Medicine::whereId($value)->where('tenant_id', getLoggedInUser()->tenant_id)->value('available_quantity') <= 0) {
                                    $fail('');
                                    Notification::make()->danger()->title(__('messages.medicine_bills.available_quantity') . ' ' . Medicine::whereId($value)->where('tenant_id', getLoggedInUser()->tenant_id)->value('name') . ' ' . __('messages.medicine_bills.is') . ' ' . Medicine::whereId($value)->where('tenant_id', getLoggedInUser()->tenant_id)->value('available_quantity'))->send();
                                }
                            },
                        ])
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->required()
                        ->columnSpan(3)
                        ->validationMessages([
                            'required' => __('messages.fields.the') . ' ' . __('messages.medicine.medicine_category') . ' ' . __('messages.fields.required'),
                        ]),
                    TextInput::make('dosage')
                        ->label(__('messages.ipd_patient_prescription.dosage') . ':')
                        ->placeholder(__('messages.ipd_patient_prescription.dosage') . ':')
                        ->columnSpan(1)
                        ->maxLength(255)
                        ->required(),
                    Select::make('day')
                        ->options(Prescription::DOSE_DURATION)
                        ->label(__('messages.prescription.duration') . ':')
                        ->live()
                        ->default(1)
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->required()
                        ->columnSpan(3)
                        ->validationMessages([
                            'required' => __('messages.fields.the') . ' ' . __('messages.prescription.duration') . ' ' . __('messages.fields.required'),
                        ]),
                    Select::make('time')
                        ->options(Prescription::MEAL_ARR)
                        ->label(__('messages.prescription.time') . ':')
                        ->live()
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->default(1)
                        ->required()
                        ->columnSpan(3)
                        ->validationMessages([
                            'required' => __('messages.fields.the') . ' ' . __('messages.prescription.time') . ' ' . __('messages.fields.required'),
                        ]),
                    Select::make('dose_interval')
                        ->options(Prescription::DOSE_INTERVAL)
                        ->label(__('messages.medicine_bills.dose_interval') . ':')
                        ->live()
                        ->searchable()
                        ->default(1)
                        ->preload()
                        ->native(false)
                        ->required()
                        ->columnSpan(3)
                        ->validationMessages([
                            'required' => __('messages.fields.the') . ' ' . __('messages.medicine_bills.dose_interval') . ' ' . __('messages.fields.required'),
                        ]),
                    Textarea::make('instruction')
                        ->rows(1)
                        ->columnSpan(2)
                        ->required()
                        ->maxLength(255)
                        ->placeholder(__('messages.ipd_patient_prescription.instruction') . ':')
                        ->label(__('messages.ipd_patient_prescription.instruction')),
                ])->columns(18)
                ->addActionLabel(__('messages.common.add'))
                ->live()
                ->rules([
                    fn(): Closure => function ($attribute, $value, $fail) {
                        $medicineIds = array_column($value, 'medicine_id');
                        if (count($medicineIds) !== count(array_unique($medicineIds))) {
                            $fail('');
                            Notification::make()->danger()->title(__('messages.medicine_bills.duplicate_medicine'))->send();
                        }
                    }
                ])
                ->deletable(function ($state) {
                    if (count($state) === 1) {
                        return false;
                    }
                    return true;
                }),
            Textarea::make('footer_note')
                ->rows(4)
                ->placeholder(__('messages.ipd_patient_prescription.footer_note') . ':')
                ->label(__('messages.ipd_patient_prescription.footer_note')),
        ];
    }

    public function getEditFormFields(): array
    {
        return [
            Hidden::make('opd_patient_department_id')->default($this->id),
            Textarea::make('header_note')
                ->rows(4)
                ->placeholder(__('messages.ipd_patient_prescription.header_note') . ':')
                ->label(__('messages.ipd_patient_prescription.header_note')),
            Repeater::make('prescription')
                ->schema([
                    Select::make('category_id')
                        ->label(__('messages.medicine.medicine_category') . ':')
                        ->placeholder(__('messages.medicine_bills.select_medicine'))
                        ->options(app(OpdPatientDepartmentRepository::class)->getMedicineCategoriesList())
                        ->searchable()
                        ->preload()
                        ->live()
                        ->native(false)
                        ->required()
                        ->columnSpan(3)
                        ->validationMessages([
                            'required' => __('messages.fields.the') . ' ' . __('messages.medicine.medicine_category') . ' ' . __('messages.fields.required'),
                        ]),
                    Select::make('medicine_id')
                        ->label(__('messages.medicine.medicine_category') . ':')
                        ->placeholder(__('messages.medicine_bills.select_medicine'))
                        ->options(fn($get) => Medicine::where('tenant_id', getLoggedInUser()->tenant_id)->where('category_id', '=', $get('category_id'))->pluck('name', 'id')->toArray())
                        ->disabled(function ($get) {
                            if (!empty(Medicine::where('tenant_id', getLoggedInUser()->tenant_id)->where('category_id', '=', $get('category_id'))->get()->toArray())) {
                                return false;
                            }
                            return true;
                        })
                        ->live()
                        ->helperText(function ($state) {
                            $qty =  Medicine::whereId($state)->where('tenant_id', getLoggedInUser()->tenant_id)->value('available_quantity');
                            if (isset($qty) && $qty > 10) {
                                return new HtmlString('<span style="color:#4BB543;">' . __('messages.item.available_quantity') . ' : ' . $qty . '</span>');
                            } elseif (isset($qty) && $qty <= 10) {
                                return new HtmlString('<span style="color:red;">' . __('messages.item.available_quantity') . ' : ' . $qty . '</span>');
                            }
                            return null;
                        })
                        ->rules([
                            fn(): Closure => function (string $attribute, $value, Closure $fail, $sta) {
                                if (Medicine::whereId($value)->where('tenant_id', getLoggedInUser()->tenant_id)->value('available_quantity') <= 0) {
                                    $fail('');
                                    Notification::make()->danger()->title(__('messages.medicine_bills.available_quantity') . ' ' . Medicine::whereId($value)->where('tenant_id', getLoggedInUser()->tenant_id)->value('name') . ' ' . __('messages.medicine_bills.is') . ' ' . Medicine::whereId($value)->where('tenant_id', getLoggedInUser()->tenant_id)->value('available_quantity'))->send();
                                }
                            },
                        ])
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->required()
                        ->columnSpan(3)
                        ->validationMessages([
                            'required' => __('messages.fields.the') . ' ' . __('messages.medicine.medicine_category') . ' ' . __('messages.fields.required'),
                        ]),
                    TextInput::make('dosage')
                        ->label(__('messages.ipd_patient_prescription.dosage') . ':')
                        ->columnSpan(1)
                        ->maxLength(255)
                        ->required(),
                    Select::make('day')
                        ->options(Prescription::DOSE_DURATION)
                        ->label(__('messages.prescription.duration') . ':')
                        ->live()
                        ->default(1)
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->required()
                        ->columnSpan(3)
                        ->validationMessages([
                            'required' => __('messages.fields.the') . ' ' . __('messages.prescription.duration') . ' ' . __('messages.fields.required'),
                        ]),
                    Select::make('time')
                        ->options(Prescription::MEAL_ARR)
                        ->label(__('messages.prescription.time') . ':')
                        ->live()
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->default(1)
                        ->required()
                        ->columnSpan(3)
                        ->validationMessages([
                            'required' => __('messages.fields.the') . ' ' . __('messages.prescription.time') . ' ' . __('messages.fields.required'),
                        ]),
                    Select::make('dose_interval')
                        ->options(Prescription::DOSE_INTERVAL)
                        ->label(__('messages.medicine_bills.dose_interval') . ':')
                        ->live()
                        ->searchable()
                        ->default(1)
                        ->preload()
                        ->native(false)
                        ->required()
                        ->columnSpan(3)
                        ->validationMessages([
                            'required' => __('messages.fields.the') . ' ' . __('messages.medicine_bills.dose_interval') . ' ' . __('messages.fields.required'),
                        ]),
                    Textarea::make('instruction')
                        ->rows(1)
                        ->columnSpan(2)
                        ->required()
                        ->maxLength(255)
                        ->label(__('messages.ipd_patient_prescription.instruction')),
                ])->columns(18)
                ->addActionLabel(__('messages.common.add'))
                ->live()
                ->rules([
                    fn(): Closure => function ($attribute, $value, $fail) {
                        $medicineIds = array_column($value, 'medicine_id');
                        if (count($medicineIds) !== count(array_unique($medicineIds))) {
                            $fail('');
                            Notification::make()->danger()->title(__('messages.medicine_bills.duplicate_medicine'))->send();
                        }
                    }
                ])
                ->deletable(function ($state) {
                    if (count($state) === 1) {
                        return false;
                    }
                    return true;
                }),
            Textarea::make('footer_note')
                ->rows(4)
                ->placeholder(__('messages.ipd_patient_prescription.footer_note') . ':')
                ->label(__('messages.ipd_patient_prescription.footer_note')),
        ];
    }

    public function getTableColumns(): array
    {
        return [
            TextColumn::make('id')
                ->searchable()
                ->formatStateUsing(fn($record) => \Carbon\Carbon::parse($record->created_at)->translatedFormat('jS M, Y'))
                ->sortable()
                ->label(__('messages.common.created_on'))
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->paginated([10,25,50])
            ->headerActions([
                Actions\CreateAction::make()
                    ->modalWidth('7xl')
                    ->createAnother(false)
                    ->form($this->getFormFields())
                    ->using(function (array $data, string $model) {
                        try {
                            $this->opdPrescriptionId = $model::create(Arr::except($data, ['prescription']));

                            return   $this->opdPrescriptionId;
                        } catch (Exception $e) {

                            Notification::make()
                                ->danger()
                                ->title($e->getMessage())
                                ->send();
                        }
                    })
                    ->after(function (array $data) {
                        if ($this->opdPrescriptionId->id) {

                            $transformedData = array_merge($data, [
                                'category_id' => array_column($data['prescription'], 'category_id'),
                                'medicine_id' => array_column($data['prescription'], 'medicine_id'),
                                'dosage' => array_column($data['prescription'], 'dosage'),
                                'day' => array_column($data['prescription'], 'day'),
                                'time' => array_column($data['prescription'], 'time'),
                                'dose_interval' => array_column($data['prescription'], 'dose_interval'),
                                'instruction' => array_column($data['prescription'], 'instruction'),
                            ]);

                            $data = Arr::except($transformedData, ['prescription']);

                            $opdDepartment = OpdPatientDepartment::with('patient', 'doctor')->whereId($data['opd_patient_department_id'])->first();
                            $amount = 0;
                            $qty = 0;
                            $medicineBill = MedicineBill::create([
                                'bill_number' => 'BIL' . generateUniqueBillNumber(),
                                'patient_id' => $opdDepartment->patient->id,
                                'doctor_id' => $opdDepartment->doctor->id,
                                'model_type' => \App\Models\OpdPrescription::class,
                                'model_id' => $this->opdPrescriptionId->id,
                                'payment_status' => MedicineBill::UNPAID,
                                'discount' => 0,
                                'net_amount' => 0,
                                'total' => 0,
                                'tax_amount' => 0,
                                'payment_type' => 0,
                                'bill_date' => Carbon::now(),
                            ]);

                            foreach ($data['category_id'] as $key => $value) {
                                $opdPrescriptionItem = [
                                    'opd_prescription_id' => $this->opdPrescriptionId->id,
                                    'category_id' => $data['category_id'][$key],
                                    'medicine_id' => $data['medicine_id'][$key],
                                    'dosage' => $data['dosage'][$key],
                                    'day' => $data['day'][$key],
                                    'time' => $data['time'][$key],
                                    'dose_interval' => $data['dose_interval'][$key],
                                    'instruction' => $data['instruction'][$key],
                                ];

                                $opdPrescriptionItem = OpdPrescriptionItem::create($opdPrescriptionItem);

                                $medicine = Medicine::find($data['medicine_id'][$key]);
                                $itemAmount = $data['day'][$key] * $data['dose_interval'][$key] * $medicine->selling_price;
                                $amount += $itemAmount;
                                $qty = $data['day'][$key] * $data['dose_interval'][$key];

                                $saleMedicineArray = [
                                    'medicine_bill_id' => $medicineBill->id,
                                    'medicine_id' => $medicine->id,
                                    'sale_quantity' => $qty,
                                    'sale_price' => $medicine->selling_price,
                                    'expiry_date' => date('Y-m-d h:i', 0000 - 00 - 00),
                                    'amount' => $amount,
                                    'tax' => 0,
                                ];

                                $saleMedicine = SaleMedicine::create($saleMedicineArray);
                            }
                            app(OpdPrescriptionRepository::class)->createNotification($data);

                            $medicineBill->update([
                                'net_amount' => $amount,
                                'total' => $amount,
                            ]);
                        } else {
                            Notification::make()
                                ->danger()
                                ->title(function (Exception $e) {
                                    return $e->getMessage();
                                })
                                ->send();
                        }
                    })
                    ->successNotificationTitle(__('messages.flash.IPD_Prescription_saved'))
                    ->modalHeading(__('messages.ipd_patient_prescription.new_prescription'))
                    ->label(__('messages.ipd_patient_prescription.new_prescription')),
            ])
            ->query(Self::GetRecord())
            ->columns($this->getTableColumns())
            ->actions([
                Actions\ViewAction::make()
                    ->iconButton()
                    ->modalWidth('6xl')
                    ->modalHeading(__('messages.ipd_patient_prescription.prescription_details'))
                    ->infolist([
                        InfolistGroupAction::make([
                            InfolistAction::make('print')
                                ->color('success')
                                ->label(__('messages.ipd_patient_prescription.print_prescription'))
                                ->icon('heroicon-s-printer')
                                ->url(fn($record) => route('opd.prescriptions.pdf', ['id' => $record->id]), shouldOpenInNewTab: true),
                        ])->alignEnd(),
                        Fieldset::make('')
                            ->schema([
                                ImageEntry::make('id')
                                    ->label('')
                                    ->defaultImageUrl(asset(getLogoUrl()))
                                    ->columnSpan(1),
                                InfolistGroup::make([
                                    TextEntry::make('id')
                                        ->label(__('messages.common.address') . ':')
                                        ->formatStateUsing(fn() => getSettingValue()['hospital_address']['value'] ?? __('messages.common.n/a'))
                                        ->inlineLabel(),
                                    TextEntry::make('id')
                                        ->label(__('messages.user.phone') . ':')
                                        ->formatStateUsing(fn() => getSettingValue()['hospital_phone']['value'] ?? __('messages.common.n/a'))
                                        ->inlineLabel(),
                                    TextEntry::make('id')
                                        ->label(__('messages.user.email') . ':')
                                        ->formatStateUsing(fn() => getSettingValue()['hospital_email']['value'] ?? __('messages.common.n/a'))
                                        ->inlineLabel(),
                                    TextEntry::make('id')
                                        ->label(__('messages.common.created_on') . ':')
                                        ->formatStateUsing(fn($record) => date('jS M, Y H:i', strtotime($record->created_at)))
                                        ->inlineLabel(),
                                ])
                            ])
                            ->columns(2),
                        Fieldset::make('')
                            ->schema([
                                TextEntry::make('id')
                                    ->label('')
                                    ->formatStateUsing(function ($record) {
                                        return  !empty($record->header_note) ? nl2br(e($record->header_note)) : __('messages.common.n/a');
                                    })
                            ]),
                        Fieldset::make('')->schema([
                            TextEntry::make('patient.opd_number')
                                ->default(__('messages.common.n/a'))
                                ->label(__('messages.opd_patient.opd_number') . ':'),
                            TextEntry::make('patient.patient.user.full_name')
                                ->default(__('messages.common.n/a'))
                                ->label(__('messages.bed_assign.patient_name') . ':'),
                            TextEntry::make('patient.patient.user.email')
                                ->default(__('messages.common.n/a'))
                                ->label(__('messages.user.email') . ':'),
                            PhoneEntry::make('patient.patient.user.phone')
                                ->default(__('messages.common.n/a'))
                                ->formatStateUsing(function ($state, $record) {
                                    if (str_starts_with($state, '+') && strlen($state) > 4) {
                                        return $state;
                                    }
                                    if (empty($record->patient->patient->user->phone) || empty($record->patient->patient->user->region_code)) {
                                        return __('messages.common.n/a');
                                    }

                                    return $record->patient->patient->user->region_code . $record->patient->patient->user->phone;
                                })
                                ->label(__('messages.user.phone') . ':'),
                            TextEntry::make('patient.patient.user.gender')
                                ->default(__('messages.common.n/a'))
                                ->formatStateUsing(function ($state) {
                                    return $state == 0 ? __('messages.user.male') : __('messages.user.female');
                                })
                                ->label(__('messages.user.gender') . ':'),
                            TextEntry::make('patient.patient.user.age')
                                ->label(__('messages.blood_donor.age') . ':')
                                ->default(__('messages.common.n/a')),
                            TextEntry::make('patient.appointment_date')
                                ->label(__('messages.opd_patient.appointment_date') . ':')
                                ->formatStateUsing(fn($state) => date('jS M, Y H:i', strtotime($state)))
                                ->default(__('messages.common.n/a')),
                            TextEntry::make('patient.patientCase.case_id')
                                ->label(__('messages.case.case_id') . ':')
                                ->default(__('messages.common.n/a')),
                            TextEntry::make('patient.doctor.user.full_name')
                                ->label(__('messages.ipd_patient.doctor_id') . ':')
                                ->default(__('messages.common.n/a')),
                            TextEntry::make('patient.height')
                                ->label(__('messages.ipd_patient.height') . ':')
                                ->formatStateUsing(fn($state) => $state == 0 || $state == null ? __('messages.common.n/a') : $state)
                                ->default(__('messages.common.n/a')),
                            TextEntry::make('patient.weight')
                                ->label(__('messages.ipd_patient.weight') . ':')
                                ->formatStateUsing(fn($state) => $state == 0 || $state == null ? __('messages.common.n/a') : $state)
                                ->default(__('messages.common.n/a')),
                            TextEntry::make('patient.bp')
                                ->label(__('messages.ipd_patient.bp') . ':')
                                ->default(__('messages.common.n/a')),
                            TextEntry::make('patient.symptoms')
                                ->label(__('messages.ipd_patient.symptoms') . ':')
                                ->default(__('messages.common.n/a')),
                        ])->columns(6),
                        Livewire::make(OpdPatientPrescriptionMedicineTable::class, ['recordId' => $this->id]),
                        Fieldset::make('')
                            ->schema([
                                TextEntry::make('footer_note')
                                    ->label('')
                                    ->formatStateUsing(function ($record) {
                                        return !empty($record->footer_note) ? nl2br(e($record->footer_note)) : __('messages.common.n/a');
                                    })
                            ])
                    ]),
                Actions\Action::make('print')
                    ->color('warning')
                    ->icon('heroicon-s-printer')
                    ->iconButton()
                    ->url(fn($record) => route('opd.prescriptions.pdf', ['id' => $record->id]), shouldOpenInNewTab: true),
                Actions\EditAction::make()
                    ->modalWidth('7xl')
                    ->iconButton()
                    ->mutateRecordDataUsing(function (Model $record, array $data): array {
                        $transformedData = OpdPrescriptionItem::where('opd_prescription_id', $record->id)->get()->toArray();

                        $data['prescription'] = $transformedData;

                        return $data;
                    })
                    ->using(function (Model $record, array $data): Model {
                        try {
                            $record->update(Arr::except($data, ['prescription']));

                            return $record;
                        } catch (Exception $e) {

                            Notification::make()
                                ->danger()
                                ->title($e->getMessage())
                                ->send();
                        }
                    })
                    ->after(function (Model $record, array $data) {
                        $input = $data;
                        if ($record->id) {
                            $medicineBill = MedicineBill::whereModelId($record->id)->whereModelType(\App\Models\OpdPrescription::class)->first();
                            $medicineBill->saleMedicine()->delete();
                            $record->opdPrescriptionItems()->delete();
                            $opdDepartment = OpdPatientDepartment::with('patient', 'doctor')->whereId($input['opd_patient_department_id'])->first();
                            $amount = 0;
                            $qty = 0;

                            $transformedData = array_merge($data, [
                                'category_id' => array_column($data['prescription'], 'category_id'),
                                'medicine_id' => array_column($data['prescription'], 'medicine_id'),
                                'dosage' => array_column($data['prescription'], 'dosage'),
                                'day' => array_column($data['prescription'], 'day'),
                                'time' => array_column($data['prescription'], 'time'),
                                'dose_interval' => array_column($data['prescription'], 'dose_interval'),
                                'instruction' => array_column($data['prescription'], 'instruction'),
                            ]);

                            $input = Arr::except($transformedData, ['prescription']);

                            foreach ($input['category_id'] as $key => $value) {
                                $opdPrescriptionItem = [
                                    'opd_prescription_id' => $record->id,
                                    'category_id' => $input['category_id'][$key],
                                    'medicine_id' => $input['medicine_id'][$key],
                                    'dosage' => $input['dosage'][$key],
                                    'day' => $input['day'][$key],
                                    'time' => $input['time'][$key],
                                    'dose_interval' => $input['dose_interval'][$key],
                                    'instruction' => $input['instruction'][$key],
                                ];
                                OpdPrescriptionItem::create($opdPrescriptionItem);

                                $medicine = Medicine::find($input['medicine_id'][$key]);
                                $amount += $input['day'][$key] * $input['dose_interval'][$key] * $medicine->selling_price;
                                $qty = $input['day'][$key] * $input['dose_interval'][$key];
                                $saleMedicineArray = [
                                    'medicine_bill_id' => $medicineBill->id,
                                    'medicine_id' => $medicine->id,
                                    'sale_quantity' => $qty,
                                    'sale_price' => $medicine->selling_price,
                                    'expiry_date' => date('Y-m-d h:i', 0000 - 00 - 00),
                                    'amount' => $amount,
                                    'tax' => 0,
                                ];
                                SaleMedicine::create($saleMedicineArray);
                            }
                            $medicineBill->update([
                                'net_amount' => $amount,
                                'total' => $amount,
                            ]);
                        } else {
                            Notification::make()
                                ->danger()
                                ->title(function (Exception $e) {
                                    return $e->getMessage();
                                })
                                ->send();
                        }
                    })
                    ->form($this->getEditFormFields())
                    ->successNotificationTitle(__('messages.flash.IPD_Prescription_updated')),
                Actions\DeleteAction::make()
                    ->iconButton()
                    ->using(function (Model $record) {
                        try {
                            if (! canAccessRecord($record, $record->id)) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('messages.flash.ipd_prescription_not_found'))
                                    ->send();
                            }
                            $record->opdPrescriptionItems()->delete();
                            $record->delete();
                        } catch (Exception $e) {
                            Notification::make()
                                ->danger()
                                ->title($e->getMessage())
                                ->send();
                        }
                    })
                    ->successNotificationTitle(__('messages.flash.IPD_prescription_deleted')),
            ])
            ->filters([
                //
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ])
            ->emptyStateHeading(__('messages.common.no_data_found'))
            ->emptyStateDescription('');
    }

    public function render()
    {
        return view('livewire.opd-patient-prescription-table');
    }
}
