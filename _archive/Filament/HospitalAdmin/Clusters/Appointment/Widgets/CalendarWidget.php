<?php

namespace App\Filament\HospitalAdmin\Clusters\Appointment\Widgets;

use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use App\Models\Appointment;


class CalendarWidget extends FullCalendarWidget
{

    public function fetchEvents(array $fetchInfo): array
    {
        return Appointment::query()
            ->where('opd_date', '>=', $fetchInfo['start'])
            ->where('opd_date', '<=', $fetchInfo['end'])
            ->get()
            ->all();
    }
}
