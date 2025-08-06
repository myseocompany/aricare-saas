<?php

namespace App\Filament\HospitalAdmin\Clusters\DoctorsCluster\Resources;

use App\Filament\HospitalAdmin\Clusters\DoctorsCluster;

use App\Filament\HospitalAdmin\Clusters\DoctorsCluster\Resources\DoctorResource\Pages;
use App\Filament\HospitalAdmin\Clusters\DoctorsCluster\Resources\DoctorResource\RelationManagers;
use App\Filament\HospitalAdmin\Clusters\DoctorsCluster\Resources\DoctorResource\Form\DoctorMinimalForm;


use Carbon\Carbon;
use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Doctor;
use App\Models\Schedule;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Appointment;
use App\Models\BirthReport;
use App\Models\DeathReport;
use App\Models\PatientCase;
use App\Models\Prescription;
use Faker\Provider\ar_EG\Text;
use App\Models\EmployeePayroll;
use App\Models\OperationReport;
use App\Models\DoctorDepartment;
use App\Models\PatientAdmission;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use App\Models\InvestigationReport;
use Filament\Tables\Filters\Filter;
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
use Filament\Infolists\Components\Tabs;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use App\Livewire\DoctorCaseRelationTable;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\SubNavigationPosition;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Livewire;
use App\Livewire\DoctorPatientRelationTable;
use App\Livewire\DoctorPayrollRelationTable;
use Filament\Infolists\Components\TextEntry;
use App\Livewire\DoctorScheduleRelationTable;
use App\Filament\HospitalAdmin\Clusters\Doctors;
use App\Livewire\AccountantPayrollRelationTable;
use App\Livewire\DoctorAppointmentRelationTable;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Components\Group as InfolistGroup;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;

class DoctorResource extends Resource
{
    protected static ?string $model = Doctor::class;

    //protected static ?string $navigationIcon = 'fas-user-doctor'; // opcional, para ícono


    protected static ?string $cluster = DoctorsCluster::class;
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    
    public static function form(Form $form): Form
    {
        if ($form->getOperation() === 'edit') {
            $doctorData = $form->model;
            $form->model = User::find($doctorData->user_id);
        }

        return $form->schema([
            ...DoctorMinimalForm::schema(),
        ]);
    }

    public static function table(Table $table): Table
    {
        /*
        if (auth()->user()->hasRole(['Admin']) && !getModuleAccess('Doctors')) {
            abort(404);
        }
            */

        return
            $table = $table->modifyQueryUsing(function (Builder $query) {
                $query->whereTenantId(auth()->user()->tenant_id);
                return $query;
            })
            ->paginated([10,25,50])
            ->defaultSort('id', 'desc')
            ->columns([
                SpatieMediaLibraryImageColumn::make('user.profile')->collection(User::COLLECTION_PROFILE_PICTURES)->rounded()->label(__('messages.case.doctor'))->width(50)->height(50)
                    ->sortable(['first_name'])
                    ->defaultImageUrl(function ($record) {
                        if (!$record->hasMedia(User::COLLECTION_PROFILE_PICTURES)) {
                            return getUserImageInitial($record->id, $record->user->first_name);
                        }
                    }),
                TextColumn::make('user.full_name')
                    ->label('')
                    ->color('primary')
                    ->weight(FontWeight::SemiBold)
                    ->formatStateUsing(function ($state, $record) {
                        return '<span>' . $record->user->full_name . '</span>';
                    })
                    ->html()
                    ->description(function ($record) {
                        return $record->user->email;
                    })
                    ->searchable(['first_name', 'last_name', 'email']),
                    TextColumn::make('user.rips_identification_type_id')
                        ->label('Tipo de documento')
                        ->formatStateUsing(fn($state) =>
                            \App\Models\Rips\RipsIdentificationType::find($state)->name ?? 'N/A'
                        )
                        ->sortable()
                        ->searchable(),

                    TextColumn::make('user.rips_identification_number')
                        ->label('Número de documento')
                        ->sortable()
                        ->searchable(),
                    /*
                    Tables\Columns\TextColumn::make('specialist')
                        ->label(__('messages.doctor.specialist'))
                        ->searchable()
                        ->sortable(),
                    TextColumn::make('user.qualification')
                        ->label(__('messages.user.qualification'))
                        ->sortable()
                        ->hidden(function () {
                            if (auth()->user()->hasRole(['Admin'])) {
                                return false;
                            }
                            return true;
                        })
                        ->default(__('messages.common.n/a')),
                        */
                // TextColumn::make('user.status')
                // ->hidden(function (){
                //     if(auth()->user()->hasRole(['Doctor']))
                //     {
                //         return false;
                //     }
                //     return true;
                // })
                //     ->formatStateUsing(function ($record) {
                //         return $record->user->status == 1 ? __('messages.common.active') : __('messages.common.deactive');
                //     })
                //     ->badge()
                //     ->color(function ($record) {
                //         return $record->user->status == 1 ? 'success' : 'danger';
                //     })
                //     ->label(__('messages.user.status'))
                //     ->sortable(),
                TextColumn::make('user.status')
                    ->label(__('messages.user.status'))
                    ->formatStateUsing(function ($record) {
                        return $record->user->status == 1 ? __('messages.common.active') : __('messages.common.deactive');
                    })
                    ->badge()
                    ->color(function ($record) {
                        return $record->user->status == 1 ? 'success' : 'danger';
                    })
                // ToggleColumn::make('user.status')
                // ->hidden(function (){
                //     if(auth()->user()->hasRole(['Admin']))
                //     {
                //         return false;
                //     }
                //     return true;
                // })
                //     ->label(__('messages.user.status'))
                //     ->updateStateUsing(function ($record, bool $state) {
                //         $user = User::find($record->user_id);
                //         $state ? $user->status = 1 : $user->status = 0;
                //         $user->save();
                //         Notification::make()
                //             ->title(__('messages.common.status_updated_successfully'))
                //             ->success()
                //             ->send();
                //     }),
            ])
            ->filters([
                // SelectFilter::make('status')
                //     ->relationship('user', 'status')
                //     ->label(__('messages.common.status'))
                //     ->options([
                //         '' => __('messages.filter.all'),
                //         1 => __('messages.common.active'),
                //         0 => __('messages.common.deactive'),
                //     ])->native(false),
                Filter::make('status')
                    ->hidden(function () {
                        if (auth()->user()->hasRole(['Admin', 'Receptionist'])) {
                            return false;
                        }
                        return true;
                    })
                    ->form([
                        Select::make(__('messages.user.status'))
                            ->options([
                                'all' => __('messages.filter.all'),
                                1 => __('messages.filter.active'),
                                0 => __('messages.filter.deactive'),
                            ])->default('all')->native(false)
                            ->label(__('messages.user.status') . ':'),
                    ])->query(function (Builder $query, array $data) {
                        if ($data[__('messages.common.status')] == 'All') {
                            $query->with('user');
                        }
                        if ($data[__('messages.common.status')] == 1) {
                            $query->with('user')->whereHas('user', fn(Builder $query) => $query->where('status', 1));
                        } elseif ($data[__('messages.common.status')] == 0) {
                            $query->with('user')->whereHas('user', fn(Builder $query) => $query->where('status', 0));
                        }
                    }),
            ])
            ->actions([

                Tables\Actions\ViewAction::make()->color('info')->iconButton()->extraAttributes(['class' => 'hidden'])->action(function ($data, $record) {
                    if (!canAccessRecord($record, $record->id)) {
                        return Notification::make()
                            ->title(__('messages.flash.not_allow_access_record'))
                            ->danger()
                            ->send();
                    }
                }),

                Tables\Actions\EditAction::make()->iconButton()->successNotificationTitle(__('messages.flash.doctor_update')),
                Tables\Actions\DeleteAction::make()->iconButton()
                    ->successNotificationTitle(__('messages.flash.doctor_delete'))
                    ->action(function ($data, $record) {
                        $doctor = Doctor::find($record->id);
                        if (!canAccessRecord(Doctor::class, $doctor->id)) {
                            return Notification::make()
                                ->title(__('messages.flash.nurse_cant_deleted'))
                                ->danger()
                                ->send();
                        }

                        if (getLoggedInUser()->is_default == 1) {
                            return Notification::make()
                                ->title(__('messages.common.this_action_is_not_allowed_for_default_record'))
                                ->danger()
                                ->send();
                        }

                        $doctorModels = [
                            PatientCase::class,
                            PatientAdmission::class,
                            Schedule::class,
                            Appointment::class,
                            BirthReport::class,
                            DeathReport::class,
                            InvestigationReport::class,
                            OperationReport::class,
                            Prescription::class,
                            IpdPatientDepartment::class,
                        ];

                        $result = canDelete($doctorModels, 'doctor_id', $doctor->id);

                        $empPayRollResult = canDeletePayroll(
                            EmployeePayroll::class,
                            'owner_id',
                            $doctor->id,
                            $doctor->user->owner_type
                        );

                        if ($result || $empPayRollResult) {
                            return Notification::make()
                                ->title(__('messages.flash.doctor_cant_deleted'))
                                ->danger()
                                ->send();
                        }

                        $doctor->user()->delete();
                        $doctor->address()->delete();
                        $doctor->delete();

                        return Notification::make()
                            ->title(__('messages.flash.doctor_delete'))
                            ->success()
                            ->send();
                    }),
            ])
            ->actionsColumnLabel((auth()->user()->hasRole(['Admin', 'Receptionist'])) ? __('messages.common.action') : '')
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ])
            ->emptyStateHeading(__('messages.common.no_data_found'));
    }

    public static function getDoctorAssociatedData(int $doctorId)
    {
        $data['doctorData'] = Doctor::with([
            'cases.patient.patientUser',
            'patients.patientUser',
            'schedules',
            'payrolls',
            'doctorUser',
            'address',
            'appointments.doctor.doctorUser',
            'appointments.patient.patientUser',
            'appointments.department',
            'ripsPatients.patientUser',
        ])->findOrFail($doctorId);
        $data['appointments'] = $data['doctorData']->appointments;

        return $data;
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
            'index' => Pages\ListDoctors::route('/'),
            'create' => Pages\CreateDoctor::route('/create'),
            'edit' => Pages\EditDoctor::route('/{record}/edit'),
            'view' => Pages\ViewDoctor::route('/{record}'),
        ];
    }
}
