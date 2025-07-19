<?php

namespace App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsResource\Form\Selects;

use App\Models\Rips\Cie10;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class Cie10Options
{
    public static function getOptions(?string $search = null): Collection
    {
        $cacheKey = 'cie10_options_' . ($search ? md5($search) : 'all');

        return Cache::remember($cacheKey, 3600, function () use ($search) {
            return Cie10::query()
                ->when($search, fn ($query) =>
                    $query->where('description', 'like', "%{$search}%")
                          ->orWhere('code', 'like', "%{$search}%")
                )
                ->orderBy('code')
                ->get()
                ->mapWithKeys(fn ($cie) => [
                    $cie->id => "{$cie->code} - {$cie->description}"
                ]);
        });
    }

    public static function getLabel($value): ?string
    {
        return Cache::remember("cie10_label_{$value}", 3600, function () use ($value) {
            $cie = Cie10::find($value);
            return $cie ? "{$cie->code} - {$cie->description}" : null;
        });
    }
}
