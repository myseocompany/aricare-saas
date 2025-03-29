<?php

namespace App\Livewire;

use App\Models\Charge;
use Livewire\Component;
use App\Models\IpdCharge;
use Filament\Tables\Table;
use Filament\Tables\Actions;
use App\Models\ChargeCategory;
use Google\Service\CloudSearch\Id;
use App\Models\IpdPatientDepartment;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Route;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;

class IpdPatientBillChargeTable extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public $record;
    public $id;
    public $processedData = [];

    public function mount()
    {
        $this->id = Route::current()->parameter('record');
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('messages.charges'))
            ->query(IpdCharge::where('ipd_patient_department_id', $this->id))
            ->columns([
                TextColumn::make('charge_type_id')
                    ->label(__('messages.ipd_patient_charges.charge_type_id'))
                    ->default(__('messages.common.n/a'))
                    ->formatStateUsing(function ($record) {
                        if ($record->charge_type_id === 1) {
                            return __('messages.charge_filter.procedure');
                        } elseif ($record->charge_type_id === 2) {
                            return __('messages.charge_filter.investigation');
                        } elseif ($record->charge_type_id === 3) {
                            return __('messages.charge_filter.supplier');
                        } elseif ($record->charge_type_id === 4) {
                            return __('messages.charge_filter.operation_theater');
                        } else {
                            return __('messages.charge_filter.others');
                        }
                    }),
                TextColumn::make('chargecategory.name')
                    ->default(__('messages.common.n/a'))
                    ->label(__('messages.medicine.category')),
                TextColumn::make('date')
                    ->label(__('messages.ipd_patient_charges.date'))
                    ->default(__('messages.common.n/a'))
                    ->formatStateUsing(fn($state) => \Carbon\Carbon::parse($state)->translatedFormat('jS M, Y')),
                TextColumn::make('applied_charge')
                    ->label(__('messages.ipd_patient_charges.applied_charge'))
                    ->alignEnd()
                    ->formatStateUsing(function ($record) {
                        if (!empty($record->applied_charge)) {
                            return getCurrencyFormat($record->applied_charge);
                        } else {
                            return __('messages.common.n/a');
                        }
                    })
                    ->summarize([
                        Sum::make()
                            ->formatStateUsing(fn($state) => (getCurrencyFormat($state)))
                            ->label(''),
                    ]),
            ])
            ->paginated(false)
            ->actionsColumnLabel(__('messages.common.action'))
            ->filters([
                //
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ])
            ->emptyStateHeading(__('messages.common.no_data_found'));
    }

    public function render()
    {
        return view('livewire.ipd-patient-bill-charge-table');
    }
}
