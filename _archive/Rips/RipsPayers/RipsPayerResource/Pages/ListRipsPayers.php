<?php

namespace App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsPayers\RipsPayerResource\Pages;


use App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsPayers\RipsPayerResource\RipsPayerResource;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRipsPayers extends ListRecords
{
    protected static string $resource = RipsPayerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
