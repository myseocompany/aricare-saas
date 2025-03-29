<?php

namespace App\Filament\HospitalAdmin\Clusters\Doctors\Resources\SchedulesResource\Pages;


use Filament\Actions;
use App\Models\Schedule;
use App\Models\ScheduleDay;
use Illuminate\Support\Arr;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\ScheduleRepository;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\HospitalAdmin\Clusters\Doctors\Resources\SchedulesResource;

class CreateSchedules extends CreateRecord
{
    protected static string $resource = SchedulesResource::class;

    protected static bool $canCreateAnother = false;

    protected function getActions(): array
    {
        return [
            Action::make('back')
                ->label(__('messages.common.back'))
                ->url(static::getResource()::getUrl('index')),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
    protected function getCreatedNotificationTitle(): ?string
    {
        return __('messages.flash.schedule_saved');
    }
    public function prepareInputForScheduleDayItem(array $input): array
    {
        $items = [];
        foreach ($input as $key => $data) {
            foreach ($data as $index => $value) {
                $items[$index][$key] = $value;
            }
        }

        return $items;
    }
    protected function handleRecordCreation(array $input): Model
    {
        $scheduleDay = [];
        $schedule = Schedule::create($input);
        foreach ($input['schedule'] as $data) {
            $data['doctor_id'] = $input['doctor_id'];
            $data['schedule_id'] = $schedule->id;
            $scheduleDay = ScheduleDay::create($data);
        }
        return $schedule;
    }
}
