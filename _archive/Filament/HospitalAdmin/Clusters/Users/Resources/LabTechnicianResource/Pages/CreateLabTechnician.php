<?php

namespace App\Filament\HospitalAdmin\Clusters\Users\Resources\LabTechnicianResource\Pages;


use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;
use App\Repositories\LabTechnicianRepository;
use App\Filament\HospitalAdmin\Clusters\Users\Resources\LabTechnicianResource;

class CreateLabTechnician extends CreateRecord
{
    protected static string $resource = LabTechnicianResource::class;
    protected static bool $canCreateAnother = false;
    protected function getActions(): array
    {
        return [
            Action::make('back')
                ->label(__('messages.common.back'))
                ->url(static::getResource()::getUrl('index')),
        ];
    }
    public function handleRecordCreation(array $input): Model
    {
        $input['region_code'] = !empty($input['phone']) ? getRegionCode($input['region_code'] ?? '') : null;
        $input['phone'] = getPhoneNumber($input['phone']);
        $input['status'] = $input['status'] ? 1 : 0;
        $labTechnician = app(LabTechnicianRepository::class)->store($input);
        return $labTechnician;
    }
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
    protected function getCreatedNotificationTitle(): ?string
    {
        return __('messages.flash.lab_technician_saved');
    }
}
