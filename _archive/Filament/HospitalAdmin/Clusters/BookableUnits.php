<?php

namespace App\Filament\HospitalAdmin\Clusters;

use Filament\Clusters\Cluster;

class BookableUnits extends Cluster
{
    protected static ?string $navigationIcon = 'fas-chair';
    protected static ?int $navigationSort = 1;



    public static function getNavigationLabel(): string
    {
        return __('messages.bookable_units.singular');
    }

    public static function getLabel(): string
    {
        return __('messages.bookable_units.singular');
    }

}
