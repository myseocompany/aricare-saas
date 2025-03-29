<?php

namespace App\Livewire;

use App\Models\IpdBill;
use Livewire\Component;
use App\Models\IpdPayment;
use Filament\Tables\Table;
use App\Models\IpdTimeline;
use Illuminate\Support\Arr;
use Filament\Tables\Actions;
use Filament\Actions\StaticAction;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\Facades\Route;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use App\Http\Controllers\IpdPaymentController;
use App\Models\IpdPatientDepartment;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Closure;
use Filament\Notifications\Notification;

class IpdPatientPaymentTable extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public $record;
    public $id;

    public function mount()
    {
        $this->id = Route::current()->parameter('record');
    }

    public function GetRecord()
    {
        $IpdPayment = IpdPayment::whereIpdPatientDepartmentId($this->id)->orderBy('id', 'desc');
        return $IpdPayment;
    }

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                Actions\Action::make('Create')
                    ->modalWidth('md')
                    ->hidden(function () {
                        $bill = IpdBill::where('ipd_patient_department_id', $this->id)->first();
                        $ipdptient = IpdPatientDepartment::find($this->id);
                        if ($bill) {
                            return $bill->net_payable_amount > 0 ? false : true;
                        }
                        if ($ipdptient->bill_status == 1) {
                            return true;
                        }
                        return false;
                    })
                    ->form([
                        Group::make([
                            Hidden::make('ipd_patient_department_id')->default($this->id),
                            TextInput::make('amount')
                                ->label(__('messages.ambulance_call.amount') . ':')
                                ->required()
                                ->rules([
                                    fn(): Closure => function (string $attribute, $value, Closure $fail) {
                                        $maxAmt = ipdPatientPaymentRule($this->id, 'create');
                                        if ($value > $maxAmt) {
                                            $fail('The :attribute must be less than ' . $maxAmt . '.');
                                        }
                                    },
                                ])
                                ->numeric()
                                ->minValue(1)
                                ->postfix(getCurrencySymbol()),
                            DatePicker::make('date')
                                ->label(__('messages.ipd_patient_timeline.date') . ':')
                                ->native(false)
                                ->required(),
                            Select::make('payment_mode')
                                ->searchable()
                                ->label(__('messages.ipd_payments.payment_mode') . ':')
                                ->native(false)
                                ->required()
                                ->validationAttribute(__('messages.ipd_payments.payment_mode'))
                                ->options(getIpdPaymentTypes()),
                            SpatieMediaLibraryFileUpload::make('document')
                                ->label(__('messages.ipd_patient_diagnosis.document') . ':')
                                ->collection(IpdPayment::IPD_PAYMENT_PATH)
                                ->disk(config('app.media_disk')),
                            Textarea::make('notes')
                                ->label(__('messages.ipd_patient.notes') . ':')
                                ->maxLength(255),
                        ])
                    ])

                    ->action(function ($data) {
                        $ipdPaymentController = app(IpdPaymentController::class);
                        $data['amount'] = removeCommaFromNumbers(number_format($data['amount'], 2));
                        $dataResponse = $ipdPaymentController->store($data);
                        if (is_array($dataResponse)) {
                            if (Arr::has($dataResponse, 'error') && $dataResponse['error'] != null) {
                                return Notification::make()->danger()->title($dataResponse['error'])->send();
                            } else if (Arr::has($dataResponse, 'url') && $dataResponse['url'] != null) {
                                $url = $dataResponse['url'];
                                return redirect($url);
                            } else if (Arr::has($dataResponse, 'payment_mode')) {
                                $this->js('razorPay(event' . ',' . $dataResponse['status'] . ', ' . $dataResponse['record'] . ', ' . $dataResponse['amount'] . ')');
                            } else {
                                return Notification::make()->success()->title(__('messages.flash.IPD_payment_saved'))->send();
                            }
                        } else {
                            return Notification::make()->success()->title(__('messages.flash.IPD_payment_saved'))->send();
                        }
                    })

                    ->modalHeading(__('messages.ipd_payments.add_ipd_payment'))
                    ->label(__('messages.ipd_payments.add_ipd_payment')),
            ])
            ->query($this->GetRecord())
            ->columns([
                TextColumn::make('date')
                    ->label(__('messages.ipd_patient_charges.date'))
                    ->default(__('messages.common.n/a'))
                    ->badge()
                    ->formatStateUsing(fn($state) => \Carbon\Carbon::parse($state)->translatedFormat('jS M, Y')),
                TextColumn::make('amount')
                    ->label(__('messages.ambulance_call.amount'))
                    ->default(__('messages.common.n/a'))
                    ->formatStateUsing(fn($state) => getCurrencySymbol() . $state),
                TextColumn::make('payment_mode')
                    ->label(__('messages.ipd_payments.payment_mode'))
                    ->badge()
                    ->formatStateUsing(fn($state) => getIpdPaymentTypes()[$state])
                    ->color('success'),
                TextColumn::make('ipd_payment_document_url')
                    ->label(__('messages.ipd_patient_diagnosis.document'))
                    ->html()
                    ->color('primary')
                    ->formatStateUsing(function ($state) {
                        if (!isset($state) || empty($state)) {
                            return __('messages.common.n/a');
                        }
                        return '<a href="' . $state . '"download>' . __('messages.document.download') . '</a>';
                    }),
                TextColumn::make('notes')
                    ->label(__('messages.ipd_patient.notes'))
                    ->default(__('messages.common.n/a')),
            ])
            ->actionsColumnLabel(__('messages.common.action'))
            ->paginated(false)
            ->actions([
                Actions\EditAction::make()
                    ->iconButton()
                    ->modalWidth("md")
                    ->form(
                        function ($record) {
                            $rec = $record;
                            return [
                                Group::make([
                                    Hidden::make('id'),
                                    Hidden::make('ipd_patient_department_id')->default($this->id),
                                    TextInput::make('amount')
                                        ->label(__('messages.ambulance_call.amount') . ':')
                                        ->required()
                                        ->rules([
                                            fn(): Closure => function (string $attribute, $value, Closure $fail) use ($rec) {
                                                $maxAmt = ipdPatientPaymentRule($this->id, 'edit', $rec->id);
                                                if ($value > $maxAmt) {
                                                    $fail('The :arrtribute must be less than ' . $maxAmt . '.');
                                                }
                                            },
                                        ])
                                        ->numeric()
                                        ->minValue(1)
                                        ->postfix(getCurrencySymbol())
                                        ->maxLength(255),
                                    DatePicker::make('date')
                                        ->label(__('messages.ipd_patient_timeline.date') . ':')
                                        ->native(false)
                                        ->required(),
                                    Select::make('payment_mode')
                                        ->label(__('messages.ipd_payments.payment_mode') . ':')
                                        ->native(false)
                                        ->options(getIpdPaymentTypes()),
                                    SpatieMediaLibraryFileUpload::make('document')
                                        ->label(__('messages.ipd_patient_diagnosis.document') . ':')
                                        ->collection(IpdPayment::IPD_PAYMENT_PATH)
                                        ->disk(config('app.media_disk')),
                                    Textarea::make('notes')
                                        ->label(__('messages.ipd_patient.notes') . ':')
                                        ->maxLength(255),
                                ])
                            ];
                        }
                    )
                    ->successNotificationTitle(__('messages.flash.IPD_payment_updated')),
                Actions\DeleteAction::make()
                    ->iconButton()
                    ->successNotificationTitle(__('messages.flash.IPD_payment_deleted')),
            ])
            ->filters([
                //
            ])
            ->bulkActions([
                //
            ])
            ->emptyStateHeading(__('messages.common.no_data_found'))
            ->emptyStateDescription('');
    }

    public function render()
    {
        return view('livewire.ipd-patient-payment-table');
    }
}
