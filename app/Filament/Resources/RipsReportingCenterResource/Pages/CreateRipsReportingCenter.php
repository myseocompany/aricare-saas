<?php

namespace App\Filament\Resources\RipsReportingCenterResource\Pages;

use App\Filament\Resources\RipsReportingCenterResource;
use App\Models\RipsReport;
use App\Services\RipsGeneratorService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateRipsReportingCenter extends CreateRecord
{
    // Indica a qué recurso pertenece esta página (la clase principal que controla el recurso)
    protected static string $resource = RipsReportingCenterResource::class;

    // Variables públicas para almacenar información generada y estado de la generación
    public $generatedData;  // Aquí se guarda el reporte generado
    public $downloadUrl;    // URL para descargar el reporte generado
    public $isGenerating = false; // Estado para saber si ya está generando el reporte

    // Define los botones que aparecerán en el formulario y qué harán
    protected function getFormActions(): array
    {
        return [
            // Botón para "Generar Reporte"
            Action::make('generate')
                ->label('Generar Reporte')   // Texto que ve el usuario
                ->action('generateReport')   // Método que se ejecuta al hacer clic
                ->color('primary')           // Color del botón (azul típico)
                ->loadingMessage('Generando reporte...') // Mensaje mientras se genera
                ->disabled($this->isGenerating),          // Deshabilita el botón si ya está generando

            // Botón para "Cancelar"
            Action::make('cancel')
                ->label('Cancelar')                      // Texto del botón
                ->url($this->getResource()::getUrl('index'))  // Redirige a la página principal del recurso
                ->color('secondary')                     // Color gris o similar
        ];
    }

    // Método principal que genera el reporte cuando el usuario hace clic en "Generar Reporte"
    public function generateReport()
    {
        // Indica que ya está en proceso de generación para evitar múltiples clics
        $this->isGenerating = true;

        // Obtiene los datos del formulario (convenio, fecha inicio y fecha fin)
        $data = $this->form->getState();

        try {
            // Instancia el servicio encargado de generar el reporte (lógica externa)
            $service = app(RipsGeneratorService::class);

            // Llama al método para generar el reporte con los datos seleccionados
            $this->generatedData = $service->generateByServices(
                $data['agreement_id'],
                $data['start_date'],
                $data['end_date']
            );

            // Prepara un nombre de archivo con fecha y hora actual
            $filename = 'RIPS_REPORT_'.now()->format('Ymd_His').'.json';

            // Guarda el reporte generado en formato JSON dentro del almacenamiento local
            Storage::put('rips-reports/'.$filename, json_encode($this->generatedData, JSON_PRETTY_UNICODE));

            // Obtiene la URL para que el usuario pueda descargar el archivo generado
            $this->downloadUrl = Storage::url('rips-reports/'.$filename);

            // Guarda un registro en la base de datos con los detalles del reporte creado
            RipsReport::create([
                'agreement_id' => $data['agreement_id'],     // Convenio seleccionado
                'start_date' => $data['start_date'],         // Fecha inicial seleccionada
                'end_date' => $data['end_date'],             // Fecha final seleccionada
                'file_path' => 'rips-reports/'.$filename,    // Ruta del archivo guardado
                'records_count' => count($this->generatedData), // Cantidad de registros generados
                'generated_by' => auth()->id()                // Usuario que generó el reporte
            ]);

            // Muestra una notificación al usuario informando que se generó el reporte correctamente
            Notification::make()
                ->title('Reporte generado exitosamente')
                ->success()
                ->send();

        } catch (\Exception $e) {
            // En caso de error, muestra una notificación con el mensaje del error
            Notification::make()
                ->title('Error al generar reporte')
                ->body($e->getMessage())
                ->danger()
                ->send();

        } finally {
            // Finalmente, indica que terminó el proceso de generación (habilita el botón otra vez)
            $this->isGenerating = false;
        }
    }

    // Método que indica a dónde se redirige al usuario después de crear el reporte (a la lista)
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
