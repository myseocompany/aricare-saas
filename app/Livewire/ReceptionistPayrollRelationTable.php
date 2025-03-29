<?php

namespace App\Livewire;

use Livewire\Component;
use Filament\Tables\Table;
use App\Models\EmployeePayroll;
use App\Models\Receptionist;
use Illuminate\Support\Facades\Route;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;

class ReceptionistPayrollRelationTable extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public $record;

    public function GetRecord()
    {
        $id = Route::current()->parameter('record');
        $nurses = Receptionist::with('payrolls')->where('id', $id)->get();

        foreach ($nurses as $item) {
            $this->record = $item->payrolls;
        }

        $payrollIds = $this->record->pluck('payroll_id')->toArray();
        $payrolls = EmployeePayroll::whereIn('payroll_id', $payrollIds);

        return $payrolls;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Self::GetRecord())
            ->paginated([10,25,50])
            ->columns([
                TextColumn::make('payroll_id')
                    ->badge()
                    ->label(__('messages.employee_payroll.payroll_id'))
                    ->searchable()
                    ->sortable()
                    ->color('primary'),
                TextColumn::make('month')
                    ->label(__('messages.employee_payroll.month'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('year')
                    ->label(__('messages.employee_payroll.year'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('basic_salary')
                    ->label(__('messages.employee_payroll.basic_salary'))
                    ->searchable()
                    ->formatStateUsing(fn($state) => getCurrencyFormat($state, 2))
                    ->sortable(),
                TextColumn::make('allowance')
                    ->label(__('messages.employee_payroll.allowance'))
                    ->searchable()
                    ->formatStateUsing(fn($state) => getCurrencyFormat($state, 2))
                    ->sortable(),
                TextColumn::make('deductions')
                    ->label(__('messages.employee_payroll.deductions'))
                    ->searchable()
                    ->formatStateUsing(fn($state) => getCurrencyFormat($state, 2))
                    ->sortable(),
                TextColumn::make('net_salary')
                    ->label(__('messages.employee_payroll.net_salary'))
                    ->searchable()
                    ->formatStateUsing(fn($state) => getCurrencyFormat($state, 2))
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('messages.common.status'))
                    ->searchable()
                    ->formatStateUsing(fn($state) => $state ? __('messages.employee_payroll.paid') : __('messages.employee_payroll.not_paid'))
                    ->badge()
                    ->color(fn($record) => $record->status ? 'success' : 'danger')
                    ->sortable(),
            ])
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
        return view('livewire.receptionist-payroll-relation-table');
    }
}
