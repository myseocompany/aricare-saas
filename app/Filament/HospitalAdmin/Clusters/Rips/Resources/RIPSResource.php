<?php

namespace App\Filament\HospitalAdmin\Clusters\Rips\Resources;
use Illuminate\Support\Facades\Log;
use App\Helpers\RipsFormatter;

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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'billingDocument',
                'consultations.diagnoses',
                'procedures',
            ])
            ->orderByDesc('service_datetime')
            ->withCount(['consultations', 'procedures']);
    }

protected function mutateFormDataBeforeFill(array $data): array
{
    return RipsFormatter::formatForForm($this->record);
}

}