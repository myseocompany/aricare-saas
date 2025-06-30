<?php

namespace App\Filament\HospitalAdmin\Clusters\Rips\Resources;

use App\Models\Rips\RipsPatientService;
use App\Models\Rips\RipsStatus;  // Usamos el modelo RipsStatus
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Support\Enums\IconPosition;

class StatusOverview extends BaseWidget
{
    // Definimos la vista asociada al widget
    protected static string $view = 'filament.hospital-admin.widgets.dashboard-status';

    public static function canView(): bool
    {
        return auth()->user()->hasRole('Admin');  // Verifica que el usuario sea Admin
    }

    // Recupera los datos para la vista
    protected function getViewData(): array
    {
        // Obtiene el ID del tenant del usuario autenticado
        $tenantId = getLoggedInUser()->tenant_id;

        // Recupera todos los estados de RIPS
        $states = RipsStatus::all();

        $stateCounts = [];
        
        // Cuenta cuántos servicios están asociados a cada estado
        foreach ($states as $state) {
            $count = RipsPatientService::where('tenant_id', $tenantId)
                                      ->where('status_id', $state->id)  // Filtra por el estado
                                      ->count();
            $stateCounts[$state->name] = $count;  // Almacena el conteo por estado
        }

        // Retorna los datos para la vista
        return [
            'stateCounts' => $stateCounts,  // Pasamos los conteos de los estados
        ];
    }

    // Si necesitas personalizar las estadísticas, lo puedes hacer aquí
    protected function getStats(): array
    {
        $tenantId = getLoggedInUser()->tenant_id;  // Obtén el tenant ID del usuario autenticado

        // Recupera los estados de RIPS
        $states = RipsStatus::all();
        $stats = [];

        // Crea un objeto Stat para cada estado con su conteo
        foreach ($states as $state) {
            $count = RipsPatientService::where('tenant_id', $tenantId)
                                      ->where('status_id', $state->id)
                                      ->count();
            // Crea una estadística para cada estado
            $stats[] = Stat::make($state->name, $count)
                           ->description($state->description ?? 'No description available')
                           ->descriptionIcon('heroicon-m-information-circle', IconPosition::Before);
        }

        return $stats;
    }
}
