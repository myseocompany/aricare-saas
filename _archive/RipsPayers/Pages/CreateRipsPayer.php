<?php


namespace App\Filament\HospitalAdmin\Clusters\RipsPayer\Pages;


use App\Filament\HospitalAdmin\Clusters\RipsPayer;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRipsPayer extends CreateRecord
{
    protected static string $resource = RipsPayerResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        
        $data['tenant_id'] = auth()->user()->tenant_id;
        return $data;
    }




}
