<?php

namespace App\Livewire;

use Livewire\Component;
use Filament\Tables\Table;
use App\Models\EmployeePayroll;
use App\Models\Receptionist;
use App\Models\Schedule;
use App\Models\ScheduleDay;
use Illuminate\Support\Facades\Route;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;

class ViewSchedulesTable extends Component implements HasForms, HasTable
{

    use InteractsWithTable;
    use InteractsWithForms;

    public $record;

    public function GetRecord()
    {
        $id = Route::current()->parameter('record');
        $schedules = ScheduleDay::where('schedule_id', $id);

        return $schedules;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Self::GetRecord())
            ->columns([
                TextColumn::make('available_on')
                    ->label(__('messages.schedule.available_on'))
                    ->formatStateUsing(fn($state) => __('messages.weekdays.' . $state)) // <-- agrega esto
                    ->searchable(),
                TextColumn::make('available_from')
                    ->default(__('messages.common.n/a'))
                    ->formatStateUsing(fn($state) => date('H:i A', strtotime($state))),
                TextColumn::make('available_to')
                    ->default(__('messages.common.n/a'))
                    ->formatStateUsing(fn($state) => date('H:i A', strtotime($state))),
            ])
            ->paginated(false)
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
        return view('livewire.view-schedules-table');
    }
}
