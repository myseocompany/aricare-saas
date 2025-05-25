<?php

namespace App\Filament\HospitalAdmin\Resources\RipsPatientServiceResource\Sections;

use Filament\Forms\Components\Group;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;

class Diagnoses
{
    public static function make(): Fieldset
    {
        return Fieldset::make('Diagnósticos')
            ->schema([
                self::diagnosisInput(0),
                self::diagnosisInput(1),
                self::diagnosisInput(2),
                self::diagnosisInput(3),
            ])
            
            ->columns(2)
            ->columnSpanFull();
    }

    protected static function diagnosisInput(int $index): Group
    {
        $pos = $index + 1;

        return Group::make()
            ->schema([
                Hidden::make("diagnoses.{$index}.sequence")
                    ->default($pos),

                Placeholder::make("diagnoses.{$index}.role")
                    ->label('Rol')
                    ->content(fn () => $index === 0
                        ? '🟢 Diagnóstico Principal'
                        : "🔵 Relacionado #{$pos}"),

                Select::make("diagnoses.{$index}.cie10_id")
                    ->label('Código CIE10')
                    ->options(\App\Models\Cie10::all()->pluck('description', 'id'))
                    ->searchable()
                    ->nullable(), // si no lo llena, no se guarda
            ])
            ->columns(2);
    }
}
