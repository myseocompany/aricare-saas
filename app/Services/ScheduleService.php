<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Schedule;
use App\Models\ScheduleDay;
use App\Models\DoctorHoliday;
use App\Models\HospitalSchedule;
use Stevebauman\Location\Facades\Location;
use App\Models\LunchBreak;

class ScheduleService
{
    public static function convertToAmPmFormat(string $time): string
    {
        $time = trim($time); // elimina espacios
        // Intenta convertir con segundos si vienen
        if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $time)) {
            return Carbon::createFromFormat('H:i:s', $time)->format('g:i A');
        }
    
        // Si solo tiene horas y minutos
        if (preg_match('/^\d{2}:\d{2}$/', $time)) {
            return Carbon::createFromFormat('H:i', $time)->format('g:i A');
        }
    
        // Fallback: intenta parsear de forma automática
        return Carbon::parse($time)->format('g:i A');
    }
    






    public static function isHospitalAvailable(Carbon $date, string $time): bool
    {
        $dayNumber = $date->dayOfWeekIso; // Lunes = 1, Domingo = 7
        $schedule = HospitalSchedule::where('day_of_week', $dayNumber)->first();

        if (!$schedule) return false;

        $check = Carbon::createFromFormat('H:i', $time);
        $start = Carbon::createFromFormat('H:i:s', $schedule->start_time);
        $end = Carbon::createFromFormat('H:i:s', $schedule->end_time);

        return $check->between($start, $end);
    }

    public static function isDoctorAvailable(int $doctorId, Carbon $date, string $time): bool
    {
        $dayName = $date->format('l');

        $scheduleDay = ScheduleDay::where('doctor_id', $doctorId)
            ->where('available_on', $dayName)
            ->first();

        if (!$scheduleDay) return false;

        $holiday = DoctorHoliday::where('doctor_id', $doctorId)
            ->whereDate('date', $date->toDateString())
            ->exists();

        if ($holiday) return false;

        $check = Carbon::createFromFormat('H:i', $time);
        $start = Carbon::createFromFormat('H:i:s', $scheduleDay->available_from);
        $end = Carbon::createFromFormat('H:i:s', $scheduleDay->available_to);

        return $check->between($start, $end);
    }

    public static function getAvailableTimeSlots(int $doctorId, Carbon $date): array
    {
        $scheduleDay = ScheduleDay::where('doctor_id', $doctorId)
            ->where('available_on', $date->format('l'))
            ->first();

        $schedule = Schedule::where('doctor_id', $doctorId)->first();
        if (!$scheduleDay || !$schedule) return [];

        $interval = $schedule->per_patient_time; // Ej. 00:30:00
        $start = Carbon::createFromFormat('H:i:s', $scheduleDay->available_from);
        $end = Carbon::createFromFormat('H:i:s', $scheduleDay->available_to);

        $slots = [];
        while ($start < $end) {
            $slotTime = $start->format('H:i');
            if (
                self::isDoctorAvailable($doctorId, $date, $slotTime) &&
                self::isHospitalAvailable($date, $slotTime)
            ) {
                $slots[$slotTime] = self::convertToAmPmFormat($slotTime);
            }
            $start->addMinutes(Carbon::parse($interval)->hour * 60 + Carbon::parse($interval)->minute);
        }

        return $slots;
    }

    public static function getSlotsForAppointmentForm(int $doctorId, Carbon $date, $opdDate): array
    {
        $timezone = Location::get(request()->ip())->timezone ?? config('app.timezone');

        $dayName = Carbon::parse($opdDate)->format('l');
        $scheduleDay = ScheduleDay::where('doctor_id', $doctorId)->where('available_on', $dayName)->first();
        $perPatientTime = Schedule::whereDoctorId($doctorId)->first();

        if (!$scheduleDay || DoctorHoliday::where('doctor_id', $doctorId)->where('date', $date)->exists()) {
            return ['label' => '', 'slots' => []];
        }

        $availableFrom = ($date->isToday())
            ? self::getAvailableStartToday($scheduleDay, $perPatientTime->per_patient_time, $timezone)
            : $scheduleDay->available_from;

        $doctorStartTime = $date->copy()->setTimeFrom(Carbon::createFromFormat('H:i:s', $availableFrom));
        $doctorEndTime = $date->copy()->setTimeFrom(Carbon::createFromFormat('H:i:s', $scheduleDay->available_to));

        if ($doctorEndTime->isBefore(Carbon::now($timezone))) {
            return ['label' => '', 'slots' => []];
        }

        $intervalMinutes = Carbon::parse($perPatientTime->per_patient_time)->hour * 60 + Carbon::parse($perPatientTime->per_patient_time)->minute;

        $slots = self::generateTimeSlots($doctorStartTime, $doctorEndTime, $intervalMinutes);
        $slots = self::excludeBreaks($slots, $doctorId, $date);

        if (empty($slots)) {
            return ['label' => '', 'slots' => []];
        }

        $translatedDay = self::getTranslatedDay($dayName);
        $label = "$translatedDay [" . self::convertToAmPmFormat($availableFrom) . " - " . self::convertToAmPmFormat($scheduleDay->available_to) . "]";

        return [
            'label' => $label,
            'slots' => self::convertListToAmPm($slots),
        ];
    }

    public static function getAvailableStartToday($scheduleDay, $perPatientTime, $timezone): string
    {
        $time = Carbon::parse($perPatientTime);
        $totalMinutes = $time->hour * 60 + $time->minute;
        $now = Carbon::now($timezone);
        $start = Carbon::parse($scheduleDay->available_from);
        $end = Carbon::parse($scheduleDay->available_to);

        if ($now->between($start, $end)) {
            $nextAvailable = $now->addMinutes($totalMinutes)->ceil("{$totalMinutes} minutes");
            return $nextAvailable->greaterThan($end) ? $end->format('H:i:s') : $nextAvailable->format('H:i:s');
        }

        return $start->format('H:i:s');
    }

    public static function generateTimeSlots(Carbon $start, Carbon $end, int $intervalMinutes): array
    {
        $slots = [];
        while ($start < $end) {
            $slots[] = $start->format('H:i');
            $start->addMinutes($intervalMinutes);
        }
        return $slots;
    }

    public static function excludeBreaks(array $slots, int $doctorId, Carbon $date): array
    {
        $breaks = LunchBreak::where('doctor_id', $doctorId)
            ->where(function ($q) use ($date) {
                $q->where('date', $date)->orWhereNotNull('every_day');
            })->get();

        foreach ($breaks as $break) {
            $from = Carbon::parse($date->format('Y-m-d') . ' ' . $break->break_from);
            $to = Carbon::parse($date->format('Y-m-d') . ' ' . $break->break_to);

            $excluded = [];
            while ($from < $to) {
                $excluded[] = $from->format('H:i');
                $from->addMinutes(1);
            }

            $slots = array_filter($slots, fn($slot) => !in_array($slot, $excluded));
        }

        return array_values($slots);
    }

    public static function convertListToAmPm(array $slots): array
    {
        $converted = [];
        foreach ($slots as $slot) {
            $converted[$slot] = Carbon::createFromFormat('H:i', $slot)->format('g:i A');
        }
        return $converted;
    }
    /*
    public static function convertListToAmPm(array $timeSlots): array
    {
        return collect($timeSlots)->mapWithKeys(function ($slot) {
            return [$slot => self::convertToAmPmFormat($slot)];
        })->toArray();
    }*/



    public static function getTranslatedDay(string $englishDay): string
    {
        return __('messages.weekdays.' . $englishDay);
    }
    /*
    public static function getTranslatedDay(string $englishDay): string
    {
        $translations = [
            'Sunday' => 'Domingo',
            'Monday' => 'Lunes',
            'Tuesday' => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday' => 'Jueves',
            'Friday' => 'Viernes',
            'Saturday' => 'Sábado',
        ];

        return $translations[$englishDay] ?? $englishDay;
    }
        */
}
