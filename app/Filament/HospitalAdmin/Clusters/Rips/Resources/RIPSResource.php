<?php

namespace App\Filament\HospitalAdmin\Clusters\Rips\Resources;
use Illuminate\Support\Facades\Log;
use App\Helpers\RipsFormatter;

use Illuminate\Support\Facades\Auth;

use App\Filament\HospitalAdmin\Clusters\RipsCluster;
use App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsResource\Pages;
use App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsResource\RelationManagers;
use App\Models\Rips\RipsPatientService;
use App\Models\Patient;
use App\Models\Rips\RipsTenantPayerAgreement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Pages\SubNavigationPosition;

use App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsResource\Form\FormService;
use App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsResource\Form\FormConsultations;
use App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsResource\Form\FormProcedures;
use App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsResource\Table\FormTable;

use Filament\Forms\Components\Grid;

use App\Services\RipsGeneratorService;

use Illuminate\Support\Facades\Storage;



class RipsResource extends Resource
{
    protected static ?string $model = RipsPatientService::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    
    protected static ?string $cluster = RipsCluster::class;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Grid::make(1) 
                ->schema(array_merge(
                    FormService::make($form)->getComponents(),
                    FormConsultations::make($form)->getComponents(),
                    FormProcedures::make($form)->getComponents()
                )),
        ]);
    }

    public static function table(Table $table): Table
    {
        return FormTable::make($table);
    }


    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRips::route('/'),
            'create' => Pages\CreateRips::route('/create'),
            'edit' => Pages\EditRips::route('/{record}/edit'),
        ];
    }






    protected function mutateFormDataBeforeFill(array $data): array
    {
        return RipsFormatter::formatForForm($this->record);
    }

    public static function getModelLabel(): string
    {
        return __('messages.rips.patientservice.title_plural');
    }

    public static function getPluralModelLabel(): string
    {
        return __('messages.rips.patientservice.title_plural');
    }

    protected static function mutateFormDataBeforeSave(array $data): array
    {
        // Combina fecha y hora en un solo campo datetime
        $data['service_datetime'] = $data['service_date'] . ' ' . $data['service_time'];
        
        // Puedes eliminar los campos separados si no se necesitan en la BD
        unset($data['service_date'], $data['service_time']);

        return $data;
    }

public static function getEloquentQuery(): Builder
{
    dd([
        'user' => Auth::user(),
        'tenant_id' => Auth::user()?->tenant_id,
    ]);

    return parent::getEloquentQuery()
        ->where('tenant_id', Auth::user()->tenant_id)
        ->with([
            'billingDocument',
            'consultations.diagnoses',
            'procedures',
        ])
        ->orderByDesc('id');
}


}