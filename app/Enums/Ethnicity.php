<?php

namespace App\Enums;

enum Ethnicity: int
{
    case Indigenous = 1;
    case Gitano = 2;
    case Raizal = 3;
    case Palenquero = 4;
    case Afrocolombian = 5;
    case None = 6;

    public function label(): string
    {
        return match ($this) {
            self::Indigenous => __('messages.patient.ethnicities.indigenous'),
            self::Gitano => __('messages.patient.ethnicities.gitano'),
            self::Raizal => __('messages.patient.ethnicities.raizal'),
            self::Palenquero => __('messages.patient.ethnicities.palenquero'),
            self::Afrocolombian => __('messages.patient.ethnicities.afrocolombian'),
            self::None => __('messages.patient.ethnicities.none'),
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $ethnicity) => [$ethnicity->value => $ethnicity->label()])
            ->toArray();
    }

    public static function values(): array
    {
        return array_map(fn (self $ethnicity) => $ethnicity->value, self::cases());
    }
}
