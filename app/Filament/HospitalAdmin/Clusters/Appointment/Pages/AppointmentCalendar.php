<?php

namespace App\Filament\HospitalAdmin\Clusters\Appointment\Pages;

use App\Filament\HospitalAdmin\Clusters\Appointment;
use App\Filament\HospitalAdmin\Clusters\Appointment\Widgets\AppoinmentCalenderWidget;
use Filament\Pages\Page;
use Filament\Pages\SubNavigationPosition;


class AppointmentCalendar extends Page
{
    protected static ?string $cluster = Appointment::class;

    protected static string $view = 'filament.hospital-admin.clusters.appointment.pages.appointment-calendar';

    protected static bool $shouldRegisterNavigation = false;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getWidgets(): array
    {
        return [
            AppoinmentCalenderWidget::class,
        ];
    }
}
