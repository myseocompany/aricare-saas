<?php

namespace App\Filament\HospitalAdmin\Resources\TenantResource\Pages;

use App\Filament\HospitalAdmin\Resources\TenantResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTenant extends CreateRecord
{
    protected static string $resource = TenantResource::class;
}
