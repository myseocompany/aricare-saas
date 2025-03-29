<?php

namespace App\Filament\HospitalAdmin\Clusters\IpdOpd\Resources\IpdPatientResource\Pages;

use Carbon\Carbon;
use App\Models\User;
use Filament\Actions;
use App\Models\Patient;
use Filament\Infolists\Infolist;
use App\Livewire\IpdPatientBillTable;
use App\Livewire\IpdPatientChargeTable;
use Filament\Infolists\Components\Tabs;
use App\Livewire\IpdPatientPaymentTable;
use Filament\Infolists\Components\Group;
use Filament\Resources\Pages\ViewRecord;
use App\Livewire\IpdPatientTimeLineTable;
use App\Livewire\IpdPatientDiagnosisTable;
use Filament\Infolists\Components\Section;
use App\Livewire\IpdPatientBillChargeTable;
use Filament\Infolists\Components\Livewire;
use App\Livewire\IpdPatientBillPaymentTable;
use App\Livewire\IpdPatientBillSummaryTable;
use Filament\Infolists\Components\TextEntry;
use App\Livewire\IpdPatientPrescriptionTable;
use App\Livewire\IpdPatientConsultantInstructionTable;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use App\Filament\HospitalAdmin\Clusters\Patients\Resources\PatientResource;
use App\Filament\HospitalAdmin\Clusters\IpdOpd\Resources\IpdPatientResource;
use App\Filament\HospitalAdmin\Clusters\Patients\Resources\PatientResource\Pages\ListPatients;
use Filament\Forms\Components\TextInput;

class ViewIpdPatient extends ViewRecord
{
    protected static string $resource = IpdPatientResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('back')
                ->label(__('messages.common.back'))
                ->outlined()
                ->url(url()->previous()),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()->schema([
                    SpatieMediaLibraryImageEntry::make('patient.user.profile')->collection(User::COLLECTION_PROFILE_PICTURES)->label("")->columnSpan(2)->width(100)->height(100)
                        ->defaultImageUrl(function ($record) {
                            if (!$record->patient->user->hasMedia(User::COLLECTION_PROFILE_PICTURES)) {
                                return getUserImageInitial($record->id, $record->patient->user->first_name);
                            }
                        })->circular()->columnSpan(1),
                    Group::make([
                        TextEntry::make('ipd_number')
                            ->label('')
                            ->badge()
                            ->formatStateUsing(fn($state) => '#' . $state)
                            ->color('warning')
                            ->columnSpan(1),
                        TextEntry::make('patient.user.full_name')
                            ->label('')
                            ->extraAttributes(['class' => 'font-black'])
                            ->color('primary')
                            ->columnSpan(1),
                        TextEntry::make('patient.user.email')
                            ->label('')
                            ->icon('fas-envelope')
                            // ->extraAttributes(['style' => 'margin: -20px;'])
                            ->formatStateUsing(fn($state) => "<a href='mailto:{$state}'>{$state}</a>")
                            ->html()
                            ->columnSpan(1),
                    ])->extraAttributes(['class' => 'display-block']),
                    Group::make([]),
                    Group::make([]),
                    TextEntry::make('id')
                        ->label('')
                        ->formatStateUsing(fn($record) => "<span class='text-2xl font-bold text-primary-600'>" . (isset($record->patient->cases) && ($record->patient->cases) ? $record->patient->cases->count() : '0') . "</span> <br> " . __('messages.patient.total_cases'))
                        ->html()->extraAttributes(['class' => 'border p-6 rounded-xl'])
                        ->columnSpan(2),
                    TextEntry::make('id')
                        ->label('')
                        ->formatStateUsing(fn($record) => "<span class='text-2xl font-bold text-primary-600'>" . (isset($record->patient->admissions) && $record->patient->admissions ? $record->patient->admissions->count() : '0')  . "</span> <br> " . __('messages.patient.total_admissions'))
                        ->html()->extraAttributes(['class' => 'border p-6 rounded-xl'])->columnSpan(2),
                    TextEntry::make('id')
                        ->label('')
                        ->formatStateUsing(fn($record) => "<span class='text-2xl font-bold text-primary-600'>" . (isset($record->patient->appointments) && $record->patient->appointments ? $record->patient->appointments->count() : '0')  . "</span> <br> " . "<span>" . __('messages.patient.total_appointments') . "</span>")
                        ->html()->extraAttributes(['class' => 'border p-6 rounded-xl'])
                        ->columnSpan(2),
                ])->columns(10),
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make(__('messages.overview'))
                            ->schema([
                                TextEntry::make('patientCase.case_id')
                                    ->default(__('messages.common.n/a'))
                                    ->badge()
                                    ->color('info')
                                    ->label(__('messages.case.case_id') . ':'),
                                TextEntry::make('height')
                                    ->formatStateUsing(fn($record) => ($record->height == 0 ? __('messages.common.n/a') : $record->height))
                                    ->default(__('messages.common.n/a'))
                                    ->label(__('messages.ipd_patient.height') . ':'),
                                TextEntry::make('weight')
                                    ->formatStateUsing(fn($record) => ($record->weight == 0 ? __('messages.common.n/a') : $record->weight))
                                    ->default(__('messages.common.n/a'))
                                    ->label(__('messages.ipd_patient.weight') . ':'),
                                TextEntry::make('bp')
                                    ->formatStateUsing(fn($record) => ($record->bp == 0 ? __('messages.common.n/a') : $record->bp))
                                    ->default(__('messages.common.n/a'))
                                    ->label(__('messages.ipd_patient.bp') . ':'),
                                TextEntry::make('admission_date')
                                    ->default(__('messages.common.n/a'))
                                    ->since()
                                    ->formatStateUsing(fn($record) => date('jS M, Y h:i A', strtotime($record->admission_date)))
                                    ->label(__('messages.ipd_patient.admission_date') . ':'),
                                TextEntry::make('doctor.doctorUser.full_name')
                                    ->default(__('messages.common.n/a'))
                                    ->label(__('messages.ipd_patient.doctor_id') . ':'),
                                TextEntry::make('bedType.title')
                                    ->default(__('messages.common.n/a'))
                                    ->label(__('messages.ipd_patient.bed_type_id') . ':'),
                                TextEntry::make('bed.name')
                                    ->default(__('messages.common.n/a'))
                                    ->label(__('messages.ipd_patient.bed_id') . ':'),
                                TextEntry::make('is_old_patient')
                                    ->default(__('messages.common.n/a'))
                                    ->formatStateUsing(fn($record) => ($record->is_old_patient == 1 ? __('messages.common.yes') : __('messages.common.no')))
                                    ->label(__('messages.ipd_patient.is_old_patient') . ':'),
                                TextEntry::make('created_at')
                                    ->default(__('messages.common.n/a'))
                                    ->since()
                                    ->label(__('messages.common.created_at') . ':'),
                                TextEntry::make('updated_at')
                                    ->default(__('messages.common.n/a'))
                                    ->since()
                                    ->label(__('messages.common.last_updated') . ':'),
                                TextEntry::make('symptoms')
                                    ->default(__('messages.common.n/a'))
                                    ->formatStateUsing(fn($record) => !empty($record->symptoms) ? nl2br(e($record->symptoms)) : __('messages.common.n/a'))
                                    ->label(__('messages.ipd_patient.symptoms') . ':'),
                                TextEntry::make('notes')
                                    ->default(__('messages.common.n/a'))
                                    ->formatStateUsing(fn($record) => !empty($record->notes) ? nl2br(e($record->notes)) : __('messages.common.n/a'))
                                    ->label(__('messages.ipd_patient.notes') . ':'),
                            ])->columns(2),
                        Tabs\Tab::make(__('messages.patient_diagnosis_test.diagnosis'))
                            ->schema([
                                Livewire::make(IpdPatientDiagnosisTable::class)
                            ]),
                        Tabs\Tab::make(__('messages.ipd_consultant_register'))
                            ->schema([
                                Livewire::make(IpdPatientConsultantInstructionTable::class)
                            ]),
                        Tabs\Tab::make(__('messages.charges'))
                            ->schema([
                                Livewire::make(IpdPatientChargeTable::class)
                            ]),
                        Tabs\Tab::make(__('messages.prescriptions'))
                            ->schema([
                                Livewire::make(IpdPatientPrescriptionTable::class)
                            ]),
                        Tabs\Tab::make(__('messages.ipd_timelines'))
                            ->schema([
                                Livewire::make(IpdPatientTimeLineTable::class)
                            ]),
                        Tabs\Tab::make(__('messages.payments'))
                            ->schema([
                                Livewire::make(IpdPatientPaymentTable::class)
                            ]),
                        Tabs\Tab::make(__('messages.bills'))
                            ->schema([
                                Livewire::make(IpdPatientBillChargeTable::class),
                                Livewire::make(IpdPatientBillPaymentTable::class),
                                Group::make([]),
                                Livewire::make(IpdPatientBillSummaryTable::class)
                            ])->columns(2),
                    ])
                    ->activeTab(1)
                    ->columnSpanFull(),
            ]);
    }
}
