<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $ethnicityMap = [
        'indígena' => 1,
        'gitano (rrom)' => 2,
        'raizal (archipiélago de san andrés, providencia y santa catalina)' => 3,
        'palenquero de san basilio' => 4,
        'negro / afrocolombiano' => 5,
        'ninguno (no pertenece a grupo étnico)' => 6,
    ];

    private array $educationLevelMap = [
        'ninguno' => 1,
        'preescolar' => 2,
        'básica primaria' => 3,
        'basica primaria' => 3,
        'básica secundaria' => 4,
        'basica secundaria' => 4,
        'media académica o técnica' => 5,
        'media academica o tecnica' => 5,
        'técnico laboral / técnico profesional' => 6,
        'tecnico laboral / tecnico profesional' => 6,
        'tecnico laboral' => 6,
        'tecnico profesional' => 6,
        'técnico profesional' => 6,
        'tecnico profesional universitario' => 6,
        'tecnico' => 6,
        'tecnico laboral o profesional' => 6,
        'tecnológico' => 7,
        'tecnologico' => 7,
        'universitario' => 8,
        'postgrado' => 9,
    ];

    public function up(): void
    {
        if (Schema::hasColumn('patients', 'ethnicity')) {
            $this->convertColumnToInteger('ethnicity', $this->ethnicityMap);
            DB::statement('ALTER TABLE patients CHANGE ethnicity ethnicity_id TINYINT UNSIGNED NULL');
        }

        if (Schema::hasColumn('patients', 'education_level')) {
            $this->convertColumnToInteger('education_level', $this->educationLevelMap);
            DB::statement('ALTER TABLE patients CHANGE education_level education_level_id TINYINT UNSIGNED NULL');
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('patients', 'ethnicity_id')) {
            $this->convertColumnToString('ethnicity_id', array_flip($this->ethnicityMap));
            DB::statement('ALTER TABLE patients CHANGE ethnicity_id ethnicity VARCHAR(255) NULL');
        }

        if (Schema::hasColumn('patients', 'education_level_id')) {
            $this->convertColumnToString('education_level_id', array_flip($this->educationLevelMap));
            DB::statement('ALTER TABLE patients CHANGE education_level_id education_level VARCHAR(255) NULL');
        }
    }

    private function convertColumnToInteger(string $column, array $map): void
    {
        DB::table('patients')
            ->select('id', $column)
            ->whereNotNull($column)
            ->orderBy('id')
            ->chunkById(200, function ($patients) use ($column, $map) {
                foreach ($patients as $patient) {
                    $value = $patient->{$column};
                    if ($value === null || $value === '') {
                        $code = null;
                    } elseif (is_numeric($value)) {
                        $code = (int) $value;
                    } else {
                        $normalized = mb_strtolower(trim($value));
                        $code = $map[$normalized] ?? null;
                    }

                    DB::table('patients')
                        ->where('id', $patient->id)
                        ->update([$column => $code]);
                }
            });
    }

    private function convertColumnToString(string $column, array $map): void
    {
        DB::table('patients')
            ->select('id', $column)
            ->whereNotNull($column)
            ->orderBy('id')
            ->chunkById(200, function ($patients) use ($column, $map) {
                foreach ($patients as $patient) {
                    $value = $patient->{$column};
                    $stringValue = $map[$value] ?? (string) $value;

                    DB::table('patients')
                        ->where('id', $patient->id)
                        ->update([$column => $stringValue]);
                }
            });
    }
};
