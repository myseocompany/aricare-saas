<?php

namespace App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsPayers\RipsPayerResource\Pages;


use App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsPayers\RipsPayerResource\RipsPayerResource;


use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateRipsPayer extends CreateRecord
{
    protected static string $resource = RipsPayerResource::class;

protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id'] = auth()->user()->tenant_id;
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['tenant_id'] = auth()->user()->tenant_id;
        return $data;
    }

            protected function handleRecordCreation(array $data): Model
    {
        $data['tenant_id'] = auth()->user()->tenant_id;

        return static::getModel()::create($data);
    }

}
