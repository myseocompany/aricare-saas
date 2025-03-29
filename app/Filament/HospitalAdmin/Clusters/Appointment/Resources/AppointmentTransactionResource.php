<?php

namespace App\Filament\HospitalAdmin\Clusters\Appointment\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Doctor;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\AppointmentTransaction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Pages\SubNavigationPosition;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\HospitalAdmin\Clusters\Appointment;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use App\Filament\HospitalAdmin\Clusters\Doctors\Resources\DoctorResource;
use App\Filament\HospitalAdmin\Clusters\Patients\Resources\PatientResource;
use App\Filament\HospitalAdmin\Clusters\Appointment\Resources\AppointmentTransactionResource\Pages;
use App\Filament\HospitalAdmin\Clusters\Appointment\Resources\AppointmentTransactionResource\RelationManagers;
use Illuminate\Database\Eloquent\Model;

class AppointmentTransactionResource extends Resource
{
    protected static ?string $model = AppointmentTransaction::class;

    protected static ?string $cluster = Appointment::class;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?int $navigationSort = 2;

    public static function getLabel(): string
    {
        return __('messages.common.appointment_transaction');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }
    public static function canViewAny(): bool
    {
        if (auth()->user()->hasRole(['Admin']) && !getModuleAccess('Appointments')) {
            return false;
        } elseif (auth()->user()->hasRole(['Admin', 'Doctor', 'Receptionist', 'Patient'])) {
            return true;
        }
        return false;
    }

    public static function table(Table $table): Table
    {
        $table = $table->modifyQueryUsing(function ($query) {
            $query->where('tenant_id', auth()->user()->tenant_id)->where('id', '!=', auth()->user()->id);
            if (! getLoggedinDoctor()) {
                if (getLoggedinPatient()) {
                    $patientId = auth()->user()->patient->id;
                    $query->whereHas('appointment', function ($q) use ($patientId) {
                        $q->where('patient_id', $patientId);
                    });
                }
            } else {
                $doctorId = getLoggedInUser()->owner_id;
                $query->whereHas('appointment', function ($q) use ($doctorId) {
                    $q->where('doctor_id', $doctorId);
                });
            }
        });

        return $table
            ->paginated([10,25,50])
            ->defaultSort('id', 'desc')
            ->columns([
                SpatieMediaLibraryImageColumn::make('appointment.patient.patientUser.profile')
                    ->label(__('messages.role.patient'))
                    ->circular()
                    ->defaultImageUrl(function ($record) {
                        if (!$record->appointment->patient->user->hasMedia(User::COLLECTION_PROFILE_PICTURES)) {
                            return getUserImageInitial($record->id, $record->appointment->patient->user->full_name);
                        }
                    })
                    ->sortable(['first_name'])
                    ->url(fn($record) => PatientResource::getUrl('view', ['record' => $record->appointment->patient->id]))
                    ->collection('profile')
                    ->width(50)->height(50),
                TextColumn::make('appointment.patient.patientUser.full_name')
                    ->label('')
                    ->description(fn($record) => $record->appointment->patient->patientUser->email ?? __('messages.common.n/a'))
                    ->color('primary')
                    ->weight(FontWeight::SemiBold)
                    ->formatStateUsing(fn($record) => '<a href="' . PatientResource::getUrl('view', ['record' => $record->appointment->patient->id]) . '" class="hoverLink">' . $record->appointment->patient->patientUser->full_name . '</a>')
                    ->html()
                    ->searchable(['users.first_name', 'users.last_name']),
                SpatieMediaLibraryImageColumn::make('appointment.doctor.doctorUser.profile')
                    ->label(__('messages.role.doctor'))
                    ->circular()
                    ->defaultImageUrl(function ($record) {
                        if (!$record->appointment->doctor->user->hasMedia(User::COLLECTION_PROFILE_PICTURES)) {
                            return getUserImageInitial($record->id, $record->appointment->doctor->user->full_name);
                        }
                    })
                    ->sortable(['first_name'])
                    ->url(fn($record) => DoctorResource::getUrl('view', ['record' => $record->appointment->doctor->id]))
                    ->collection('profile')
                    ->width(50)->height(50),
                TextColumn::make('appointment.doctor.doctorUser.full_name')
                    ->label('')
                    ->color('primary')
                    ->weight(FontWeight::SemiBold)
                    ->description(fn($record) => $record->appointment->doctor->doctorUser->email ?? __('messages.common.n/a'))
                    ->formatStateUsing(fn($record) => '<a href="' . DoctorResource::getUrl('view', ['record' => $record->appointment->doctor->id]) . '" class="hoverLink">' . $record->appointment->doctor->doctorUser->full_name . '</a>')
                    ->html()
                    ->searchable(['users.first_name', 'users.last_name']),
                TextColumn::make('appointment.opd_date')
                    ->label(__('messages.opd_patient.appointment_date'))
                    ->sortable()
                    ->badge()
                    ->getStateUsing(function ($record) {
                        $time = \Carbon\Carbon::parse($record->appointment->opd_date)->isoFormat('LT');
                        $date = \Carbon\Carbon::parse($record->appointment->opd_date)->translatedFormat('jS M, Y');
                        return "<div class='text-center'><span>{$time}</span><br><span>{$date}</span></div>";
                    })
                    ->html()
                    ->searchable(),
                TextColumn::make('appointment.payment_type')
                    ->label(__('messages.purchase_medicine.payment_mode'))
                    ->badge()
                    ->getStateUsing(function ($record) {
                        if ($record->appointment->payment_type == \App\Models\Appointment::TYPE_STRIPE) {
                            return __('messages.setting.stripe');
                        } elseif ($record->appointment->payment_type == \App\Models\Appointment::TYPE_RAZORPAY) {
                            return __('messages.setting.razorpay');
                        } elseif ($record->appointment->payment_type == \App\Models\Appointment::TYPE_PAYPAL) {
                            return __('messages.setting.paypal');
                        } elseif ($record->appointment->payment_type == \App\Models\Appointment::TYPE_CASH) {
                            return __('messages.transaction_filter.cash');
                        } elseif ($record->appointment->payment_type == \App\Models\Appointment::FLUTTERWAVE) {
                            return __('messages.flutterwave.flutterwave');
                        } elseif ($record->appointment->payment_type == \App\Models\Appointment::CHEQUE) {
                            return __('messages.cheque');
                        } elseif ($record->appointment->payment_type == \App\Models\Appointment::PAYSTACK) {
                            return __('messages.setting.paystack');
                        } elseif ($record->appointment->payment_type == \App\Models\Appointment::PHONEPE) {
                            return __('messages.phonepe.phonepe');
                        } else {
                            return __('messages.common.n/a');
                        }
                    })
                    ->color(function ($record) {
                        if ($record->appointment->payment_type == \App\Models\Appointment::TYPE_STRIPE) {
                            return 'primary';
                        } elseif ($record->appointment->payment_type == \App\Models\Appointment::TYPE_RAZORPAY) {
                            return 'success';
                        } elseif ($record->appointment->payment_type == \App\Models\Appointment::TYPE_PAYPAL) {
                            return 'primary';
                        } elseif ($record->appointment->payment_type == \App\Models\Appointment::TYPE_CASH) {
                            return 'info';
                        } elseif ($record->appointment->payment_type == \App\Models\Appointment::FLUTTERWAVE) {
                            return 'info';
                        } elseif ($record->appointment->payment_type == \App\Models\Appointment::CHEQUE) {
                            return 'warning';
                        } elseif ($record->appointment->payment_type == \App\Models\Appointment::PAYSTACK) {
                            return 'warning';
                        } elseif ($record->appointment->payment_type == \App\Models\Appointment::PHONEPE) {
                            return 'success';
                        } else {
                            return 'primary';
                        }
                    })
                    ->sortable()
                    ->searchable(),
                TextColumn::make('appointment.doctor.appointment_charge')
                    ->label(__('messages.ambulance_call.amount'))
                    ->sortable()
                    ->formatStateUsing(fn($record) => getCurrencyFormat($record->appointment->doctor->appointment_charge))
                    ->searchable()
                    ->alignRight(),
                TextColumn::make('created_at')
                    ->label(__('messages.common.created_at'))
                    ->sortable()
                    ->badge()
                    ->getStateUsing(function ($record) {
                        return  \Carbon\Carbon::parse($record->created_at)->translatedFormat('jS M, Y');
                    })
            ])
            ->filters([
                //
            ])
            // ->recordAction(null)
            ->recordUrl(null)
            ->actions([
                // Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListAppointmentTransactions::route('/'),
            'create' => Pages\CreateAppointmentTransaction::route('/create'),
            'edit' => Pages\EditAppointmentTransaction::route('/{record}/edit'),
        ];
    }
}
