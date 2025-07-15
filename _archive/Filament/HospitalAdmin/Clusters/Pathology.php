<?php

namespace App\Filament\HospitalAdmin\Clusters;

use Filament\Clusters\Cluster;

class Pathology extends Cluster
{
    protected static ?string $navigationIcon = 'fas-flask';

    protected static ?int $navigationSort = 21;

    public function mount(): void
    {
        if (empty($this->getCachedSubNavigation())) {
            abort(404);
        }
        foreach ($this->getCachedSubNavigation() as $navigationGroup) {
            foreach ($navigationGroup->getItems() as $navigationItem) {
                redirect($navigationItem->getUrl());

                return;
            }
        }
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.pathologies');
    }

    public static function canAccessClusteredComponents(): bool
    {
        if (auth()->user()->hasRole(['Pharmacist','Lab Technician'])  && !getModuleAccess('Pathology Tests')) {
            return false;
        } elseif (auth()->user()->hasRole(['Doctor', 'Accountant', 'Case Manager','Nurse','Patient'])) {
            return false;
        } elseif (auth()->user()->hasRole(['Admin', 'Receptionist']) && !getModuleAccess('Pathology Tests') && !getModuleAccess('Pathology Categories')) {
            return false;
        }
        return true;
    }
}
