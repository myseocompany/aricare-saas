<?php

namespace App\Filament\HospitalAdmin\Resources\RipsPatientServiceResource\Pages;

use App\Filament\HospitalAdmin\Resources\RipsPatientServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
    use Illuminate\Support\Facades\Auth;

class CreateRipsPatientService extends CreateRecord
{
    protected static string $resource = RipsPatientServiceResource::class;


    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id'] = Auth::user()->tenant_id;
        return $data;
    }
}
