<?php

namespace App\Filament\HospitalAdmin\Clusters\BookableUnits\Resources\BookableUnitResource\Pages;

use App\Filament\HospitalAdmin\Clusters\BookableUnits\Resources\BookableUnitResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBookableUnits extends ListRecords
{
    protected static string $resource = BookableUnitResource::class;

    public function getTitle(): string
    {
        return __('messages.bookable_units.list');
    }


    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('messages.bookable_units.create')),
        ];
    }
}
