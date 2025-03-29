<?php

namespace App\Filament\HospitalAdmin\Clusters\Settings\Resources\SidebarSettingResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Redirect;
use App\Filament\HospitalAdmin\Clusters\Settings\Resources\SidebarSettingResource;

class EditSidebarSetting extends EditRecord
{
    protected static string $resource = SidebarSettingResource::class;
}
