<?php

namespace App\Filament\HospitalAdmin\Clusters\Patients\Resources\PatientResource\Pages;

use Carbon\Carbon;
use App\Models\User;
use App\Enums\MaritalStatus;
use Filament\Actions;
use App\Models\Patient;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Tabs;
use Filament\Resources\Pages\ViewRecord;
use App\Livewire\PatientBillRelationTable;
use App\Livewire\PatientCasesRelationTable;
use Filament\Infolists\Components\Livewire;
use Filament\Infolists\Components\TextEntry;
use App\Livewire\PatientInvoiceRelationTable;
use App\Livewire\PatientDocumentRelationTable;
use App\Livewire\PatientAdmissionRelationTable;
use App\Livewire\PatientAppointmentRelationTable;
use App\Livewire\PatientVaccinationRelationTable;
use App\Livewire\PatientAdvancePaymentRelationTable;
use Filament\Infolists\Components\Group as InfolistGroup;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use App\Filament\HospitalAdmin\Clusters\Patients\Resources\PatientResource;
use Ysfkaya\FilamentPhoneInput\Infolists\PhoneEntry;

class ViewPatient extends ViewRecord
{
    protected static string $resource = PatientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('back')
                ->label(__('messages.common.back'))
                ->outlined()
                ->url(url()->previous()),
        ];
    }

    public  function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfolistSection::make()->schema([
                    SpatieMediaLibraryImageEntry::make('user.profile')->collection(User::COLLECTION_PROFILE_PICTURES)->label("")->columnSpan(2)->width(100)->height(100)->defaultImageUrl(function ($record) {
                        if (!$record->user->hasMedia(User::COLLECTION_PROFILE_PICTURES)) {
                            return getUserImageInitial($record->id, $record->user->full_name);
                        }
                    })->circular()->columnSpan(1),
                    InfolistGroup::make([
                        TextEntry::make('user.status')
                            ->label('')
                            ->formatStateUsing(fn($state) => $state ? __('messages.common.active') : __('messages.common.deactive'))
                            ->badge()
                            ->color(fn($state) => $state ? 'success' : 'danger')
                            ->columnSpan(1),
                        TextEntry::make('user.full_name')
                            ->label('')
                            ->extraAttributes(['class' => 'font-black'])
                            ->color('primary')
                            ->columnSpan(1),
                        TextEntry::make('email_for_display')
                            ->label('')
                            ->icon('fas-envelope')
                            ->formatStateUsing(function ($state, $record) {
                                $value = $state ?? __('messages.common.n/a');

                                if (empty($state)) {
                                    return $value;
                                }

                                if (auth()->user()->hasRole('Patient')) {
                                    return $value;
                                }

                                return "<a href='mailto:{$state}'>{$state}</a>";
                            })
                            ->html()
                            ->columnSpan(1),
                    ]),
                    TextEntry::make('user.address')
                        ->label('')
                        ->icon('fas-location-dot')
                        ->getStateUsing(function ($record) {
                            if (!empty($record->address->address1) && !empty($record->address->address2)) {
                                return $record->address->address1 . ', ' . $record->address->address2;
                            } elseif (!empty($record->address->address1)) {
                                return $record->address->address1;
                            } elseif (!empty($record->address->address2)) {
                                return $record->address->address2;
                            } else {
                                return __('messages.common.n/a');
                            }
                        })
                        ->html()->columnSpan(2),
                    TextEntry::make('id')
                        ->label('')
                        ->formatStateUsing(fn($record) => "<span class='text-2xl font-bold text-primary-600'>" . (isset($record->cases) && $record->cases ? $record->cases->count() : '0') . "</span> <br> " . __('messages.patient.total_cases'))
                        ->html()->extraAttributes(['class' => 'border p-6 rounded-xl'])
                        ->columnSpan(2),
                    TextEntry::make('id')
                        ->label('')
                        ->formatStateUsing(fn($record) => "<span class='text-2xl font-bold text-primary-600'>" . (isset($record->admissions) && $record->admissions ? $record->admissions->count() : '0')  . "</span> <br> " . __('messages.patient.total_admissions'))
                        ->html()->extraAttributes(['class' => 'border p-6 rounded-xl'])->columnSpan(2),
                    TextEntry::make('id')
                        ->label('')
                        ->formatStateUsing(fn($record) => "<span class='text-2xl font-bold text-primary-600'>" . (isset($record->appointments) && $record->appointments ? $record->appointments->count() : '0')  . "</span> <br> " . "<span>" . __('messages.patient.total_appointments') . "</span>")
                        ->html()->extraAttributes(['class' => 'border p-6 rounded-xl'])
                        ->columnSpan(2),
                ])->columns(10),
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make(__('messages.overview'))->schema([
                            /*
                            PhoneEntry::make('user.phone')
                                ->label(__('messages.user.phone') . ':')
                                ->default(__('messages.common.n/a'))
                                ->formatStateUsing(function ($state, $record) {
                                    if (str_starts_with($state, '+') && strlen($state) > 4) {
                                        return $state;
                                    }
                                    if (empty($record->user->phone)) {
                                        return __('messages.common.n/a');
                                    }
                                    return $record->user->region_code . $record->user->phone;
                                }),
                            */
                            TextEntry::make('user.gender')
                                ->label(__('messages.user.gender') . ':')
                                ->getStateUsing(fn($record) => $record->user->gender == 0 ? __('messages.user.male') : __('messages.user.female')),

                            TextEntry::make('ripsIdentificationType.name')
                                ->label('Tipo de documento RIPS:')
                                ->getStateUsing(fn($record) => optional($record->ripsIdentificationType)?->name ?? __('messages.common.n/a')),
                            TextEntry::make('document_number')
                                ->label('Número de documento:')
                                ->getStateUsing(fn($record) => $record->document_number ?? __('messages.common.n/a')),
                            TextEntry::make('ripsCountry.name')
                                ->label('País de residencia:')
                                ->getStateUsing(fn($record) => optional($record->ripsCountry)?->name ?? __('messages.common.n/a')),
                            TextEntry::make('birth_place')
                                ->label(__('messages.patient.birth_place') . ':')
                                ->getStateUsing(fn($record) => $record->birth_place ?? __('messages.common.n/a')),
                            TextEntry::make('residence_address')
                                ->label(__('messages.patient.residence_address') . ':')
                                ->getStateUsing(fn($record) => $record->residence_address ?? __('messages.common.n/a')),
                            TextEntry::make('occupation')
                                ->label(__('messages.patient.occupation') . ':')
                                ->getStateUsing(fn($record) => $record->occupation ?? __('messages.common.n/a')),
                            TextEntry::make('ethnicity_label')
                                ->label(__('messages.patient.ethnicity') . ':')
                                ->getStateUsing(fn($record) => $record->ethnicity_label ?? __('messages.common.n/a')),
                            TextEntry::make('education_level_label')
                                ->label(__('messages.patient.education_level') . ':')
                                ->getStateUsing(fn($record) => $record->education_level_label ?? __('messages.common.n/a')),
                            TextEntry::make('phone_secondary')
                                ->label(__('messages.patient.phone_secondary') . ':')
                                ->getStateUsing(fn($record) => $record->phone_secondary ?? __('messages.common.n/a')),
                            TextEntry::make('marital_status_id')
                                ->label(__('messages.patient.marital_status_label') . ':')
                                ->getStateUsing(fn($record) => optional(MaritalStatus::tryFrom($record->marital_status_id))?->label() ?? __('messages.common.n/a')),
                            TextEntry::make('responsible_name')
                                ->label(__('messages.patient.responsible_name') . ':')
                                ->getStateUsing(fn($record) => $record->responsible_name ?? __('messages.common.n/a')),
                            TextEntry::make('responsible_phone')
                                ->label(__('messages.patient.responsible_phone') . ':')
                                ->getStateUsing(fn($record) => $record->responsible_phone ?? __('messages.common.n/a')),
                            TextEntry::make('responsible_relationship')
                                ->label(__('messages.patient.responsible_relationship') . ':')
                                ->getStateUsing(fn($record) => $record->responsible_relationship ?? __('messages.common.n/a')),
                            TextEntry::make('emergency_contact_name')
                                ->label(__('messages.patient.emergency_contact_name') . ':')
                                ->getStateUsing(fn($record) => $record->emergency_contact_name ?? __('messages.common.n/a')),
                            TextEntry::make('emergency_contact_phone')
                                ->label(__('messages.patient.emergency_contact_phone') . ':')
                                ->getStateUsing(fn($record) => $record->emergency_contact_phone ?? __('messages.common.n/a')),
                            TextEntry::make('user.dob')
                                ->label(__('messages.user.dob') . ':')
                                ->getStateUsing(fn($record) => $record->user->dob ? Carbon::parse($record->user->dob)->translatedFormat('jS M, Y') : __('messages.common.n/a')),
                        ])->columns(2),
                        Tabs\Tab::make(__('messages.cases'))->schema([
                            Livewire::make(PatientCasesRelationTable::class)
                        ]),
                        /*
                        Tabs\Tab::make(__('messages.patient_admissions'))->schema([
                            Livewire::make(PatientAdmissionRelationTable::class)
                        ]),
                        Tabs\Tab::make(__('messages.appointments'))->schema([
                            Livewire::make(PatientAppointmentRelationTable::class)
                        ]),
                        Tabs\Tab::make(__('messages.bills'))->schema([
                            Livewire::make(PatientBillRelationTable::class)
                        ]),
                        Tabs\Tab::make(__('messages.invoices'))->schema([
                            Livewire::make(PatientInvoiceRelationTable::class)
                        ]),
                        Tabs\Tab::make(__('messages.advanced_payments'))->schema([
                            Livewire::make(PatientAdvancePaymentRelationTable::class)
                        ]),
                        Tabs\Tab::make(__('messages.documents'))->schema([
                            Livewire::make(PatientDocumentRelationTable::class)
                        ]),
                        Tabs\Tab::make(__('messages.vaccinations'))->schema([
                            Livewire::make(PatientVaccinationRelationTable::class)
                        ]),
                        */
                    ])->columnSpanFull(),
            ]);
    }
}
