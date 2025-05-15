<?php

// app/Enums/Gender.php

namespace App\Enums;

enum Gender: int
{
    case Male = 0;
    case Female = 1;
    case Indeterminate = 2;

    public function label(): string
    {
        return match ($this) {
            self::Male => 'Masculino',
            self::Female => 'Femenino',
            self::Indeterminate => 'Indeterminado',
        };
    }

    public function sexCode(): string
    {
        return match ($this) {
            self::Male => 'M',
            self::Female => 'F',
            self::Indeterminate => 'I',
        };
    }

    public static function options(): array
    {
        return [
            self::Male->value => self::Male->label(),
            self::Female->value => self::Female->label(),
            self::Indeterminate->value => self::Indeterminate->label(),
        ];
    }
}
