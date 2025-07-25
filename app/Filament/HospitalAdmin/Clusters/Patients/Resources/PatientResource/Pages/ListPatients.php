<?php

namespace App\Filament\HospitalAdmin\Clusters\Patients\Resources\PatientResource\Pages;

use App\Filament\HospitalAdmin\Clusters\Patients\Resources\PatientResource;
use App\Filament\Resources\RipsReportingCenterResource\Pages\GenerateRipsReport;
use App\Models\Patient;
use App\Services\RipsGeneratorService;
use Illuminate\Support\Facades\Storage;
use Filament\Actions;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\IconPosition;
use Illuminate\Contracts\Database\Query\Builder;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;



class ListPatients extends ListRecords
{
    protected static string $resource = PatientResource::class;

    protected $i = 1;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            /*
            ActionGroup::make([
                Actions\CreateAction::make()
                    ->icon('')
                    ->label(__('messages.new_patient')),
                
                ExportAction::make()->icon('heroicon-o-arrow-right-start-on-rectangle')
                    ->disabled(!Patient::where('tenant_id', auth()->user()->tenant_id)->with('patientUser.media')->whereTenantId(auth()->user()->tenant_id)->exists())
                    ->label(__('messages.common.export_to_excel'))->exports([
                        ExcelExport::make()
                            ->withFilename(__('messages.patients') . '-' . now()->format('Y-m-d') . '.xlsx')
                            ->modifyQueryUsing(function (Builder $query) {
                                return $query->where('tenant_id', auth()->user()->tenant_id);
                            })
                            ->withColumns([
                                Column::make('id')->heading('No')->formatStateUsing(function () {
                                    return $this->i++;
                                }),
                                Column::make('user.full_name')->heading(heading: __('messages.case.patient'))
                                    ->formatStateUsing(fn($record) => $record->user->full_name ?? __('messages.common.n/a')),
                                Column::make('user.email')->heading(heading: __('messages.user.email')),
                                Column::make('user.phone')->heading(heading: __('messages.user.phone')),
                                Column::make('user.gender')->heading(heading: __('messages.user.gender'))
                                    ->formatStateUsing(fn($state) => $state == 0 ? __('messages.user.male') : __('messages.user.female')),
                                Column::make('user.blood_group')->heading(heading: __('messages.user.blood_group')),
                                Column::make('user.dob')->heading(heading: __('messages.user.dob'))
                                    ->formatStateUsing(function ($record) {
                                        return $record->user->dob ? \Carbon\Carbon::parse($record->user->dob)->translatedFormat('jS M, Y') : __('messages.common.n/a');
                                    }),
                                Column::make('user.status')->heading(heading: __('messages.common.status'))
                                    ->formatStateUsing(fn($state) => $state ? __('messages.common.active') : __('messages.common.deactive')),
                            ]),
                    ]),
                    
                // Acción para abrir GenerateRipsReport directamente
                Actions\Action::make('testRipsService')
                    ->label('Probar Servicio RIPS')
                    ->icon('heroicon-o-beaker')
                    ->modalHeading('Prueba del Generador RIPS')
                    ->modalDescription('Genera un JSON de prueba para validar el servicio')
                    ->form([
                       Select::make('agreement_id')
                            ->label('Convenio')
                            ->options(\App\Models\Rips\RipsTenantPayerAgreement::pluck('name', 'id'))
                            ->required(),


                        Radio::make('report_type')
                            ->label('Tipo de Reporte')
                            ->options([
                                'with_invoice' => 'Con Factura',
                                'without_invoice' => 'Sin Factura',
                            ])
                            ->default('with_invoice')
                            ->required(),

                        DatePicker::make('start_date')->label('Fecha Inicial')->required(),
                        DatePicker::make('end_date')->label('Fecha Final')->required(),
                    ])
                    ->action(function (array $data) {
                        try {
                            $service = app(RipsGeneratorService::class);
                            $result = $service->generateByServices(
                                $data['agreement_id'],
                                $data['start_date'],
                                $data['end_date'],
                                $data['report_type'] === 'with_invoice'
                            );

                            return response()->streamDownload(function () use ($result) {
                                echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                            }, 'rips_report.json');
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error en la generación')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();

                            throw $e;
                        }
                    })
                    ->modalSubmitActionLabel('Generar Prueba')
                    ->modalCancelActionLabel('Cancelar'),
            ]),*/
            
        ];
    }


}
