<?php

namespace App\Livewire;

use App\Filament\HospitalAdmin\Clusters\Encounters\Resources\EncounterResource\EncounterResource as EncounterFilamentResource;
use App\Models\Rda\Encounter;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Route;
use Livewire\Component;

class PatientEncountersRelationTable extends Component implements HasTable, HasForms
{
    use InteractsWithForms;
    use InteractsWithTable;

    public int $patientId;

    public function mount(): void
    {
        $patientId = (int) Route::current()?->parameter('record');

        abort_if($patientId === 0, 404);

        $this->patientId = $patientId;
    }

    protected function getEncountersQuery(): Builder
    {
        return Encounter::query()
            ->with(['doctor.user', 'encounterType', 'status'])
            ->where('patient_id', $this->patientId)
            ->orderByDesc('start_at');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getEncountersQuery())
            ->paginated([10, 25, 50])
            ->columns([
                TextColumn::make('encounterType.name')
                    ->label(__('messages.encounter_type'))
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('doctor.user.full_name')
                    ->label(__('messages.patient_admission.doctor'))
                    ->formatStateUsing(fn ($state) => $state ?? __('messages.common.n/a'))
                    ->weight(FontWeight::SemiBold)
                    ->description(fn ($record) => $record->doctor?->user?->email ?? __('messages.common.n/a'))
                    ->searchable(['doctor.user.first_name', 'doctor.user.last_name'])
                    ->toggleable(),
                TextColumn::make('start_at')
                    ->label(__('messages.encounter_start_at'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('status.name')
                    ->label(__('messages.common.status'))
                    ->formatStateUsing(fn ($state) => $state ?? __('messages.common.n/a'))
                    ->badge()
                    ->color(fn ($state, $record) => match ($record->status?->code) {
                        'planned' => 'gray',
                        'in-progress' => 'info',
                        'finished' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('reason')
                    ->label(__('messages.encounter_reason'))
                    ->limit(50)
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                Action::make('createEncounter')
                    ->label(__('messages.encounters_create'))
                    ->icon('heroicon-o-plus')
                    ->color('primary')
                    ->url(fn () => EncounterFilamentResource::getUrl('create', [
                        'patient_id' => $this->patientId,
                    ])),
            ])
            ->emptyStateHeading(__('messages.common.no_data_found'));
    }

    public function render()
    {
        return view('livewire.patient-encounters-relation-table');
    }
}
