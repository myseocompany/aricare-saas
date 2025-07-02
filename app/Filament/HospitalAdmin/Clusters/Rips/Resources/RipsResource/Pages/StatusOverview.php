<?php

namespace App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsResource\Pages;

use App\Models\Rips\RipsPatientService;
use App\Models\Rips\RipsStatus;  // Usamos el modelo RipsStatus
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Support\Enums\IconPosition;

class StatusOverview extends BaseWidget
{
    // Definimos la vista asociada al widget
//    protected static string $view = 'filament.hospital-admin.clusters.rips.resources.rips-resource.widgets.dashboard-status';
    protected static string $view = 'filament.hospital-admin.widgets.rips-dashboard-status';

   // protected static string $view = 'filament.hospital-admin.widgets.patient-state';

    public static function canView(): bool
    {
        //dd(auth()->user()->roles);

        return auth()->user()->hasRole('Admin');  // Verifica que el usuario sea Admin
    }

    // Recupera los datos para la vista
    protected function getViewData(): array
    {
        // Obtiene el ID del tenant del usuario autenticado
        $tenantId = getLoggedInUser()->tenant_id;

        // Recupera todos los estados de RIPS
        $status = RipsStatus::all();
        
        $statusCounts = [];
        
        // Cuenta cuántos servicios están asociados a cada estado
        foreach ($status as $state) {
            $count = RipsPatientService::where('tenant_id', $tenantId)
                                      ->where('status_id', $state->id)  // Filtra por el estado
                                      ->count();
            $statusCounts[$state->name] = $count;  // Almacena el conteo por estado
        }
        
        
        // Retorna los datos para la vista
        return [
            'statusCounts' => $statusCounts,  // Pasamos los conteos de los estados
        ];
    }

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

        // Verifica si se está calculando correctamente el conteo
        \Log::info("State: {$state->name}, Count: {$count}");

        // Crea una estadística para cada estado
        $stats[] = Stat::make($state->name, $count)
                       ->description($state->description ?? 'No description available')
                       ->descriptionIcon('heroicon-m-information-circle', IconPosition::Before);
    }

    return $stats;
}

}
