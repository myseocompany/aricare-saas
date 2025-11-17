<?php

namespace App\Filament\HospitalAdmin\Clusters\Encounters;

use Filament\Clusters\Cluster;

class EncountersCluster extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';

    protected static ?int $navigationSort = 40;

    public static function getNavigationLabel(): string
    {
        return __('messages.encounters');
    }
}
