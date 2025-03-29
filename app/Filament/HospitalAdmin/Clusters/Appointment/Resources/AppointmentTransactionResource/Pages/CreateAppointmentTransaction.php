<?php

namespace App\Filament\HospitalAdmin\Clusters\Appointment\Resources\AppointmentTransactionResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Redirect;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\HospitalAdmin\Clusters\Appointment\Resources\AppointmentTransactionResource;

class CreateAppointmentTransaction extends CreateRecord
{
    public function mount(): void
    {
        Redirect::to($this->getResource()::getUrl('index'));

        $this->authorizeAccess();

        $this->fillForm();

        $this->previousUrl = url()->previous();
    }

    protected static string $resource = AppointmentTransactionResource::class;
}
