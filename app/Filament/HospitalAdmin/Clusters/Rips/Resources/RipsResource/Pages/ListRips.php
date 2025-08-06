<?php

namespace App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsResource\Pages;

use Illuminate\Support\Facades\Auth;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\ViewAction;
use Illuminate\Database\Eloquent\Builder;

use App\Models\Rips\RipsPatientService;
use App\Helpers\RipsFormatter;
use App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsResource;

class ListRips extends ListRecords
{
    protected static string $resource = RipsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    protected function getHeaderWidgets(): array
    {
        return [
            StatusOverview::class
        ];
    }


protected function getTableActions(): array
{
    return [
        ViewAction::make()
            ->label('Ver')
            ->record(function (RipsPatientService $record) {
                return $record->load([
                    'billingDocument',
                    'consultations.diagnoses',
                    'consultations.principalDiagnoses',
                    'consultations.relatedDiagnoses',
                    'procedures',
                ]);
            })
            ->modalHeading('Vista detallada de RIPS')
            ->modalContent(function (RipsPatientService $record): \Filament\Forms\ComponentContainer {
                $data = \App\Helpers\RipsFormatter::formatForForm($record);

                return Grid::make(1)->schema([
                    TextEntry::make('Número de Factura')
                        ->value($data['billing_document_number'] ?? 'N/A'),

                    TextEntry::make('Consultas')
                        ->value(count($data['consultations']) . ' registradas'),

                    TextEntry::make('Procedimientos')
                        ->value(count($data['procedures']) . ' registrados'),
                    
                    // Agrega más detalles si lo deseas aquí
                ]);
            }),
    ];
}


}
