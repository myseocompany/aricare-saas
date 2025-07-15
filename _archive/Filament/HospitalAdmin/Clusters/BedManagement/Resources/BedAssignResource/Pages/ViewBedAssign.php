<?php

namespace App\Filament\HospitalAdmin\Clusters\BedManagement\Resources\BedAssignResource\Pages;

use Filament\Actions;
use App\Models\PatientCase;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\TextEntry;
use Ysfkaya\FilamentPhoneInput\Infolists\PhoneEntry;
use App\Filament\HospitalAdmin\Clusters\BedManagement\Resources\BedAssignResource;
use App\Models\Bed;
use App\Models\BedAssign;
use Filament\Infolists\Components\Section;

class ViewBedAssign extends ViewRecord
{
    protected static string $resource = BedAssignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('back')
                ->label(__('messages.common.back'))
                ->outlined()
                ->url(fn() => BedAssignResource::getUrl('index')),
        ];
    }

    public function mount(int | string $record): void
    {
        $case_id = BedAssign::find($record)->case_id;

        $this->record = PatientCase::with('patient', 'doctor')->where('case_id', $case_id)->first();

        $this->authorizeAccess();

        $this->hasInfolist();
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('')
                    ->schema([
                        TextEntry::make('case_id')
                            ->label(__('messages.operation_report.case_id') . ':')
                            ->prefix('#')
                            ->badge(),
                        TextEntry::make('patient.patientUser.full_name')
                            ->default(__('messages.common.n/a'))
                            ->label(__('messages.case.patient') . ':'),
                        TextEntry::make('phone')
                            ->default(__('messages.common.n/a'))
                            ->label(__('messages.case.phone') . ':'),
                        TextEntry::make('doctor.doctorUser.full_name')
                            ->default(__('messages.common.n/a'))
                            ->label(__('messages.case.doctor') . ':'),
                        TextEntry::make('date')
                            ->default(__('messages.common.n/a'))
                            ->label(__('messages.case.case_date') . ':')
                            ->getStateUsing(fn($record) => $record->created_at->translatedFormat('jS M,Y g:i A') ?? __('messages.common.n/a')),
                        TextEntry::make('fee')
                            ->default(__('messages.common.n/a'))
                            ->label(__('messages.case.fee') . ':'),
                        TextEntry::make('created_at')
                            ->default(__('messages.common.n/a'))
                            ->label(__('messages.common.created_at') . ':')
                            ->getStateUsing(fn($record) => $record->created_at->diffForHumans() ?? __('messages.common.n/a')),
                        TextEntry::make('updated_at')
                            ->default(__('messages.common.n/a'))
                            ->label(__('messages.common.last_updated') . ':')
                            ->getStateUsing(fn($record) => $record->updated_at->diffForHumans() ?? __('messages.common.n/a')),
                        TextEntry::make('status')
                            ->label(__('messages.common.status') . ':')
                            ->getStateUsing(function ($record) {
                                return $record->status == 1 ? __('messages.common.active') : __('messages.common.de_active');
                            })
                            ->color(function ($record) {
                                return $record->status == 1 ? 'success' : 'danger';
                            })
                            ->badge(),
                        TextEntry::make('description')
                            ->default(__('messages.common.n/a'))
                            ->label(__('messages.common.description') . ':'),
                    ])->columns(2),
            ]);
    }
}
