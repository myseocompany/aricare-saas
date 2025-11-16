<?php

namespace Database\Seeders;

use App\Models\Rda\RdaOccupation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class RdaOccupationSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/RDA-CUOC-2025/Ocupación-Tabla 1.csv');

        if (! File::exists($path)) {
            $this->command?->warn("CSV file not found at {$path}");
            return;
        }

        if (($handle = fopen($path, 'r')) === false) {
            $this->command?->error("Unable to open CSV file at {$path}");
            return;
        }

        $headers = fgetcsv($handle, 0, ';');
        if (! $headers) {
            fclose($handle);
            return;
        }

        $headers = array_map(fn ($header) => trim(mb_strtolower($header)), $headers);

        $codeIndex = array_search('código de la ocupación', $headers);
        $nameIndex = array_search('nombre de la ocupación', $headers);
        $majorCodeIndex = array_search('código de gran grupo', $headers);
        $majorNameIndex = array_search('nombre de gran grupo', $headers);
        $subCodeIndex = array_search('código de subgrupo', $headers);
        $subNameIndex = array_search('nombre de subgrupo', $headers);

        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            $code = $codeIndex !== false ? trim($row[$codeIndex] ?? '') : null;
            if (empty($code)) {
                continue;
            }

            RdaOccupation::updateOrCreate(
                ['code' => $code],
                [
                    'name' => $nameIndex !== false ? trim($row[$nameIndex] ?? '') : null,
                    'major_group_code' => $majorCodeIndex !== false ? trim($row[$majorCodeIndex] ?? '') : null,
                    'major_group_name' => $majorNameIndex !== false ? trim($row[$majorNameIndex] ?? '') : null,
                    'subgroup_code' => $subCodeIndex !== false ? trim($row[$subCodeIndex] ?? '') : null,
                    'subgroup_name' => $subNameIndex !== false ? trim($row[$subNameIndex] ?? '') : null,
                    'is_active' => true,
                ]
            );
        }

        fclose($handle);
    }
}
