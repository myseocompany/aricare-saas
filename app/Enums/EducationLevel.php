<?php

namespace App\Enums;

enum EducationLevel: int
{
    case None = 1;
    case Preschool = 2;
    case Primary = 3;
    case Secondary = 4;
    case HighSchool = 5;
    case Technical = 6;
    case Technological = 7;
    case University = 8;
    case Postgraduate = 9;

    public function label(): string
    {
        return match ($this) {
            self::None => __('messages.patient.education_levels.none'),
            self::Preschool => __('messages.patient.education_levels.preschool'),
            self::Primary => __('messages.patient.education_levels.primary'),
            self::Secondary => __('messages.patient.education_levels.secondary'),
            self::HighSchool => __('messages.patient.education_levels.high_school'),
            self::Technical => __('messages.patient.education_levels.technical'),
            self::Technological => __('messages.patient.education_levels.technological'),
            self::University => __('messages.patient.education_levels.university'),
            self::Postgraduate => __('messages.patient.education_levels.postgraduate'),
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $level) => [$level->value => $level->label()])
            ->toArray();
    }

    public static function values(): array
    {
        return array_map(fn (self $level) => $level->value, self::cases());
    }
}
