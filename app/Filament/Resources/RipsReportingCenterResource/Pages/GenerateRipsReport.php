<?php

namespace App\Filament\Resources\RipsReportingCenterResource\Pages;

use App\Filament\Resources\RipsReportingCenterResource;
use App\Services\RipsGeneratorService;
use Filament\Actions\Action;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
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
        return [
            Select::make('agreement_id')
                ->label('Convenio')
                ->options(function() {
                    return \App\Models\Rips\RipsTenantPayerAgreements::where('tenant_id', auth()->user()->tenant_id)
                        ->pluck('name', 'id');
                })
                ->required()
                ->searchable(),
                
            Radio::make('report_type')
                ->label('Tipo de Reporte')
                ->options([
                    'with_invoice' => 'Con Factura',
                    'without_invoice' => 'Sin Factura'
                ])
                ->default('with_invoice')
                ->required(),
                
            DatePicker::make('start_date')
                ->label('Fecha Inicial')
                ->required(),
                
            DatePicker::make('end_date')
                ->label('Fecha Final')
                ->required(),
        ];
    }

    public function generate()
    {
        $data = $this->form->getState();
        
        try {
            $service = app(RipsGeneratorService::class);
            $this->ripsData = $service->generateByServices(
                $data['agreement_id'],
                $data['start_date'],
                $data['end_date'],
                $data['report_type'] === 'with_invoice'
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
        /*return [
            Action::make('generate')
                ->label('Generar RIPS')
                ->action('generate')
                ->color('primary'),
        ];*/
        return [
        FormAction::make('generate')
            ->label('Generar RIPS')
            ->action('generate')
            ->color('primary'),
        ];
    }
}