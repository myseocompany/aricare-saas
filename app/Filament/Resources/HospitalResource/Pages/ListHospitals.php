<?php

namespace App\Filament\Resources\HospitalResource\Pages;

use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\HospitalResource;

class ListHospitals extends ListRecords
{
    protected static string $resource = HospitalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function impersonate($recordId)
    {
        $user = User::findOrFail($recordId);
        getLoggedInUser()->impersonate($user);
        
        return redirect(route('filament.hospitalAdmin.pages.dashboard'));
    }
}
