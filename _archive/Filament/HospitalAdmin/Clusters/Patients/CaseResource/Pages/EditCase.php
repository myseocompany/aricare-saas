<?php

namespace App\Filament\HospitalAdmin\Clusters\Patients\Resources\CaseResource\Pages;

use Carbon\Carbon;
use Filament\Actions;
use App\Models\Patient;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Repositories\PatientCaseRepository;
use App\Filament\HospitalAdmin\Clusters\Patients\Resources\CaseResource;

class EditCase extends EditRecord
{
    protected static string $resource = CaseResource::class;


    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label(__('messages.common.back'))
                ->url(static::getResource()::getUrl('index')),
        ];
    }
    protected function getSavedNotificationTitle(): ?string
    {
        return __('messages.flash.case_updated');
    }

    protected function handleRecordUpdate(Model $record, array $input): Model
    {
        $patientId = Patient::with('patientUser')->whereId($input['patient_id'])->first();
        $birthDate = $patientId->patientUser->dob;
        $caseDate = Carbon::parse($input['date'])->toDateString();
        if (! empty($birthDate) && $caseDate < $birthDate) {
            Notification::make()
                ->title(__('messages.flash.case_date_smaller'))
                ->danger()
                ->send();

            return redirect()->back()->withInput($input);
        }
        $input['fee'] = removeCommaFromNumbers($input['fee']);
        // $input['status'] = isset($input['status']) ? 1 : 0;

        $patientCase = app(PatientCaseRepository::class)->update($input, $record->id);

        $record = new ($this->getModel())($input);

        return $record;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
