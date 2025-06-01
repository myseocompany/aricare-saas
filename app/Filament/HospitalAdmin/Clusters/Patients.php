<?php

namespace App\Filament\HospitalAdmin\Clusters;

use Filament\Clusters\Cluster;

class Patients extends Cluster
{
    protected static ?string $navigationIcon = 'fas-user-injured';

    protected static ?int $navigationSort = 20;

    public function mount(): void
    {
        if (auth()->user()->hasRole(['Doctor']) && !getModuleAccess('Patients') && !getModuleAccess('Patient Admissions')) {
            abort(404);
        }
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
        if (auth()->user()->hasRole('Patient')) {
            return __('messages.patients_cases');
        }
        return __('messages.patients');
    }

public static function canAccessClusteredComponents(): bool
{
    
    if (auth()->user()->hasRole(['Patient'])) {
        return getModuleAccess('Patient Admissions');
    }

    if (auth()->user()->hasRole(['Case Manager'])) {
        return getModuleAccess('Cases') || getModuleAccess('Patient Admissions');
    }

    if (auth()->user()->hasRole(['Accountant', 'Pharmacist', 'Lab Technician', 'Nurse'])) {
        return false;
    }

    if (auth()->user()->hasRole(['Doctor'])) {
        return getModuleAccess('Patients') || getModuleAccess('Patient Admissions');
    }

    if (auth()->user()->hasRole(['Admin', 'Receptionist'])) {
        return getModuleAccess('Patients') || getModuleAccess('Cases') || getModuleAccess('Patient Admissions') || getModuleAccess('Case Handlers');
    }

        return false;
    }


    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccessClusteredComponents();
    }

}
