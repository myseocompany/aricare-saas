<?php

namespace App\Filament\HospitalAdmin\Clusters\Settings\Resources\SidebarSettingResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Redirect;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\HospitalAdmin\Clusters\Settings\Resources\SidebarSettingResource;

class CreateSidebarSetting extends CreateRecord
{
    protected static string $resource = SidebarSettingResource::class;

    public function mount(): void
    {
        Redirect::to(static::getResource()::getUrl('index'));
    }
}
