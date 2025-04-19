<?php

namespace App\Models;

use App\Traits\PopulateTenantID;
use Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

/**
 * App\Models\HospitalSchedule
 *
 * @property int $id
 * @property string $day_of_week
 * @property string $start_time
 * @property string $end_time
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|HospitalSchedule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HospitalSchedule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HospitalSchedule query()
 * @method static \Illuminate\Database\Eloquent\Builder|HospitalSchedule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HospitalSchedule whereDayOfWeek($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HospitalSchedule whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HospitalSchedule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HospitalSchedule whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HospitalSchedule whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class HospitalSchedule extends Model
{
    use BelongsToTenant, PopulateTenantID, HasFactory;

    const Mon = 1;
    const Tue = 2;
    const Wed = 3;
    const Thu = 4;
    const Fri = 5;
    const Sat = 6;
    const Sun = 7;

    const WEEKDAY = [
        self::Mon => 'MON',
        self::Tue => 'TUE',
        self::Wed => 'WED',
        self::Thu => 'THU',
        self::Fri => 'FRI',
        self::Sat => 'SAT',
        self::Sun => 'SUN',
    ];

    const WEEKDAY_FULL_NAME = [
        self::Mon => 'Monday',
        self::Tue => 'Tuesday',
        self::Wed => 'Wednesday',
        self::Thu => 'Thursday',
        self::Fri => 'Friday',
        self::Sat => 'Saturday',
        self::Sun => 'Sunday',
    ];

    public $fillable = [
        'day_of_week',
        'start_time',
        'end_time',
        'tenant_id', // por si acaso también se usa en mass assignment
        'is_active', // ✅ este es el importante
    ];

    protected $table = 'hospital_schedules';


    public static function getWeekdaysShort(): array
    {
        return __('messages.weekdays.short');
    }

    public static function getWeekdaysFull(): array
    {
        return __('messages.weekdays.full');
    }

    // Opcional: método para retornar nombre por día
    public static function getFullDayName(int $day): string
    {
        return self::getWeekdaysFull()[$day] ?? '';
    }

    public static function getDayNumberFromName(string $dayName): ?int
    {
        // Normalize the input (e.g., "monday" => "Monday")
        $dayName = ucfirst(strtolower($dayName));

        $dayNumber = array_search($dayName, self::WEEKDAY_FULL_NAME);

        return $dayNumber !== false ? $dayNumber : null;
    }

    public static function getDayNumberFromShortName(string $shortName): ?int
    {
        $shortName = strtoupper($shortName);

        // Acceder solo a los índices numéricos del array de días
        $shortWeekdays = __('messages.weekdays.short');

        // Filtra solo los días que tienen índice numérico (1-7)
        $numericDays = array_filter($shortWeekdays, fn($key) => is_int($key), ARRAY_FILTER_USE_KEY);

        \Log::info("→ SHORT NAME:", [$shortName]);
        \Log::info("→ NUMERIC DAYS:", [$numericDays]);

        $dayNumber = array_search($shortName, $numericDays);

        return $dayNumber !== false ? $dayNumber : null;
    }

    public static function getNumericShortWeekdays(): array
    {
        return array_filter(
            __('messages.weekdays.short'),
            fn($key) => is_int($key),
            ARRAY_FILTER_USE_KEY
        );
    }

    public static function getNumericFullWeekdays(): array
    {
        return array_filter(
            __('messages.weekdays.full'),
            fn($key) => is_int($key),
            ARRAY_FILTER_USE_KEY
        );
    }


}
