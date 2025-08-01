<?php


namespace App\Filament\HospitalAdmin\Clusters;

use Filament\Clusters\Cluster;

class DoctorsCluster extends Cluster
{
    
    protected static ?string $navigationIcon = 'fas-user-doctor';
    protected static ?int $navigationSort = 21;
    
}
