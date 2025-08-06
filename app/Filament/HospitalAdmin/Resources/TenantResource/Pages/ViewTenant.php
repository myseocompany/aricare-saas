<?php

namespace App\Filament\HospitalAdmin\Resources\TenantResource\Pages;

use App\Filament\HospitalAdmin\Resources\TenantResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;

class ViewTenant extends ViewRecord
{
    protected static string $resource = TenantResource::class;

    public function getTitle(): string
    {
        return 'Ajustes';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record->getKey()]);
    }

        protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
