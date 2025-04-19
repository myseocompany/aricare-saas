<?php

namespace App\Livewire;

use App\Models\Doctor;
use Livewire\Component;
use Filament\Tables\Table;
use App\Models\ScheduleDay;
use Illuminate\Support\Facades\Route;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;

class DoctorScheduleRelationTable extends Component implements HasTable, HasForms
{

    use InteractsWithForms;
    use InteractsWithTable;

    public $record;
    public function GetRecordOld()
    {
        $id = Route::current()->parameter('record');

        $doctors = Doctor::with('schedules')->where('id', $id)->get();

        foreach ($doctors as $item) {
            $this->record = $item->cases;
        }

        $schedule_ids = $this->record->pluck('doctor_id')->toArray();

        $data = ScheduleDay::whereIn('doctor_id', $schedule_ids);

        return $data;
    }

    public function GetRecord()
    {
        $id = Route::current()->parameter('record');
        $doctor = Doctor::with('schedules')->findOrFail($id);
        $schedule_ids = $doctor->schedules->pluck('id')->toArray();
    
        return ScheduleDay::query()
            ->whereIn('schedule_id', $schedule_ids)
            ->where('doctor_id', $doctor->id);
    }
    


    public function table(Table $table): Table
    {
        return $table
            ->query(Self::GetRecord())
            ->columns([
                TextColumn::make('available_on')
                    ->searchable()
                    ->label(__('messages.schedule.available_on'))
                    ->formatStateUsing(fn($state) => __('messages.weekdays.full.' . (int) $state)),

                TextColumn::make('available_from')
                    ->label(__('messages.schedule.available_from'))
                    ->searchable()
                    ->getStateUsing(function ($record) {
                        if ($record->available_from == '00:00:00') {
                            return __('messages.common.n/a');
                        } else {
                            return date('H:i A', strtotime($record->available_from));
                        }
                    }),
                TextColumn::make('available_to')
                    ->searchable()
                    ->label(__('messages.schedule.available_to'))
                    ->getStateUsing(function ($record) {
                        if ($record->available_to == '00:00:00') {
                            return __('messages.common.n/a');
                        } else {
                            return date('H:i A', strtotime($record->available_to));
                        }
                    }),
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
        return view('livewire.doctor-schedule-relation-table');
    }
    public function mount()
    {
        $id = Route::current()->parameter('record');
        ///$this->doctor = Doctor::with('schedules.scheduleDays')->findOrFail($id);
    }
}
