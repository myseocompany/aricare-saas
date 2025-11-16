<?php

namespace App\Enums;

enum MaritalStatus: int
{
    case Single = 1;
    case Married = 2;
    case Divorced = 3;
    case Widowed = 4;
    case Separated = 5;

    public function label(): string
    {
        return match ($this) {
            self::Single => __('messages.patient.marital_status.single'),
            self::Married => __('messages.patient.marital_status.married'),
            self::Divorced => __('messages.patient.marital_status.divorced'),
            self::Widowed => __('messages.patient.marital_status.widowed'),
            self::Separated => __('messages.patient.marital_status.separated'),
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $status) => [$status->value => $status->label()])
            ->toArray();
    }
}
