<?php

namespace App\Filament\HospitalAdmin\Clusters\Patients\Resources\RipsPatientServiceResource\Form;

use Filament\Forms\Components\Select;

class FormConsultationSimpleDiagnoses
{
    public static function schema(bool $isPrincipal = false): Select
    {
        return Select::make('cie10_id')
            ->label('Diagnóstico')
            ->searchable()
            ->options(fn (string $search = null) =>
                \App\Models\Rips\Cie10::query()
                    ->when($search, fn ($q) => $q->where('description', 'like', "%{$search}%"))
                    ->limit(20)
                    ->pluck('description', 'id')
            )
            ->required();
    }
}
