<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Medicine;
use App\Models\Pharmacist;
use Filament\Tables\Table;
use App\Models\Prescription;
use App\Models\PackageService;
use App\Models\EmployeePayroll;
use App\Models\InsuranceDisease;
use Illuminate\Support\Facades\Route;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use App\Models\PrescriptionMedicineModal;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;

class PackageServiceTable extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public $record;
    public $id;

    public function mount()
    {
        $this->id = Route::current()->parameter('record');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query((PackageService::where('package_id', $this->id)))
            ->heading(__('messages.services'))
            ->columns([
                TextColumn::make('service.name')
                    ->label(__('messages.package.service'))
                    ->default(__('messages.common.n/a')),
                TextColumn::make('quantity')
                    ->label(__('messages.package.qty'))
                    ->default(__('messages.common.n/a')),
                TextColumn::make('rate')
                    ->label(__('messages.package.rate'))
                    ->formatStateUsing(fn($state) => getCurrencyFormat($state, 2))
                    ->alignEnd()
                    ->default(__('messages.common.n/a')),
                TextColumn::make('amount')
                    ->label(__('messages.package.amount'))
                    ->formatStateUsing(fn($state) => getCurrencyFormat($state, 2))
                    ->alignEnd()
                    ->default(__('messages.common.n/a')),
            ])
            ->paginated(false)
            ->filters([
                //
            ])
            ->bulkActions([
                // 
            ])
            ->emptyStateHeading(__('messages.common.no_data_found'));
    }

    public function render()
    {
        return view('livewire.package-service-table');
    }
}
