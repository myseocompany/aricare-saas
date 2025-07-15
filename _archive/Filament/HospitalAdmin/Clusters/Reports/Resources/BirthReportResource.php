<?php

namespace App\Filament\HospitalAdmin\Clusters\Reports\Resources;

use Carbon\Carbon;
use App\Models\User;
use Filament\Tables;
use App\Models\Doctor;
use App\Models\Patient;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\BirthReport;
use App\Models\PatientCase;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Pages\SubNavigationPosition;
use App\Repositories\BirthReportRepository;
use Filament\Forms\Components\DateTimePicker;
use App\Filament\HospitalAdmin\Clusters\Reports;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use App\Filament\HospitalAdmin\Clusters\Patients\Resources\CaseResource;
use App\Filament\HospitalAdmin\Clusters\Doctors\Resources\DoctorResource;
use App\Filament\HospitalAdmin\Clusters\Patients\Resources\PatientResource;
use App\Filament\HospitalAdmin\Clusters\Reports\Resources\BirthReportResource\Pages;

class BirthReportResource extends Resource
{
    protected static ?string $model = BirthReport::class;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?int $navigationSort = 1;

    protected static ?string $cluster = Reports::class;

    public static function shouldRegisterNavigation(): bool
    {
        if (auth()->user()->hasRole(['Admin'])  && !getModuleAccess('Birth Reports')) {
            return false;
        } elseif (!auth()->user()->hasRole(['Admin']) && !getModuleAccess('Birth Reports')) {
            return false;
        }
        return true;
    }

    public static function canCreate(): bool
    {
        if (auth()->user()->hasRole(['Admin', 'Doctor']) && getModuleAccess('Birth Reports')) {
            return true;
        }
        return false;
    }
    public static function canEdit(Model $record): bool
    {
        if (auth()->user()->hasRole(['Admin', 'Doctor']) && getModuleAccess('Birth Reports')) {
            return true;
        }
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        if (auth()->user()->hasRole(['Admin', 'Doctor']) && getModuleAccess('Birth Reports')) {
            return true;
        }

        return false;
    }

    public static function canViewAny(): bool
    {
        if (auth()->user()->hasRole(['Admin', 'Doctor', 'Patient'])) {
            return true;
        }
        return false;
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('case_id')
                    ->required()
                    ->label(__('messages.death_report.case_id'))
                    ->searchable()
                    ->options(function () {
                        $bedAssignRepo = app(BirthReportRepository::class);
                        return $bedAssignRepo->getCases();
                    })
                    ->native(false)
                    ->validationMessages([
                        'required' => __('messages.fields.the') . ' ' . __('messages.death_report.case_id') . ' ' . __('messages.fields.required'),
                    ]),

                Select::make('doctor_id')
                    ->label(__('messages.role.doctor'))
                    ->hidden(!auth()->user()->hasRole('Admin'))
                    ->required()
                    ->searchable()
                    ->options(function () {
                        $bedAssignRepo = app(BirthReportRepository::class);
                        return $bedAssignRepo->getDoctors();
                    })
                    ->native(false)
                    ->validationMessages([
                        'required' => __('messages.fields.the') . ' ' . __('messages.role.doctor') . ' ' . __('messages.fields.required'),
                    ]),

                DateTimePicker::make("date")
                    ->required()
                    ->validationAttribute(__('messages.death_report.date'))
                    ->label(__('messages.death_report.date'))
                    ->placeholder(__('messages.birth_report.date'))
                    ->maxDate(now())
                    ->default(Carbon::now())
                    ->native(false),

                Textarea::make("description")
                    ->label(__('messages.death_report.description'))
                    ->placeholder(__('messages.birth_report.description'))

            ])->columns(1);
    }

    // public static function infolist(Infolist $infolist): Infolist
    // {
    //     return
    // }

    public static function table(Table $table): Table
    {
        if (auth()->user()->hasRole(['Admin', 'Doctor', 'Patient']) && !getModuleAccess('Birth Reports')) {
            abort(404);
        }

        $table = $table->modifyQueryUsing(function ($query) {
            $query->where('tenant_id', Auth::user()->tenant_id);


            if (getLoggedinDoctor()) {
                $doctorId = Doctor::where('tenant_id', getLoggedInUser()->tenant_id)->where('user_id', getLoggedInUserId())->first();
                $query = $query->where('doctor_id', $doctorId->id);
                // dd($query->get());
            }

            if (getLoggedinPatient()) {
                $patientId = Patient::where('user_id', getLoggedInUserId())->first();
                $query = $query->where('patient_id', $patientId->id);
            }
            return $query;
        });
        return $table
            ->paginated([10,25,50])
            ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('case_id')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable()
                    ->url(fn($record) => BirthReportResource::getUrl('viewCase', ['record' => $record->caseFromBirthReport->id])),
                SpatieMediaLibraryImageColumn::make('patient.user.profile')
                    ->label(__('messages.invoice.patient'))
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
                    ->description(function ($record) {
                        return $record->patient->user->email;
                    })
                    ->html()
                    ->formatStateUsing(fn($state, $record) => '<a href="' . PatientResource::getUrl('view', ['record' => $record->patient->id]) . '"class="hoverLink">' . $state . '</a>')
                    ->color('primary')
                    ->weight(FontWeight::SemiBold)
                    ->searchable(['first_name', 'last_name', 'email']),

                SpatieMediaLibraryImageColumn::make('doctor.doctorUser.profile')
                    ->label(__('messages.case.doctor'))
                    ->circular()
                    ->sortable(['first_name'])
                    ->defaultImageUrl(function ($record) {
                        if (!$record->doctor->user->hasMedia(User::COLLECTION_PROFILE_PICTURES)) {
                            return getUserImageInitial($record->id, $record->doctor->user->full_name);
                        }
                    })
                    ->url(fn($record) => DoctorResource::getUrl('view', ['record' => $record->doctor->id]))
                    ->collection('profile')
                    ->width(50)->height(50),
                TextColumn::make('doctor.doctorUser.full_name')
                    ->label('')
                    ->description(fn($record) => $record->doctor->doctorUser->email ?? 'N/A')
                    ->html()
                    ->formatStateUsing(fn($state, $record) => '<a href="' . DoctorResource::getUrl('view', ['record' => $record->doctor->id]) . '"class="hoverLink">' . $state . '</a>')
                    ->color('primary')
                    ->weight(FontWeight::SemiBold)
                    ->searchable(['users.first_name', 'users.last_name']),

                Tables\Columns\TextColumn::make('date')
                    ->formatStateUsing(
                        fn($state) =>
                        Carbon::parse($state)->format('g:i A') . '<br>' . Carbon::parse($state)->format('jS M, Y')
                    )
                    ->badge()
                    ->html()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordAction(null)
            ->recordUrl(null)
            ->actions([
                Tables\Actions\ViewAction::make()->color('info')->iconButton(),
                Tables\Actions\EditAction::make()->iconButton()
                    ->action(function ($record, array $data) {

                        $patientId = PatientCase::select('patient_id')->whereCaseId($data['case_id'])->first();
                        $data['patient_id'] = $patientId->patient_id;

                        $birthReport = BirthReport::find($record->id);

                        $birthReport->update($data);

                        return
                            Notification::make()
                            ->title(__('messages.flash.birth_report_updated'))
                            ->success()
                            ->send();
                    }),
                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->action(function (BirthReport  $record) {
                        if (! canAccessRecord(BirthReport::class, $record->id)) {
                            return Notification::make()
                                ->danger()
                                ->title(__('messages.flash.birth_report_not_found'))
                                ->send();
                        }

                        if (getLoggedInUser()->hasRole('Doctor')) {
                            $patientCaseHasDoctor = BirthReport::whereId($record->id)->whereDoctorId(getLoggedInUser()->owner_id)->exists();
                            if (! $patientCaseHasDoctor) {
                                return Notification::make()
                                    ->danger()
                                    ->title(__('messages.flash.birth_report_not_found'))
                                    ->send();
                            }
                        }
                        $record->delete();
                        return Notification::make()
                            ->success()
                            ->title(__('messages.flash.birth_report_deleted'))
                            ->send();
                    })
                    ->successNotificationTitle(__('messages.flash.birth_report_deleted')),
            ])->actionsColumnLabel("Action")
            ->bulkActions([
                //
            ])
            ->emptyStateHeading(__('messages.common.no_data_found'));
    }

    public static function getPages(): array
    {
        return [
            'viewCase' => Pages\ViewBirthReportCase::route('/{record}/view-case'),
            'view' => Pages\ViewBirthReports::route('/{record}'),
            'index' => Pages\ManageBirthReports::route('/'),
        ];
    }
}
