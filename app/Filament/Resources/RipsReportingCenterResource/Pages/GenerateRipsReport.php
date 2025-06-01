<?php

namespace App\Filament\Resources\RipsReportingCenterResource\Pages;

use App\Filament\Resources\RipsReportingCenterResource;
use App\Services\RipsGeneratorService;
use Filament\Actions\Action;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Storage;

class GenerateRipsReport extends Page implements HasForms
{
    use InteractsWithForms;
    
    protected static string $resource = RipsReportingCenterResource::class;
    protected static string $view = 'filament.resources.rips-reporting-center-resource.pages.generate-rips-report';

    public $ripsData;
    public $showPreview = false;
    public $downloadUrl;

    public function mount()
    {
        $this->form->fill();
    }

    protected function getFormSchema(): array
    {
        return RipsReportingCenterResource::form($this->getForm())->getSchema();
    }

    public function generate()
    {
        $data = $this->form->getState();
        
        try {
            $service = app(RipsGeneratorService::class);
            $this->ripsData = $service->generateByServices(
                $data['agreement_id'],
                $data['start_date'],
                $data['end_date']
            );
            
            // Crear archivo temporal
            $tempFilename = 'rips_temp_'.now()->timestamp.'.json';
            Storage::put($tempFilename, json_encode($this->ripsData, JSON_PRETTY_UNICODE));
            $this->downloadUrl = route('download.temp.rips', ['file' => $tempFilename]);
            
            $this->showPreview = true;
            
            Notification::make()
                ->title('Reporte generado exitosamente')
                ->success()
                ->send();
                
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error al generar reporte')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('generate')
                ->label('Generar RIPS')
                ->action('generate')
                ->color('primary'),
        ];
    }
}